@extends('layouts.app')

@section('title', 'Riwayat Pengaduan Publik — SIGALING PADANG')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 hairline-b">
            <div class="space-y-2">
                <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                    Arsip Publik Terbuka
                </span>
                <h1 class="text-3xl sm:text-4xl font-semibold text-[#1d1d1f] tracking-tight">
                    {{ request('mode') === 'saya' ? 'Riwayat Pengaduan Saya' : 'Semua Pengaduan Publik' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#6e6e73]">
                    Transparansi seluruh penanganan masalah kebersihan dan lingkungan hidup Kota Padang.
                </p>
            </div>

            @auth
                <div class="flex items-center gap-2 text-xs">
                    <a href="{{ route('riwayat') }}" class="px-4 py-1.5 rounded-full transition-colors {{ request('mode') !== 'saya' ? 'bg-[#1d1d1f] text-white' : 'text-[#6e6e73] hover:text-[#1d1d1f] bg-white border border-[#e5e5ea]' }}">
                        Semua Publik
                    </a>
                    <a href="{{ route('riwayat', ['mode' => 'saya']) }}" class="px-4 py-1.5 rounded-full transition-colors {{ request('mode') === 'saya' ? 'bg-[#1d1d1f] text-white' : 'text-[#6e6e73] hover:text-[#1d1d1f] bg-white border border-[#e5e5ea]' }}">
                        Laporan Saya
                    </a>
                </div>
            @endauth
        </div>

        <!-- Filters Bar -->
        <form method="GET" action="{{ route('riwayat') }}" class="pt-6 flex flex-wrap items-center gap-3 text-xs">
            @if(request('mode'))
                <input type="hidden" name="mode" value="{{ request('mode') }}">
            @endif

            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-3.5 h-3.5 text-[#86868b] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, kode tiket, alamat..." class="w-full pl-9 pr-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:border-[#1d1d1f]">
            </div>

            <select name="kategori" onchange="this.form.submit()" class="px-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                <option value="">Semua Status</option>
                <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="Dalam Penanganan" {{ request('status') == 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>

            @if(request('search') || request('kategori') || request('status'))
                <a href="{{ route('riwayat', request('mode') ? ['mode' => request('mode')] : []) }}" class="text-[#86868b] hover:text-[#1d1d1f] px-2 py-1">
                    Reset Filter
                </a>
            @endif
        </form>
    </section>

    <!-- ===== OPEN REPORTS FEED ===== -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="divide-y divide-[#e5e5ea] hairline-y">
            @forelse($laporans as $laporan)
                @php
                    $badgeClass = match($laporan->status) {
                        'Menunggu Verifikasi' => 'badge-menunggu',
                        'Terverifikasi' => 'badge-terverifikasi',
                        'Dalam Penanganan' => 'badge-proses',
                        'Selesai' => 'badge-selesai',
                        default => 'badge-ditolak',
                    };
                @endphp
                <div class="py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white px-3 -mx-3 rounded-lg transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs text-[#86868b] tabular-nums">{{ $laporan->kode_laporan }}</span>
                            <span class="badge-status {{ $badgeClass }}">{{ $laporan->status }}</span>
                            <span class="text-xs text-[#0f5132] font-medium">{{ $laporan->kategori->nama_kategori ?? 'Umum' }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-[#1d1d1f]">
                            <a href="{{ route('lapor.detail', $laporan->kode_laporan) }}" class="hover:underline">
                                {{ $laporan->judul }}
                            </a>
                        </h3>
                        <p class="text-xs text-[#6e6e73] line-clamp-1 max-w-2xl">
                            {{ $laporan->deskripsi }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-[#86868b] flex-shrink-0">
                        <span>{{ $laporan->kecamatan ?? 'Kota Padang' }}</span>
                        <span class="tabular-nums">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d M Y') }}</span>
                        <a href="{{ route('lapor.detail', $laporan->kode_laporan) }}" class="text-[#1d1d1f] hover:text-[#0f5132] font-medium flex items-center gap-1">
                            <span>Detail</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-xs text-[#86868b]">
                    Tidak ada arsip pengaduan yang sesuai dengan kriteria pencarian.
                </div>
            @endforelse
        </div>

        <div class="pt-8">
            {{ $laporans->links() }}
        </div>
    </div>
@endsection
