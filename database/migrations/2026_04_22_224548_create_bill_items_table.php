<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete();

            // What type of charge
            $table->enum('item_type', [
                'consultation',
                'lab',
                'pharmacy',
                'procedure',
                'bed',
                'other',
            ]);

            // Optional FK back to the source module — at most one will be set
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->foreignId('lab_test_id')
                ->nullable()
                ->constrained('lab_tests')
                ->nullOnDelete();

            $table->foreignId('prescription_item_id')
                ->nullable()
                ->constrained('prescription_items')
                ->nullOnDelete();

            // Free-text fallback (always required for human readability)
            $table->string('description');

            // Pricing — snapshot at time of billing, NOT live FK
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->comment('Price at time of billing');
            $table->decimal('nhif_rate', 10, 2)->nullable()->comment('SHA tariff for this item if applicable');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->comment('(unit_price * quantity) - discount_amount');

            // Insurance
            $table->boolean('is_insurance_covered')->default(false);
            $table->decimal('insurance_amount', 10, 2)->default(0)->comment('Portion covered by insurer');
            $table->decimal('patient_portion', 10, 2)->default(0)->comment('Remaining after insurance');

            // Bed-specific (only relevant when item_type = bed)
            $table->string('ward_name')->nullable();
            $table->date('admission_date')->nullable();
            $table->date('discharge_date')->nullable();
            $table->integer('days')->nullable();

            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['bill_id', 'item_type']);
            $table->index('is_insurance_covered');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
