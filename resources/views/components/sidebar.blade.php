@props(['current' => ''])

<aside class="w-64 bg-slate-900 text-slate-200 min-h-screen flex flex-col justify-between flex-shrink-0 border-r border-slate-800">
    <div>
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800/80 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="font-extrabold text-base tracking-tight text-white block">SIGALING</span>
                <span class="text-[11px] text-emerald-400 font-medium tracking-wide uppercase block">Civic Tech DLH Kota</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-3 space-y-1 text-sm font-medium">
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>

            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('home') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-grid" class="w-4 h-4 {{ request()->routeIs('home') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Beranda</span>
            </a>

            <a href="{{ route('peta') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('peta*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="map" class="w-4 h-4 {{ request()->routeIs('peta*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Peta Laporan</span>
            </a>

            <a href="{{ route('lapor') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('lapor') && !request()->routeIs('lapor.tracking') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="plus-circle" class="w-4 h-4 {{ request()->routeIs('lapor') && !request()->routeIs('lapor.tracking') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Buat Laporan</span>
            </a>

            <a href="{{ route('lapor.tracking') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('lapor.tracking') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="search" class="w-4 h-4 {{ request()->routeIs('lapor.tracking') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Lacak Status</span>
            </a>

            <a href="{{ route('riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('riwayat*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="history" class="w-4 h-4 {{ request()->routeIs('riwayat*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Riwayat Pengaduan</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Informasi & Edukasi</div>

            <a href="{{ route('alur') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('alur') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="git-branch" class="w-4 h-4 {{ request()->routeIs('alur') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Alur Pelaporan</span>
            </a>

            <a href="{{ route('edukasi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('edukasi') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="book-open" class="w-4 h-4 {{ request()->routeIs('edukasi') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Edukasi & 3R</span>
            </a>

            <a href="{{ route('jadwal-sampah') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('jadwal-sampah') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="truck" class="w-4 h-4 {{ request()->routeIs('jadwal-sampah') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Jadwal Sampah</span>
            </a>
        </nav>
    </div>

    <!-- User Account Panel Bottom -->
    <div class="p-3 border-t border-slate-800/80">
        @auth
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-slate-900 font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <span class="block text-xs font-semibold text-white truncate">{{ auth()->user()->nama }}</span>
                        <span class="block text-[10px] text-slate-400 truncate capitalize">{{ auth()->user()->role ?: 'Warga' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-1 pt-1.5 border-t border-slate-700/60">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex-1 text-center py-1.5 px-2 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-semibold hover:bg-emerald-500/30 transition">
                            Admin
                        </a>
                    @elseif(auth()->user()->isPetugas())
                        <a href="{{ route('petugas.dashboard') }}" class="flex-1 text-center py-1.5 px-2 rounded-lg bg-indigo-500/20 text-indigo-300 text-xs font-semibold hover:bg-indigo-500/30 transition">
                            Petugas
                        </a>
                    @else
                        <a href="{{ route('profil') }}" class="flex-1 text-center py-1.5 px-2 rounded-lg bg-slate-700/80 text-slate-200 text-xs font-medium hover:bg-slate-700 transition">
                            Profil
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 transition" title="Keluar">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                <span class="block text-xs text-slate-300">Akses akun SIGALING</span>
                <div class="flex gap-1.5">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 px-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-500 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 px-2.5 rounded-xl bg-slate-700 text-slate-200 font-medium text-xs hover:bg-slate-600 transition">
                        Daftar
                    </a>
                </div>
            </div>
        @endauth
    </div>
</aside>
