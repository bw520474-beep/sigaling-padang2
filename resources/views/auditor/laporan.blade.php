@extends('layouts.auditor')

@section('title', 'Monitoring Pengaduan — Pengawas Independen')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Monitoring Pengaduan Publik</h1>
        <p class="text-xs sm:text-sm text-slate-500">Pemantauan transparansi seluruh laporan publik tanpa hak pengubahan berkas (Read-Only).</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form action="{{ route('auditor.laporan') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode tiket atau judul..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:outline-none w-full sm:w-64">
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-slate-50/50 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ request('status') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Terverifikasi" {{ request('status') === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="Dalam Penanganan" {{ request('status') === 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                    <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Kode Laporan</th>
                        <th class="px-5 py-3">Judul Permasalahan</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Wilayah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Privasi Pelapor</th>
                        <th class="px-5 py-3">Tanggal Lapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $lap)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-mono font-bold text-slate-900">{{ $lap->kode_laporan }}</td>
                            <td class="px-5 py-3 max-w-xs">
                                <span class="font-semibold text-slate-900 block line-clamp-1">{{ $lap->judul }}</span>
                                <span class="text-slate-400 text-[11px] line-clamp-1">{{ $lap->alamat }}</span>
                            </td>
                            <td class="px-5 py-3">{{ $lap->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $lap->kecamatan ?? 'Kota Padang' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $lap->status === 'Selesai' ? 'bg-emerald-50 text-emerald-800' : ($lap->status === 'Menunggu Verifikasi' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800') }}">
                                    {{ $lap->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-[11px] font-mono text-slate-600">
                                    {{ $lap->masked_reporter_name }} ({{ strtoupper($lap->protection_level ?? 'public') }})
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px] text-slate-500">
                                {{ \Carbon\Carbon::parse($lap->created_at)->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada data laporan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
