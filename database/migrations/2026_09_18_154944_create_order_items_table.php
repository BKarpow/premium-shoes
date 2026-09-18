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
        Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();

                $table->string('product_name'); // Назва товару на момент покупки
                $table->string('size_value')->nullable(); // Розмір (наприклад, "42")
                $table->decimal('price', 10, 2);          // Ціна за одиницю
                $table->unsignedInteger('quantity');      // Кількість

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
