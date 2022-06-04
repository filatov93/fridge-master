<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'booking_uid' => Str::random(12),
            'user_id' => $this->faker->numberBetween(1, 12),
            'price' => $this->faker->randomFloat(2, 100, 500),
        ];
    }
}
