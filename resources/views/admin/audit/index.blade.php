@extends('layouts.admin')

@section('title', 'Audit Trail Sistem — Konsol Administrator')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Audit Trail & Log Aktivitas</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 text-[10px] font-bold border border-slate-200">Tamper-Resistant</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Pencatatan mutasi status, disposisi instansi, penugasan petugas, dan konfigurasi platform secara kronologis dan transparan.</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <form action="{{ route('admin.audit-log') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktor, entitas, atau IP..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:outline-none focus:border-slate-800 w-full sm:w-64">
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter jenis aksi..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:outline-none w-full sm:w-48">
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Cari Log</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Waktu Kejadian</th>
                        <th class="px-5 py-3">Aktor & Peran</th>
                        <th class="px-5 py-3">Aksi (Action)</th>
                        <th class="px-5 py-3">Objek / Entitas</th>
                        <th class="px-5 py-3">Detail Perubahan</th>
                        <th class="px-5 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-500 tabular-nums">
                                {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i:s') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-bold text-slate-900 block">{{ $log->actor_name ?: 'Sistem Otomatis' }}</span>
                                <span class="text-[10px] uppercase font-mono text-slate-500">{{ $log->actor_role ?: 'system' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600 font-medium">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="px-5 py-3 max-w-xs">
                                @if($log->new_values)
                                    <pre class="text-[10px] font-mono text-slate-600 bg-slate-50 p-1.5 rounded border border-slate-200 overflow-x-auto max-h-16">{{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-500">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat log audit yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
