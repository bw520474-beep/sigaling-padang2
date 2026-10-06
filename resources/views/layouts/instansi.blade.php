<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Instansi — SIGALING')</title>

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
<body class="bg-slate-50/70 text-slate-800 font-sans min-h-screen flex flex-col antialiased selection:bg-slate-900 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-2xs"
            x-data="{ mobileNav: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                
                <!-- Brand & Agency Scope -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('instansi.dashboard') }}" class="flex items-center gap-2 group">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-xs shadow-blue-500/50"></span>
                        <span class="text-[15px] font-extrabold tracking-tight text-slate-900">SIGALING</span>
                        <span class="text-xs font-semibold text-blue-900 bg-blue-50 border border-blue-200/70 px-2 py-0.5 rounded-md">Instansi</span>
                    </a>

                    @php
                        $userOrg = Auth::user()->organization;
                    @endphp
                    @if($userOrg)
                        <span class="hidden sm:inline-block text-xs font-medium text-slate-600 border-l border-slate-200 pl-3">
                            {{ $userOrg->name }}
                        </span>
                    @endif
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium text-slate-600">
                    <a href="{{ route('instansi.dashboard') }}" class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('instansi.dashboard') ? 'text-slate-900 font-bold bg-slate-100 border border-slate-200' : 'hover:text-slate-900 hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('instansi.laporan.index') }}" class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('instansi.laporan*') ? 'text-slate-900 font-bold bg-slate-100 border border-slate-200' : 'hover:text-slate-900 hover:bg-slate-50' }}">
                        Penugasan & Kasus
                    </a>
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-md text-emerald-800 bg-emerald-50 hover:bg-emerald-100 font-semibold transition-colors">
                            &larr; Konsol Global Superadmin
                        </a>
                    @endif
                </nav>

                <!-- User & Logout -->
                <div class="hidden md:flex items-center gap-3 text-[13px] font-medium">
                    <span class="text-slate-700 font-semibold bg-slate-100 px-2.5 py-1 rounded-lg text-xs">{{ Auth::user()->nama }}</span>
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

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-900 flex items-center gap-2 shadow-2xs">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-900 flex items-center gap-2 shadow-2xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
        });
    </script>
</body>
</html>
