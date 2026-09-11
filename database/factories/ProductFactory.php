<?php
namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);
        $price = fake()->randomFloat(2, 1000, 5500);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'title' => ucfirst($title),
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'description' => fake()->paragraphs(2, true),
            'price' => $price,
            'old_price' => fake()->boolean(30) ? $price + fake()->numberBetween(300, 1000) : null,
            'gender' => fake()->randomElement(['men', 'women', 'kids', 'unisex']),
            'color' => fake()->randomElement(['Чорний', 'Білий', 'Синій', 'Сірий', 'Червоний']),
            'material' => fake()->randomElement(['Натуральна шкіра', 'Замша', 'Текстиль/Сітка', 'Еко-шкіра']),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
