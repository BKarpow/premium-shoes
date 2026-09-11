<?php
namespace Database\Factories;

use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size_id' => Size::factory(),
            'sku' => strtoupper(Str::random(3)) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'stock' => fake()->numberBetween(0, 15),
            'price_override' => null,
        ];
    }
}
