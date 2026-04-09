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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('sale_type', ['prescription', 'otc', 'insurance'])->default('otc')->after('user_id');
            $table->enum('payment_method', ['cash', 'mpesa', 'insurance', 'credit', 'billed'])->default('cash')->after('sale_type');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->enum('payment_status', ['paid', 'pending', 'waived', 'billed'])->default('paid')->after('payment_reference');
            $table->string('billed_to')->nullable()->after('payment_status'); // e.g. company name, insurer
            $table->date('bill_due_date')->nullable()->after('billed_to');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['prescription_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn([
                'prescription_id',
                'patient_id',
                'sale_type',
                'payment_method',
                'payment_reference',
                'payment_status',
                'billed_to',
                'bill_due_date',
            ]);
        });
    }
};
