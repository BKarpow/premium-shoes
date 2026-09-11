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
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('value');                     // Наприклад: 40, 41, 42, 42.5
            $table->decimal('length_cm', 4, 1)->nullable(); // Наприклад: 26.5 см (для підказок покупцеві)
            $table->integer('sort_order')->default(0);   // Для правильного сортування від меншого до більшого
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
