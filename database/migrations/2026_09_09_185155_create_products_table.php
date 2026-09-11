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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');                     // Назва: Чоловічі кросівки Nike Air Force 1
            $table->string('slug')->unique();            // nike-air-force-1
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);             // Поточна ціна
            $table->decimal('old_price', 10, 2)->nullable(); // Стара ціна (для знижок)

            $table->string('gender')->default('unisex'); // men, women, kids, unisex
            $table->string('color')->nullable();         // Основний колір (Чорний, Білий)
            $table->string('material')->nullable();      // Матеріал: Натуральна шкіра, Замша, Сітка

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Для блоку "Популярні товари" / "Новинки"
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
