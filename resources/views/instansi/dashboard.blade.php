@extends('layouts.instansi')

@section('title', 'Dashboard ' . ($org->name ?? 'Instansi') . ' — SIGALING')

@section('content')
<div class="space-y-8">
    <!-- Header Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ $org->name ?? 'Dashboard Instansi' }}</h1>
                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">OPD Portal</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemantauan kasus, verifikasi laporan masuk, penugasan armada teknis, dan pengawasan batas waktu SLA.</p>
        </div>
        <a href="{{ route('instansi.laporan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold shadow-xs">
            <i data-lucide="inbox" class="w-4 h-4"></i>
            <span>Buka Seluruh Laporan</span>
        </a>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Total Laporan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Penugasan</span>
            <div class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ $totalLaporan }}</div>
            <span class="text-[10px] text-slate-400">Tergabung di instansi ini</span>
        </div>

        <!-- Masuk / Menunggu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider block">Menunggu Verifikasi</span>
            <div class="text-2xl sm:text-3xl font-bold text-amber-700 tracking-tight">{{ $laporanMasuk }}</div>
            <span class="text-[10px] text-amber-600 font-medium">Perlu tindakan supervisor</span>
        </div>

        <!-- Sedang Proses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider block">Sedang Ditangani</span>
            <div class="text-2xl sm:text-3xl font-bold text-blue-700 tracking-tight">{{ $laporanProses }}</div>
            <span class="text-[10px] text-blue-600 font-medium">Petugas sedang bekerja</span>
        </div>

        <!-- Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Selesai Tuntas</span>
            <div class="text-2xl sm:text-3xl font-bold text-emerald-700 tracking-tight">{{ $laporanSelesai }}</div>
            <span class="text-[10px] text-emerald-600 font-medium">Pekerjaan terverifikasi</span>
        </div>

        <!-- Terlambat / Overdue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider block">Lewat SLA (Overdue)</span>
            <div class="text-2xl sm:text-3xl font-bold text-rose-700 tracking-tight">{{ $laporanOverdue }}</div>
            <span class="text-[10px] text-rose-600 font-medium">Eskalasi prioritas</span>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Incoming Reports -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Penugasan Terbaru</h2>
                    <p class="text-xs text-slate-500">Antrean tugas terbaru yang dialirkan oleh Smart Routing.</p>
                </div>
                <a href="{{ route('instansi.laporan.index') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentAssignments as $asg)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-slate-900">{{ $asg->laporan->kode_laporan ?? '-' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $asg->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : ($asg->status === 'received' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700') }}">
                                    {{ strtoupper($asg->status) }}
                                </span>
                                @if($asg->laporan && $asg->laporan->subcategory)
                                    <span class="text-[11px] text-slate-500">&bull; {{ $asg->laporan->subcategory->name }}</span>
                                @endif
                            </div>
                            <h3 class="font-medium text-slate-900 line-clamp-1">{{ $asg->laporan->judul ?? '-' }}</h3>
                            <p class="text-[11px] text-slate-400 line-clamp-1">{{ $asg->laporan->alamat ?? '-' }}</p>
                        </div>

                        <a href="{{ route('instansi.laporan.show', $asg->id) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium flex-shrink-0">
                            Kelola &rarr;
                        </a>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">Belum ada penugasan laporan untuk instansi ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Field Officers Status -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Petugas Lapangan</h2>
                    <p class="text-xs text-slate-500">Tim teknis internal instansi ({{ $petugasList->count() }} orang)</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($petugasList as $petugas)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                {{ substr($petugas->nama, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-semibold text-slate-900 block">{{ $petugas->nama }}</span>
                                <span class="text-[11px] text-slate-400">{{ $petugas->no_hp ?? $petugas->email }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">Siap Tugas</span>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">Belum ada akun petugas lapangan terdaftar di OPD ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
