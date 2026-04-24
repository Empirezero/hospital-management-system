<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('e.g. CONS-GP-001');
            $table->string('name');
            $table->enum('category', [
                'consultation',
                'lab',
                'pharmacy',
                'procedure',
                'bed',
                'other',
            ]);
            $table->decimal('standard_price', 10, 2);
            $table->decimal('nhif_price', 10, 2)->nullable()->comment('SHA tariff price');
            $table->boolean('is_nhif_covered')->default(false);
            $table->string('nhif_code')->nullable()->comment('Official SHA/NHIF procedure code');
            $table->string('unit')->default('per visit')->comment('per visit, per day, per test, per tablet');
            $table->string('department')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('is_active');
            $table->index('is_nhif_covered');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
