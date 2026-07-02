@extends('layouts.app')

@section('title', 'Kirim Keluhan')
@section('page-title', 'Kirim Keluhan')
@section('page-subtitle', 'Laporkan kendala kamar atau fasilitas kost.')

@section('content')
<form method="POST" action="{{ route('tenant.complaints.store') }}" class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    <label for="description" class="mb-2 block text-sm font-bold text-slate-800">Deskripsi Keluhan</label>
    <textarea id="description" name="description" rows="7" required class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: AC kamar tidak dingin sejak kemarin malam.">{{ old('description') }}</textarea>
    @error('description')
        <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
    <button type="submit" class="mt-5 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
        <i class="fa-solid fa-paper-plane mr-2"></i>Kirim Keluhan
    </button>
</form>
@endsection
