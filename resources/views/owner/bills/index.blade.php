@extends('layouts.app')

@section('title', 'Tagihan Bulanan')
@section('page-title', 'Tagihan Bulanan')
@section('page-subtitle', 'Kelola invoice bulanan dan status pelunasan.')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 p-6">
            <h2 class="font-bold text-slate-900">Daftar Tagihan</h2>
            <a href="{{ route('owner.bills.create') }}"
                class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                <i class="fa-solid fa-plus mr-2"></i>Buat Invoice
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Penyewa</th>
                        <th class="px-6 py-3">Kamar</th>
                        <th class="px-6 py-3">Bulan</th>
                        <th class="px-6 py-3">Nominal</th>
                        <th class="px-6 py-3">VA</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Bukti Pembayaran</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($bills as $bill)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $bill->tenant?->user?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">Kamar {{ $bill->tenant?->room?->room_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $bill->bill_month }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">Rp
                                {{ number_format($bill->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $bill->va_number }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $bill->status === 'lunas' ? '✓ Lunas' : 'Belum lunas' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if ($bill->bukti_pembayaran_path)
                                    @php
                                        // Ambil nama file murni
                                        $rawFileName = basename($bill->bukti_pembayaran_path);
                                        $checkPath = 'bills/bukti_pembayaran/' . $rawFileName;

                                        // Cek keberadaan file murni di folder storage lokal
                                        $isExist = Storage::disk('public')->exists($checkPath);

                                        // KUNCINYA DI SINI: Gunakan asset() atau Storage::url() langsung mengarah ke public symlink Laragon
                                        $urlAkhir = asset('storage/' . $checkPath);
                                    @endphp

                                    @if ($isExist)
                                        <div class="flex items-center gap-3">
                                            <a href="{{ $urlAkhir }}" target="_blank"
                                                title="Klik untuk memperbesar gambar asli">
                                                <img src="{{ $urlAkhir }}"
                                                    alt="Bukti pembayaran {{ $bill->bill_month }}"
                                                    class="h-12 w-16 rounded-lg object-cover border border-slate-200 hover:scale-105 transition duration-200 shadow-sm" />
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-left">
                                            <span class="text-[11px] font-semibold text-red-500 block">File tidak di
                                                folder</span>
                                            <span class="text-[9px] text-slate-400 block font-mono">Nama:
                                                {{ $rawFileName }}</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-[11px] font-semibold text-slate-400">(Belum ada bukti)</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <a href="{{ route('owner.bills.edit', $bill) }}"
                                            class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <button type="button" onclick="deleteBill({{ $bill->id }})"
                                            class="text-xs font-semibold text-red-600 hover:text-red-700">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </div>

                                    @if ($bill->status === 'belum_dibayar')
                                        <form method="POST"
                                            action="{{ route('owner.bills.confirm', ['bill' => $bill->id]) }}"
                                            class="mt-1 flex gap-2 items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="lunas">
                                            <button type="submit"
                                                class="rounded-xl bg-emerald-600 px-3 py-2 text-[11px] font-semibold text-white hover:bg-emerald-700">
                                                Validasi & Tandai Lunas
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                                <i class="fa-solid fa-inbox mb-3 block text-2xl text-slate-300"></i>
                                Belum ada tagihan. <a href="{{ route('owner.bills.create') }}"
                                    class="text-emerald-600 hover:text-emerald-700 font-semibold">Buat invoice sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6">{{ $bills->links() }}</div>
    </div>

    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function deleteBill(billId) {
            if (confirm('Apakah Anda yakin ingin menghapus tagihan ini?')) {
                const form = document.getElementById('delete-form');
                form.action = `/owner/bills/${billId}`;
                form.submit();
            }
        }
    </script>
@endpush
