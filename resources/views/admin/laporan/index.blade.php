@extends('layouts.admin')

@section('title', 'Manajemen Pengaduan — Admin SIGALING')

@section('content')
    <!-- ===== 1. WORKSPACE HEADER ===== -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-4 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pengaduan</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh aduan masyarakat terdaftar di Kota Padang.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.laporan.rekap') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-600 bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-2xs transition-colors">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Rekap & Cetak</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-emerald-500 hover:text-emerald-700 text-xs font-semibold text-slate-700 bg-white shadow-2xs transition-colors">
                <i data-lucide="download" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    <!-- ===== 2. STATUS SEGMENTS & SEARCH ===== -->
    <div class="space-y-4 pb-6">
        <!-- Status Segment Filter Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs text-slate-500 font-medium border-b border-slate-200">
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('status', 'page'))) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ !request('status') ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Semua Aduan
            </a>
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['status' => 'Menunggu Verifikasi'])) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ request('status') === 'Menunggu Verifikasi' ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Menunggu Verifikasi
            </a>
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['status' => 'Terverifikasi'])) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ request('status') === 'Terverifikasi' ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Terverifikasi
            </a>
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['status' => 'Dalam Penanganan'])) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ request('status') === 'Dalam Penanganan' ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Dalam Penanganan
            </a>
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['status' => 'Selesai'])) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ request('status') === 'Selesai' ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Selesai Tuntas
            </a>
            <a href="{{ route('admin.laporan.index', array_merge(request()->except('page'), ['status' => 'Ditolak'])) }}" class="pb-2.5 px-1 border-b-2 transition-colors {{ request('status') === 'Ditolak' ? 'border-emerald-600 text-emerald-800 font-bold' : 'border-transparent hover:text-slate-900' }}">
                Ditolak
            </a>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap items-center gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search -->
            <div class="relative flex-1 min-w-[240px]">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode tiket, judul, pelapor, atau kecamatan..." class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
            </div>

            <!-- Kategori -->
            <select name="kategori" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                @endforeach
            </select>

            <!-- Prioritas -->
            <select name="prioritas" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                <option value="">Semua Prioritas</option>
                <option value="Tinggi" {{ request('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="Sedang" {{ request('prioritas') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Rendah" {{ request('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
            </select>

            @if(request('search') || request('kategori') || request('prioritas') || request('status'))
                <a href="{{ route('admin.laporan.index') }}" class="text-xs text-slate-500 hover:text-emerald-700 flex items-center gap-1 font-medium">
                    <i data-lucide="x" class="w-3 h-3"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </form>
    </div>

    <!-- ===== 3. OPEN DATA TABLE (NO DECORATIVE CARD) ===== -->
    <div class="overflow-x-auto border-y border-slate-200">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[10px] uppercase font-bold text-slate-500 tracking-wider border-b border-slate-200 bg-slate-50/60">
                    <th class="py-3 px-3">Kode Tiket</th>
                    <th class="py-3 px-3">Pengaduan</th>
                    <th class="py-3 px-3">Pelapor & Lokasi</th>
                    <th class="py-3 px-3">Prioritas</th>
                    <th class="py-3 px-3">Status</th>
                    <th class="py-3 px-3 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($laporans as $item)
                    @php
                        $badgeClass = match($item->status) {
                            'Menunggu Verifikasi' => 'badge-menunggu',
                            'Terverifikasi' => 'badge-terverifikasi',
                            'Dalam Penanganan' => 'badge-proses',
                            'Selesai' => 'badge-selesai',
                            default => 'badge-ditolak',
                        };
                        $priorityClass = match($item->prioritas) {
                            'Tinggi' => 'priority-tinggi',
                            'Sedang' => 'priority-sedang',
                            default => 'priority-rendah',
                        };
                    @endphp
                    <tr class="hover:bg-emerald-50/20 group transition-colors">
                        <!-- Kode & Waktu -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs tabular-nums block w-max">{{ $item->kode_laporan }}</span>
                            <span class="text-[11px] text-slate-400 tabular-nums block mt-1">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }}</span>
                        </td>

                        <!-- Judul & Kategori -->
                        <td class="py-3.5 px-3 max-w-sm">
                            <a href="{{ route('admin.laporan.detail', $item->id) }}" class="font-bold text-slate-900 group-hover:text-emerald-700 transition-colors block truncate">
                                {{ $item->judul }}
                            </a>
                            <span class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200/60 font-semibold px-2 py-0.5 rounded inline-block mt-0.5">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                        </td>

                        <!-- Pelapor & Kecamatan -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold block">{{ $item->nama_pelapor }}</span>
                            <span class="text-[11px] text-slate-500">{{ $item->kecamatan ?? '-' }}</span>
                        </td>

                        <!-- Prioritas -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="{{ $priorityClass }} text-[11px]">{{ $item->prioritas ?? 'Sedang' }}</span>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="badge-status {{ $badgeClass }}">{{ $item->status }}</span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 px-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.laporan.detail', $item->id) }}" class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-bold text-xs transition-colors">
                                <span>Tinjau</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                            Tidak ada data pengaduan yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ===== 4. PAGINATION ===== -->
    <div class="pt-6">
        {{ $laporans->links() }}
    </div>
@endsection
