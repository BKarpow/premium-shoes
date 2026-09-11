<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Обов'язковий метод definition
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // або '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Кастомний метод для створення користувача з ролью
     */
    public function withRole(string $roleName): static
    {
        return $this->afterCreating(function ($user) use ($roleName) {
            Role::firstOrCreate(['name' => $roleName]);
            $user->assignRole($roleName);
        });
    }
}
