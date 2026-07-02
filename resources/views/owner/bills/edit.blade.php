@extends('layouts.app')

@section('title', 'Edit Tagihan')
@section('page-title', 'Edit Tagihan')
@section('page-subtitle', 'Perbarui informasi tagihan.')

@section('content')
<form method="POST" action="{{ route('owner.bills.update', $bill) }}" class="max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf
    @method('PATCH')

    <div class="border-b border-slate-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900">Edit Tagihan</h2>
                <p class="mt-1 text-sm text-slate-500">Perbarui data tagihan: {{ $bill->tenant->user->name }}</p>
            </div>
            <span class="inline-flex rounded-full {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-3 py-1 text-xs font-bold">
                {{ $bill->status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
            </span>
        </div>
    </div>

    <div class="space-y-6 p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Tenant Selection -->
            <div>
                <label for="tenant_id" class="block text-sm font-bold text-slate-800">Penyewa *</label>
                <select id="tenant_id" name="tenant_id" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach ($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ old('tenant_id', $bill->tenant_id) == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->user->name }} - Kamar {{ $tenant->room?->room_number ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bill Month -->
            <div>
                <label for="bill_month" class="block text-sm font-bold text-slate-800">Bulan Tagihan *</label>
                <input id="bill_month" type="month" name="bill_month" required value="{{ old('bill_month', $bill->bill_month) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('bill_month')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-bold text-slate-800">Nominal Tagihan *</label>
                <div class="mt-2 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4">
                    <span class="text-slate-500">Rp</span>
                    <input id="amount" type="number" name="amount" required min="0" value="{{ old('amount', $bill->amount) }}" class="w-full bg-transparent py-3 text-sm focus:outline-none" placeholder="0">
                </div>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-bold text-slate-800">Status *</label>
                <select id="status" name="status" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="belum_dibayar" {{ old('status', $bill->status) == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>

                    <option value="menunggu_verifikasi" {{ old('status', $bill->status) == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>

                    <option value="lunas" {{ old('status', $bill->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                @error('status')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- VA Number -->
            <div class="md:col-span-2">
                <label for="va_number" class="block text-sm font-bold text-slate-800">Virtual Account Number *</label>
                <input id="va_number" type="text" name="va_number" required value="{{ old('va_number', $bill->va_number) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-mono focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('va_number')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('owner.bills.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection
