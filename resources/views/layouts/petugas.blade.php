<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Petugas Lapangan - SIGALING')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons (Lokal Cepat & CDN Fallback) -->
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
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex flex-col selection:bg-purple-500 selection:text-white">

    <!-- Topbar Petugas -->
    <header class="bg-slate-900 text-white sticky top-0 z-40 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold">
                    <i data-lucide="hard-hat" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-extrabold text-sm tracking-tight text-white block">{{ Auth::user()->nama ?? (Auth::user()->name ?? 'PETUGAS LAPANGAN') }}</span>
                    <span class="text-[10px] text-purple-400 font-bold uppercase tracking-wider block">
                        {{ Auth::user()->opd->nama_opd ?? 'DLH / OPD Kota Padang' }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex items-center gap-2 text-xs font-semibold">
                <a href="{{ route('petugas.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('petugas.dashboard') ? 'bg-purple-600 text-white' : 'text-slate-300 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('petugas.tugas.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('petugas.tugas*') ? 'bg-purple-600 text-white' : 'text-slate-300 hover:text-white' }}">
                    Daftar Tugas
                </a>
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hidden sm:block">
                    Website Publik
                </a>

                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition-colors" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Flash notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        @yield('content')
    </main>

    <!-- Workspace Minimal Footer -->
    <footer class="border-t border-slate-200 bg-slate-100 text-slate-600 text-[11px] py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>SIGALING PADANG &bull; Portal Lapangan Petugas DLH / OPD Kota Padang</span>
            <span class="font-mono text-slate-500">Versi Lapangan</span>
        </div>
    </footer>

    <script>
        function triggerLucideIcons() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons({
                    attrs: {
                        'stroke-width': 1.9,
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round'
                    }
                });
            } else if (typeof window.initLucide === 'function') {
                window.initLucide();
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            triggerLucideIcons();
            setTimeout(triggerLucideIcons, 100);
            setTimeout(triggerLucideIcons, 400);
            setTimeout(triggerLucideIcons, 1000);
        });
        window.addEventListener('load', triggerLucideIcons);
        document.addEventListener('alpine:initialized', triggerLucideIcons);
    </script>

    @stack('scripts')
</body>
</html>
