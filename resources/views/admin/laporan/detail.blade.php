@extends('layouts.admin')

@section('title', 'Kasus ' . $laporan->kode_laporan . ' — Admin SIGALING')

@section('content')
    <!-- ===== 1. CASE HEADER ===== -->
    <div class="space-y-3 pb-8">
        <div>
            <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-emerald-700 transition-colors font-medium">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Semua Laporan</span>
            </a>
        </div>

        <div class="flex flex-wrap items-baseline gap-4 pt-1">
            <span class="font-mono text-sm font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-md tabular-nums tracking-wide">
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

            <span class="text-xs text-slate-500">
                Prioritas: <strong class="text-slate-900 font-bold">{{ $laporan->prioritas ?? 'Sedang' }}</strong>
            </span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight max-w-4xl">
            {{ $laporan->judul }}
        </h1>
    </div>

    <!-- ===== 2. SPLIT CASE MANAGEMENT DOSSIER ===== -->
    <div class="border-t border-slate-200 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- LEFT COLUMN: INCIDENT FACTS & EVIDENCE (7 Cols) -->
            <div class="lg:col-span-7 space-y-10">
                <!-- Description -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Deskripsi Permasalahan</span>
                    <p class="text-sm text-slate-800 leading-relaxed bg-slate-50/60 p-4 rounded-xl border border-slate-200">
                        {{ $laporan->deskripsi }}
                    </p>
                </div>

                <!-- Facts Matrix (Hairline Rows) -->
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-3">Fakta Kejadian</span>
                    
                    <div class="divide-y divide-slate-200 border-y border-slate-200 text-xs">
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Kategori</span>
                            <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/50 text-right">{{ $laporan->kategori->nama_kategori ?? '-' }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Waktu Masuk</span>
                            <span class="font-mono tabular-nums text-slate-900 font-medium text-right">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Pelapor</span>
                            <span class="font-bold text-slate-900 text-right">{{ $laporan->nama_pelapor }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Kontak Pelapor</span>
                            <span class="font-mono tabular-nums text-slate-900 font-medium text-right">{{ $laporan->kontak_pelapor ?? '-' }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Wilayah</span>
                            <span class="text-slate-900 font-medium text-right">{{ $laporan->kecamatan ?? '-' }} &bull; {{ $laporan->kelurahan ?? '-' }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-4">
                            <span class="text-slate-500 flex-shrink-0">Alamat / Patokan</span>
                            <span class="text-slate-900 font-medium text-right max-w-md">{{ $laporan->alamat }}</span>
                        </div>
                        @if($laporan->latitude && $laporan->longitude)
                            <div class="py-2.5 flex items-center justify-between gap-4">
                                <span class="text-slate-500">Titik Koordinat GPS</span>
                                <span class="font-mono tabular-nums text-emerald-700 font-bold text-right">{{ $laporan->latitude }}, {{ $laporan->longitude }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documentation Photos -->
                <div class="space-y-4">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Dokumentasi Foto Bukti</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Foto Pelapor -->
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-700 block">Bukti Dari Warga</span>
                            @if($laporan->fotoBuktiLaporan->isNotEmpty())
                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-56">
                                    <img src="{{ asset('uploads/laporan/' . $laporan->fotoBuktiLaporan->first()->nama_file) }}" alt="Foto Aduan Warga" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-56 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-xs text-slate-400">
                                    Tidak ada foto bukti terlampir
                                </div>
                            @endif
                        </div>

                        <!-- Foto Penanganan -->
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-slate-700 block">Bukti Penyelesaian Lapangan</span>
                            @php
                                $fotoPenangananUrl = null;
                                if ($laporan->fotoBuktiPenanganan->isNotEmpty()) {
                                    $fotoPenangananUrl = $laporan->fotoBuktiPenanganan->first()->url;
                                } elseif ($laporan->penanganans->whereNotNull('foto')->isNotEmpty()) {
                                    $fotoPenangananUrl = $laporan->penanganans->whereNotNull('foto')->last()->foto_url;
                                }
                            @endphp

                            @if($fotoPenangananUrl)
                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-56">
                                    <img src="{{ $fotoPenangananUrl }}" alt="Foto Tuntas Petugas" class="w-full h-full object-cover">
                                </div>
                                <div class="text-[11px] text-emerald-700 font-medium">
                                    Diunggah oleh: {{ $laporan->penanganans->last()->petugas->nama ?? ($laporan->penanganans->last()->petugas->name ?? 'Petugas Lapangan') }}
                                </div>
                            @else
                                <div class="h-56 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-xs text-slate-400">
                                    Belum ada foto penanganan tuntas
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: CHRONOLOGICAL TIMELINE & ACTIONS CONSOLE (5 Cols) -->
            <div class="lg:col-span-5 space-y-10">
                
                <!-- TIMELINE (EDITORIAL PROGRESS STEM) -->
                <div class="space-y-4">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Alur Waktu Penanganan</span>

                    <div class="pl-2 border-l border-slate-200 space-y-6 text-xs relative">
                        <!-- Step 1: Dibuat -->
                        <div class="relative pl-6">
                            <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-emerald-600 border-2 border-white shadow-xs"></span>
                            <div class="font-bold text-slate-900">Laporan Diterima</div>
                            <div class="text-slate-400 text-[11px] tabular-nums">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d M Y, H:i') }} WIB</div>
                        </div>

                        <!-- Step 2: Diverifikasi / Disposisi -->
                        <div class="relative pl-6">
                            @if($laporan->status !== 'Menunggu Verifikasi')
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-blue-600 border-2 border-white shadow-xs"></span>
                                <div class="font-bold text-slate-900">Terverifikasi & Disposisi</div>
                                @if($laporan->laporanOpd)
                                    <div class="text-blue-700 font-medium text-[11px]">{{ $laporan->laporanOpd->opd->nama_opd ?? 'OPD Pelaksana' }}</div>
                                @endif
                            @else
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-slate-400">Menunggu Verifikasi Petugas</div>
                            @endif
                        </div>

                        <!-- Step 3: Dalam Penanganan -->
                        <div class="relative pl-6">
                            @if(in_array($laporan->status, ['Dalam Penanganan', 'Selesai']))
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-purple-600 border-2 border-white shadow-xs"></span>
                                <div class="font-bold text-slate-900">Aksi Armada Lapangan</div>
                                <div class="text-purple-700 font-medium text-[11px]">Tim teknis berada di titik koordinat</div>
                            @else
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-slate-400">Penugasan Lapangan</div>
                            @endif
                        </div>

                        <!-- Step 4: Selesai -->
                        <div class="relative pl-6">
                            @if($laporan->status === 'Selesai')
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-emerald-600 border-2 border-white shadow-xs"></span>
                                <div class="font-bold text-emerald-800">Tuntas Diselesaikan</div>
                                <div class="text-emerald-600 text-[11px]">Foto bukti tuntas terverifikasi</div>
                            @else
                                <span class="absolute -left-[17px] top-0.5 w-3 h-3 rounded-full bg-white border-2 border-slate-300"></span>
                                <div class="text-slate-400">Penyelesaian Tuntas</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PRIMARY ACTIONS & CASE CONSOLE -->
                <div class="space-y-6 pt-4 border-t border-slate-200">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Konsol Tindakan Administrator</span>

                    <!-- Update Status Form -->
                    <form action="{{ route('admin.laporan.update-status', $laporan->id) }}" method="POST" class="space-y-4 bg-white p-5 rounded-xl border border-slate-200 shadow-2xs">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Pembaruan Status</label>
                            <select name="status" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="Menunggu Verifikasi" {{ $laporan->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="Terverifikasi" {{ $laporan->status == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi (Disetujui)</option>
                                <option value="Dalam Penanganan" {{ $laporan->status == 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan (Proses)</option>
                                <option value="Selesai" {{ $laporan->status == 'Selesai' ? 'selected' : '' }}>Selesai (Tuntas)</option>
                                <option value="Ditolak" {{ $laporan->status == 'Ditolak' ? 'selected' : '' }}>Ditolak (Tidak Valid)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Tingkat Prioritas</label>
                            <select name="prioritas" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="Rendah" {{ $laporan->prioritas == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="Sedang" {{ ($laporan->prioritas == 'Sedang' || empty($laporan->prioritas)) ? 'selected' : '' }}>Sedang</option>
                                <option value="Tinggi" {{ $laporan->prioritas == 'Tinggi' ? 'selected' : '' }}>Tinggi (Mendesak)</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all">
                            Simpan Perubahan Status
                        </button>
                    </form>

                    <!-- Disposisi OPD Form -->
                    <form action="{{ route('admin.laporan.teruskan', $laporan->id) }}" method="POST" class="space-y-4 bg-white p-5 rounded-xl border border-slate-200 shadow-2xs">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Disposisi ke OPD Pelaksana</label>
                            <select name="opd_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="">-- Pilih Instansi OPD --</option>
                                @foreach($opds as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Catatan Instruksi Khusus</label>
                            <textarea name="catatan" rows="2" placeholder="Catatan armada atau lokasi spesifik..." class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-lg border border-emerald-600 text-emerald-800 hover:bg-emerald-600 hover:text-white text-xs font-bold transition-all shadow-2xs">
                            Teruskan Disposisi ke OPD
                        </button>

                        @if($laporan->laporanOpd)
                            <div class="pt-3 border-t border-slate-100 text-xs text-slate-500">
                                <span>Saat ini diteruskan ke:</span>
                                <strong class="text-emerald-800 block font-bold mt-0.5">{{ $laporan->laporanOpd->opd->nama_opd ?? '-' }}</strong>
                            </div>
                        @endif
                    </form>

                    <!-- Delete Case (Quiet Danger Action) -->
                    <div class="pt-2 flex items-center justify-between text-xs text-slate-500">
                        <span>Hapus berkas laporan permanen</span>
                        <form action="{{ route('admin.laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ $laporan->kode_laporan }} secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 font-semibold hover:underline">
                                Hapus Laporan
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
