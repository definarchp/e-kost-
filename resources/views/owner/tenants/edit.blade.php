@extends('layouts.app')

@section('title', 'Edit Anak Kost')
@section('page-title', 'Edit Anak Kost')
@section('page-subtitle', 'Perbarui informasi penyewa.')

@section('content')
<form method="POST" action="{{ route('owner.tenants.update', $tenant) }}" enctype="multipart/form-data" class="max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf
    @method('PATCH')

    <div class="border-b border-slate-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900">Edit Data Penyewa</h2>
                <p class="mt-1 text-sm text-slate-500">Perbarui informasi penyewa: {{ $tenant->user->name }}</p>
            </div>
            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                Kamar {{ $tenant->room?->room_number ?? '-' }}
            </span>
        </div>
    </div>

    <div class="space-y-6 p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- User Selection -->
            <div>
                <label for="user_id" class="block text-sm font-bold text-slate-800">Akun Pengguna *</label>
                <select id="user_id" name="user_id" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $tenant->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Room Selection -->
            <div>
                <label for="room_id" class="block text-sm font-bold text-slate-800">Kamar yang Ditempati</label>
                <select id="room_id" name="room_id" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih kamar (opsional)...</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id', $tenant->room_id) == $room->id ? 'selected' : '' }}>
                            Kamar {{ $room->room_number }} - Rp {{ number_format($room->price, 0, ',', '.') }} ({{ $room->status }})
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- NIK -->
            <div>
                <label for="nik" class="block text-sm font-bold text-slate-800">NIK *</label>
                <input id="nik" type="text" name="nik" required maxlength="32" value="{{ old('nik', $tenant->nik) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('nik')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-bold text-slate-800">Nomor Telepon *</label>
                <input id="phone_number" type="tel" name="phone_number" required maxlength="30" value="{{ old('phone_number', $tenant->phone_number) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('phone_number')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Emergency Contact -->
            <div>
                <label for="emergency_contact" class="block text-sm font-bold text-slate-800">Kontak Darurat *</label>
                <input id="emergency_contact" type="tel" name="emergency_contact" required maxlength="30" value="{{ old('emergency_contact', $tenant->emergency_contact) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('emergency_contact')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-bold text-slate-800">Tanggal Jatuh Tempo *</label>
                <input id="due_date" type="date" name="due_date" required value="{{ old('due_date', $tenant->due_date) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('due_date')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- KTP Upload -->
            <div class="md:col-span-2">
                <label for="ktp_path" class="block text-sm font-bold text-slate-800">Foto KTP / Identitas</label>
                @if ($tenant->ktp_path && \Storage::disk('public')->exists($tenant->ktp_path))
                    <div class="mt-2 mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-800">
                            <i class="fa-solid fa-check-circle mr-2"></i> File KTP sudah tersimpan
                        </p>
                        <a href="{{ asset('storage/' . $tenant->ktp_path) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700">
                            <i class="fa-solid fa-image"></i> Lihat File KTP
                        </a>
                    </div>
                @endif
                <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center hover:border-emerald-400 hover:bg-emerald-50 transition" data-ktp-drop>
                    <i class="fa-solid fa-cloud-arrow-up block text-2xl text-slate-400 mb-2"></i>
                    <input id="ktp_path" type="file" name="ktp_path" accept="image/*" class="hidden" data-ktp-input>
                    <p class="text-sm font-semibold text-slate-700">
                        <span class="cursor-pointer text-emerald-600 hover:text-emerald-700">Klik untuk update</span> atau drag & drop
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, GIF (Max. 5MB)</p>
                    <p class="mt-2 text-xs font-medium text-slate-600" data-ktp-preview>Tidak ada perubahan</p>
                </div>
                @error('ktp_path')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('owner.tenants.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const ktpInput = document.querySelector('[data-ktp-input]');
    const ktpDrop = document.querySelector('[data-ktp-drop]');
    const ktpPreview = document.querySelector('[data-ktp-preview]');

    if (ktpDrop && ktpInput) {
        ktpDrop.addEventListener('click', () => ktpInput.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            ktpDrop.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            ktpDrop.addEventListener(eventName, () => {
                ktpDrop.classList.add('border-emerald-400', 'bg-emerald-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            ktpDrop.addEventListener(eventName, () => {
                ktpDrop.classList.remove('border-emerald-400', 'bg-emerald-50');
            });
        });

        ktpDrop.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            ktpInput.files = files;
            updatePreview();
        });

        ktpInput.addEventListener('change', updatePreview);

        function updatePreview() {
            if (ktpInput.files && ktpInput.files[0]) {
                const file = ktpInput.files[0];
                ktpPreview.textContent = `✓ ${file.name} (${(file.size / 1024).toFixed(2)} KB) - akan diperbarui`;
            } else {
                ktpPreview.textContent = 'Tidak ada perubahan';
            }
        }
    }
</script>
@endpush
