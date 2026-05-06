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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('bill_item_id')
                ->nullable()
                ->constrained('bill_items')
                ->nullOnDelete()
                ->after('encounter_id');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['bill_item_id']);
            $table->dropColumn('bill_item_id');
        });
    }
};
