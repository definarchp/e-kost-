@extends('layouts.app')

@section('title', 'Keluhan')
@section('page-title', 'Keluhan')
@section('page-subtitle', 'Keluhan yang sedang menunggu dan diproses oleh pemilik.')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Daftar Keluhan</h3>
            <p class="mt-1 text-sm text-slate-500">Keluhan bertanda <span class="font-semibold">pending</span> atau <span class="font-semibold">diproses</span>.</p>
        </div>
        <a href="{{ route('owner.dashboard') }}" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="overflow-x-auto p-6">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Penyewa</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Respon Pemilik</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($complaints as $complaint)
                    <tr>
                        <td class="px-4 py-4 text-slate-600">{{ $complaint->created_at?->format('d M Y') }}</td>
                        <td class="px-4 py-4 font-semibold text-slate-800">{{ $complaint->tenant?->user?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $complaint->tenant?->room?->room_number ? 'Kamar '.$complaint->tenant->room->room_number : '-' }}</td>
                        <td class="px-4 py-4 text-slate-700">{{ $complaint->description }}</td>
                        <td class="px-4 py-4">
                            @php($status = $complaint->status)
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold capitalize {{ $status === 'pending' ? 'bg-amber-50 text-amber-700' : ($status === 'diproses' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-slate-600">
                            @if ($complaint->owner_response)
                                <span class="text-slate-700">{{ $complaint->owner_response }}</span>
                            @else
                                <span class="text-slate-400">(belum ada respon)</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if ($complaint->status !== 'selesai')
                                <form method="POST" action="{{ route('owner.complaints.update', $complaint) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="selesai">

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Tanggapan Pemilik</label>
                                        <textarea name="owner_response" required rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('owner_response') }}</textarea>
                                        @error('owner_response')
                                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow transition hover:bg-emerald-700">
                                        <i class="fa-solid fa-check"></i>
                                        Selesaikan
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-semibold text-emerald-700">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada keluhan pending/diproses.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

