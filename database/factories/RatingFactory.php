<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Rating;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'driver_id' => Driver::factory(),
            'value' => $this->faker->randomElement(/** enum_attributes **/),
            'comment' => $this->faker->word,
        ];
    }
}
