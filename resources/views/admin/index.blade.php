@extends('layouts.admin')

@section('title', 'Dashboard Operasional — Admin SIGALING')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- ===== 1. EDITORIAL GREETING & ACTION HEADER ===== -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/70 px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                        Pusat Kendali DLH Kota Padang
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    Selamat datang, {{ Auth::user()->nama }}.
                </h1>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <span class="text-sm text-slate-600">
                        Total <strong class="font-bold text-slate-900 tabular-nums">{{ number_format($totalLaporan) }}</strong> aduan tercatat di sistem.
                    </span>

                    @if($menungguVerifikasi > 0)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-900 bg-amber-50 border border-amber-200/80 px-2.5 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>{{ number_format($menungguVerifikasi) }} aduan menunggu verifikasi segera</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-900 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-full">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>Semua pengaduan tervalidasi</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 self-start lg:self-center">
                @if($menungguVerifikasi > 0)
                    <a href="{{ route('admin.laporan.index', ['status' => 'Menunggu Verifikasi']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs hover:shadow transition-all">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        <span>Validasi {{ $menungguVerifikasi }} Aduan Masuk</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                @endif

                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 hover:text-slate-900 text-slate-700 text-xs font-semibold transition-all bg-white shadow-2xs">
                    <i data-lucide="inbox" class="w-4 h-4 text-slate-500"></i>
                    <span>Buka Seluruh Pengaduan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ===== 2. EXECUTIVE KPI METRICS (TOP POSITIONED) ===== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Total Masuk -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Aduan</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums block">{{ number_format($totalLaporan) }}</span>
            <span class="text-[11px] text-slate-500 block">Keseluruhan data</span>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-amber-50/50 border border-amber-200/80 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Menunggu Verifikasi</span>
            <span class="text-2xl sm:text-3xl font-black text-amber-950 tabular-nums block">{{ number_format($menungguVerifikasi) }}</span>
            <span class="text-[11px] text-amber-700 font-medium block">Tindakan admin</span>
        </div>

        <!-- Terverifikasi -->
        <div class="bg-blue-50/50 border border-blue-200/80 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Terverifikasi</span>
            <span class="text-2xl sm:text-3xl font-black text-blue-950 tabular-nums block">{{ number_format($terverifikasi) }}</span>
            <span class="text-[11px] text-blue-700 font-medium block">Siap ditugaskan</span>
        </div>

        <!-- Dalam Penanganan -->
        <div class="bg-purple-50/50 border border-purple-200/80 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-purple-800 uppercase tracking-wider block">Penanganan Aktif</span>
            <span class="text-2xl sm:text-3xl font-black text-purple-950 tabular-nums block">{{ number_format($dalamPenanganan) }}</span>
            <span class="text-[11px] text-purple-700 font-medium block">Tim di lokasi</span>
        </div>

        <!-- Selesai Tuntas -->
        <div class="bg-emerald-50/50 border border-emerald-200/80 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Selesai Tuntas</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-950 tabular-nums block">{{ number_format($selesai) }}</span>
            <span class="text-[11px] text-emerald-700 font-medium block">Berita acara valid</span>
        </div>

        <!-- SLA Rerata -->
        <div class="bg-slate-100/70 border border-slate-200/90 p-4 rounded-xl space-y-1 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">Efisiensi SLA</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums block">{{ $slaDisplay }}</span>
            <span class="text-[11px] text-emerald-800 font-semibold block">&le; Standar dinas</span>
        </div>
    </div>

    <!-- ===== 3. OPERATIONAL TWO-COLUMN COCKPIT GRID ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Antrean Verifikasi Awal (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Perlu Tindakan</h2>
                            @if($menungguVerifikasi > 0)
                                <span class="text-[11px] font-extrabold text-amber-800 bg-amber-100/80 px-2 py-0.5 rounded-full">
                                    {{ $menungguVerifikasi }} Antrean
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Laporan warga yang menunggu verifikasi admin sebelum penugasan OPD.</p>
                    </div>

                    @if($menungguVerifikasi > 5)
                        <a href="{{ route('admin.laporan.index', ['status' => 'Menunggu Verifikasi']) }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-900 hover:underline flex items-center gap-1 flex-shrink-0">
                            <span>Lihat {{ $menungguVerifikasi - 5 }} lainnya</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- List Content -->
            <div class="p-5 sm:p-6 flex-grow">
                @if($laporanButuhVerifikasi->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($laporanButuhVerifikasi as $aduan)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                                <div class="flex items-start gap-3.5">
                                    <span class="text-sm font-mono font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/60 w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                        0{{ $loop->iteration }}
                                    </span>
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-mono text-[11px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                                {{ $aduan->kode_laporan }}
                                            </span>
                                            <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded">
                                                {{ $aduan->kategori->nama_kategori ?? 'Umum' }}
                                            </span>
                                            <span class="text-xs text-slate-300">&bull;</span>
                                            <span class="text-xs text-slate-500">{{ $aduan->kecamatan ?? 'Kota Padang' }}</span>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-800 transition-colors line-clamp-1">
                                            <a href="{{ route('admin.laporan.detail', $aduan->id) }}">
                                                {{ $aduan->judul }}
                                            </a>
                                        </h3>
                                        <p class="text-[11px] text-slate-500">
                                            Pelapor: <span class="font-medium text-slate-700">{{ $aduan->nama_pelapor }}</span> &bull; Masuk {{ \Carbon\Carbon::parse($aduan->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center pl-10 sm:pl-0 flex-shrink-0">
                                    <a href="{{ route('admin.laporan.detail', $aduan->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-700 text-emerald-900 hover:text-white border border-emerald-200/80 hover:border-transparent text-xs font-bold transition-all shadow-2xs">
                                        <span>Tinjau</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                            <i data-lucide="check" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Antrean Verifikasi Bersih</p>
                        <p class="text-[11px] text-slate-500 max-w-sm mx-auto">Seluruh pengaduan yang masuk telah tervalidasi atau ditindaklanjuti.</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-3.5 sm:px-6 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Prioritas penanganan awal dinas</span>
                <a href="{{ route('admin.laporan.index', ['status' => 'Menunggu Verifikasi']) }}" class="font-bold text-emerald-800 hover:underline">
                    Lihat Semua Antrean &rarr;
                </a>
            </div>
        </div>

        <!-- Column 2: Aktivitas & Tren 7 Hari (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Tren 7 Hari</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Volume aduan harian yang masuk ke sistem.</p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md font-mono">
                        Real-time
                    </span>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="p-5 sm:p-6 flex-grow flex flex-col justify-center">
                <div class="h-60 sm:h-64 w-full">
                    <canvas id="adminTrendChart"></canvas>
                </div>
            </div>

            <!-- Chart Summary Footer -->
            <div class="p-3.5 sm:px-6 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Total minggu ini: <strong class="font-bold text-slate-800 tabular-nums">{{ $trendCounts->sum() }}</strong> aduan</span>
                <a href="{{ route('admin.statistik') }}" class="font-bold text-emerald-800 hover:underline">
                    Buka Statistik Lengkap &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- ===== 4. 5 LAPORAN TERAKHIR MASUK (FIXED HORIZONTAL SCROLL & ALIGNMENT) ===== -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <!-- Section Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">5 Laporan Terakhir Masuk</h2>
                    <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Terbaru</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Arus aktivitas terkini pengaduan masyarakat di Kota Padang.</p>
            </div>

            <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-900 hover:underline">
                <span>Buka Tabel Semua Pengaduan</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- 
            HORIZONTAL SCROLL CONTAINER ("PAS GESER"):
            - Uses 'overflow-x-auto' strictly isolated to this container
            - Uses 'touch-pan-x overscroll-x-contain' to guarantee smooth horizontal touch swipe on mobile without horizontal page wobble
            - Table has 'min-w-[760px]' with fixed column widths so columns never compress or truncate awkwardly
        -->
        <div class="overflow-x-auto overscroll-x-contain touch-pan-x scrollbar-thin">
            <table class="w-full text-left text-xs min-w-[760px] whitespace-nowrap">
                <thead>
                    <tr class="text-[10px] uppercase font-bold text-slate-500 tracking-wider bg-slate-50/80 border-b border-slate-200/80">
                        <th class="py-3 px-4 w-28">Kode Laporan</th>
                        <th class="py-3 px-4 min-w-[260px]">Judul Aduan & Pelapor</th>
                        <th class="py-3 px-4 w-36">Kategori</th>
                        <th class="py-3 px-4 w-32">Kecamatan</th>
                        <th class="py-3 px-4 w-36">Status</th>
                        <th class="py-3 px-4 w-32">Waktu Masuk</th>
                        <th class="py-3 px-4 w-24 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($laporanTerbaru as $terbaru)
                        @php
                            $bClass = match($terbaru->status) {
                                'Menunggu Verifikasi' => 'badge-menunggu',
                                'Terverifikasi' => 'badge-terverifikasi',
                                'Dalam Penanganan' => 'badge-proses',
                                'Selesai' => 'badge-selesai',
                                default => 'badge-ditolak',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Kode -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                <span class="bg-slate-100/90 px-2 py-0.5 rounded text-[11px]">
                                    {{ $terbaru->kode_laporan }}
                                </span>
                            </td>

                            <!-- Judul & Pelapor -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.laporan.detail', $terbaru->id) }}" class="font-bold text-slate-900 group-hover:text-emerald-800 transition-colors block text-xs truncate max-w-sm">
                                    {{ $terbaru->judul }}
                                </a>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    Pelapor: <span class="text-slate-600 font-medium">{{ $terbaru->nama_pelapor }}</span>
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $terbaru->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>

                            <!-- Kecamatan -->
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $terbaru->kecamatan }}
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="badge-status {{ $bClass }}">
                                    {{ $terbaru->status }}
                                </span>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px] tabular-nums">
                                {{ \Carbon\Carbon::parse($terbaru->created_at)->translatedFormat('d M H:i') }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.laporan.detail', $terbaru->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 font-semibold text-xs transition-colors shadow-2xs">
                                    <span>Tinjau</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                Belum ada laporan yang tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Scroll Indicator Hint ("Geser ke samping") -->
        <div class="sm:hidden px-4 py-2.5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <span class="inline-flex items-center gap-1.5 font-medium">
                <i data-lucide="move-horizontal" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Geser tabel untuk melihat rincian</span>
            </span>
            <span class="font-mono text-slate-400 text-[10px]">5 laporan</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var canvas = document.getElementById('adminTrendChart');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var dates = @json($trendDates);
        var counts = @json($trendCounts);

        if (typeof Chart === 'undefined') return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Jumlah Aduan Masuk',
                    data: counts,
                    borderColor: '#0f5132',
                    borderWidth: 2,
                    backgroundColor: 'rgba(15, 81, 50, 0.05)',
                    pointBackgroundColor: '#0f5132',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1d1d1f',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(c) {
                                return c.parsed.y + ' Pengaduan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6e6e73',
                            font: { size: 11 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f5f5f7',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            color: '#6e6e73',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
