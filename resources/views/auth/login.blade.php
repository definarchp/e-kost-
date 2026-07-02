@extends('layouts.app')

@section('title', "Login - E-Kost D'Brissel")

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-emerald-600 via-teal-700 to-cyan-800 px-4 py-10">
    <div class="flex w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl md:flex-row">
        <section class="relative flex flex-col justify-between overflow-hidden bg-slate-900 p-8 text-white md:w-1/2 md:p-12">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.18),transparent_50%)]"></div>
            <div class="relative z-10">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 shadow-lg">
                        <i class="fa-solid fa-hotel text-lg text-white"></i>
                    </div>
                    <span class="text-xl font-bold tracking-wider">D'BRISSEL</span>
                </div>
                <h1 class="mb-4 text-3xl font-extrabold leading-tight text-emerald-400">Kost Eksklusif Masa Kini</h1>
                <p class="mb-6 text-sm leading-relaxed text-slate-300">Sistem terintegrasi untuk pengelolaan administrasi kost yang transparan, aman, dan serba digital.</p>
                <div class="space-y-3 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-emerald-400"></i>
                        <span>Jl. Turen IV No. 18, Salatiga, Jawa Tengah</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-emerald-400"></i>
                        <span>+62 821-2345-6789</span>
                    </div>
                </div>
            </div>
            <p class="relative z-10 mt-10 text-xs text-slate-500">&copy; {{ date('Y') }} E-Kost D'Brissel</p>
        </section>

        <section class="bg-white p-8 md:w-1/2 md:p-12">
            <h2 class="mb-2 text-2xl font-bold text-slate-800">Selamat Datang</h2>
            <p class="mb-6 text-sm text-slate-500">Pilih peran Anda, lalu masuk memakai email dan kata sandi.</p>

            <div class="mb-6 grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1.5">
                <button type="button" data-role-tab="pemilik" class="role-tab rounded-lg bg-white px-3 py-2.5 text-sm font-medium text-emerald-700 shadow-sm">
                    <i class="fa-solid fa-user-tie mr-1.5"></i>Pemilik
                </button>
                <button type="button" data-role-tab="penyewa" class="role-tab rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:text-slate-900">
                    <i class="fa-solid fa-house-user mr-1.5"></i>Penyewa
                </button>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="selected_role" id="selected-role" value="pemilik">

                <div>
                    <label for="email" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat saya</span>
                </label>

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 font-semibold text-white shadow-md transition hover:bg-emerald-700 hover:shadow-lg">
                    <span>Masuk ke Sistem</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const selectedRole = document.getElementById('selected-role');
    const tabs = document.querySelectorAll('[data-role-tab]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            selectedRole.value = tab.dataset.roleTab;
            tabs.forEach((item) => {
                item.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
                item.classList.add('text-slate-600');
            });
            tab.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
            tab.classList.remove('text-slate-600');
        });
    });
</script>
@endpush
