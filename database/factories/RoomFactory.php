<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $facilities = [
            ['WiFi', 'AC', 'Lemari'],
            ['WiFi', 'AC', 'Kamar Mandi Dalam'],
            ['WiFi', 'Kipas Angin', 'Kamar Mandi Dalam', 'Dapur Mini'],
            ['WiFi', 'AC', 'Kamar Mandi Dalam', 'Meja Kerja'],
            ['AC', 'Kamar Mandi Dalam', 'Smart TV'],
            ['WiFi', 'AC', 'Kamar Mandi Dalam', 'Kasur Premium'],
            ['WiFi', 'Kipas Angin', 'Lemari', 'Meja'],
            ['WiFi', 'AC', 'Kamar Mandi Luar'],
        ];

        $roomPrices = [400000, 450000, 500000, 550000, 600000, 700000, 800000, 900000, 1000000];

        return [
            'room_number' => fake()->unique()->numerify('##'),
            'price' => fake()->randomElement($roomPrices),
            'status' => fake()->randomElement(['tersedia', 'terisi']),
            'facilities' => fake()->randomElement($facilities),
        ];
    }

    /**
     * Indicate room is available
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tersedia',
        ]);
    }

    /**
     * Indicate room is occupied
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terisi',
        ]);
    }
}
