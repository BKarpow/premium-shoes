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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            // Зв'язок з основною таблицею users
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete(); // При видаленні користувача його профіль видалиться автоматично

            // Персональні/контактні дані
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('phone')->nullable();

            // Дані для доставки Новою Поштою
            $table->string('np_city_ref')->nullable();        // Ref міста з API НП
            $table->string('np_city_name')->nullable();       // Назва міста
            $table->string('np_warehouse_ref')->nullable();   // Ref відділення/поштомату
            $table->string('np_warehouse_name')->nullable();  // Назва/номер відділення
            $table->string('np_warehouse_address')->nullable(); // Адреса відділення

            // Додаткові резервні/майбутні поля (nullable)
            $table->date('birth_date')->nullable();           // Дата народження
            $table->string('gender')->nullable();             // Стать
            $table->string('telegram_chat_id')->nullable();   // Telegram ID для сповіщень
            $table->string('discount_card')->nullable();      // Дисконтна картка
            $table->decimal('bonus_balance', 10, 2)->default(0.00)->nullable(); // Бонусні бали
            $table->text('notes')->nullable();                // Примітки/нотатки про клієнта

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
