<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('bill_number')->unique()->comment('e.g. BILL-2025-00001');
            $table->enum('bill_type', ['outpatient', 'inpatient', 'emergency']);

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('status', [
                'draft',       // being built, not yet presented to patient
                'open',        // presented, payment pending
                'partial',     // some payment received
                'paid',        // fully settled
                'void',        // cancelled/reversed
                'written_off', // bad debt, management approved
            ])->default('draft');

            // Financials — all in KES
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Sum of bill_items.line_total');
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Bill-level discount %');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('VAT if applicable');
            $table->decimal('insurance_covered', 12, 2)->default(0)->comment('Amount covered by insurer');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('Patient liability after insurance & discount');
            $table->decimal('amount_paid', 12, 2)->default(0)->comment('Sum of confirmed payments');
            $table->decimal('balance_due', 12, 2)->default(0)->comment('total_amount - amount_paid');

            // Dates
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('bill_type');
            $table->index('created_at');
            $table->index(['patient_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
