<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_number')->unique()->comment('e.g. RCP-2025-00001');

            $table->foreignId('bill_id')->constrained('bills')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();

            // Snapshots — intentionally denormalised
            // These must NOT change if the patient record changes
            $table->string('patient_name');
            $table->string('patient_number')->nullable();
            $table->string('bill_number');
            $table->string('payment_method');

            // Financials at time of receipt
            $table->decimal('amount_received', 12, 2);
            $table->decimal('bill_total', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);

            // Issue & void
            $table->timestamp('issued_at');
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();

            $table->timestamps();

            $table->index('issued_at');
            $table->index('bill_id');
            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
