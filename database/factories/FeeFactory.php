<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Fee;
use App\Models\Province;

class FeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'province_id' => Province::factory(),
            'heavy' => $this->faker->randomFloat(0, 0, 9999999999.),
            'light' => $this->faker->randomFloat(0, 0, 9999999999.),
            'truck' => $this->faker->randomFloat(0, 0, 9999999999.),
            'full_percentage' => $this->faker->randomNumber(),
        ];
    }
}
