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
        Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

                // Контактні дані
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone');
                $table->string('email')->nullable();

                // Доставка
                $table->string('shipping_type'); // 'pickup' або 'nova_poshta'
                $table->string('city_ref')->nullable();       // Ref міста НП
                $table->string('city_name')->nullable();      // Назва міста для зручності читання в БД
                $table->string('warehouse_ref')->nullable();  // Ref відділення/поштомату НП
                $table->string('warehouse_address')->nullable(); // Текстова адреса відділення

                // Фінанси та статус
                $table->decimal('total_price', 10, 2);
                $table->string('status')->default('new'); // new, processing, shipped, completed, cancelled
                $table->string('payment_status')->default('pending'); // pending, paid

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
