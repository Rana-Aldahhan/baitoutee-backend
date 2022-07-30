<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
            'email_verified_at' => now(),
            'location_id'=>$this->faker->randomElement([1,2,3]),
            'phone_number'=>$this->faker->unique()->numerify('09########'),
            'birth_date'=>$this->faker->dateTimeBetween('1980-01-01', '2003-12-31')->format('Y-m-d'),
            'gender'=>$this->faker->randomElement(['f','m']),
            'national_id'=>$this->faker->unique()->numerify('##########'),
            'campus_card_id'=>$this->faker->unique()->numerify('#####'),
            'campus_unit_number'=>$this->faker->randomNumber(2, true),
            'campus_card_expiry_date'=>now()->addMonths(3)->format('Y-m-d'),
            'study_specialty'=>$this->faker->word(),
            'study_year'=>$this->faker->randomElement([1,2,3,4,5,6]),
            'approved_at'=>$this->faker->dateTimeThisMonth(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
