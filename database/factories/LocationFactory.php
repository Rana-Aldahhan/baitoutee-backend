<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'longitude'=>$this->faker->randomFloat(7, 30, 35),
            'latitude'=>$this->faker->randomFloat(7, 34,36),
            'name'=>$this->faker->sentence(4),
            'distance_to_first_location'=>$this->faker->randomFloat(2, 0, 20),
            'distance_to_second_location'=>$this->faker->randomFloat(2, 0, 20),
            'distance_to_third_location'=>$this->faker->randomFloat(2, 0, 20),
        ];
    }
}
