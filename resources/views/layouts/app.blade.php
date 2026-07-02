<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', "E-Kost D'Brissel")</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    @auth
        @php
            $isOwner = auth()->user()->role === 'pemilik';
            $navItems = $isOwner
                ? [
                    ['label' => 'Dashboard Utama', 'icon' => 'fa-chart-line', 'route' => 'owner.dashboard'],
                    ['label' => 'Data Anak Kost', 'icon' => 'fa-users', 'route' => 'owner.tenants.index'],
                    ['label' => 'Kamar Kost', 'icon' => 'fa-door-open', 'route' => 'owner.rooms.index'],
                    ['label' => 'Keluhan', 'icon' => 'fa-headset', 'route' => 'owner.complaints.index'],
                    ['label' => 'Tagihan Bulanan', 'icon' => 'fa-file-invoice-dollar', 'route' => 'owner.bills.index'],
                    ['label' => 'Laporan Keuangan Kost', 'icon' => 'fa-chart-pie', 'route' => 'owner.financial-reports.index'],
                ]

                : [
                    ['label' => 'Portal Saya', 'icon' => 'fa-house-user', 'route' => 'tenant.dashboard'],
                    ['label' => 'Tagihan', 'icon' => 'fa-receipt', 'route' => 'tenant.bills.index'],
                    ['label' => 'Kirim Keluhan', 'icon' => 'fa-headset', 'route' => 'tenant.complaints.create'],
                ];
        @endphp

        <div class="min-h-screen md:flex">
            <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-slate-900 text-white transition-transform duration-200 md:static md:w-64 md:translate-x-0">
                <div class="flex h-full flex-col">
                    <div class="flex items-center justify-between border-b border-slate-800 p-6">
                        <a href="{{ route($isOwner ? 'owner.dashboard' : 'tenant.dashboard') }}" class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 shadow-lg">
                                <i class="fa-solid fa-hotel text-white"></i>
                            </span>
                            <span>
                                <span class="block text-sm font-bold tracking-wide">D'BRISSEL</span>
                                <span class="block text-[11px] font-medium text-emerald-400">{{ $isOwner ? 'Pemilik / Admin' : 'Portal Penyewa' }}</span>
                            </span>
                        </a>
                        <button type="button" data-sidebar-close class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white md:hidden">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <nav class="flex-1 space-y-1.5 overflow-y-auto p-4">
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-slate-800 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                                <i class="fa-solid {{ $item['icon'] }} w-5"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="border-t border-slate-800 bg-slate-950 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-semibold text-slate-200">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] capitalize text-slate-500">{{ auth()->user()->role }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-900 hover:text-red-400" title="Keluar">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div id="sidebar-backdrop" data-sidebar-close class="fixed inset-0 z-30 hidden bg-slate-950/40 md:hidden"></div>

            <main class="min-h-screen flex-1">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur md:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <button type="button" data-sidebar-open class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 md:hidden">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                            <div class="min-w-0">
                                <h1 class="truncate text-lg font-bold text-slate-900">@yield('page-title', "E-Kost D'Brissel")</h1>
                                <p class="truncate text-xs text-slate-500">@yield('page-subtitle', 'Sistem manajemen kost digital.')</p>
                            </div>
                        </div>
                        <div class="hidden items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 sm:flex">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>Sistem Aktif</span>
                        </div>
                    </div>
                </header>

                <section class="p-4 md:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </section>
            </main>
        </div>
    @else
        @yield('content')
    @endauth

    <script>
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
            button.addEventListener('click', () => {
                sidebar?.classList.remove('-translate-x-full');
                backdrop?.classList.remove('hidden');
            });
        });

        document.querySelectorAll('[data-sidebar-close]').forEach((button) => {
            button.addEventListener('click', () => {
                sidebar?.classList.add('-translate-x-full');
                backdrop?.classList.add('hidden');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
