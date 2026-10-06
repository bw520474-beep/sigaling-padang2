<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Pengawas Independen — SIGALING')</title>

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
            document.head.appendChild(s);
        }
    </script>

    <!-- Stylesheet -->
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
<body class="bg-slate-50/70 text-slate-800 font-sans min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('auditor.dashboard') }}" class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600 shadow-xs shadow-purple-500/50"></span>
                        <span class="text-[15px] font-extrabold tracking-tight text-slate-900">SIGALING</span>
                        <span class="text-xs font-semibold text-purple-900 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-md">Auditor</span>
                    </a>
                    <span class="hidden sm:inline-block text-xs font-medium text-slate-500 border-l border-slate-200 pl-3">
                        Pengawasan Independen &bull; Read-Only
                    </span>
                </div>

                <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium text-slate-600">
                    <a href="{{ route('auditor.dashboard') }}" class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('auditor.dashboard') ? 'text-slate-900 font-bold bg-slate-100' : 'hover:text-slate-900' }}">
                        Dashboard Pengawasan
                    </a>
                    <a href="{{ route('auditor.laporan') }}" class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('auditor.laporan*') ? 'text-slate-900 font-bold bg-slate-100' : 'hover:text-slate-900' }}">
                        Monitoring Laporan
                    </a>
                    <a href="{{ route('auditor.audit-log') }}" class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('auditor.audit-log*') ? 'text-slate-900 font-bold bg-slate-100' : 'hover:text-slate-900' }}">
                        Audit Trail Log
                    </a>
                </nav>

                <div class="flex items-center gap-3 text-xs">
                    <span class="text-slate-700 font-semibold bg-slate-100 px-2.5 py-1 rounded-lg">{{ Auth::user()->nama }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors" title="Keluar">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
        });
    </script>
</body>
</html>
