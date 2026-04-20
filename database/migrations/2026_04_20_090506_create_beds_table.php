<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Wards first
        if (!Schema::hasTable('wards')) {
            Schema::create('wards', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['general', 'icu', 'emergency', 'private']);
                $table->integer('total_beds')->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Beds second (depends on wards)
        if (!Schema::hasTable('beds')) {
            Schema::create('beds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ward_id')->constrained()->onDelete('cascade');
                $table->string('bed_number');
                $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['ward_id', 'bed_number']);
            });
        }

        // 3. Admissions last (depends on beds and wards)
        if (!Schema::hasTable('admissions')) {
            Schema::create('admissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bed_id')->constrained()->onDelete('cascade');
                $table->foreignId('ward_id')->constrained()->onDelete('cascade');
                $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
                $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
                $table->string('patient_name');
                $table->string('patient_email')->nullable();
                $table->string('patient_phone')->nullable();
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['admitted', 'discharged', 'transferred'])->default('admitted');
                $table->timestamp('admitted_at')->useCurrent();
                $table->timestamp('discharged_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('wards');
    }
};
