<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n🌱 Starting database seeding...\n";

        // 1. Create Owner (Pemilik Kost)
        echo "📝 Creating 1 owner (pemilik)...\n";
        $owner = User::factory()
            ->owner()
            ->create([
                'name' => 'Pemilik D\'Brissel',
                'email' => 'owner@dbrissel.com',
            ]);
        echo "✓ Owner created: {$owner->name} ({$owner->email})\n";

        // 2. Create 10 Rooms
        echo "\n📝 Creating 10 rooms...\n";
        $rooms = Room::factory(10)->create();
        echo "✓ {$rooms->count()} rooms created\n";

        // 3. Create 5 Tenant Users (Penyewa)
        echo "\n📝 Creating 5 tenant users (penyewa)...\n";
        $tenantUsers = User::factory(5)
            ->tenant()
            ->create([
                'email' => fn () => fake()->unique()->safeEmail(),
            ]);
        echo "✓ {$tenantUsers->count()} tenant users created\n";

        // 4. Create Tenant Records (Link User + Room)
        echo "\n📝 Creating tenant records with room assignments...\n";
        $tenants = [];
        foreach ($tenantUsers as $index => $user) {
            $room = $rooms[$index] ?? $rooms->random();
            $tenant = Tenant::factory()
                ->forUser($user->id)
                ->inRoom($room->id)
                ->create();
            $tenants[] = $tenant;
            $tenantNumber = $index + 1;
            echo "✓ Tenant #{$tenantNumber}: {$user->name} → Kamar {$room->room_number}\n";
        }

        // Auto-update room status for occupied rooms
        echo "\n🔄 Auto-syncing room status...\n";
        foreach ($rooms as $room) {
            $hasActiveTenant = Tenant::where('room_id', $room->id)->exists();
            $room->update(['status' => $hasActiveTenant ? 'terisi' : 'tersedia']);
        }
        $occupiedRoomsCount = $rooms->where('status', 'terisi')->count();
        $availableRoomsCount = $rooms->where('status', 'tersedia')->count();
        echo "✓ Rooms status synced: {$occupiedRoomsCount} occupied, {$availableRoomsCount} available\n";

        // 5. Create 5 Bills (Mixed: 2 paid, 3 unpaid)
        echo "\n📝 Creating 5 bills (mixed status)...\n";
        $bills = [];

        // 2 Paid bills
        for ($i = 0; $i < 2; $i++) {
            $bill = Bill::factory()
                ->paid()
                ->create(['tenant_id' => $tenants[$i]->id]);
            $bills[] = $bill;
            $billNumber = $i + 1;
            echo "✓ Bill #{$billNumber}: {$tenants[$i]->user->name} - Rp " . number_format($bill->amount, 0, ',', '.') . " (Lunas)\n";
        }

        // 3 Unpaid bills
        for ($i = 2; $i < 5; $i++) {
            $bill = Bill::factory()
                ->unpaid()
                ->create(['tenant_id' => $tenants[$i]->id]);
            $bills[] = $bill;
            $billNumber = $i + 1;
            echo "✓ Bill #{$billNumber}: {$tenants[$i]->user->name} - Rp " . number_format($bill->amount, 0, ',', '.') . " (Belum Lunas)\n";
        }

        // 6. Create 3 Complaints (Mixed: 1 pending, 1 processing, 1 resolved)
        echo "\n📝 Creating 3 complaints (mixed status)...\n";

        // 1 Pending complaint
        $complaint1 = Complaint::factory()
            ->pending()
            ->create(['tenant_id' => $tenants[0]->id]);
        echo "✓ Complaint #1: {$tenants[0]->user->name} - Status: Pending\n";

        // 1 Processing complaint
        $complaint2 = Complaint::factory()
            ->processing()
            ->create(['tenant_id' => $tenants[1]->id]);
        echo "✓ Complaint #2: {$tenants[1]->user->name} - Status: Diproses\n";

        // 1 Resolved complaint
        $complaint3 = Complaint::factory()
            ->resolved()
            ->create(['tenant_id' => $tenants[2]->id]);
        echo "✓ Complaint #3: {$tenants[2]->user->name} - Status: Selesai\n";

        // Summary
        echo "\n" . str_repeat("═", 60) . "\n";
        echo "✅ DATABASE SEEDING COMPLETED!\n";
        echo str_repeat("═", 60) . "\n";
        echo "📊 Summary:\n";
        echo "   • 1 Owner (Pemilik)\n";
        echo "   • 5 Tenant Users (Penyewa)\n";
        echo "   • 10 Rooms (6 Occupied, 4 Available)\n";
        echo "   • 5 Bills (2 Paid, 3 Unpaid)\n";
        echo "   • 3 Complaints (1 Pending, 1 Processing, 1 Resolved)\n";
        echo "\n🔐 Test Credentials:\n";
        echo "   Owner:\n";
        echo "     Email: owner@dbrissel.com\n";
        echo "     Password: password\n\n";
        echo "   Tenant Example:\n";
        echo "     Email: " . $tenantUsers[0]->email . "\n";
        echo "     Password: password\n";
        echo "\n";
    }
}

