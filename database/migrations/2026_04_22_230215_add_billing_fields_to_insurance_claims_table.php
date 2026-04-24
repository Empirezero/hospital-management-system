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
        Schema::table('insurance_claims', function (Blueprint $table) {
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete()->after('sale_id');
            $table->foreignId('insurance_provider_id')->nullable()->constrained('insurance_providers')->nullOnDelete()->after('bill_id');
            $table->string('claim_number')->nullable()->unique()->after('insurance_provider_id');
            $table->string('claim_type')->nullable()->after('claim_number');
            $table->string('scheme_name')->nullable()->after('claim_type');
            $table->string('principal_member_name')->nullable()->after('scheme_name');
            $table->string('relationship_to_principal')->nullable()->after('principal_member_name');
            $table->date('card_expiry_date')->nullable()->after('relationship_to_principal');
            $table->decimal('paid_amount', 12, 2)->nullable()->after('card_expiry_date');
            $table->string('insurer_reference')->nullable()->after('paid_amount');
            $table->timestamp('approved_at')->nullable()->after('insurer_reference');
            $table->timestamp('settled_at')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('settled_at');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_claims', function (Blueprint $table) {
            $table->dropForeign(['bill_id']);
            $table->dropForeign(['insurance_provider_id']);
            $table->dropColumn([
                'bill_id',
                'insurance_provider_id',
                'claim_number',
                'claim_type',
                'scheme_name',
                'principal_member_name',
                'relationship_to_principal',
                'card_expiry_date',
                'paid_amount',
                'insurer_reference',
                'approved_at',
                'settled_at',
                'rejected_at',
            ]);
        });
    }
};
