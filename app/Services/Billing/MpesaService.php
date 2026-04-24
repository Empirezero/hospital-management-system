<?php

namespace App\Services\Billing;

use App\Models\Bill;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Enums\Billing\MpesaStatus;
use App\Enums\Billing\PaymentMethod;
use App\Enums\Billing\PaymentStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MpesaService
{
    private string $baseUrl;

    public function __construct(
        private readonly NumberGeneratorService $numbers,
    ) {
        $this->baseUrl = config('mpesa.environment') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    // =========================================================================
    // ACCESS TOKEN
    // =========================================================================

    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_access_token', 55 * 60, function () {
            $response = Http::withBasicAuth(
                config('mpesa.consumer_key'),
                config('mpesa.consumer_secret'),
            )->get("{$this->baseUrl}/oauth/v1/generate", [
                'grant_type' => 'client_credentials',
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Failed to get M-Pesa access token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    // =========================================================================
    // STK PUSH
    // =========================================================================

    public function initiateStk(
        Bill    $bill,
        string  $phoneNumber,
        float   $amount,
        ?string $description = null,
    ): MpesaTransaction {
        $phone     = $this->normalizePhone($phoneNumber);
        $amount    = (int) ceil($amount);
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp
        );

        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(30)
            ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                'BusinessShortCode' => config('mpesa.shortcode'),
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $amount,
                'PartyA'            => $phone,
                'PartyB'            => config('mpesa.shortcode'),
                'PhoneNumber'       => $phone,
                'CallBackURL'       => config('mpesa.callback_url'),
                'AccountReference'  => $bill->bill_number,
                'TransactionDesc'   => $description ?? "Payment for {$bill->bill_number}",
            ]);

        if (! $response->successful()) {
            Log::error('M-Pesa STK Push failed', [
                'bill'     => $bill->bill_number,
                'phone'    => $phone,
                'response' => $response->json(),
            ]);
            throw new \RuntimeException('M-Pesa request failed: ' . $response->body());
        }

        $data = $response->json();

        if (($data['ResponseCode'] ?? '1') !== '0') {
            throw new \RuntimeException('M-Pesa rejected the request: ' . ($data['ResponseDescription'] ?? 'Unknown error'));
        }

        return MpesaTransaction::create([
            'bill_id'             => $bill->id,
            'merchant_request_id' => $data['MerchantRequestID'],
            'checkout_request_id' => $data['CheckoutRequestID'],
            'phone_number'        => $phone,
            'amount'              => $amount,
            'account_reference'   => $bill->bill_number,
            'transaction_desc'    => $description ?? "Payment for {$bill->bill_number}",
            'status'              => MpesaStatus::Initiated,
        ]);
    }

    // =========================================================================
    // CALLBACK HANDLER
    // =========================================================================

    public function handleCallback(array $callbackData): bool
    {
        $body              = $callbackData['Body']['stkCallback'] ?? [];
        $merchantRequestId = $body['MerchantRequestID'] ?? null;

        if (! $merchantRequestId) {
            Log::warning('M-Pesa callback received with no MerchantRequestID', $callbackData);
            return false;
        }

        $transaction = MpesaTransaction::where('merchant_request_id', $merchantRequestId)->first();

        if (! $transaction) {
            Log::error('M-Pesa callback: no matching transaction found', [
                'merchant_request_id' => $merchantRequestId,
            ]);
            return false;
        }

        if ($transaction->status->isTerminal()) {
            Log::info('M-Pesa callback: transaction already terminal, skipping', [
                'id'     => $transaction->id,
                'status' => $transaction->status->value,
            ]);
            return $transaction->status === MpesaStatus::Completed;
        }

        $resultCode = $body['ResultCode'] ?? -1;
        $resultDesc = $body['ResultDesc'] ?? 'Unknown';

        $transaction->update([
            'result_code'        => $resultCode,
            'result_description' => $resultDesc,
            'raw_callback'       => $callbackData,
        ]);

        if ($resultCode !== 0) {
            $transaction->update([
                'status' => match ($resultCode) {
                    1032   => MpesaStatus::Cancelled,
                    default => MpesaStatus::Failed,
                },
            ]);

            Log::info('M-Pesa payment failed', [
                'transaction_id' => $transaction->id,
                'result_code'    => $resultCode,
                'result_desc'    => $resultDesc,
            ]);

            return false;
        }

        // Extract metadata
        $items = collect($body['CallbackMetadata']['Item'] ?? [])
            ->pluck('Value', 'Name');

        $receiptNumber   = $items->get('MpesaReceiptNumber');
        $transactionDate = $items->get('TransactionDate');

        $transaction->update([
            'status'               => MpesaStatus::Completed,
            'mpesa_receipt_number' => $receiptNumber,
            'transaction_date'     => $transactionDate
                ? \Carbon\Carbon::createFromFormat('YmdHis', (string) $transactionDate)
                : now(),
        ]);

        // Create confirmed payment
        $payment = Payment::create([
            'payment_number'   => $this->numbers->paymentNumber(),
            'bill_id'          => $transaction->bill_id,
            'patient_id'       => $transaction->bill->patient_id,
            'received_by'      => 1, // system user
            'payment_method'   => PaymentMethod::Mpesa,
            'amount'           => $transaction->amount,
            'status'           => PaymentStatus::Confirmed,
            'reference_number' => $receiptNumber,
            'paid_at'          => now(),
            'confirmed_at'     => now(),
            'notes'            => 'M-Pesa STK Push — auto confirmed via callback',
        ]);

        $transaction->update(['payment_id' => $payment->id]);

        // Recalculate bill
        $transaction->bill->recalculate();

        Log::info('M-Pesa payment confirmed', [
            'transaction_id' => $transaction->id,
            'receipt'        => $receiptNumber,
            'bill'           => $transaction->bill->bill_number,
            'amount'         => $transaction->amount,
        ]);

        return true;
    }

    // =========================================================================
    // STATUS QUERY
    // =========================================================================

    public function queryStatus(MpesaTransaction $transaction): array
    {
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp
        );

        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(30)
            ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
                'BusinessShortCode' => config('mpesa.shortcode'),
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $transaction->checkout_request_id,
            ]);

        $data = $response->json();

        if (isset($data['ResultCode'])) {
            $this->handleCallback([
                'Body' => [
                    'stkCallback' => [
                        'MerchantRequestID' => $transaction->merchant_request_id,
                        'CheckoutRequestID' => $transaction->checkout_request_id,
                        'ResultCode'        => (int) $data['ResultCode'],
                        'ResultDesc'        => $data['ResultDesc'] ?? '',
                        'CallbackMetadata'  => ['Item' => []],
                    ],
                ],
            ]);
        }

        return $data;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        return match (true) {
            str_starts_with($phone, '254') => $phone,
            str_starts_with($phone, '0')   => '254' . substr($phone, 1),
            str_starts_with($phone, '7')   => '254' . $phone,
            str_starts_with($phone, '1')   => '254' . $phone,
            default => throw new \InvalidArgumentException("Unrecognised phone format: {$phone}"),
        };
    }
}
