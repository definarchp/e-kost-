<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // Will be set in seeder
            'room_id' => null, // Will be set in seeder
            'nik' => fake()->unique()->numerify('################'), // 16 digit NIK
            'phone_number' => fake()->numerify('08##########'),
            'emergency_contact' => fake()->numerify('08##########'),
            'ktp_path' => null, // No file in seeder
            'due_date' => fake()->dateTimeBetween('+1 day', '+28 days'),
        ];
    }

    /**
     * Indicate tenant is connected to a user
     */
    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Indicate tenant occupies a room
     */
    public function inRoom($roomId): static
    {
        return $this->state(fn (array $attributes) => [
            'room_id' => $roomId,
        ]);
    }
}
