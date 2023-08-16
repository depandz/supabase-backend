<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Driver;
use App\Models\PickupRequest;

class PickupRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PickupRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sid' => $this->faker->word,
            'client_id' => Client::factory(),
            'driver_id' => Driver::factory(),
            'destination' => '{}',
            'location' => '{}',
            'date_requested' => $this->faker->dateTime(),
            'estimated_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'estimated_distance' => $this->faker->randomFloat(0, 0, 9999999999.),
            'estimated_time' => $this->faker->numberBetween(-10000, 10000),
            'state' => $this->faker->randomElement(["0","1","2","3","4"]),
            'total' => $this->faker->randomFloat(0, 0, 9999999999.),
            'vehicle_type' => $this->faker->randomElement(["light","heavy","truck"]),
            'is_vehicle_empty' => $this->faker->boolean,
            'vehicle_licence_plate' => $this->faker->numberBetween(-10000, 10000),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
