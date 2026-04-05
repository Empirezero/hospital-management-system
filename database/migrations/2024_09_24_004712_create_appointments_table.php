<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('number')->nullable();
            $table->dateTime('scheduled_at');
            $table->text('message')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');
            $table->enum('type', [
                'outpatient',
                'follow_up',
                'emergency',
                'consultation'
            ])->default('outpatient');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
