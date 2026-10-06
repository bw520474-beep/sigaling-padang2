<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Konsol Administrator — SIGALING PADANG')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="{{ asset('vendor/lucide/lucide.min.js') }}"></script>
    <script>
        if (typeof lucide === 'undefined' || typeof lucide.createIcons !== 'function') {
            const s = document.createElement('script');
            s.src = 'https://unpkg.com/lucide@latest';
            s.onload = function() {
                if (typeof triggerLucideIcons === 'function') triggerLucideIcons();
            };
            document.head.appendChild(s);
        }
    </script>

    <!-- Stylesheet & Script SIGALING (Tahan Banting: Laptop & HP) -->
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true) ?: [];
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="bg-slate-50/70 text-slate-800 font-sans min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Product Workspace Navigation Bar (with Dynamic Scroll Fade) -->
    <header class="sticky top-0 z-40 transition-all duration-300"
            x-data="{ mobileNav: false, masterDataOpen: false, scrolled: false }"
            x-init="scrolled = ((window.pageYOffset || document.documentElement.scrollTop) > 5)"
            @scroll.window="scrolled = ((window.pageYOffset || document.documentElement.scrollTop) > 5)"
            :class="scrolled ? 'bg-white/75 backdrop-blur-md border-b border-slate-200/90 shadow-xs' : 'bg-white/95 backdrop-blur-sm border-b border-slate-200/60'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                
                <!-- Brand & Scope -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shadow-xs shadow-emerald-500/50 group-hover:scale-110 transition-transform"></span>
                        <span class="text-[15px] font-extrabold tracking-tight text-slate-900">SIGALING</span>
                        <span class="text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">Admin</span>
                    </a>

                    <!-- Desktop Primary Workspace Links -->
                    <nav class="hidden md:flex items-center gap-0.5 text-[13px] font-medium text-slate-600">
                        <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('admin.laporan.index', 'admin.laporan.show', 'admin.laporan.detail') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Pengaduan
                        </a>
                        <a href="{{ route('admin.laporan.rekap') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('admin.laporan.rekap', 'admin.laporan.cetak') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Rekap & Cetak
                        </a>
                        <a href="{{ route('admin.statistik') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('admin.statistik') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Statistik
                        </a>

                        <!-- Master Data Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="px-2.5 py-1.5 rounded-md transition-colors flex items-center gap-1 {{ request()->is('admin/kategori*', 'admin/opd*', 'admin/petugas*', 'admin/pengguna*', 'admin/tps*', 'admin/wilayah*', 'admin/instansi*', 'admin/routing*', 'admin/sla*', 'admin/audit-log*') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                                <span>Master Data</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                            </button>
                            <div x-show="open" x-transition class="absolute left-0 mt-1.5 w-56 bg-white rounded-xl shadow-md border border-slate-200 py-1.5 text-xs text-slate-700 z-50 divide-y divide-slate-100">
                                <div class="py-1">
                                    <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Civic Multi-Instansi</div>
                                    <a href="{{ route('admin.instansi.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.instansi*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Instansi & Unit Bidang</a>
                                    <a href="{{ route('admin.wilayah.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.wilayah*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Wilayah & Kecamatan</a>
                                    <a href="{{ route('admin.routing.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.routing*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Smart Routing Rules</a>
                                    <a href="{{ route('admin.sla.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.sla*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Aturan Target SLA</a>
                                    <a href="{{ route('admin.audit.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.audit*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Audit Trail & Security</a>
                                </div>
                                <div class="py-1">
                                    <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Data Operasional</div>
                                    <a href="{{ route('admin.kategori.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.kategori*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Kategori & Subkategori</a>
                                    <a href="{{ route('admin.petugas.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.petugas*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Petugas Lapangan</a>
                                    <a href="{{ route('admin.pengguna.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.pengguna*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Pengguna Sistem</a>
                                    <a href="{{ route('admin.tps.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.tps*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Data Titik TPS</a>
                                    <a href="{{ route('admin.opd.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('admin.opd*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Data OPD (Legacy)</a>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Right Utility Bar -->
                <div class="hidden md:flex items-center gap-3 text-[13px] font-medium">
                    <a href="{{ route('home') }}" target="_blank" class="text-slate-500 hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <span>Portal Publik</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>

                    <span class="text-slate-200">|</span>

                    <span class="text-slate-700 font-semibold bg-slate-100 px-2.5 py-1 rounded-lg text-xs">{{ Auth::user()->nama }}</span>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Keluar">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>

                <!-- Mobile Toggle -->
                <button @click="mobileNav = !mobileNav" class="md:hidden p-1.5 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none">
                    <i data-lucide="menu" class="w-4.5 h-4.5" x-show="!mobileNav"></i>
                    <i data-lucide="x" class="w-4.5 h-4.5" x-show="mobileNav" style="display: none;"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" x-collapse class="md:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-2 text-[14px] font-medium text-slate-800 shadow-md max-h-[85vh] overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="block py-1.5 px-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'font-bold text-emerald-900 bg-emerald-50 border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">Dashboard</a>
            <a href="{{ route('admin.laporan.index') }}" class="block py-1.5 px-2.5 rounded-lg {{ request()->routeIs('admin.laporan.index', 'admin.laporan.show', 'admin.laporan.detail') ? 'font-bold text-emerald-900 bg-emerald-50 border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">Semua Pengaduan</a>
            <a href="{{ route('admin.laporan.rekap') }}" class="block py-1.5 px-2.5 rounded-lg {{ request()->routeIs('admin.laporan.rekap', 'admin.laporan.cetak') ? 'font-bold text-emerald-900 bg-emerald-50 border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">Rekap & Cetak Aduan</a>
            <a href="{{ route('admin.statistik') }}" class="block py-1.5 px-2.5 rounded-lg {{ request()->routeIs('admin.statistik') ? 'font-bold text-emerald-900 bg-emerald-50 border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">Statistik</a>
            
            <div class="pt-2 border-t border-slate-100 text-xs text-slate-400 font-bold uppercase tracking-wider px-2">Civic Platform</div>
            <a href="{{ route('admin.instansi.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Instansi & Bidang</a>
            <a href="{{ route('admin.wilayah.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Wilayah / Kecamatan</a>
            <a href="{{ route('admin.routing.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Smart Routing Rules</a>
            <a href="{{ route('admin.sla.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Aturan Target SLA</a>
            <a href="{{ route('admin.audit.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Audit Trail & Security</a>

            <div class="pt-2 border-t border-slate-100 text-xs text-slate-400 font-bold uppercase tracking-wider px-2">Operasional</div>
            <a href="{{ route('admin.tps.index') }}" class="block py-1.5 px-2.5 rounded-lg {{ request()->routeIs('admin.tps*') ? 'font-bold text-emerald-900 bg-emerald-50 border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">Data Titik TPS</a>
            <a href="{{ route('admin.kategori.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Master Kategori</a>
            <a href="{{ route('admin.opd.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Master OPD</a>
            <a href="{{ route('admin.petugas.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Master Petugas</a>
            <a href="{{ route('admin.pengguna.index') }}" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:bg-slate-50">Master Pengguna</a>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[13.5px]">
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-emerald-700">Lihat Portal Publik</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-rose-600 font-semibold hover:underline">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Workspace Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4 text-emerald-700"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Workspace Minimal Footer -->
    <footer class="border-t border-slate-200 bg-slate-100 text-slate-600 text-[11px] py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>SIGALING PADANG &bull; Konsol Kerja Administrator DLH Kota Padang</span>
            <span class="font-mono text-slate-500">Versi Produksi</span>
        </div>
    </footer>

    <!-- Lucide Icons Activation -->
    <script>
        function triggerLucideIcons() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons({
                    attrs: {
                        'stroke-width': 1.75,
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round'
                    }
                });
            } else if (typeof window.initLucide === 'function') {
                window.initLucide();
            }
        }
        document.addEventListener('DOMContentLoaded', triggerLucideIcons);
        window.addEventListener('load', triggerLucideIcons);
        document.addEventListener('alpine:initialized', triggerLucideIcons);
    </script>

    @stack('scripts')
</body>
</html>
