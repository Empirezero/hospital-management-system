<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_number')->unique()->comment('e.g. PAY-2025-00001');

            $table->foreignId('bill_id')->constrained('bills')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('payment_method', [
                'cash',
                'mpesa',
                'nhif',
                'sha',
                'insurance',  // private/corporate insurer direct settlement
                'corporate',  // company billed — invoice later
                'waiver',     // management-approved waiver
            ]);

            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',    // initiated but not confirmed (e.g. M-Pesa STK not yet acknowledged)
                'confirmed',  // received and verified
                'failed',     // M-Pesa failure, cheque bounce, etc.
                'reversed',   // refunded or cancelled post-confirmation
            ])->default('confirmed');

            // Reference numbers
            $table->string('reference_number')->nullable()->comment('M-Pesa code, bank ref, cheque no, insurance auth code');
            $table->string('bank_name')->nullable();
            $table->string('cheque_number')->nullable();

            $table->timestamp('paid_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bill_id', 'status']);
            $table->index('payment_method');
            $table->index('paid_at');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
