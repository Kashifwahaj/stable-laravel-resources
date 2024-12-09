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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Product name
            $table->text('description'); // Product description
            $table->decimal('price', 10, 2); // Product price, allows 2 decimal places
            $table->integer('stock_quantity')->default(0); // Quantity in stock
            $table->string('sku')->nullable(); // SKU for product identification
            $table->string('image')->nullable(); // Optional product image URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
