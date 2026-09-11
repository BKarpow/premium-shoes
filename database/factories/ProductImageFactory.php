<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            // Для тесту використовуємо сервіс згенерованих зображень
            'path' => 'https://via.placeholder.com/600x600.png/002244?text=Shoe+Photo',
            'sort_order' => 0,
            'is_main' => false,
        ];
    }
}
