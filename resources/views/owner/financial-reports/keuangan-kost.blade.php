@extends('layouts.app')

@section('title', 'Laporan Keuangan Kost')
@section('page-title', 'Laporan Keuangan Kost')
@section('page-subtitle', 'Rekap pendapatan kost berdasarkan Siklus Periode Buku (cut-off tanggal 10).')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-900">Laporan Keuangan Kost (Owner)</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Cut-off: dari <span class="font-semibold">tanggal 10</span> ke <span class="font-semibold">tanggal 10 bulan berikutnya</span> berdasarkan <span class="font-mono">bills.updated_at</span>.
                </p>
            </div>

            <form method="GET" action="{{ route('owner.financial-reports.index') }}" class="flex flex-wrap gap-3 items-end">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Laporan</label>
                    <select name="type" onchange="this.form.submit()"
                        class="w-44 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Laporan Per Bulan</option>
                        <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Laporan Per Tahun</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Urutan</label>
                    <select name="order" onchange="this.form.submit()"
                        class="w-44 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="newest" {{ $order === 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $order === 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

                <button type="submit" class="hidden">Submit</button>
            </form>
        </div>

        <div class="p-6">
            @if(empty($summary))
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6">
                    <div class="text-center">
                        <div class="text-2xl">💸</div>
                        <div class="mt-2 font-semibold text-slate-800">Belum ada data transaksi lunas</div>
                        <div class="mt-1 text-sm text-slate-600">Buat/mvalidasi tagihan hingga status menjadi <span class="font-semibold">lunas</span>.</div>
                    </div>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($summary as $row)
                        @php
                            $label = $row['label'];
                            $total = $row['total_pendapatan'];
                            $count = $row['jumlah_invoice'];
                        @endphp

                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wide text-emerald-700">Periode</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $label }}</div>
                                </div>
                                <div class="rounded-xl bg-emerald-600 px-3 py-1 text-xs font-bold text-white">{{ $type === 'monthly' ? 'Bulan' : 'Tahun' }}</div>
                            </div>

                            <div class="mt-4">
                                <div class="text-xs font-semibold text-slate-600">Total Pendapatan</div>
                                <div class="mt-1 text-lg font-bold text-emerald-800">Rp {{ number_format($total, 0, ',', '.') }}</div>
                            </div>

                            <div class="mt-3">
                                <div class="text-xs font-semibold text-slate-600">Jumlah Invoice (Lunas)</div>
                                <div class="mt-1 text-lg font-bold text-slate-900">{{ $count }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                    <div class="mt-8">
                        <div class="mb-4 flex items-center justify-end">
                                <a href="{{ route('owner.financial-reports.export', request()->query()) }}"
                               class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                <i class="fa-solid fa-download mr-2"></i>Unduh Laporan (PDF)
                            </a>
                        </div>

                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-bold text-slate-900">Rincian Riwayat Uang Masuk (Lunas)</h3>
                        <div class="text-xs text-slate-500">Menampilkan data dengan pagination</div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">

                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>

                                    <th class="px-4 py-3">Penyewa</th>
                                    <th class="px-4 py-3">Nomor Kamar</th>
                                    <th class="px-4 py-3">Periode Invoice Asli</th>
                                    <th class="px-4 py-3">Nominal</th>
                                    <th class="px-4 py-3">Tanggal Validasi Lunas</th>
                                    <th class="px-4 py-3">Siklus Periode Buku</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($detail as $bill)
                                    @php
                                        $tenantName = $bill->tenant?->user?->name ?? '-';
                                        $roomNumber = 'Kamar ' . ($bill->tenant?->room?->room_number ?? '-');
                                        $nominal = $bill->amount;

                                        $validatedAt = $bill->updated_at ? 
                                            \Carbon\Carbon::parse($bill->updated_at) : null;

                                        $validatedAtText = $validatedAt ? $validatedAt->format('Y/m/d H:i') : '-';

                                        $cycleBadge = $bill->periode_buku ?? '-';
                                        $cycleLabel = $cycleBadge;
                                    @endphp

                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-4 font-semibold text-slate-800">{{ $tenantName }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $roomNumber }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $bill->bill_month }}</td>
                                        <td class="px-4 py-4 font-bold text-slate-800">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4 font-mono text-xs text-slate-600">{{ $validatedAtText }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-100">
                                                Siklus Periode Buku: {{ $cycleLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                            Belum ada transaksi lunas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $detail->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

