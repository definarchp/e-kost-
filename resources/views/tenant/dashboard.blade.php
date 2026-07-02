@extends('layouts.app')

@section('title', "Portal Penyewa - E-Kost D'Brissel")
@section('page-title', 'Portal Penyewa')
@section('page-subtitle', 'Informasi kamar, tagihan bulan ini, dan riwayat keluhan Anda.')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp '.number_format((int) $value, 0, ',', '.');
    $facilities = is_array($room?->facilities) ? $room->facilities : [];
@endphp

<div class="space-y-6">
    @if (! $tenant)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
            Profil penyewa belum terhubung ke akun ini. Hubungi pemilik kost untuk melengkapi data tenant.
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kamar ditempati</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Kamar {{ $room?->room_number ?? '-' }}</h2>
                        <p class="mt-2 text-sm text-slate-500">Jatuh tempo pembayaran setiap {{ $tenant->due_date?->format('d M Y') }}.</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 px-5 py-4 text-right">
                        <p class="text-xs font-semibold text-emerald-700">Tarif Bulanan</p>
                        <p class="mt-1 text-xl font-extrabold text-emerald-700">{{ $formatRupiah($room?->price ?? 0) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($facilities as $facility)
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <i class="fa-solid fa-circle-check mr-2 text-emerald-500"></i>{{ $facility }}
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500">
                            Fasilitas belum dicatat.
                        </div>
                    @endforelse
                </div>
            </section>

            <div id="active-bill-alert" class="hidden mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-800 shadow-sm">

                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                Perhatian: Anda memiliki tagihan aktif yang belum dibayar!
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tagihan Bulan Ini</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">{{ now()->format('F Y') }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-600">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                        @if ($currentBill)
                        @if ($currentBill->status === 'belum_dibayar')
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    document.getElementById('active-bill-alert')?.classList.remove('hidden');
                                });
                            </script>
                        @endif


                        <p class="text-3xl font-extrabold text-slate-900">{{ $formatRupiah($currentBill->amount) }}</p>
                        <p class="mt-2 text-sm text-slate-500">VA: {{ $currentBill->va_number }}</p>
                        <span class="mt-4 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $currentBill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $currentBill->status === 'lunas' ? 'Lunas' : ($currentBill->status === 'menunggu_verifikasi' ? 'Menunggu verifikasi' : 'Belum dibayar') }}
                        </span>


                    @if ($currentBill->status !== 'lunas')
                    @else
                    @endif
                @else
                    <div class="rounded-xl border border-dashed border-slate-200 p-5 text-sm text-slate-500">Belum ada tagihan untuk bulan ini.</div>
                @endif
            </section>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-100 p-6">
                    <div>
                        <h3 class="font-bold text-slate-900">Riwayat Tagihan</h3>
                        <p class="mt-1 text-xs text-slate-500">Daftar invoice yang pernah diterbitkan.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                                <th class="px-6 py-3">Bulan</th>
                                <th class="px-6 py-3">Nominal</th>
                                <th class="px-6 py-3">VA</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($bills as $bill)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->bill_month }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $formatRupiah($bill->amount) }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $bill->va_number }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $bill->status === 'lunas' ? 'Lunas' : 'Belum lunas' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('tenant.bills.download', $bill->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                            <i class="fa-solid fa-download mr-1"></i>Unduh
                                        </a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada riwayat tagihan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">Riwayat Keluhan</h3>
                        <p class="mt-1 text-xs text-slate-500">Status laporan yang Anda kirim.</p>
                    </div>
                    <a href="{{ route('tenant.complaints.create') }}" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">Buat</a>
                </div>

                <div class="space-y-4">
                    @forelse ($complaints as $complaint)
                        <article class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <span class="text-xs text-slate-400">{{ $complaint->created_at?->format('d M Y') }}</span>
                                <span class="rounded-full bg-white px-2 py-1 text-[11px] font-bold capitalize text-slate-600">{{ $complaint->status }}</span>
                            </div>
                            <p class="text-sm font-medium leading-5 text-slate-700">{{ $complaint->description }}</p>
                            @if ($complaint->owner_response)
                                <div class="mt-3 rounded-lg bg-white p-3 text-xs leading-5 text-slate-600">
                                    <span class="font-bold text-emerald-700">Respon:</span> {{ $complaint->owner_response }}
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada keluhan.</div>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</div>

<!-- Invoice Print Template (Hidden) -->
<div id="invoice-print-template" class="hidden">
    <div class="space-y-6 p-8 bg-white text-slate-900" style="width: 210mm; height: 297mm;">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-300 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold">D'BRISSEL</h1>
                <p class="text-sm text-slate-600">Sistem Manajemen Kost Digital</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-lg">INVOICE</p>
                <p id="inv-number" class="text-sm text-slate-600"></p>
            </div>
        </div>

        <!-- Tenant Info -->
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Tenant</p>
                <p id="inv-tenant-name" class="mt-1 font-bold"></p>
                <p id="inv-tenant-room" class="text-sm text-slate-600"></p>
                <p id="inv-tenant-phone" class="text-sm text-slate-600"></p>
            </div>
            <div class="text-right">
                <p class="text-xs font-semibold text-slate-500 uppercase">Invoice</p>
                <p id="inv-date" class="mt-1 font-bold"></p>
                <p id="inv-month" class="text-sm text-slate-600"></p>
            </div>
        </div>

        <!-- Items -->
        <table class="w-full text-sm">
            <thead class="border-b-2 border-t-2 border-slate-300">
                <tr>
                    <th class="py-2 text-left">Deskripsi</th>
                    <th class="py-2 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3">Tagihan Sewa Kamar Bulanan</td>
                    <td id="inv-amount" class="py-3 text-right font-bold"></td>
                </tr>
            </tbody>
            <tfoot class="border-t-2 border-slate-300">
                <tr>
                    <td class="py-4 font-bold">TOTAL</td>
                    <td id="inv-total" class="py-4 text-right text-lg font-extrabold"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Payment Info -->
        <div class="rounded-lg bg-slate-50 p-4">
            <p class="text-xs font-semibold text-slate-600 uppercase mb-2">Informasi Pembayaran</p>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Virtual Account:</span>
                    <span id="inv-va" class="font-mono font-bold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Jatuh Tempo:</span>
                    <span id="inv-due-date" class="font-bold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Status:</span>
                    <span id="inv-status" class="font-bold"></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-auto border-t-2 border-slate-300 pt-6 text-center text-xs text-slate-600">
            <p>Terima kasih telah membayar tepat waktu.</p>
            <p class="mt-2">Sistem E-Kost D'Brissel - {{ now()->format('Y') }}</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const paymentModal = document.getElementById('payment-modal');

    const alertEl = document.getElementById('active-bill-alert');

    async function refreshActiveBillAlert() {
        try {
            const res = await fetch('{{ route('tenant.bills.active-status') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (!alertEl) return;

            if (data.status === 'belum_dibayar') {
                alertEl.classList.remove('hidden');
            } else {
                alertEl.classList.add('hidden');
            }
        } catch (e) {
            // ignore
        }
    }

    // Poll ringan agar alert hilang otomatis saat status divalidasi
    setInterval(refreshActiveBillAlert, 8000);

    const payQris = document.getElementById('pay-qris');
    const payVa = document.getElementById('pay-va');
    const vaNumber = document.getElementById('pay-va-number');

    document.querySelectorAll('[data-payment-open]').forEach((button) => {
        button.addEventListener('click', () => {
            paymentModal?.classList.remove('hidden');
            paymentModal?.classList.add('flex');
        });
    });

    document.querySelectorAll('[data-payment-close]').forEach((button) => {
        button.addEventListener('click', () => {
            paymentModal?.classList.add('hidden');
            paymentModal?.classList.remove('flex');
        });
    });

    // Payment confirm UX (konfirmasi UI, backend bisa ditambahkan belakangan)
    document.querySelectorAll('[data-payment-confirm]').forEach((button) => {
        button.addEventListener('click', () => {
            paymentModal?.classList.add('hidden');
            paymentModal?.classList.remove('flex');
            alert('Konfirmasi diterima. Silakan tunggu status tagihan diperbarui oleh pemilik.');
        });
    });


    // Print Invoice Functions
    const billsData = {!! json_encode($bills->map(fn($b) => [
        'id' => $b->id,
        'month' => $b->bill_month,
        'amount' => $b->amount,
        'va_number' => $b->va_number,
        'status' => $b->status,
        'tenant_name' => $b->tenant->user->name,
        'tenant_room' => 'Kamar ' . ($b->tenant->room?->room_number ?? '-'),
        'tenant_phone' => $b->tenant->phone_number,
        'due_date' => $b->tenant->due_date->format('d M Y'),
    ])) !!};

    function formatRupiah(value) {
        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function printInvoice(billId) {
        const bill = billsData.find(b => b.id === billId);
        if (!bill) return;

        const template = document.getElementById('invoice-print-template').innerHTML;
        const printWindow = window.open('', '', 'width=800,height=600');

        document.getElementById('inv-number').textContent = `#${bill.va_number}`;
        document.getElementById('inv-tenant-name').textContent = bill.tenant_name;
        document.getElementById('inv-tenant-room').textContent = bill.tenant_room;
        document.getElementById('inv-tenant-phone').textContent = bill.tenant_phone;
        document.getElementById('inv-date').textContent = new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('inv-month').textContent = bill.month;
        document.getElementById('inv-amount').textContent = formatRupiah(bill.amount);
        document.getElementById('inv-total').textContent = formatRupiah(bill.amount);
        document.getElementById('inv-va').textContent = bill.va_number;
        document.getElementById('inv-due-date').textContent = bill.due_date;
        document.getElementById('inv-status').textContent = bill.status === 'lunas' ? '✓ Lunas' : 'Belum Lunas';

        const content = document.getElementById('invoice-print-template').innerHTML;

    </script>
@endpush


