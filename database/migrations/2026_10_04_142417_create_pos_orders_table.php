<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();

            // Продавець, який здійснив продаж (залогінений користувач/адмін)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Товар та конкретна варіація (розмір)
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();

            // Кількість та ціна продажу
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);

            // Дані покупця (необов'язково для швидкого продажу в магазині)
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            // Спосіб оплати (готівка, термінал тощо)
            $table->string('payment_method')->default('cash');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
