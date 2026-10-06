<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGALING PADANG — Sistem Pengaduan Lingkungan Hidup Kota Padang')</title>
    <meta name="description" content="Layanan resmi pengaduan dan pemantauan masalah kebersihan dan lingkungan hidup masyarakat Kota Padang. Dikelola oleh Dinas Lingkungan Hidup Pemerintah Kota Padang.">
    <meta name="author" content="Dinas Lingkungan Hidup Kota Padang">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />

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
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Main Navigation Header (Clean Workspace with Dynamic Frosted Glass Scroll Fade) -->
    <header class="sticky top-0 z-40 transition-all duration-300"
            x-data="{ mobileMenuOpen: false, scrolled: false }"
            x-init="scrolled = ((window.pageYOffset || document.documentElement.scrollTop) > 5)"
            @scroll.window="scrolled = ((window.pageYOffset || document.documentElement.scrollTop) > 5)"
            :class="scrolled ? 'bg-white/75 backdrop-blur-md border-b border-slate-200/90 shadow-xs' : 'bg-white/95 backdrop-blur-sm border-b border-slate-200/50'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                
                <!-- Brand & Scope (Admin-like brand with green pop up badge & dot indicator) -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shadow-xs shadow-emerald-500/50 group-hover:scale-110 transition-transform"></span>
                        <span class="text-[15px] font-extrabold tracking-tight text-slate-900">SIGALING</span>
                        <span class="text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">Masyarakat</span>
                    </a>

                    <!-- Desktop Primary Workspace Links (With Green Pop Up Active Pills & Dropdown) -->
                    <nav class="hidden lg:flex items-center gap-0.5 text-[13px] font-medium text-slate-600">
                        <a href="{{ route('home') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('home') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Beranda
                        </a>
                        <a href="{{ route('peta') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('peta') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Peta Sebaran
                        </a>
                        <a href="{{ route('riwayat') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('riwayat') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Riwayat Publik
                        </a>
                        <a href="{{ route('transparansi') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('transparansi') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Transparansi
                        </a>
                        <a href="{{ route('lapor.tracking') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('lapor.tracking') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            Lacak Aduan
                        </a>

                        <!-- Dropdown Pop-Up Menu (Like Master Data in Admin Navbar) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="px-2.5 py-1.5 rounded-md transition-colors flex items-center gap-1 {{ request()->routeIs('jadwal-sampah', 'edukasi', 'alur', 'tps.*') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                                <span>Informasi</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                            </button>
                            <div x-show="open" x-transition class="absolute left-0 mt-1.5 w-52 bg-white rounded-xl shadow-md border border-slate-200 py-1.5 text-xs text-slate-700 z-50">
                                <a href="{{ route('tps.index') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('tps.*') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Titik Lokasi TPS (11 Kec)</a>
                                <a href="{{ route('jadwal-sampah') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('jadwal-sampah') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Jadwal Armada</a>
                                <a href="{{ route('edukasi') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('edukasi') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Edukasi Lingkungan</a>
                                <a href="{{ route('alur') }}" class="block px-3.5 py-1.5 hover:bg-emerald-50 hover:text-emerald-900 {{ request()->routeIs('alur') ? 'font-bold text-emerald-900 bg-emerald-50/70' : '' }}">Alur SOP</a>
                            </div>
                        </div>

                        <a href="{{ route('faq') }}" class="px-2.5 py-1.5 rounded-md transition-colors {{ request()->routeIs('faq') ? 'text-emerald-900 font-bold bg-emerald-50 border border-emerald-200/70 shadow-2xs' : 'hover:text-slate-900 hover:bg-slate-100/80' }}">
                            FAQ
                        </a>
                    </nav>
                </div>

                <!-- Right Utility Bar (Admin-like clean utility bar + Nike-sharp high contrast button) -->
                <div class="hidden sm:flex items-center gap-3 text-xs">
                    @auth
                        @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold transition-colors flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Konsol Admin</span>
                            </a>
                        @elseif(in_array(Auth::user()->role, ['admin_instansi', 'supervisor']))
                            <a href="{{ route('instansi.dashboard') }}" class="px-2.5 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-900 font-semibold transition-colors flex items-center gap-1.5">
                                <i data-lucide="building-2" class="w-3.5 h-3.5 text-blue-600"></i>
                                <span>Portal Instansi</span>
                            </a>
                        @elseif(Auth::user()->role === 'auditor')
                            <a href="{{ route('auditor.dashboard') }}" class="px-2.5 py-1.5 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-900 font-semibold transition-colors flex items-center gap-1.5">
                                <i data-lucide="scale" class="w-3.5 h-3.5 text-amber-600"></i>
                                <span>Pengawasan</span>
                            </a>
                        @elseif(Auth::user()->role === 'petugas')
                            <a href="{{ route('petugas.dashboard') }}" class="px-2.5 py-1.5 rounded-md bg-purple-50 hover:bg-purple-100 text-purple-900 font-semibold transition-colors flex items-center gap-1.5">
                                <i data-lucide="hard-hat" class="w-3.5 h-3.5 text-purple-600"></i>
                                <span>Portal Petugas</span>
                            </a>
                        @else
                            <a href="{{ route('profil') }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium transition-colors flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span>{{ Auth::user()->nama }}</span>
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Keluar">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 font-medium px-2 py-1 transition-colors">
                            Masuk
                        </a>
                    @endauth

                    <span class="text-slate-200">|</span>

                    <!-- Sharp, Confident Action Button (Vibrant Civic Green Style) -->
                    <a href="{{ route('lapor') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold tracking-tight shadow-xs hover:shadow transition-all">
                        <i data-lucide="plus" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                        <span>Buat Laporan</span>
                    </a>
                </div>

                <!-- Mobile Menu Toggle Button -->
                <div class="flex items-center gap-2 lg:hidden">
                    <a href="{{ route('lapor') }}" class="px-3 py-1 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold sm:hidden flex items-center gap-1 shadow-2xs">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Lapor</span>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-1.5 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none transition-colors">
                        <i data-lucide="menu" class="w-4.5 h-4.5" x-show="!mobileMenuOpen"></i>
                        <i data-lucide="x" class="w-4.5 h-4.5" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileMenuOpen" x-collapse class="lg:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-2 text-[14px] font-medium text-slate-800 shadow-lg">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Beranda
            </a>
            <a href="{{ route('peta') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('peta') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Peta Sebaran Laporan
            </a>
            <a href="{{ route('riwayat') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('riwayat') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Riwayat Pengaduan Publik
            </a>
            <a href="{{ route('transparansi') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('transparansi') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Transparansi & SLA
            </a>
            <a href="{{ route('lapor.tracking') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('lapor.tracking') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Lacak Status Aduan
            </a>
            <a href="{{ route('tps.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('tps.*') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Titik Lokasi TPS (11 Kecamatan)
            </a>
            <a href="{{ route('jadwal-sampah') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('jadwal-sampah') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Jadwal Armada Sampah
            </a>
            <a href="{{ route('edukasi') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('edukasi') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Edukasi Lingkungan
            </a>
            <a href="{{ route('alur') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('alur') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                Standar Operasional (SOP)
            </a>
            <a href="{{ route('faq') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('faq') ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200/70' : 'text-slate-600 hover:bg-slate-50' }}">
                FAQ (Tanya Jawab)
            </a>

            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[13.5px]">
                @auth
                    @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                        <a href="{{ route('admin.dashboard') }}" class="font-bold text-emerald-700">Konsol Admin</a>
                    @elseif(in_array(Auth::user()->role, ['admin_instansi', 'supervisor']))
                        <a href="{{ route('instansi.dashboard') }}" class="font-bold text-blue-700">Portal Instansi</a>
                    @elseif(Auth::user()->role === 'auditor')
                        <a href="{{ route('auditor.dashboard') }}" class="font-bold text-amber-700">Pengawasan</a>
                    @elseif(Auth::user()->role === 'petugas')
                        <a href="{{ route('petugas.dashboard') }}" class="font-bold text-purple-700">Portal Petugas</a>
                    @else
                        <a href="{{ route('profil') }}" class="font-bold text-slate-800">{{ Auth::user()->nama }}</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-rose-600 font-semibold hover:underline">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-800 font-bold">Masuk Akun</a>
                    <a href="{{ route('register') }}" class="text-emerald-700 font-bold">Daftar Akun Warga</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- 3. Dignified Civic Footer (Pemerintah Kota Padang) -->
    <footer class="bg-slate-100 text-slate-600 border-t border-slate-200 text-xs mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-slate-200">
                <!-- Col 1: Identity & Office -->
                <div class="md:col-span-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                            <i data-lucide="leaf" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <span class="font-black text-sm text-slate-900 tracking-tight block">SIGALING PADANG</span>
                            <span class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider block">Dinas Lingkungan Hidup Kota Padang</span>
                        </div>
                    </div>
                    <p class="text-slate-600 leading-relaxed text-xs max-w-sm pt-1">
                        Sistem Informasi & Pengaduan Kebersihan Lingkungan Hidup resmi masyarakat Kota Padang. Setiap laporan divalidasi dan ditindaklanjuti dengan standar transparansi publik.
                    </p>
                    <div class="pt-2 text-[11px] text-slate-600 space-y-1.5">
                        <p class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-700 flex-shrink-0"></i>
                            <span>Jl. Simpang By Pass No. 7, Aie Pacah, Kota Padang, Sumatera Barat</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-emerald-700 flex-shrink-0"></i>
                            <span>dlh@padang.go.id &bull; sigaling@padang.go.id</span>
                        </p>
                    </div>
                </div>

                <!-- Col 2: Pelayanan Publik -->
                <div class="md:col-span-4 space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Layanan Pengaduan & Informasi</h3>
                    <ul class="space-y-2 text-slate-600">
                        <li><a href="{{ route('lapor') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Buat Laporan Pengaduan</a></li>
                        <li><a href="{{ route('lapor.tracking') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Lacak Status Tiket Aduan</a></li>
                        <li><a href="{{ route('peta') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Peta Sebaran Titik Masalah</a></li>
                        <li><a href="{{ route('jadwal-sampah') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Jadwal Armada Pengangkutan</a></li>
                        <li><a href="{{ route('riwayat') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Riwayat Pengaduan Publik</a></li>
                    </ul>
                </div>

                <!-- Col 3: Panduan & Bantuan -->
                <div class="md:col-span-3 space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pusat Bantuan & SOP</h3>
                    <ul class="space-y-2 text-slate-600">
                        <li><a href="{{ route('alur') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Standar Operasional (SOP)</a></li>
                        <li><a href="{{ route('edukasi') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Edukasi Pemilahan Sampah</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-emerald-800 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-emerald-600"></i> Tanya Jawab (FAQ)</a></li>
                    </ul>
                    <div class="pt-2">
                        <span class="text-[11px] font-semibold text-slate-700 block mb-2">Pengaduan Darurat Cepat:</span>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="https://wa.me/62811666112" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                <span>WhatsApp DLH</span>
                            </a>
                            <a href="{{ route('lapor') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs hover:shadow transition-all">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Lapor Aduan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="pt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} Dinas Lingkungan Hidup Pemerintah Kota Padang. Hak cipta dilindungi.</p>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1 text-emerald-800 font-medium"><i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Portal Resmi Pemko Padang</span>
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('alur') }}" class="hover:text-slate-900 transition-colors">SOP Pelayanan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>

    <!-- Lucide Icons Activation -->
    <script>
        function triggerLucideIcons() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons !== 'function') {
                lucide.createIcons({
                    attrs: {
                        'stroke-width': 1.8,
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
