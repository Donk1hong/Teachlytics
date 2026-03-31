<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Группа ' . fake()->bothify('??-###'),
            'department' => fake()->randomElement(['Информатика', 'Биология', 'Экономика', 'Дизайн']),
            'average_score' => 0,
        ];
    }
}
