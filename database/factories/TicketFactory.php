<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = '2020-01-01';
        $endDate = '2022-12-31';

        // Generate a random date within the custom range
        $randomDate = fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');

        return [
            'creator' => 1,
            'title' => fake()->word(6, true),
            'description' => fake()->paragraph(),
            'priority' => rand(1, 4),
            'status' => rand(1, 4),
            'created_at' => $randomDate,
            'updated_at' => $randomDate,
        ];
    }
}
