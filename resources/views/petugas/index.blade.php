@extends('layouts.petugas')

@section('title', 'Dashboard Petugas Lapangan - SIGALING PADANG')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Tugas Lapangan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola dan laporkan hasil eksekusi penanganan pengaduan lingkungan hidup.</p>
        </div>
        <a href="{{ route('petugas.tugas.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-colors flex items-center gap-2 shadow-sm w-fit">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i>
            <span>Buka Seluruh Tugas Saya</span>
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Tugas</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalTugas }}</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-amber-200 shadow-xs bg-amber-50/20">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Menunggu</span>
            <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $menunggu }}</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-purple-200 shadow-xs bg-purple-50/20">
            <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider block">Dalam Proses</span>
            <span class="text-2xl font-black text-purple-600 mt-1 block">{{ $proses }}</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-emerald-200 shadow-xs bg-emerald-50/20">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Selesai Tuntas</span>
            <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $selesai }}</span>
        </div>
    </div>

    <!-- 2 Columns: Tugas Aktif Saya & Tugas Baru Tersedia -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Tugas Aktif Saya -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i data-lucide="play-circle" class="w-4 h-4 text-purple-600"></i>
                        <span>Tugas yang Sedang Berjalan ({{ $tugasAktif->count() }})</span>
                    </h3>
                    <a href="{{ route('petugas.tugas.index') }}" class="text-xs font-bold text-purple-600 hover:underline">Semua &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse($tugasAktif as $t)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                            <div class="overflow-hidden">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-mono font-bold bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-700">
                                        {{ $t->laporan->kode_laporan ?? '-' }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">
                                        {{ $t->status }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 truncate">{{ $t->laporan->judul ?? 'Pengaduan' }}</h4>
                                <span class="text-[10px] text-slate-500 block truncate">{{ $t->laporan->alamat ?? '-' }}</span>
                            </div>
                            <a href="{{ route('petugas.tugas.detail', $t->id) }}" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs flex-shrink-0 transition-colors">
                                Kerjakan
                            </a>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 text-xs">
                            <p>Tidak ada tugas aktif yang sedang berjalan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tugas Tersedia di Antrean OPD -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <i data-lucide="inbox" class="w-4 h-4 text-emerald-600"></i>
                        <span>Antrean Tugas Baru Tersedia ({{ $tugasTersedia->count() }})</span>
                    </h3>
                </div>

                <div class="space-y-3">
                    @forelse($tugasTersedia as $item)
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center justify-between gap-3 hover:border-emerald-500 transition-colors">
                            <div class="overflow-hidden">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                        {{ $item->laporan->kode_laporan ?? '-' }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-emerald-700">{{ $item->opd->nama_opd ?? 'OPD' }}</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item->laporan->judul ?? '-' }}</h4>
                                <span class="text-[10px] text-slate-400 block truncate">{{ $item->laporan->alamat ?? '-' }}</span>
                            </div>
                            <form action="{{ route('petugas.tugas.ambil', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex-shrink-0 transition-colors">
                                    Ambil Tugas
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 text-xs">
                            <p>Belum ada tugas baru di antrean OPD.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
