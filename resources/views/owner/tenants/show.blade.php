@extends('layouts.app')

@section('title', $tenant->user->name . ' - Data Penyewa')
@section('page-title', $tenant->user->name)
@section('page-subtitle', 'Detail informasi penyewa dan riwayat keluhan.')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $tenant->user->name }}</h1>
                <p class="mt-1 text-slate-600">{{ $tenant->user->email }}</p>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('owner.tenants.edit', $tenant) }}" class="rounded-lg bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-200">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edit
                    </a>
                    <button type="button" onclick="deleteTenant()" class="rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-200">
                        <i class="fa-solid fa-trash mr-2"></i> Hapus
                    </button>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-emerald-100 text-4xl text-emerald-700">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- NIK -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">NIK</p>
            <p class="mt-2 text-xl font-bold text-slate-900">{{ $tenant->nik }}</p>
        </div>

        <!-- Phone -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nomor Telepon</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $tenant->phone_number }}</p>
            <a href="tel:{{ $tenant->phone_number }}" class="mt-2 text-sm text-blue-600 hover:text-blue-700">Hubungi sekarang</a>
        </div>

        <!-- Emergency Contact -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kontak Darurat</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $tenant->emergency_contact }}</p>
            <a href="tel:{{ $tenant->emergency_contact }}" class="mt-2 text-sm text-blue-600 hover:text-blue-700">Hubungi sekarang</a>
        </div>

        <!-- Room -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kamar Ditempati</p>
            @if ($tenant->room)
                <p class="mt-2 text-lg font-bold text-slate-900">Kamar {{ $tenant->room->room_number }}</p>
                <p class="mt-1 text-sm text-slate-600">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}/bulan</p>
                <span class="mt-2 inline-flex rounded-full {{ $tenant->room->status === 'tersedia' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} px-2.5 py-1 text-xs font-bold">
                    {{ ucfirst($tenant->room->status) }}
                </span>
            @else
                <p class="mt-2 text-lg font-bold text-slate-500">-</p>
            @endif
        </div>

        <!-- Due Date -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jatuh Tempo</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $tenant->due_date->format('d M Y') }}</p>
            <p class="mt-1 text-sm text-slate-600">Tanggal {{ $tenant->due_date->day }} setiap bulan</p>
        </div>

        <!-- Member Since -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Terdaftar Sejak</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $tenant->created_at->format('d M Y') }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $tenant->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <!-- KTP File -->
    @if ($tenant->ktp_path && \Storage::disk('public')->exists($tenant->ktp_path))
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="mb-4 text-sm font-bold text-slate-800">File KTP</p>
            <a href="{{ asset('storage/' . $tenant->ktp_path) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                <i class="fa-solid fa-image"></i> Lihat File KTP
            </a>
        </div>
    @endif

    <!-- Bills Section -->
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 p-6">
            <h2 class="font-bold text-slate-900">Riwayat Tagihan</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar invoice yang telah diterbitkan untuk penyewa ini.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Bulan</th>
                        <th class="px-6 py-3">Nominal</th>
                        <th class="px-6 py-3">VA</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tenant->bills as $bill)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->bill_month }}</td>
                            <td class="px-6 py-4 text-slate-600">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $bill->va_number }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $bill->status === 'lunas' ? '✓ Lunas' : 'Belum lunas' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada tagihan untuk penyewa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complaints Section -->
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 p-6">
            <h2 class="font-bold text-slate-900">Riwayat Keluhan</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar keluhan dan tanggapan untuk penyewa ini.</p>
        </div>
        <div class="space-y-3 p-6">
            @forelse ($tenant->complaints as $complaint)
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-600">{{ $complaint->created_at->format('d M Y H:i') }}</span>
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold {{ $complaint->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : ($complaint->status === 'diproses' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700') }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-800">{{ $complaint->description }}</p>
                    @if ($complaint->owner_response)
                        <div class="mt-3 rounded-lg bg-slate-50 p-3 text-xs leading-5 text-slate-700">
                            <span class="font-bold text-emerald-700">Respon:</span> {{ $complaint->owner_response }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
                    Belum ada keluhan dari penyewa ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function deleteTenant() {
        if (confirm('Apakah Anda yakin ingin menghapus penyewa ini? Kamar akan otomatis menjadi tersedia.')) {
            const form = document.getElementById('delete-form');
            form.action = `/owner/tenants/{{ $tenant->id }}`;
            form.submit();
        }
    }
</script>
@endpush
