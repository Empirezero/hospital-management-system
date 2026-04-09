<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete(); // pharmacist

            // Insurer details
            $table->string('insurer_name');
            $table->string('policy_number');
            $table->string('member_number')->nullable();

            // Claim financials
            $table->decimal('claimed_amount', 10, 2);
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('patient_copay', 10, 2)->default(0); // amount patient still owes

            // Status tracking
            $table->enum('status', [
                'draft',        // pharmacist created but not submitted
                'submitted',    // sent to insurer
                'under_review', // insurer is reviewing
                'approved',     // insurer approved
                'partial',      // insurer partially approved
                'rejected',     // insurer rejected
                'paid',         // payment received
                'appealed',     // rejection appealed
            ])->default('draft');

            // Dates
            $table->date('submitted_at')->nullable();
            $table->date('response_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();

            // Notes
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_reference')->nullable(); // insurer payment ref

            // Admin tracking
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }
};
