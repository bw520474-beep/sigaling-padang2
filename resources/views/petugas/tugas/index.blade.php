@extends('layouts.petugas')

@section('title', 'Daftar Tugas Lapangan - Petugas SIGALING PADANG')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Tugas Lapangan Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status pengerjaan dan perbarui progres pembersihan/penanganan.</p>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-xs mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('petugas.tugas.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ !request('status') ? 'bg-purple-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Semua Tugas Saya
        </a>
        <a href="{{ route('petugas.tugas.index', ['status' => 'Menunggu']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') == 'Menunggu' ? 'bg-amber-500 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Menunggu
        </a>
        <a href="{{ route('petugas.tugas.index', ['status' => 'Dalam Proses']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') == 'Dalam Proses' ? 'bg-purple-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Dalam Proses
        </a>
        <a href="{{ route('petugas.tugas.index', ['status' => 'Selesai']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') == 'Selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Selesai
        </a>
    </div>

    <!-- Table of Tugas Saya -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden mb-10">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Tanggal</th>
                        <th class="px-5 py-3.5">Judul Aduan</th>
                        <th class="px-5 py-3.5">Lokasi</th>
                        <th class="px-5 py-3.5">Status Pengerjaan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tugasSaya as $t)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-slate-800 block">{{ $t->laporan->kode_laporan ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($t->tanggal_penanganan)->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="font-bold text-slate-900 block truncate">{{ $t->laporan->judul ?? '-' }}</span>
                                <span class="text-[10px] text-emerald-700 font-semibold">{{ $t->laporan->kategori->nama_kategori ?? 'Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <span class="text-slate-700 block truncate">{{ $t->laporan->alamat ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t->laporan->kecamatan ?? 'Kota Padang' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $bClass = match($t->status) {
                                        'Menunggu' => 'bg-amber-100 text-amber-800',
                                        'Dalam Proses' => 'bg-purple-100 text-purple-800',
                                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $bClass }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('petugas.tugas.detail', $t->id) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition-colors">
                                    <span>Update Progress</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs">
                                Belum ada riwayat tugas pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $tugasSaya->links() }}
        </div>
    </div>
@endsection
