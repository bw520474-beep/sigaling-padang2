@extends('layouts.auditor')

@section('title', 'Audit Trail Log — Pengawas Independen')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Audit Trail & Rekam Jejak Sistem</h1>
        <p class="text-xs sm:text-sm text-slate-500">Pemeriksaan kronologi seluruh mutasi status, alur disposisi, dan aktivitas penanganan pengaduan masyarakat.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <form action="{{ route('auditor.audit-log') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aktor, aksi, atau ID..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:outline-none w-full sm:w-64">
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Cari Log</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Aktor</th>
                        <th class="px-5 py-3">Aksi</th>
                        <th class="px-5 py-3">Entitas</th>
                        <th class="px-5 py-3">Perubahan</th>
                        <th class="px-5 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $l)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-500 tabular-nums">
                                {{ \Carbon\Carbon::parse($l->created_at)->translatedFormat('d M Y, H:i:s') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-bold text-slate-900 block">{{ $l->actor_name ?: 'Sistem' }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $l->actor_role ?: 'system' }}</span>
                            </td>
                            <td class="px-5 py-3 font-mono font-semibold text-slate-800">
                                {{ $l->action }}
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                {{ $l->entity_type }} #{{ $l->entity_id }}
                            </td>
                            <td class="px-5 py-3 max-w-xs">
                                @if($l->new_values)
                                    <pre class="text-[10px] font-mono text-slate-600 bg-slate-50 p-1.5 rounded border border-slate-200 overflow-x-auto max-h-16">{{ json_encode($l->new_values, JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-500">
                                {{ $l->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat aktivitas log.</td>
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
