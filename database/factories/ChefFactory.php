<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chef>
 */
class ChefFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number'=>$this->faker->unique()->numerify('09########'),
            'birth_date'=>$this->faker->dateTimeBetween('1950-01-01', '1999-12-31')->format('Y-m-d'),
            'gender'=>$this->faker->randomElement(['f','m']),
            'approved_at'=>$this->faker->dateTimeThisMonth(),
            // 'delivery_starts_at'=>'10:00:00',
            // 'delivery_ends_at'=>'18:00:00',
            'max_meals_per_day'=>$this->faker->numberBetween(0, 100),
            'profile_picture'=>'/storage/profiles/'. $this->faker->name(),
        ];
    }
}
