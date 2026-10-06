@extends('layouts.instansi')

@section('title', 'Daftar Penugasan Kasus — ' . ($org->name ?? 'Instansi'))

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Daftar Penugasan Pengaduan</h1>
            <p class="text-xs sm:text-sm text-slate-500">Seluruh laporan publik yang dialokasikan ke instansi {{ $org->name ?? '' }} oleh Smart Routing Engine.</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form action="{{ route('instansi.laporan.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode tiket, judul, atau alamat..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-slate-50/50 focus:outline-none focus:border-slate-800 w-full sm:w-64">
                
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50/50 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received (Baru Masuk)</option>
                    <option value="verification" {{ request('status') === 'verification' ? 'selected' : '' }}>Verification</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned (Ditugaskan)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                </select>

                <select name="priority" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50/50 focus:outline-none">
                    <option value="">Semua Prioritas</option>
                    <option value="Darurat" {{ request('priority') === 'Darurat' ? 'selected' : '' }}>Darurat</option>
                    <option value="Tinggi" {{ request('priority') === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="Sedang" {{ request('priority') === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Rendah" {{ request('priority') === 'Rendah' ? 'selected' : '' }}>Rendah</option>
                </select>

                <select name="officer_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50/50 focus:outline-none">
                    <option value="">Semua Petugas</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ request('officer_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Kode Registrasi</th>
                        <th class="px-5 py-3">Judul & Isu</th>
                        <th class="px-5 py-3">Prioritas</th>
                        <th class="px-5 py-3">Status Tugas</th>
                        <th class="px-5 py-3">Petugas Penangan</th>
                        <th class="px-5 py-3">SLA Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $asg)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-mono font-bold text-slate-900">
                                {{ $asg->laporan->kode_laporan ?? '-' }}
                            </td>
                            <td class="px-5 py-3 max-w-xs">
                                <span class="font-semibold text-slate-900 block line-clamp-1">{{ $asg->laporan->judul ?? '-' }}</span>
                                <span class="text-[11px] text-slate-500">
                                    {{ $asg->laporan->subcategory->name ?? ($asg->laporan->kategori->nama_kategori ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $asg->priority === 'Darurat' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($asg->priority === 'Tinggi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $asg->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-semibold">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px]
                                    {{ $asg->status === 'resolved' ? 'bg-emerald-50 text-emerald-800' : ($asg->status === 'received' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800') }}">
                                    {{ strtoupper($asg->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                {{ $asg->officer ? $asg->officer->nama : 'Belum Ditugaskan' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($asg->sla_status === 'overdue')
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">Overdue</span>
                                @elseif($asg->sla_status === 'warning')
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">Warning</span>
                                @else
                                    <span class="text-[10px] font-medium text-slate-600">On Track</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('instansi.laporan.show', $asg->id) }}" class="px-3 py-1 rounded-lg bg-slate-900 text-white hover:bg-black font-semibold text-[11px] inline-flex items-center gap-1">
                                    <span>Tindak Lanjut</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada penugasan kasus yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
