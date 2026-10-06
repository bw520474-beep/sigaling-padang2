@extends('layouts.admin')

@section('title', 'Rekapitulasi Pengaduan Masyarakat — Admin SIGALING')

@section('content')
    <!-- ===== 1. HEADER DENGAN AKSI REKAP ===== -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Laporan & Audit</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Rekapitulasi Pengaduan</h1>
            <p class="text-xs text-slate-500 mt-1">Saring data pengaduan berdasarkan rentang tanggal, wilayah kecamatan, dan instansi OPD pelaksana.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.laporan.cetak', request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / Ekspor PDF</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-xs font-semibold text-slate-700 bg-white shadow-2xs transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- ===== 2. METRIK RINGKASAN DATA HASIL FILTER ===== -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5 my-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Aduan</span>
            <span class="text-xl font-black text-slate-900 mt-0.5 block">{{ $totalLaporan }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Menunggu Verifikasi</span>
            <span class="text-xl font-black text-amber-600 mt-0.5 block">{{ $totalMenunggu }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-purple-200 shadow-xs bg-purple-50/20">
            <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block">Dalam Penanganan</span>
            <span class="text-xl font-black text-purple-600 mt-0.5 block">{{ $totalProses }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-xs bg-emerald-50/20">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Selesai Tuntas</span>
            <span class="text-xl font-black text-emerald-600 mt-0.5 block">{{ $totalSelesai }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-rose-200 shadow-xs bg-rose-50/20 col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Ditolak</span>
            <span class="text-xl font-black text-rose-600 mt-0.5 block">{{ $totalDitolak }}</span>
        </div>
    </div>

    <!-- ===== 3. PANEL FILTER ADVANCED ===== -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs mb-8">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
            <i data-lucide="filter" class="w-4 h-4 text-emerald-600"></i>
            <span class="text-xs font-bold text-slate-800">Filter Parameter Rekapitulasi</span>
        </div>

        <form method="GET" action="{{ route('admin.laporan.rekap') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
            <!-- Rentang Tanggal Mulai -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            </div>

            <!-- Rentang Tanggal Selesai -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Aduan</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="Dalam Penanganan" {{ request('status') == 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Kecamatan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kecamatan</label>
                <select name="kecamatan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">Semua Kecamatan (11)</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <!-- OPD Disposisi -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Instansi OPD</label>
                <select name="opd_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">Semua OPD (5)</option>
                    @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->nama_opd }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-xs">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->anyFilled(['tgl_mulai', 'tgl_selesai', 'status', 'kecamatan', 'opd_id']))
                    <a href="{{ route('admin.laporan.rekap') }}" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ===== 4. TABEL REKAPITULASI HASIL FILTER ===== -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50/60 border-b border-slate-200/80 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700">Daftar Pengaduan Sesuai Kriteria Rekap ({{ $laporans->total() }} Data)</span>
            <a href="{{ route('admin.laporan.cetak', request()->all()) }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Dokumen Lengkap</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Kode & Tanggal</th>
                        <th class="px-5 py-3.5">Judul Pengaduan</th>
                        <th class="px-5 py-3.5">Wilayah & Alamat</th>
                        <th class="px-5 py-3.5">Pelapor</th>
                        <th class="px-5 py-3.5">Disposisi OPD</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-slate-400">
                                {{ $laporans->firstItem() + $idx }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-800 block">{{ $item->kode_laporan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="font-bold text-slate-900 block truncate">{{ $item->judul }}</span>
                                <span class="text-[10px] text-emerald-700 font-semibold">{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="font-semibold text-slate-800 block truncate">{{ $item->kecamatan ?? 'Kota Padang' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ $item->alamat }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="text-slate-800 font-medium block">{{ $item->nama_pelapor }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->kontak_pelapor }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($item->laporanOpd && $item->laporanOpd->opd)
                                    <span class="text-[11px] font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                                        {{ $item->laporanOpd->opd->nama_opd }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum Disposisi</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $stClass = match($item->status) {
                                        'Menunggu Verifikasi' => 'bg-amber-100 text-amber-800',
                                        'Terverifikasi' => 'bg-blue-100 text-blue-800',
                                        'Dalam Penanganan' => 'bg-purple-100 text-purple-800',
                                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                                        'Ditolak' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-slate-100 text-slate-800'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $stClass }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                <p class="text-xs">Tidak ditemukan data laporan pengaduan yang sesuai dengan kriteria filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporans->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
@endsection
