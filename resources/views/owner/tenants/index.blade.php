@extends('layouts.app')

@section('title', 'Data Anak Kost')
@section('page-title', 'Data Anak Kost')
@section('page-subtitle', 'Daftar profil penyewa yang terhubung ke akun dan kamar.')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 p-6">
        <h2 class="font-bold text-slate-900">Manajemen Anak Kost</h2>
        <a href="{{ route('owner.tenants.create') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
            <i class="fa-solid fa-plus mr-2"></i>Tambah
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">NIK</th>
                    <th class="px-6 py-3">Kamar</th>
                    <th class="px-6 py-3">Telepon</th>
                    <th class="px-6 py-3">Jatuh Tempo</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tenants as $tenant)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $tenant->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $tenant->nik }}</td>
                        <td class="px-6 py-4">
                            @if ($tenant->room)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <i class="fa-solid fa-door-open"></i> Kamar {{ $tenant->room->room_number }}
                                </span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $tenant->phone_number }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $tenant->due_date?->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('owner.tenants.show', $tenant) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                                <a href="{{ route('owner.tenants.edit', $tenant) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <button type="button" onclick="deleteTenant({{ $tenant->id }})" class="text-xs font-semibold text-red-600 hover:text-red-700">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                            <i class="fa-solid fa-inbox mb-3 block text-2xl text-slate-300"></i>
                            Belum ada data penyewa. <a href="{{ route('owner.tenants.create') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold">Tambah penyewa sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6">{{ $tenants->links() }}</div>
</div>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function deleteTenant(tenantId) {
        if (confirm('Apakah Anda yakin ingin menghapus penyewa ini? Kamar akan otomatis menjadi tersedia.')) {
            const form = document.getElementById('delete-form');
            form.action = `/owner/tenants/${tenantId}`;
            form.submit();
        }
    }
</script>
@endpush
