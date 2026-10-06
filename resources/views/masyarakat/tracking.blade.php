@extends('layouts.app')

@section('title', 'Lacak Status Pengaduan Publik — SIGALING')

@section('content')
    <!-- ===== EDITORIAL SEARCH HEADER ===== -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
        <div class="space-y-3 text-center max-w-xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-[11px] font-medium text-slate-700">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Pelacakan Publik Mandiri &bull; Civic Tracking Engine</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-semibold text-[#1d1d1f] tracking-tight">
                Lacak Status Penanganan
            </h1>
            <p class="text-xs sm:text-sm text-[#6e6e73] leading-relaxed">
                Pantau progres verifikasi, disposisi cerdas lintas instansi, hingga foto hasil penanganan lapangan secara real-time.
            </p>

            <!-- Search Form -->
            <form action="{{ route('lapor.tracking') }}" method="GET" class="pt-3 flex items-center border border-[#e5e5ea] rounded-full bg-white px-4 py-2 focus-within:border-[#1d1d1f] transition-all shadow-xs">
                <i data-lucide="search" class="w-4 h-4 text-[#86868b] mr-3 flex-shrink-0"></i>
                <input type="text" name="kode" value="{{ $kode }}" placeholder="Masukkan kode registrasi tiket (misal: SIG-2026-... atau LPR-...)" required class="w-full text-xs text-[#1d1d1f] placeholder-[#86868b] bg-transparent focus:outline-none">
                <button type="submit" class="text-xs font-semibold text-white bg-slate-900 hover:bg-black px-5 py-1.5 rounded-full transition-colors flex-shrink-0 shadow-xs cursor-pointer">
                    Lacak
                </button>
            </form>
        </div>
    </section>

    <!-- ===== CASE AUDIT RESULTS ===== -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        @if($errorMessage)
            <div class="p-8 rounded-2xl bg-white border border-[#e5e5ea] text-center space-y-3 shadow-xs">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <h2 class="text-sm font-semibold text-[#1d1d1f]">Kode Laporan Tidak Ditemukan</h2>
                <p class="text-xs text-[#6e6e73] max-w-md mx-auto">{{ $errorMessage }}</p>
                <p class="text-[11px] text-[#86868b] pt-1">Pastikan kode yang dimasukkan sesuai dengan bukti pengajuan Anda.</p>
            </div>
        @elseif($laporan)
            <div class="hairline-t pt-8 space-y-10">
                
                <!-- Header Dossier -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-sm font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg">
                                {{ $laporan->kode_laporan }}
                            </span>

                            @php
                                $badgeClass = match($laporan->status) {
                                    'Menunggu Verifikasi' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'Terverifikasi' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'Dalam Penanganan' => 'bg-purple-50 text-purple-800 border-purple-200',
                                    'Selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    default => 'bg-rose-50 text-rose-800 border-rose-200',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                                {{ $laporan->status }}
                            </span>

                            <!-- Protection level badge -->
                            @if($laporan->protection_level === 'anonymous')
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1">
                                    <i data-lucide="user-x" class="w-3 h-3"></i>
                                    <span>Pelapor Anonim</span>
                                </span>
                            @elseif($laporan->protection_level === 'confidential')
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                                    <i data-lucide="lock" class="w-3 h-3"></i>
                                    <span>Identitas Rahasia</span>
                                </span>
                            @else
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-200 flex items-center gap-1">
                                    <i data-lucide="globe" class="w-3 h-3"></i>
                                    <span>Publik</span>
                                </span>
                            @endif
                        </div>

                        <!-- SLA status indicator -->
                        <div class="flex items-center gap-2">
                            @if($laporan->effective_sla_status === 'resolved')
                                <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    <span>SLA Terpenuhi</span>
                                </span>
                            @elseif($laporan->effective_sla_status === 'overdue')
                                <span class="text-[11px] font-medium text-rose-700 bg-rose-50 px-2 py-1 rounded-lg border border-rose-200 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span>Melewati Target SLA</span>
                                </span>
                            @else
                                <span class="text-[11px] font-medium text-slate-600 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 flex items-center gap-1">
                                    <i data-lucide="timer" class="w-3.5 h-3.5"></i>
                                    <span>SLA: Dalam Target Waktu</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#1d1d1f] tracking-tight">
                            {{ $laporan->judul }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-[#6e6e73] mt-1.5">
                            <span class="font-medium text-slate-900">{{ $laporan->kategori->nama_kategori ?? '-' }}</span>
                            @if($laporan->subcategory)
                                <span>&bull;</span>
                                <span class="font-medium text-slate-900">{{ $laporan->subcategory->name }}</span>
                            @endif
                            <span>&bull;</span>
                            <span>{{ $laporan->kecamatan ?? 'Kota Padang' }}</span>
                            <span>&bull;</span>
                            <span>Diajukan: {{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Vertical Progress Timeline Progression -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-6">
                    <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block">Alur & Linimasa Penanganan</span>

                    <div class="pl-2 border-l-2 border-slate-200 space-y-7 text-xs relative ml-2">
                        <!-- Step 1: Laporan Diterima -->
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-slate-900 border-2 border-white ring-2 ring-slate-200"></span>
                            <div class="font-semibold text-slate-900">Laporan Diterima Sistem SIGALING</div>
                            <div class="text-[#86868b] text-[11px] tabular-nums mt-0.5">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d M Y, H:i') }} WIB</div>
                            <p class="text-slate-600 text-[11px] mt-1">Laporan berhasil diarsipkan dan Smart Routing Engine mengidentifikasi instansi yang berwenang.</p>
                        </div>

                        <!-- Step 2: Disposisi ke Instansi Berwenang -->
                        <div class="relative pl-6">
                            @if($laporan->status !== 'Menunggu Verifikasi')
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-600 border-2 border-white ring-2 ring-blue-200"></span>
                                <div class="font-semibold text-slate-900">Diverifikasi & Disposisi Instansi</div>
                                <div class="text-slate-600 text-[11px] mt-1">
                                    @if($laporan->assignments->isNotEmpty())
                                        @foreach($laporan->assignments as $asg)
                                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mt-1.5 flex items-center justify-between">
                                                <div>
                                                    <span class="font-semibold text-slate-900 block">{{ $asg->organization->name }}</span>
                                                    <span class="text-slate-500 text-[10px]">{{ $asg->department ? $asg->department->name : 'Unit Pelaksana Teknis' }}</span>
                                                </div>
                                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                                    {{ strtoupper($asg->status) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @elseif($laporan->laporanOpd)
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mt-1.5">
                                            Diteruskan ke: <strong class="text-slate-900">{{ $laporan->laporanOpd->opd->nama_opd ?? 'Instansi Berwenang' }}</strong>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-[#86868b] font-medium">Menunggu Verifikasi Supervisor Instansi</div>
                                <p class="text-[#86868b] text-[11px] mt-0.5">Sedang dalam antrean validasi data awal oleh supervisor instansi.</p>
                            @endif
                        </div>

                        <!-- Step 3: Penugasan Petugas Lapangan -->
                        <div class="relative pl-6">
                            @if(in_array($laporan->status, ['Dalam Penanganan', 'Selesai']))
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-purple-600 border-2 border-white ring-2 ring-purple-200"></span>
                                <div class="font-semibold text-slate-900">Penanganan Lapangan Aktif</div>
                                <p class="text-slate-600 text-[11px] mt-0.5">Petugas teknis telah ditugaskan dan sedang melakukan penanganan fisik di titik lokasi.</p>
                            @else
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-[#86868b] font-medium">Penugasan Petugas Lapangan</div>
                                <p class="text-[#86868b] text-[11px] mt-0.5">Petugas lapangan akan dikerahkan setelah verifikasi disetujui.</p>
                            @endif
                        </div>

                        <!-- Step 4: Selesai Tuntas -->
                        <div class="relative pl-6">
                            @if($laporan->status === 'Selesai')
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-emerald-600 border-2 border-white ring-2 ring-emerald-200"></span>
                                <div class="font-semibold text-emerald-800">Penyelesaian Tuntas</div>
                                <p class="text-emerald-700 text-[11px] mt-0.5">Penanganan telah diselesaikan dan diverifikasi oleh pihak pengawas.</p>
                            @else
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-[#86868b] font-medium">Verifikasi Penyelesaian</div>
                                <p class="text-[#86868b] text-[11px] mt-0.5">Tahap penutupan pengaduan setelah pengerjaan lapangan rampung.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Facts & Description -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-6">
                    <div>
                        <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block mb-2">Deskripsi Fakta Lapangan</span>
                        <p class="text-xs sm:text-sm text-[#1d1d1f] leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                            {{ $laporan->deskripsi }}
                        </p>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Nama Pelapor</span>
                            <span class="font-medium text-slate-900">{{ $laporan->masked_reporter_name }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Kontak Pelapor</span>
                            <span class="font-medium text-slate-900">{{ $laporan->masked_contact }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Lokasi / Alamat</span>
                            <span class="font-medium text-slate-900 text-right">{{ $laporan->alamat }} (Kec. {{ $laporan->kecamatan ?? 'Padang' }})</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Prioritas Penanganan</span>
                            <span class="font-medium text-slate-900">{{ $laporan->prioritas ?? 'Sedang' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Evidence Photos -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
                    <span class="text-[11px] font-semibold text-[#86868b] uppercase tracking-wider block">Dokumentasi Bukti Lapangan</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Foto Awal Pelapor -->
                        <div class="space-y-2">
                            <span class="text-xs text-slate-500 block">Kondisi Awal (Pelapor)</span>
                            @if($laporan->fotoBuktiLaporan->isNotEmpty())
                                @foreach($laporan->fotoBuktiLaporan as $foto)
                                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                        <img src="{{ asset('uploads/laporan/' . $foto->nama_file) }}" alt="Foto Bukti" class="w-full h-52 object-cover" onerror="this.src='https://images.unsplash.com/photo-1611288875785-5b432a514d87?w=600&auto=format&fit=crop&q=60'">
                                    </div>
                                @endforeach
                            @else
                                <div class="h-40 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-xs text-slate-400">
                                    Tidak ada lampiran foto awal
                                </div>
                            @endif
                        </div>

                        <!-- Foto Hasil Penanganan -->
                        <div class="space-y-2">
                            <span class="text-xs text-emerald-800 font-medium block">Hasil Penanganan (Petugas Lapangan)</span>
                            @if($laporan->fotoBuktiPenanganan->isNotEmpty())
                                @foreach($laporan->fotoBuktiPenanganan as $foto)
                                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                        <img src="{{ $foto->url }}" alt="Foto Penanganan" class="w-full h-52 object-cover">
                                    </div>
                                @endforeach
                            @elseif($laporan->penanganans->whereNotNull('foto')->isNotEmpty())
                                @foreach($laporan->penanganans->whereNotNull('foto') as $p)
                                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                        <img src="{{ $p->foto_url }}" alt="Foto Penanganan" class="w-full h-52 object-cover">
                                    </div>
                                @endforeach
                            @else
                                <div class="h-40 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-xs text-slate-400">
                                    Dokumentasi penanganan belum diunggah
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
@endsection
