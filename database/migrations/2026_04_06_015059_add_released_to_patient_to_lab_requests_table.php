<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->boolean('released_to_patient')->default(false)->after('status');
            $table->timestamp('released_at')->nullable()->after('released_to_patient');
        });
    }

    public function down(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropColumn(['released_to_patient', 'released_at']);
        });
    }
};
