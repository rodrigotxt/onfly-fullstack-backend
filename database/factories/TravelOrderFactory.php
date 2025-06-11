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
        $startDate = $this->faker->dateTimeBetween('now', '+6 months');
        $minEndDate = (clone $startDate)->modify('+7 days');
        $endDate = $this->faker->dateTimeBetween($minEndDate, (clone $minEndDate)->modify('+3 months'));

        return [
            'user_id' => User::factory(),
            'order_id' => 'TRAVEL-' . strtoupper(uniqid()),
            'customer_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            "start_date" => $startDate->format('Y-m-d'),
            "end_date" => $endDate->format('Y-m-d'),
        ];
    }
}
