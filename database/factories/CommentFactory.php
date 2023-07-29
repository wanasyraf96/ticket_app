<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $startDate = '2023-01-01';
        $endDate = '2023-05-31';

        // Generate a random date within the custom range
        $randomDate = fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d H:i:s');

        $user = User::get()->count();
        return [
            'ticket_id' => 1,
            'user_id' => rand(1, $user),
            'comment' => fake()->paragraph(fake()->numberBetween(3, 20)),
            'created_at' => $randomDate,
            'updated_at' => $randomDate,
        ];
    }
}
