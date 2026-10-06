@extends('layouts.auditor')

@section('title', 'Dashboard Pengawasan & Audit — SIGALING')

@section('content')
<div class="space-y-8">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Pengawasan Kinerja & SLA Publik</h1>
            <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[10px] font-bold border border-purple-200">Inspektorat & Pengawas</span>
        </div>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit independen untuk memantau kepatuhan target SLA instansi, deteksi kasus mangkrak, dan evaluasi rantai penanganan.</p>
    </div>

    <!-- Metrik Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Pengaduan</span>
            <div class="text-3xl font-bold text-slate-900 tracking-tight">{{ $totalLaporan }}</div>
            <span class="text-[10px] text-slate-400">Seluruh wilayah</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Selesai Tuntas</span>
            <div class="text-3xl font-bold text-emerald-700 tracking-tight">{{ $totalSelesai }}</div>
            <span class="text-[10px] text-emerald-600 font-medium">Terverifikasi lapangan</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider block">Kasus Overdue (Terlambat)</span>
            <div class="text-3xl font-bold text-rose-700 tracking-tight">{{ $totalOverdue }}</div>
            <span class="text-[10px] text-rose-600 font-medium">Melewati target SLA</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-1">
            <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider block">Tingkat Kepatuhan SLA</span>
            <div class="text-3xl font-bold text-blue-700 tracking-tight">{{ $slaComplianceRate }}%</div>
            <span class="text-[10px] text-blue-600 font-medium">Target minimum 85%</span>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overdue Warnings -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                        <span>Kasus Terlambat Membutuhkan Eskalasi</span>
                    </h2>
                    <p class="text-xs text-slate-500">Daftar penugasan instansi yang melampaui batas waktu SLA.</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($overdueAssignments as $asg)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <span class="font-mono font-bold text-rose-700">{{ $asg->laporan->kode_laporan ?? '-' }}</span>
                            <h3 class="font-medium text-slate-900 line-clamp-1">{{ $asg->laporan->judul ?? '-' }}</h3>
                            <span class="text-[11px] text-slate-500 block">{{ $asg->organization->name ?? '-' }}</span>
                        </div>
                        <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-1 rounded-lg border border-rose-200 flex-shrink-0">
                            Terlambat
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">Tidak ada kasus yang melewati batas SLA saat ini. Kinerja instansi optimal.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Audit Log Trail -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <i data-lucide="history" class="w-4 h-4 text-slate-700"></i>
                        <span>Log Aktivitas Terkini (Audit Trail)</span>
                    </h2>
                    <p class="text-xs text-slate-500">Pencatatan real-time perubahan status dan mutasi kasus.</p>
                </div>
                <a href="{{ route('auditor.audit-log') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentLogs as $log)
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $log->actor_name ?: 'Sistem' }}</span>
                            <span class="font-mono text-[11px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">{{ $log->action }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 tabular-nums">
                            {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M, H:i') }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">Belum ada riwayat aktivitas log.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
