<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => 'TRAVEL-' . strtoupper(uniqid()),
            'customer_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            "start_date" => $this->faker->date(),
            "end_date" => $this->faker->date(+1, "+7 days"),
        ];
    }
}
