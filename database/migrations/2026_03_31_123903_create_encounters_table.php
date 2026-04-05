<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->text('chief_complaint');
            $table->text('examination_notes')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->enum('visit_type', [
                'outpatient',
                'inpatient',
                'emergency',
                'follow_up'
            ])->default('outpatient');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('visited_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
