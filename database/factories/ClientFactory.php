<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

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
            'location' => '{}',
            'email' => $this->faker->safeEmail,
            'photo' => $this->faker->numberBetween(-100000, 100000),
            'messaging_token' => $this->faker->word,
            'reported_count' => $this->faker->randomNumber(),
            'account_status' => $this->faker->randomElement(["pending","active","suspended"]),
            'registered_at' => $this->faker->dateTime(),
        ];
    }
}
