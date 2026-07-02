<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenantId = fake()->numberBetween(1, 5);
        
        // PERBAIKAN 1: Menghapus named arguments (start: & end:) agar tidak error di Faker PHP 8
        $month = fake()->dateTimeBetween('-6 months', 'now')->format('Y-m');

        return [
            'tenant_id' => $tenantId,
            'bill_month' => $month,
            'amount' => fake()->randomElement([400000, 450000, 500000, 550000, 600000, 700000, 800000]),
            
            // PERBAIKAN 2: Menyesuaikan status enum default dengan database ('belum_dibayar')
            'status' => fake()->randomElement(['lunas', 'belum_dibayar']),
            
            'va_number' => $this->generateVANumber($tenantId, $month),
            
            // TAMBAHAN: Kolom path bukti pembayaran default agar sinkron dengan Blade
            'bukti_pembayaran_path' => null, 
        ];
    }

    /**
     * Indicate bill is paid
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lunas',
            // Kita beri nama file contoh jika tagihan ditandai lunas lewat seeder
            'bukti_pembayaran_path' => 'contoh_bukti.png', 
        ]);
    }

    /**
     * Indicate bill is unpaid
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            // PERBAIKAN 3: Mengubah 'belum_lunas' menjadi 'belum_dibayar' sesuai enum DB terbaru
            'status' => 'belum_dibayar',
            'bukti_pembayaran_path' => null,
        ]);
    }

    /**
     * Generate unique VA number
     */
    private function generateVANumber(int $tenantId, string $month): string
    {
        $tenantCode = str_pad($tenantId, 5, '0', STR_PAD_LEFT);
        $monthCode = str_replace('-', '', $month);
        $randomCode = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);

        return "{$tenantCode}{$monthCode}{$randomCode}";
    }
}