@extends('layouts.app')

@section('title', 'Tambah Kamar Kost')
@section('page-title', 'Tambah Kamar Kost')
@section('page-subtitle', 'Isi data kamar kost dan fasilitas yang tersedia.')

@section('content')
<form method="POST" action="{{ route('owner.rooms.store') }}" class="max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm" enctype="multipart/form-data">
    @csrf

    <div class="border-b border-slate-100 p-6">
        <h2 class="font-bold text-slate-900">Data Kamar</h2>
        <p class="mt-1 text-sm text-slate-500">Tambahkan kamar baru ke sistem.</p>
    </div>

    <div class="space-y-6 p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="room_number" class="block text-sm font-bold text-slate-800">Nomor Kamar *</label>
                <input id="room_number" type="text" name="room_number" value="{{ old('room_number') }}" required
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: 101">
                @error('room_number')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-bold text-slate-800">Harga Kamar (Rp) *</label>
                <input id="price" type="number" name="price" value="{{ old('price') }}" required min="0"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: 1500000">
                @error('price')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800">Fasilitas *</label>
                <div class="mt-2 space-y-2">
                    @php
                        $facilityOptions = ['AC', 'Kasur', 'Kamar Mandi Dalam', 'Listrik', 'Air', 'WiFi', 'Meja', 'Lemari', 'TV', 'Dapur Bersama', 'Laundry', 'Parkir'];
                        $selected = old('facilities', []);
                        if (!is_array($selected)) $selected = [];
                    @endphp

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($facilityOptions as $opt)
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                <input type="checkbox" name="facilities[]" value="{{ $opt }}" {{ in_array($opt, $selected) ? 'checked' : '' }}>
                                <span class="text-slate-700">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('facilities')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @error('facilities.*')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <p class="mt-2 text-xs text-slate-500">Pilih salah satu atau lebih.</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Kamar</p>
            <div class="mt-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                Kamar baru akan default bertipe <span class="font-bold">tersedia</span>.
            </div>
            <input type="hidden" name="status" value="tersedia">
        </div>

        <div class="flex gap-3">
            <a href="{{ route('owner.rooms.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Tambah Kamar
            </button>
        </div>
    </div>
</form>
@endsection

