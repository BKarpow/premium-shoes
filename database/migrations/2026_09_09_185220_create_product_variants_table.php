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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->constrained()->cascadeOnDelete();

            $table->string('sku')->unique();             // Унікальний артикул конкретної пари (наприклад, NK-AF1-BLK-42)
            $table->integer('stock')->default(0);        // Кількість на складі
            $table->decimal('price_override', 10, 2)->nullable(); // Якщо якийсь розмір коштує дорожче/дешевше

            $table->timestamps();

            // Унікальний індекс, щоб у одного товару не було двох однакових розмірів
            $table->unique(['product_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
