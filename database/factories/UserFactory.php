<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'pseudo' => $this->faker->userName(),
            'password' => bcrypt('12345'), // mot de passe par défaut
            'role' => $this->faker->boolean(30), // 30% de chance que ce soit un admin (true)
        ];
    }
}
