@extends('layouts.app')

@section('title', 'Aduan ' . $laporan->kode_laporan . ' — SIGALING PADANG')

@section('content')
    <!-- ===== CASE DOSSIER HEADER ===== -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
        <div class="space-y-3">
            <div>
                <a href="{{ route('riwayat') }}" class="inline-flex items-center gap-1.5 text-xs text-[#86868b] hover:text-[#1d1d1f] transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Riwayat</span>
                </a>
            </div>

            <div class="flex flex-wrap items-baseline gap-3 pt-1">
                <span class="font-mono text-sm font-semibold text-[#86868b] tabular-nums">
                    {{ $laporan->kode_laporan }}
                </span>

                @php
                    $badgeClass = match($laporan->status) {
                        'Menunggu Verifikasi' => 'badge-menunggu',
                        'Terverifikasi' => 'badge-terverifikasi',
                        'Dalam Penanganan' => 'badge-proses',
                        'Selesai' => 'badge-selesai',
                        default => 'badge-ditolak',
                    };
                @endphp
                <span class="badge-status {{ $badgeClass }} text-xs">
                    {{ $laporan->status }}
                </span>

                <span class="text-xs text-[#86868b] tabular-nums">
                    {{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y, H:i') }} WIB
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-semibold text-[#1d1d1f] tracking-tight leading-tight">
                {{ $laporan->judul }}
            </h1>
        </div>
    </section>

    <!-- ===== CASE CONTENT ===== -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-12">
        
        <!-- Description & Facts -->
        <div class="hairline-t pt-8 space-y-6">
            <div class="space-y-2">
                <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block">Uraian Pengaduan</span>
                <p class="text-sm text-[#1d1d1f] leading-relaxed">
                    {{ $laporan->deskripsi }}
                </p>
            </div>

            <div class="divide-y divide-[#e5e5ea] hairline-y text-xs">
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-[#86868b]">Kategori Masalah</span>
                    <span class="font-medium text-[#1d1d1f]">
                        {{ $laporan->kategori->nama_kategori ?? '-' }}
                        @if($laporan->subcategory)
                            &bull; <span class="text-slate-600 font-normal">{{ $laporan->subcategory->name }}</span>
                        @endif
                    </span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-[#86868b]">Pelapor</span>
                    <span class="font-medium text-[#1d1d1f]">{{ $laporan->masked_reporter_name }}</span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-[#86868b]">Wilayah</span>
                    <span class="text-[#1d1d1f]">{{ $laporan->kecamatan ?? 'Kota Padang' }} &bull; {{ $laporan->kelurahan ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex items-start justify-between gap-4">
                    <span class="text-[#86868b] flex-shrink-0">Alamat / Patokan</span>
                    <span class="text-[#1d1d1f] text-right">{{ $laporan->alamat }}</span>
                </div>
            </div>
        </div>

        <!-- Evidence Photos -->
        <div class="hairline-t pt-8 space-y-4">
            <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block">Dokumentasi Bukti Lapangan</span>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Foto Awal -->
                <div class="space-y-2">
                    <span class="text-xs text-[#86868b] block">Foto Kondisi Awal (Pelapor)</span>
                    @if($laporan->fotoBuktiLaporan->isNotEmpty())
                        @foreach($laporan->fotoBuktiLaporan as $foto)
                            <div class="rounded-xl overflow-hidden border border-[#e5e5ea] bg-white">
                                <img src="{{ asset('uploads/laporan/' . $foto->nama_file) }}" alt="Foto Pelapor" class="w-full h-52 object-cover" onerror="this.src='https://images.unsplash.com/photo-1611288875785-5b432a514d87?w=600&auto=format&fit=crop&q=60'">
                            </div>
                        @endforeach
                    @else
                        <div class="h-40 rounded-xl border border-[#e5e5ea] bg-white flex items-center justify-center text-xs text-[#86868b]">
                            Tidak ada foto awal
                        </div>
                    @endif
                </div>

                <!-- Foto Penanganan -->
                <div class="space-y-2">
                    <span class="text-xs text-[#065f46] font-medium block">Foto Hasil Penanganan (Petugas)</span>
                    @if($laporan->fotoBuktiPenanganan->isNotEmpty())
                        @foreach($laporan->fotoBuktiPenanganan as $foto)
                            <div class="rounded-xl overflow-hidden border border-[#e5e5ea] bg-white">
                                <img src="{{ $foto->url }}" alt="Foto Penanganan" class="w-full h-52 object-cover">
                            </div>
                        @endforeach
                    @elseif($laporan->penanganans->whereNotNull('foto')->isNotEmpty())
                        @foreach($laporan->penanganans->whereNotNull('foto') as $p)
                            <div class="rounded-xl overflow-hidden border border-[#e5e5ea] bg-white">
                                <img src="{{ $p->foto_url }}" alt="Foto Penanganan" class="w-full h-52 object-cover">
                            </div>
                        @endforeach
                    @else
                        <div class="h-40 rounded-xl border border-dashed border-[#e5e5ea] bg-white flex items-center justify-center text-xs text-[#86868b]">
                            Belum ada dokumentasi tuntas
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Penanganan Log jika ada -->
        @if($laporan->penanganans->isNotEmpty())
            <div class="hairline-t pt-8 space-y-4">
                <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block">Catatan Petugas Lapangan</span>
                <div class="divide-y divide-[#e5e5ea] hairline-y text-xs">
                    @foreach($laporan->penanganans as $penanganan)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div>
                                <span class="font-medium text-[#1d1d1f] block">{{ $penanganan->petugas->nama ?? 'Petugas Lapangan' }}</span>
                                <p class="text-[#6e6e73] mt-0.5">{{ $penanganan->catatan_petugas ?? 'Telah ditindaklanjuti.' }}</p>
                            </div>
                            <span class="text-[11px] text-[#86868b] tabular-nums whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($penanganan->tanggal_penanganan)->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
