@extends('layouts.app')

@section('title', 'Tambah Anak Kost')
@section('page-title', 'Tambah Anak Kost')
@section('page-subtitle', 'Daftarkan penyewa baru dan kamar yang ditempati.')

@section('content')
<form method="POST" action="{{ route('owner.tenants.store') }}" enctype="multipart/form-data" class="max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf

    <div class="border-b border-slate-100 p-6">
        <h2 class="font-bold text-slate-900">Data Penyewa</h2>
        <p class="mt-1 text-sm text-slate-500">Lengkapi informasi penyewa baru.</p>
    </div>

    <div class="space-y-6 p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- User (dibuat otomatis) -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-800">Nama Penyewa *</label>
                <input id="name" type="text" name="name" required value="{{ old('name') }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: Budi Santoso">
                @error('name')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-bold text-slate-800">Email *</label>
                <input id="email" type="email" name="email" required value="{{ old('email') }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="contoh@email.com">
                @error('email')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-bold text-slate-800">Password *</label>
                <input id="password" type="password" name="password" required
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Room Selection -->
            <div>
                <label for="room_id" class="block text-sm font-bold text-slate-800">Kamar yang Ditempati</label>


                <select id="room_id" name="room_id" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih kamar (opsional)...</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
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
                <input id="nik" type="text" name="nik" required maxlength="32" value="{{ old('nik') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: 1234567890123456">
                @error('nik')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-bold text-slate-800">Nomor Telepon *</label>
                <input id="phone_number" type="tel" name="phone_number" required maxlength="30" value="{{ old('phone_number') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: 0812345678">
                @error('phone_number')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Emergency Contact -->
            <div>
                <label for="emergency_contact" class="block text-sm font-bold text-slate-800">Kontak Darurat *</label>
                <input id="emergency_contact" type="tel" name="emergency_contact" required maxlength="30" value="{{ old('emergency_contact') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: 0898765432">
                @error('emergency_contact')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-bold text-slate-800">Tanggal Jatuh Tempo *</label>
                <input id="due_date" type="date" name="due_date" required value="{{ old('due_date') }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('due_date')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- KTP Upload -->
            <div class="md:col-span-2">
                <label for="ktp_path" class="block text-sm font-bold text-slate-800">Foto KTP / Identitas</label>
                <div class="mt-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center hover:border-emerald-400 hover:bg-emerald-50 transition" data-ktp-drop>
                    <i class="fa-solid fa-cloud-arrow-up block text-2xl text-slate-400 mb-2"></i>
                    <input id="ktp_path" type="file" name="ktp_path" accept="image/*" class="hidden" data-ktp-input>
                    <p class="text-sm font-semibold text-slate-700">
                        <span class="cursor-pointer text-emerald-600 hover:text-emerald-700">Klik untuk upload</span> atau drag & drop
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, GIF (Max. 5MB)</p>
                    <p class="mt-2 text-xs font-medium text-slate-600" data-ktp-preview>Belum ada file dipilih</p>
                </div>
                @error('ktp_path')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('owner.tenants.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Tambah Penyewa
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
                ktpPreview.textContent = `✓ ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
            } else {
                ktpPreview.textContent = 'Belum ada file dipilih';
            }
        }
    }
</script>
@endpush
