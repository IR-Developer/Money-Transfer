<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'src_id' => $this->faker->numberBetween(1,10),
            'des_id' => $this->faker->numberBetween(11,20),
            'status' => $this->faker->boolean,
            'bank_ref_code' => $this->faker->unique()->numerify('############'),
            'our_ref_code'   => rand(100, 999).time().rand(100, 999),
            'amount' => $this->faker->numberBetween(100000,500000000),
        ];
    }
}
