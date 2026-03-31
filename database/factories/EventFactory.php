<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['Сдача ведомостей', 'Педсовет', 'Праздник', 'Собрание']),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'type' => fake()->randomElement(['deadline', 'meeting', 'event']),
            'color' => fake()->randomElement(['bg-indigo-500', 'bg-rose-500', 'bg-emerald-500']),
        ];
    }
}
