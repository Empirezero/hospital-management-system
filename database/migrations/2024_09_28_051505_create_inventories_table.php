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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade'); // Foreign key medicine_id
            $table->foreignId('user_id')->constrained()->onDelete('cascade');     // Foreign key user_id
            $table->integer('stock_added');
            $table->integer('current_stock');
            $table->date('stock_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('price', 8, 2); // Add price column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
