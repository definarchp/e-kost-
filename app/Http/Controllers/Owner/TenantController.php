<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;


class TenantController extends Controller
{
    public function index(): View
    {
        return view('owner.tenants.index', [
            'tenants' => Tenant::with(['user', 'room'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('owner.tenants.create', [
            'users' => User::where('role', 'penyewa')->whereDoesntHave('tenant')->get(),
            'rooms' => Room::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Akun penyewa dibuat dulu
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'name' => ['required', 'string', 'max:255'],

            'room_id' => ['nullable', 'exists:rooms,id'],
            'nik' => ['required', 'string', 'max:32', 'unique:tenants,nik', 'regex:/^[0-9]{16}$/'],
            'phone_number' => ['required', 'string', 'max:30', 'regex:/^08[0-9]{8,11}$/'],
            'emergency_contact' => ['required', 'string', 'max:30', 'regex:/^08[0-9]{8,11}$/'],
            'ktp_path' => [
                'nullable',
                'image',
                'max:2048', // 2MB max
                'mimes:jpg,jpeg,png',
                'dimensions:min_width=100,min_height=100',
            ],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'ktp_path.max' => 'File KTP tidak boleh lebih dari 2MB.',
            'ktp_path.mimes' => 'File KTP hanya boleh format JPG, JPEG, atau PNG.',
            'ktp_path.image' => 'File harus berupa gambar yang valid.',
            'ktp_path.dimensions' => 'Ukuran gambar minimal 100x100 pixel.',
            'nik.regex' => 'NIK harus terdiri dari 16 digit angka.',
            'phone_number.regex' => 'Nomor telepon harus dimulai dengan 08 dan valid.',
            'emergency_contact.regex' => 'Nomor kontak darurat harus dimulai dengan 08 dan valid.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo harus hari ini atau setelahnya.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);


        // Handle file upload
        if ($request->hasFile('ktp_path')) {
            $file = $request->file('ktp_path');


            // Additional security check
            if (!$file->isValid()) {
                return back()->with('error', 'File upload tidak valid.');
            }

            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('tenants/ktp', $filename, 'public');
            $validated['ktp_path'] = $path;
        }

        // Buat user penyewa dulu
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'penyewa',
            'password' => Hash::make($validated['password']),
        ]);

        // Hapus field yang tidak ada di tenants
        $tenantPayload = collect($validated)->except(['name', 'email', 'password'])->all();
        $tenantPayload['user_id'] = $user->id;

        $tenant = Tenant::create($tenantPayload);

        // Auto-update room status if room_id provided
        if ($tenant->room_id) {
            $this->updateRoomStatus($tenant->room_id);
        }


        return redirect()->route('owner.tenants.index')->with('status', 'Data penyewa berhasil dibuat.');
    }

    public function show(Tenant $tenant): View
    {
        return view('owner.tenants.show', [
            'tenant' => $tenant->load(['user', 'room', 'bills', 'complaints']),
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('owner.tenants.edit', [
            'tenant' => $tenant->load(['user', 'room']),
            'users' => User::where('role', 'penyewa')->get(),
            'rooms' => Room::all(),
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:tenants,user_id,'.$tenant->id],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'nik' => ['required', 'string', 'max:32', 'unique:tenants,nik,'.$tenant->id, 'regex:/^[0-9]{16}$/'],
            'phone_number' => ['required', 'string', 'max:30', 'regex:/^08[0-9]{8,11}$/'],
            'emergency_contact' => ['required', 'string', 'max:30', 'regex:/^08[0-9]{8,11}$/'],
            'ktp_path' => [
                'nullable',
                'image',
                'max:2048', // 2MB max
                'mimes:jpg,jpeg,png',
                'dimensions:min_width=100,min_height=100',
            ],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'ktp_path.max' => 'File KTP tidak boleh lebih dari 2MB.',
            'ktp_path.mimes' => 'File KTP hanya boleh format JPG, JPEG, atau PNG.',
            'ktp_path.image' => 'File harus berupa gambar yang valid.',
            'ktp_path.dimensions' => 'Ukuran gambar minimal 100x100 pixel.',
            'nik.regex' => 'NIK harus terdiri dari 16 digit angka.',
            'phone_number.regex' => 'Nomor telepon harus dimulai dengan 08 dan valid.',
            'emergency_contact.regex' => 'Nomor kontak darurat harus dimulai dengan 08 dan valid.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo harus hari ini atau setelahnya.',
        ]);

        // Handle file upload
        if ($request->hasFile('ktp_path')) {
            // Delete old file if exists
            if ($tenant->ktp_path && \Storage::disk('public')->exists($tenant->ktp_path)) {
                \Storage::disk('public')->delete($tenant->ktp_path);
            }

            $file = $request->file('ktp_path');

            // Additional security check
            if (!$file->isValid()) {
                return back()->with('error', 'File upload tidak valid.');
            }

            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('tenants/ktp', $filename, 'public');
            $validated['ktp_path'] = $path;
        } else {
            // Unset if user explicitly removes file
            if (isset($validated['ktp_path']) && $validated['ktp_path'] === null) {
                if ($tenant->ktp_path && \Storage::disk('public')->exists($tenant->ktp_path)) {
                    \Storage::disk('public')->delete($tenant->ktp_path);
                }
            }
        }

        $oldRoomId = $tenant->room_id;
        $tenant->update($validated);

        // Update room status for both old and new room
        if ($oldRoomId) {
            $this->updateRoomStatus($oldRoomId);
        }
        if ($tenant->room_id && $tenant->room_id !== $oldRoomId) {
            $this->updateRoomStatus($tenant->room_id);
        }

        return redirect()->route('owner.tenants.index')->with('status', 'Data penyewa berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $roomId = $tenant->room_id;

        // Delete KTP file
        if ($tenant->ktp_path && \Storage::disk('public')->exists($tenant->ktp_path)) {
            \Storage::disk('public')->delete($tenant->ktp_path);
        }

        $tenant->delete();

        // Update room status
        if ($roomId) {
            $this->updateRoomStatus($roomId);
        }

        return redirect()->route('owner.tenants.index')->with('status', 'Data penyewa berhasil dihapus.');
    }

    private function updateRoomStatus(int $roomId): void
    {
        $room = Room::find($roomId);
        if (! $room) {
            return;
        }

        $hasActiveTenant = Tenant::where('room_id', $roomId)->exists();
        $room->update(['status' => $hasActiveTenant ? 'terisi' : 'tersedia']);
    }
}
