<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $username;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->username = config('services.africastalking.username');
        $this->apiKey   = config('services.africastalking.api_key');
        $this->sender   = config('services.africastalking.sender') ?: null;
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $client   = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->post('https://api.sandbox.africastalking.com/version1/messaging', [
                'headers' => [
                    'Accept' => 'application/json',
                    'apiKey' => $this->apiKey,
                ],
                'form_params' => [
                    'username' => $this->username,
                    'to'       => $this->formatPhone($phone),
                    'message'  => $message,
                ],
            ]);

            $body = $response->getBody()->getContents();
            \Illuminate\Support\Facades\Log::info('SMS response: ' . $body);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMS failed: ' . $e->getMessage());
            return false;
        }
    }
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '0')) {
            return '+254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '254')) {
            return '+' . $phone;
        }
        return $phone;
    }
    
    

    
}
