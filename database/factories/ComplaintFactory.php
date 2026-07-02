<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $complaintTexts = [
            'AC kamar tidak berfungsi sejak kemarin pagi.',
            'WiFi sangat lambat di kamar saya, tidak bisa browsing.',
            'Pintu kamar macet, kunci tidak bisa dibuka sempurna.',
            'Lampu di kamar mati, baterai sudah coba diganti tapi masih tidak nyala.',
            'Terdapat kebocoran air di sudut kamar.',
            'Kamar Mandi Dalam bau tidak sedap, mungkin saluran tersumbat.',
            'Jendela tidak bisa ditutup dengan rapat, ada celah udara masuk.',
            'Lantai kamar sangat basah setiap kali ujan.',
        ];

        $responses = [
            'Teknisi AC sudah dipanggil, akan di-service dalam 2 hari kerja.',
            'Router WiFi sudah di-restart, kecepatan sudah kembali normal.',
            'Kunci pintu diganti yang baru, mohon coba sekarang.',
            'Lampu ganti dengan yang baru, sudah dinyalakan.',
            'Pipa air sudah di-perbaiki oleh tukang, tidak ada lagi kebocoran.',
            'Saluran kamar mandi sudah dibersihkan, sudah lancar sekarang.',
            'Jendela sudah diperbaiki dengan sealant, tidak ada lagi angin masuk.',
            'Lantai sudah dikering-kan dan direcat, cek lagi setelah 3 hari.',
        ];

        return [
            'tenant_id' => fake()->numberBetween(1, 5),
            'description' => fake()->randomElement($complaintTexts),
            'status' => fake()->randomElement(['pending', 'diproses', 'selesai']),
            'owner_response' => fake()->randomElement([null, null, ...array_values($responses)]),
        ];
    }

    /**
     * Indicate complaint is pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'owner_response' => null,
        ]);
    }

    /**
     * Indicate complaint is being processed
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'diproses',
        ]);
    }

    /**
     * Indicate complaint is resolved
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
        ]);
    }
}
