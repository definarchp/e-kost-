@extends('layouts.app')

@section('title', 'Tagihan Saya')
@section('page-title', 'Tagihan Saya')
@section('page-subtitle', 'Riwayat tagihan dan status pembayaran.')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Bulan</th>
                    <th class="px-6 py-3">Nominal</th>
                    <th class="px-6 py-3">VA</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Bukti Pembayaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($bills as $bill)
                    <tr>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->bill_month }}</td>
                        <td class="px-6 py-4 text-slate-600">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $bill->va_number }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $bill->status === 'lunas' ? 'Lunas' : 'Belum lunas' }}
                            </span>
                        </td>

<td class="px-6 py-4">
@php
    $path = $bill->bukti_pembayaran_path;
    $disk = Storage::disk('public');
    $exists = $path ? $disk->exists($path) : false;
@endphp

@if (($bill->status === 'lunas' || $bill->status === 'menunggu_verifikasi' || $bill->status === 'belum_dibayar') && $path)
    @if ($exists)
        <div class="flex items-center gap-3">
            <img
                src="{{ route('tenant.bills.proof', $bill->id) }}"
                alt="Bukti pembayaran {{ $bill->bill_month }}"
                class="h-12 w-16 rounded-lg object-cover border border-slate-200"
            />
            <span class="text-[11px] font-semibold text-slate-600 truncate max-w-28">Bukti tersedia</span>
        </div>
    @else
        <span class="text-[11px] font-semibold text-amber-600">Bukti belum tersimpan (file tidak ditemukan)</span>
    @endif
@else
    @if (in_array($bill->status, ['belum_dibayar', 'menunggu_verifikasi'], true))
        <form method="POST" action="{{ route('tenant.bills.payment-proof', $bill->id) }}" enctype="multipart/form-data" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            @csrf
            <input type="file" name="bukti_pembayaran" accept="image/*" required class="block w-full text-xs text-slate-600"/>
            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Upload</button>
        </form>
    @else
        <span class="text-xs text-slate-500">(Belum ada bukti)</span>
    @endif
@endif
</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

