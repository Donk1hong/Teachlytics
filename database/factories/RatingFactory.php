<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'student_id' => Student::factory(),
            'attendance' => $this->faker->numberBetween(50, 100),
            'activity' => $this->faker->numberBetween(50, 100),
            'discipline' => $this->faker->numberBetween(50, 100),
            'total_score' => $this->faker->randomFloat(1, 3, 5),
            'period' => 'month',
            'created_at' => $this->faker->dateTimeBetween('-10 months', 'now'),
        ];
    }
}
