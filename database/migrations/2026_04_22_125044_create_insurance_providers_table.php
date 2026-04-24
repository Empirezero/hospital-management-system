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
        Schema::create('insurance_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable()->comment('e.g. AAR, Jubilee, CIC');
            $table->enum('type', ['sha', 'nhif', 'corporate', 'private']);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('claim_submission_method')->nullable()->comment('email, portal, physical');
            $table->string('claim_email')->nullable();
            $table->string('portal_url')->nullable();
            $table->integer('credit_limit_days')->default(30)->comment('Days allowed before payment expected');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_providers');
    }
};
