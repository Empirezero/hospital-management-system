<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Billing\MpesaService;
use App\Services\Billing\NumberGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('M-Pesa STK callback received', [
            'merchant_request_id' => data_get($payload, 'Body.stkCallback.MerchantRequestID'),
            'result_code'         => data_get($payload, 'Body.stkCallback.ResultCode'),
        ]);

        try {
            $mpesa = new MpesaService(new NumberGeneratorService);
            $mpesa->handleCallback($payload);
        } catch (\Throwable $e) {
            Log::error('M-Pesa callback processing error', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
        }

        // Always return 200 to Safaricom — they retry on non-200
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}
