<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'image'=>'/storage/mealsImages/'. $this->faker->word(),
            'name' => $this->faker->word(),
            'price'=>$this->faker->numberBetween(500,20000),
            'max_meals_per_day'=>$this->faker->numberBetween(3,50),
            'expected_preparation_time'=>$this->faker->numberBetween(10,120),
            'ingredients'=>$this->faker->sentence(20),
        ];
    }
}
