@extends('layouts.app')

@section('title', 'Kamar Kost')
@section('page-title', 'Kamar Kost')
@section('page-subtitle', 'Pantau status kamar, harga, dan fasilitas.')

@section('content')
<div class="flex items-center justify-between gap-4">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Daftar Kamar</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola kamar kost dan fasilitasnya.</p>
    </div>
    <a href="{{ route('owner.rooms.create') }}" class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">
        <i class="fa-solid fa-plus mr-2"></i>Tambah Kamar Kost
    </a>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

    @forelse ($rooms as $room)
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Kamar</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $room->room_number }}</h2>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $room->status === 'terisi' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($room->status) }}</span>
            </div>
            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (($room->facilities ?? []) as $facility)
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $facility }}</span>
                @endforeach
            </div>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-slate-500 md:col-span-2 xl:col-span-3">Belum ada data kamar.</div>
    @endforelse
</div>
<div class="mt-6">{{ $rooms->links() }}</div>
@endsection
