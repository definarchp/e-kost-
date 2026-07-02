@extends('layouts.app')

@section('title', 'Buat Tagihan')
@section('page-title', 'Buat Tagihan')
@section('page-subtitle', 'Buat tagihan bulanan untuk penyewa.')

@section('content')
<form method="POST" action="{{ route('owner.bills.store') }}" class="max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf

    <div class="border-b border-slate-100 p-6">
        <h2 class="font-bold text-slate-900">Buat Tagihan Baru</h2>
        <p class="mt-1 text-sm text-slate-500">Buat tagihan per penyewa atau untuk semua penyewa sekaligus.</p>
    </div>

    <div class="space-y-6 p-6">
        <!-- Type Selection -->
        <div>
            <label class="block text-sm font-bold text-slate-800 mb-3">Tipe Pembuatan Tagihan *</label>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="relative">
                    <input id="type-single" type="radio" name="type" value="single" checked class="absolute opacity-0" onchange="updateBillForm()">
                    <label for="type-single" class="block cursor-pointer rounded-xl border-2 border-emerald-500 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        <i class="fa-solid fa-user-check mr-2"></i> Tagihan Per Penyewa
                    </label>
                </div>
                <div class="relative">
                    <input id="type-bulk" type="radio" name="type" value="bulk" class="absolute opacity-0" onchange="updateBillForm()">
                    <label for="type-bulk" class="block cursor-pointer rounded-xl border-2 border-slate-200 p-4 text-sm font-semibold text-slate-600">
                        <i class="fa-solid fa-users mr-2"></i> Tagihan Massal (Semua Penyewa)
                    </label>
                </div>
            </div>
            @error('type')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Single Bill Form -->
        <div id="single-form" class="space-y-6">
            <!-- Tenant Selection -->
            <div>
                <label for="tenant_id" class="block text-sm font-bold text-slate-800">Pilih Penyewa *</label>
                <select id="tenant_id" name="tenant_id" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" onchange="updateTenantInfo()">
                    <option value="">-- Pilih Penyewa --</option>
                    @foreach ($tenants as $tenant)
                        <option value="{{ $tenant->id }}" data-room-price="{{ $tenant->room?->price ?? 0 }}" data-room-number="{{ $tenant->room?->room_number ?? '-' }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->user->name }} - Kamar {{ $tenant->room?->room_number ?? '-' }} ({{ $tenant->phone_number }})
                        </option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-bold text-slate-800">Nominal Tagihan *</label>
                <div class="mt-2 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4">
                    <span class="text-slate-500">Rp</span>
                    <input id="amount" type="number" name="amount" required min="0" value="{{ old('amount') }}" class="w-full bg-transparent py-3 text-sm focus:outline-none" placeholder="0">
                </div>
                <p class="mt-2 text-xs text-slate-500">💡 Nominal akan otomatis terisi dari tarif kamar saat memilih penyewa</p>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Common Fields -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Bill Month -->
            <div>
                <label for="bill_month" class="block text-sm font-bold text-slate-800">Bulan Tagihan *</label>
                <input id="bill_month" type="month" name="bill_month" required value="{{ old('bill_month', now()->format('Y-m')) }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('bill_month')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-bold text-slate-800">Status *</label>
                <select id="status" name="status" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="belum_dibayar" {{ old('status') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="menunggu_verifikasi" {{ old('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                @error('status')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bulk Creation Info -->
        <div id="bulk-info" class="hidden rounded-2xl border border-blue-200 bg-blue-50 p-4">
            <p class="text-sm font-semibold text-blue-900">
                <i class="fa-solid fa-info-circle mr-2"></i> Tagihan akan dibuat untuk semua penyewa dengan nominal sesuai tarif kamar mereka.
            </p>
            <p class="mt-2 text-xs text-blue-800">Tagihan yang sudah ada untuk bulan ini akan dilewati (tidak duplikat).</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('owner.bills.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
                <i class="fa-solid fa-check-circle"></i> Buat Tagihan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function updateBillForm() {
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const selectedType = Array.from(typeRadios).find(r => r.checked)?.value;

        const singleForm = document.getElementById('single-form');
        const bulkInfo = document.getElementById('bulk-info');

        const typeLabels = document.querySelectorAll('label[for^="type-"]');
        typeLabels.forEach(label => {
            label.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-800');
            label.classList.add('border-slate-200', 'text-slate-600');
        });

        if (selectedType === 'single') {
            singleForm.classList.remove('hidden');
            bulkInfo.classList.add('hidden');
            document.getElementById('type-single').nextElementSibling.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-800');
            document.getElementById('type-single').nextElementSibling.classList.remove('border-slate-200', 'text-slate-600');
            document.getElementById('tenant_id').required = true;
            document.getElementById('amount').required = true;
        } else {
            singleForm.classList.add('hidden');
            bulkInfo.classList.remove('hidden');
            document.getElementById('type-bulk').nextElementSibling.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-800');
            document.getElementById('type-bulk').nextElementSibling.classList.remove('border-slate-200', 'text-slate-600');
            document.getElementById('tenant_id').required = false;
            document.getElementById('amount').required = false;
        }
    }

    function updateTenantInfo() {
        const tenantSelect = document.getElementById('tenant_id');
        const selected = tenantSelect.options[tenantSelect.selectedIndex];
        const roomPrice = selected.dataset.roomPrice || 0;

        if (roomPrice > 0) {
            document.getElementById('amount').value = roomPrice;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', updateBillForm);
</script>
@endpush
