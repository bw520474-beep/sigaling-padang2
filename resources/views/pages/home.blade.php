@extends('layouts.app')

@section('title', 'SIGALING PADANG — Sistem Pengaduan Lingkungan Hidup Kota Padang')

@section('content')
    @php
        $getKategoriIcon = function($nama) {
            $namaLower = strtolower($nama);
            if (str_contains($namaLower, 'sampah') && (str_contains($namaLower, 'bakar') || str_contains($namaLower, 'api'))) return 'flame';
            if (str_contains($namaLower, 'sampah') || str_contains($namaLower, 'tps')) return 'trash-2';
            if (str_contains($namaLower, 'drainase') || str_contains($namaLower, 'saluran') || str_contains($namaLower, 'parit')) return 'waves';
            if (str_contains($namaLower, 'pohon') || str_contains($namaLower, 'ranting') || str_contains($namaLower, 'tumbang')) return 'trees';
            if (str_contains($namaLower, 'udara') || str_contains($namaLower, 'asap') || str_contains($namaLower, 'polusi')) return 'wind';
            if (str_contains($namaLower, 'sungai') || str_contains($namaLower, 'muara')) return 'droplets';
            if (str_contains($namaLower, 'air')) return 'droplets';
            if (str_contains($namaLower, 'limbah') || str_contains($namaLower, 'b3') || str_contains($namaLower, 'medis')) return 'biohazard';
            if (str_contains($namaLower, 'bising') || str_contains($namaLower, 'suara')) return 'volume-2';
            if (str_contains($namaLower, 'pesisir') || str_contains($namaLower, 'pantai')) return 'palmtree';
            if (str_contains($namaLower, 'taman') || str_contains($namaLower, 'rth')) return 'flower-2';
            return 'help-circle';
        };

        $getKategoriColors = function($nama) {
            $namaLower = strtolower($nama);
            if (str_contains($namaLower, 'sampah') && str_contains($namaLower, 'bakar')) return ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'];
            if (str_contains($namaLower, 'sampah')) return ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'];
            if (str_contains($namaLower, 'drainase')) return ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'];
            if (str_contains($namaLower, 'pohon')) return ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200'];
            if (str_contains($namaLower, 'udara')) return ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200'];
            if (str_contains($namaLower, 'air') || str_contains($namaLower, 'sungai')) return ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200'];
            if (str_contains($namaLower, 'limbah')) return ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'];
            if (str_contains($namaLower, 'bising')) return ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200'];
            return ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'];
        };
    @endphp

    <!-- ===== 1. HERO SECTION (CLEAN CIVIC IDENTITY — MICROSOFT / GOOGLE / APPLE / NIKE AESTHETIC) ===== -->
    <section class="bg-white text-slate-900 py-12 sm:py-16 lg:py-20 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Left Text Column -->
                <div class="lg:col-span-7 space-y-5">
                    
                    <h1 class="text-3xl sm:text-5xl lg:text-[48px] font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                        Wujudkan Padang <span class="text-slate-900 underline decoration-slate-300 underline-offset-8">Bersih, Asri & Nyaman</span> Bersama.
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl font-normal">
                        Salurkan laporan tumpukan sampah liar, saluran drainase tersumbat, pencemaran air, hingga pohon rawan tumbang di sekitar Anda. Setiap aduan divalidasi dan diteruskan langsung ke tim reaksi cepat lapangan.
                    </p>

                    <!-- Direct Action Buttons (Vibrant Civic Green Style) -->
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="{{ route('lapor') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs hover:shadow transition-all">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Buat Pengaduan Sekarang</span>
                        </a>

                        <a href="{{ route('peta') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-semibold text-xs sm:text-sm border border-slate-200 shadow-2xs transition-all">
                            <i data-lucide="map" class="w-4 h-4 text-emerald-600"></i>
                            <span>Peta Sebaran Laporan</span>
                        </a>
                    </div>

                    <!-- Fast Ticket Tracking Widget -->
                    <div class="pt-2 max-w-xl">
                        <form action="{{ route('lapor.tracking') }}" method="GET" class="relative flex items-center">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none"></i>
                            <input type="text" name="kode" placeholder="Punya kode laporan? Contoh: LPR-2026..." required class="w-full pl-10 pr-24 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:bg-white focus:border-emerald-500 transition-all">
                            <button type="submit" class="absolute right-1 px-3.5 py-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-2xs">
                                Lacak
                            </button>
                        </form>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Lacak perkembangan penanganan tanpa harus login akun</span>
                        </p>
                    </div>
                </div>

                <!-- Right Feature Panel: Standar Layanan Publik (Microsoft Fluent / Google Health clean card) -->
                <div class="lg:col-span-5">
                    <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Standar Pelayanan Publik</span>
                                <h2 class="text-sm sm:text-base font-bold text-slate-900">Komitmen DLH Kota Padang</h2>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-200/80 text-slate-800 border border-slate-300">
                                24/7 Aktif
                            </span>
                        </div>

                        <div class="space-y-3 pt-4">
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/70 shadow-2xs">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Verifikasi Cepat &lt; 24 Jam</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Admin memvalidasi foto bukti dan kejelasan titik koordinat lokasi aduan.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/70 shadow-2xs">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="truck" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Disposisi Armada Lapangan</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Dump truck & petugas kebersihan diterjunkan langsung ke titik koordinat.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/70 shadow-2xs">
                                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="camera" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Dokumentasi Bukti Tuntas</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Petugas wajib mengunggah foto lokasi setelah dibersihkan dan diselesaikan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3.5 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                            <span>Mencakup 11 Kecamatan di Padang</span>
                            <a href="{{ route('alur') }}" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1">
                                Pelajari SOP <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 2. QUICK STATS STRIP (KOLOM PETAK PEMISAH & LEBIH KOMPAK) ===== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-5 sm:-mt-6 relative z-20">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Petak 1: Total Pengaduan -->
            <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 shadow-2xs hover:border-slate-300 transition-colors flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Pengaduan</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 font-tabular leading-tight block mt-0.5">{{ number_format($totalLaporan) }}</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="folder-open" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                </div>
            </div>

            <!-- Petak 2: Terverifikasi -->
            <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 shadow-2xs hover:border-blue-300 transition-colors flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Terverifikasi</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 font-tabular leading-tight block mt-0.5">{{ number_format($laporanTerverifikasi) }}</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                </div>
            </div>

            <!-- Petak 3: Penanganan -->
            <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 shadow-2xs hover:border-purple-300 transition-colors flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-purple-600 uppercase tracking-wider block">Penanganan</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 font-tabular leading-tight block mt-0.5">{{ number_format($laporanProses) }}</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="loader" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                </div>
            </div>

            <!-- Petak 4: Selesai Tuntas -->
            <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 shadow-2xs hover:border-slate-300 transition-colors flex items-center justify-between">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Selesai Tuntas</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 font-tabular leading-tight block mt-0.5">{{ number_format($laporanSelesai) }}</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle-2" class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-blue-600"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 3. KATEGORI PERMASALAHAN LINGKUNGAN ===== -->
    <section class="py-14 sm:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-slate-500 tracking-wider uppercase block">Jenis Pengaduan</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Kategori Masalah Lingkungan</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-xl">Pilih kategori yang sesuai untuk mempercepat proses identifikasi dan disposisi armada ke lapangan.</p>
                </div>
                <a href="{{ route('lapor') }}" class="mt-3 md:mt-0 inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700">
                    <span>Semua Formulir Pengaduan</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Grid 10 Kategori Presisi -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                @forelse($kategoris as $kategori)
                    @php
                        $iconName = $getKategoriIcon($kategori->nama_kategori);
                        $colors = $getKategoriColors($kategori->nama_kategori);
                    @endphp
                    <a href="{{ route('lapor', ['kategori' => $kategori->id]) }}" class="group p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-slate-400 hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl {{ $colors['bg'] }} {{ $colors['text'] }} flex items-center justify-center group-hover:scale-105 transition-transform mb-3 border {{ $colors['border'] }}">
                                <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight">
                                {{ $kategori->nama_kategori }}
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $kategori->deskripsi ?? 'Pengaduan isu ' . strtolower($kategori->nama_kategori) }}
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-semibold">
                            <span class="text-slate-700 font-mono">{{ $kategori->laporans_count ?? 0 }} Aduan</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-900 group-hover:translate-x-0.5 transition-transform"></i>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                        Belum ada kategori yang dimuat.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===== 4. LAPORAN TERKINI MASYARAKAT (APPLE NEWSROOM & MICROSOFT FLUENT HUB) ===== -->
    <section class="py-16 bg-slate-50/80 border-y border-slate-200/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-slate-500 tracking-wider uppercase block">Transparansi Penanganan</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Laporan Terkini Masyarakat</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Aktivitas penanganan aduan lingkungan hidup real-time di seluruh wilayah Kota Padang.</p>
                </div>
                <a href="{{ route('riwayat') }}" class="mt-3 md:mt-0 inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700">
                    <span>Lihat Seluruh Riwayat Laporan</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            @if($laporanTerbaru->isNotEmpty())
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($laporanTerbaru as $laporan)
                            <a href="{{ route('lapor.detail', $laporan->kode_laporan) }}" class="group block p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-slate-400 hover:shadow-xs transition-all">
                                <div class="flex items-center justify-between gap-3 sm:gap-4">
                                    <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
                                        <!-- Thumbnail or Icon -->
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-200/80">
                                            @if($laporan->fotoBuktiLaporan->isNotEmpty())
                                                <img src="{{ asset('uploads/laporan/' . $laporan->fotoBuktiLaporan->first()->nama_file) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="file-text" class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors"></i>
                                            @endif
                                        </div>

                                        <!-- Text Details -->
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-[10.5px] font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                                    {{ $laporan->kode_laporan }}
                                                </span>
                                                <span class="text-[10px] text-slate-400">•</span>
                                                <span class="text-[11px] text-slate-500 font-medium truncate flex items-center gap-1">
                                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 flex-shrink-0"></i>
                                                    <span class="truncate">{{ $laporan->kecamatan ?? 'Kota Padang' }}</span>
                                                </span>
                                            </div>
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                                {{ $laporan->judul }}
                                            </h4>
                                            <p class="text-[11.5px] text-slate-500 truncate mt-0.5">
                                                {{ $laporan->deskripsi }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Status Pill & Date -->
                                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                        @php
                                            $badgeClass = match($laporan->status) {
                                                'Menunggu Verifikasi' => 'badge-menunggu',
                                                'Terverifikasi' => 'badge-terverifikasi',
                                                'Dalam Penanganan' => 'badge-proses',
                                                'Selesai' => 'badge-selesai',
                                                default => 'badge-ditolak',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold {{ $badgeClass }}">
                                            {{ $laporan->status }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">
                                            {{ \Carbon\Carbon::parse($laporan->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('riwayat') }}" class="w-full py-3.5 px-5 rounded-2xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-all flex items-center justify-between group shadow-2xs">
                            <span>Lihat Seluruh Arsip & Rekapitulasi Pengaduan Lingkungan</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-1 group-hover:text-slate-900 transition-all"></i>
                        </a>
                    </div>
                </div>
            @else
                <div class="py-12 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                    <h4 class="text-sm font-bold text-slate-700">Belum Ada Pengaduan Masuk</h4>
                    <p class="text-xs text-slate-400 mt-1">Jadilah warga pertama yang berkontribusi menjaga kebersihan lingkungan.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- ===== 5. ALUR PENANGANAN SOP (APPLE & MICROSOFT CONNECTED STEPPER) ===== -->
    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-semibold text-slate-500 tracking-wider uppercase block">Standar Operasional Prosedur</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Alur Penanganan Pengaduan</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Proses kerja terpadu DLH Kota Padang dari pelaporan warga hingga verifikasi bukti tuntas.</p>
            </div>

            <!-- Connected Stepper Container (Microsoft / Apple Workflow Architecture) -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-10 shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                    <!-- Progress Line Across Desktop -->
                    <div class="hidden md:block absolute top-6 left-12 right-12 h-0.5 bg-slate-200 -z-0"></div>

                    <!-- Step 01 -->
                    <div class="relative z-10 flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            01
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900">Pelaporan Warga</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Pengisian formulir online disertai foto bukti dan titik koordinat akurat di peta GPS.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200/60">
                            <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                            <span>SLA: Real-time</span>
                        </span>
                    </div>

                    <!-- Step 02 -->
                    <div class="relative z-10 flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            02
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900">Validasi & Triage</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Admin DLH memverifikasi keabsahan aduan dan menentukan urgensi penanganan.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200/60">
                            <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                            <span>SLA: &lt; 24 Jam</span>
                        </span>
                    </div>

                    <!-- Step 03 -->
                    <div class="relative z-10 flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            03
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900">Disposisi & Aksi</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Penugasan armada dump truck, satgas kebersihan, atau instansi OPD teknis ke lokasi.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200/60">
                            <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                            <span>SLA: 1 - 3 Hari</span>
                        </span>
                    </div>

                    <!-- Step 04 -->
                    <div class="relative z-10 flex flex-col items-center md:items-start text-center md:text-left space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            <i data-lucide="check" class="w-5 h-5 stroke-[2.5]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900">Bukti Selesai</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Petugas mengunggah foto tuntas lapangan. Pelapor menerima konfirmasi via kode tiket.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-mono text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200/60">
                            <i data-lucide="shield-check" class="w-3 h-3"></i>
                            <span>Status: Selesai</span>
                        </span>
                    </div>
                </div>

                <!-- SLA Commitment Banner -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
                    <span class="flex items-center gap-2">
                        <i data-lucide="badge-check" class="w-4 h-4 text-blue-600"></i>
                        <span>Diproses langsung di bawah pengawasan Dinas Lingkungan Hidup Kota Padang.</span>
                    </span>
                    <a href="{{ route('alur') }}" class="font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        <span>Pelajari Dokumen SOP Selengkapnya</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 6. SUARA WARGA & DAMPAK NYATA (APPLE CUSTOMER STORIES / TESTIMONIAL SHOWCASE) ===== -->
    <section class="py-16 bg-slate-50/80 border-y border-slate-200/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-semibold text-slate-500 tracking-wider uppercase block">Pengalaman Masyarakat</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Tanggapan & Dampak Nyata</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Pengalaman warga Kota Padang yang telah merasakan tindak lanjut cepat pengaduan lingkungan.</p>
            </div>

            <!-- Asymmetric Testimonial Showcase -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <!-- Featured Citizen Story (Left 5 Cols) -->
                @if(!empty($pesanMasyarakat))
                    @php $featuredPesan = $pesanMasyarakat[0]; @endphp
                    <div class="lg:col-span-5 bg-slate-900 text-white rounded-3xl p-8 sm:p-10 flex flex-col justify-between shadow-md relative overflow-hidden">
                        <div class="space-y-6 relative z-10">
                            <span class="text-5xl font-serif text-slate-700 leading-none select-none block">&ldquo;</span>
                            <p class="text-base sm:text-lg text-slate-200 leading-relaxed font-medium italic -mt-4">
                                {{ $featuredPesan['pesan'] }}
                            </p>
                        </div>

                        <div class="pt-8 border-t border-slate-800 relative z-10 space-y-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-xs border border-slate-700">
                                    {{ strtoupper(substr($featuredPesan['nama'], 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $featuredPesan['nama'] }}</h4>
                                    <span class="text-xs text-slate-400 block">{{ $featuredPesan['lokasi'] }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-300 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700">
                                <i data-lucide="check-check" class="w-3.5 h-3.5 text-blue-400"></i>
                                <span>{{ $featuredPesan['kategori'] }} &bull; Selesai Ditangani</span>
                            </span>
                        </div>
                    </div>

                    <!-- Secondary Feedback Cards (Right 7 Cols) -->
                    <div class="lg:col-span-7 flex flex-col justify-between space-y-3.5">
                        @foreach(array_slice($pesanMasyarakat, 1, 3) as $pesan)
                            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:border-slate-400 transition-all flex flex-col justify-between">
                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed italic">
                                    &ldquo;{{ $pesan['pesan'] }}&rdquo;
                                </p>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                            {{ strtoupper(substr($pesan['nama'], 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $pesan['nama'] }}</span>
                                            <span class="text-[10px] text-slate-500">{{ $pesan['lokasi'] }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-mono text-slate-400">
                                        {{ $pesan['tanggal'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- ===== 7. PERTANYAAN UMUM (FAQ) ACCORDION ===== -->
    <section id="faq" class="py-16 sm:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 tracking-wider uppercase border border-slate-200">
                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-slate-500"></i>
                    Pusat Bantuan & Tanya Jawab
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-2.5">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Jawaban praktis seputar tata cara pengaduan, privasi pelapor, waktu penanganan (SLA), dan pelacakan tiket.
                </p>
            </div>

            <!-- Accordion List (Alpine.js) -->
            <div class="space-y-3" x-data="{ active: 1 }">
                <!-- Item 1 -->
                <div class="rounded-xl border transition-all duration-200 overflow-hidden"
                     :class="active === 1 ? 'bg-white border-slate-400 ring-1 ring-slate-400/20 shadow-xs' : 'bg-white border-slate-200/90 hover:border-slate-300'">
                    <button type="button" @click="active = (active === 1 ? null : 1)" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-800 text-[11px] font-black flex items-center justify-center flex-shrink-0">01</span>
                            Bagaimana cara membuat laporan pengaduan lingkungan?
                        </span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors flex-shrink-0"
                             :class="active === 1 ? 'bg-slate-100 text-slate-800' : 'bg-slate-100 text-slate-500'">
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="active === 1 ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div x-show="active === 1" x-collapse>
                        <div class="px-5 pb-4 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100">
                            <p>Klik tombol <strong>"Buat Laporan"</strong> pada navigasi atas atau beranda. Isi formulir dengan deskripsi masalah, tandai titik lokasi pada peta interaktif GPS, dan lampirkan minimal 1 foto bukti kondisi lapangan. Setelah dikirim, Anda akan langsung menerima <strong>Kode Laporan unik</strong> untuk pelacakan.</p>
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="rounded-xl border transition-all duration-200 overflow-hidden"
                     :class="active === 2 ? 'bg-white border-slate-400 ring-1 ring-slate-400/20 shadow-xs' : 'bg-white border-slate-200/90 hover:border-slate-300'">
                    <button type="button" @click="active = (active === 2 ? null : 2)" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-800 text-[11px] font-black flex items-center justify-center flex-shrink-0">02</span>
                            Apakah wajib login atau mendaftar akun untuk melapor?
                        </span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors flex-shrink-0"
                             :class="active === 2 ? 'bg-slate-100 text-slate-800' : 'bg-slate-100 text-slate-500'">
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="active === 2 ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div x-show="active === 2" x-collapse>
                        <div class="px-5 pb-4 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100">
                            <p><strong>Tidak wajib.</strong> Anda dapat membuat laporan tanpa mendaftar akun dan tetap dapat memantau perkembangannya melalui menu <em>Lacak Status</em>. Namun jika memiliki akun, seluruh riwayat aduan Anda akan tersimpan rapi pada profil Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="rounded-xl border transition-all duration-200 overflow-hidden"
                     :class="active === 3 ? 'bg-white border-slate-400 ring-1 ring-slate-400/20 shadow-xs' : 'bg-white border-slate-200/90 hover:border-slate-300'">
                    <button type="button" @click="active = (active === 3 ? null : 3)" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-800 text-[11px] font-black flex items-center justify-center flex-shrink-0">03</span>
                            Berapa lama estimasi waktu penanganan (SLA)?
                        </span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors flex-shrink-0"
                             :class="active === 3 ? 'bg-slate-100 text-slate-800' : 'bg-slate-100 text-slate-500'">
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="active === 3 ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div x-show="active === 3" x-collapse>
                        <div class="px-5 pb-4 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 space-y-1">
                            <p>&bull; <strong>Verifikasi Administrasi:</strong> Maksimal 1x24 jam kerja.</p>
                            <p>&bull; <strong>Disposisi Tim Lapangan:</strong> Dilakukan pada hari yang sama setelah verifikasi.</p>
                            <p>&bull; <strong>Eksekusi Lapangan:</strong> Umumnya 1 sampai 3 hari kerja tergantung urgensi dan volume masalah.</p>
                        </div>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="rounded-xl border transition-all duration-200 overflow-hidden"
                     :class="active === 4 ? 'bg-white border-slate-400 ring-1 ring-slate-400/20 shadow-xs' : 'bg-white border-slate-200/90 hover:border-slate-300'">
                    <button type="button" @click="active = (active === 4 ? null : 4)" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-800 text-[11px] font-black flex items-center justify-center flex-shrink-0">04</span>
                            Apakah identitas pelapor dijamin aman dan rahasia?
                        </span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors flex-shrink-0"
                             :class="active === 4 ? 'bg-slate-100 text-slate-800' : 'bg-slate-100 text-slate-500'">
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="active === 4 ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div x-show="active === 4" x-collapse>
                        <div class="px-5 pb-4 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100">
                            <p><strong>Ya, sepenuhnya dijamin.</strong> Nomor telepon dan identitas pelapor hanya dapat diakses oleh petugas internal untuk konfirmasi lokasi darurat dan tidak pernah disebarluaskan ke publik.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Box -->
            <div class="mt-10 p-5 rounded-2xl bg-slate-50 border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Masih punya pertanyaan lainnya?</h4>
                        <p class="text-[11px] text-slate-500">Kunjungi halaman FAQ lengkap atau hubungi narahubung resmi kami.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('faq') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold transition-colors">
                        FAQ Lengkap
                    </a>
                    <a href="https://wa.me/62811666112" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>WhatsApp Bantuan</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 8. BOTTOM CTA: PARTISIPASI WARGA (APPLE & NIKE REFINED CARD) ===== -->
    <section class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white border border-slate-200/90 p-6 sm:p-10 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                        <i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-slate-600"></i>
                        <span>Partisipasi Warga Padang</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Temukan Masalah Lingkungan di Sekitar Anda?
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Jangan didiamkan. Ambil foto, tandai lokasinya di peta, dan kirimkan aduan Anda. Tim reaksi cepat DLH Kota Padang siap menindaklanjuti.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                    <a href="{{ route('lapor') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs hover:shadow transition-all flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Buat Laporan Sekarang</span>
                    </a>
                    <a href="{{ route('jadwal-sampah') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-semibold text-xs sm:text-sm border border-slate-200 transition-all flex items-center gap-2 shadow-2xs">
                        <i data-lucide="truck" class="w-4 h-4 text-slate-600"></i>
                        <span>Jadwal Armada</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
