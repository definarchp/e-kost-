@extends('layouts.app')

@section('title', "Dashboard Pemilik - E-Kost D'Brissel")
@section('page-title', 'Dashboard Pemilik')
@section('page-subtitle', 'Tinjauan operasional, hunian, dan rekap keuangan bulan ini.')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp '.number_format((int) $value, 0, ',', '.');
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Anak Kost</span>
                <h2 class="text-3xl font-extrabold text-slate-900">{{ $totalTenants }}</h2>
                <span class="text-[11px] font-medium text-emerald-600"><i class="fa-solid fa-circle-check"></i> Terdaftar aktif</span>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-xl text-sky-600">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hunian Kamar</span>
                <h2 class="text-3xl font-extrabold text-slate-900">{{ $occupiedRooms }}/{{ $totalRooms }}</h2>
                <span class="text-[11px] font-medium text-amber-600">{{ $availableRooms }} kamar tersedia</span>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl text-indigo-600">
                <i class="fa-solid fa-door-open"></i>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pendapatan Bulan Ini</span>
                <h2 class="text-2xl font-extrabold text-emerald-600">{{ $formatRupiah($monthlyRevenue) }}</h2>
                <span class="text-[11px] text-slate-500">{{ $paidBillsThisMonth }} tagihan lunas</span>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-600">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Keluhan Pending</span>
                <h2 class="text-3xl font-extrabold text-red-500">{{ $pendingComplaints }}</h2>
                <span class="text-[11px] font-medium text-red-600">Butuh tindakan</span>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-xl text-red-600">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 p-6">
                <div>
                    <h3 class="font-bold text-slate-900">Tagihan Terbaru</h3>
                    <p class="mt-1 text-xs text-slate-500">Ringkasan invoice penyewa terakhir.</p>
                </div>
                <a href="{{ route('owner.bills.index') }}" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">Kelola</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3">Penyewa</th>
                            <th class="px-6 py-3">Kamar</th>
                            <th class="px-6 py-3">Bulan</th>
                            <th class="px-6 py-3">Nominal</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentBills as $bill)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->tenant?->user?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $bill->tenant?->room?->room_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $bill->bill_month }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $formatRupiah($bill->amount) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ str_replace('_', ' ', ucfirst($bill->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada data tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Keluhan Terbaru</h3>
                    <p class="mt-1 text-xs text-slate-500">Laporan yang perlu dipantau.</p>
                </div>
                <i class="fa-solid fa-headset text-indigo-600"></i>
            </div>

            <div class="space-y-4">
                @forelse ($recentComplaints as $complaint)
                    <article class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $complaint->tenant?->user?->name ?? 'Penyewa' }}</p>
                            <span class="rounded-full bg-white px-2 py-1 text-[11px] font-bold capitalize text-slate-600">{{ $complaint->status }}</span>
                        </div>
                        <p class="line-clamp-3 text-xs leading-5 text-slate-600">{{ $complaint->description }}</p>
                        <p class="mt-3 text-[11px] text-slate-400">{{ $complaint->created_at?->format('d M Y') }}</p>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada keluhan terbaru.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
