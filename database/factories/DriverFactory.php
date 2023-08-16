<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Province;

class DriverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Driver::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            's_id' => $this->faker->word,
            'full_name' => $this->faker->word,
            'phone_number' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(["pending","active","suspended"]),
            'identity_card_number' => $this->faker->numberBetween(-10000, 10000),
            'licence_plate' => $this->faker->numberBetween(-10000, 10000),
            'photo' => $this->faker->word,
            'province_id' => Province::factory(),
            'location' => '{}',
            'email' => $this->faker->safeEmail,
            'is_online' => $this->faker->boolean,
            'reported_count' => $this->faker->randomNumber(),
            'messaging_token' => $this->faker->word,
            'account_status' => $this->faker->randomElement(["pending","active","suspended"]),
            'vehicle_type' => $this->faker->word,
            'commercial_register_number' => $this->faker->numberBetween(-10000, 10000),
            'capacity' => $this->faker->randomNumber(),
            'company_id' => Company::factory(),
            'is_default_for_company' => $this->faker->boolean,
            'can_transport_goods' => $this->faker->boolean,
        ];
    }
}
