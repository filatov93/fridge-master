<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $availableSpace = $this->faker->randomFloat('2', '0', '2');
        $fulfilled = $availableSpace == 2;
        return [
            'fulfilled' => $fulfilled,
            'available_space' => $availableSpace,
            'warehouse_id' => $this->faker->numberBetween(1, 12),
        ];
    }
}
