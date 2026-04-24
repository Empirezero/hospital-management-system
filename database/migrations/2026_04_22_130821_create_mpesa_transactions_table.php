<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();

            // Linked to a payment only AFTER callback confirms success
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();

            // Safaricom STK Push identifiers
            $table->string('merchant_request_id')->unique()->comment('MerchantRequestID from STK Push response');
            $table->string('checkout_request_id')->unique()->comment('CheckoutRequestID from STK Push response');

            // Filled in by the callback
            $table->string('mpesa_receipt_number')->nullable()->comment('e.g. RKX0E1ABC0 — from callback');

            // Request details
            $table->string('phone_number')->comment('Format: 2547XXXXXXXX');
            $table->decimal('amount', 12, 2);
            $table->string('account_reference')->comment('Bill number sent as account ref');
            $table->string('transaction_desc')->nullable();

            // Status tracking
            $table->enum('status', [
                'initiated',   // STK Push sent, waiting for user action
                'pending',     // User saw prompt, awaiting callback
                'completed',   // ResultCode = 0, payment confirmed
                'failed',      // ResultCode != 0
                'cancelled',   // User cancelled on their phone
                'timeout',     // No callback received within expected window
            ])->default('initiated');

            // Callback payload
            $table->integer('result_code')->nullable()->comment('0 = success');
            $table->string('result_description')->nullable();
            $table->timestamp('transaction_date')->nullable()->comment('From M-Pesa callback TransactionDate');

            // Store full raw callback for audit/debugging
            $table->json('raw_callback')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('phone_number');
            $table->index('mpesa_receipt_number');
            $table->index('bill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
