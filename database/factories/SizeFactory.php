<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SizeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'value' => (string) fake()->numberBetween(36, 46),
            'length_cm' => fake()->randomElement([23.5, 24.0, 25.0, 26.0, 27.0, 28.0, 29.0]),
            'sort_order' => 0,
        ];
    }
}
