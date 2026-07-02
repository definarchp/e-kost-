<?php

namespace App\Http\Controllers\Owner;

use App\Models\Room;
use App\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        return view('owner.rooms.index', [
            'rooms' => Room::withCount('tenants')
                ->with(['currentTenant.user'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('owner.rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number'],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['tersedia', 'terisi'])],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:100'],
        ]);

        // Pastikan facilities disimpan sebagai JSON string (kolom biasanya text/json)
        if (array_key_exists('facilities', $validated)) {
            $validated['facilities'] = $validated['facilities'];
        }

        Room::create($validated);


        return redirect()->route('owner.rooms.index')->with('status', 'Kamar berhasil dibuat.');
    }

    public function show(Room $room): View
    {
        return view('owner.rooms.show', [
            'room' => $room->load('tenants.user'),
        ]);
    }

    public function edit(Room $room): View
    {
        return view('owner.rooms.edit', [
            'room' => $room,
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number,'.$room->id],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['tersedia', 'terisi'])],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:100'],
        ]);

        $room->update($validated);

        return redirect()->route('owner.rooms.index')->with('status', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        // Check if room has active tenants
        if ($room->tenants()->exists()) {
            return redirect()->back()->with('error', 'Kamar masih memiliki penyewa. Hapus penyewa terlebih dahulu.');
        }

        $room->delete();

        return redirect()->route('owner.rooms.index')->with('status', 'Kamar berhasil dihapus.');
    }

    public function syncStatus(): RedirectResponse
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            $hasActiveTenant = $room->tenants()->exists();
            $room->update(['status' => $hasActiveTenant ? 'terisi' : 'tersedia']);
        }

        return redirect()->back()->with('status', 'Status kamar telah disinkronkan.');
    }
}
