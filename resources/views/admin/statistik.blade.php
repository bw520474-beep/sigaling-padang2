@extends('layouts.admin')

@section('title', 'Statistik & Analisis SLA — Admin SIGALING')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <div class="space-y-1 pb-6 border-b border-slate-200">
        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">
            Analitik Operasional
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Statistik & Distribusi Penanganan
        </h1>
        <p class="text-xs text-slate-500">
            Analisis beban kerja di 11 kecamatan, komposisi kategori masalah, dan distribusi disposisi instansi OPD pelaksana.
        </p>
    </div>

    <!-- ===== MULTI-PERIOD TIME SERIES CHART (HARIAN, MINGGUAN, BULANAN, TAHUNAN) ===== -->
    <div class="py-6">
        <div class="p-6 rounded-3xl border border-slate-200/90 bg-white shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Tren Pengaduan Masyarakat</h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Analisis histori fluktuasi laporan aduan berdasarkan rentang waktu pilihan.</p>
                </div>

                <!-- Interactive Period Switcher Tabs -->
                <div class="flex items-center bg-slate-100/80 p-1 rounded-xl border border-slate-200 text-xs font-semibold" id="periodSwitcher">
                    <button type="button" onclick="switchPeriod('harian')" id="btn-harian" class="period-tab px-3 py-1.5 rounded-lg transition-all duration-200 bg-white text-emerald-800 shadow-2xs font-bold">
                        Harian
                    </button>
                    <button type="button" onclick="switchPeriod('mingguan')" id="btn-mingguan" class="period-tab px-3 py-1.5 rounded-lg transition-all duration-200 text-slate-600 hover:text-slate-900 font-medium">
                        Mingguan
                    </button>
                    <button type="button" onclick="switchPeriod('bulanan')" id="btn-bulanan" class="period-tab px-3 py-1.5 rounded-lg transition-all duration-200 text-slate-600 hover:text-slate-900 font-medium">
                        Bulanan
                    </button>
                    <button type="button" onclick="switchPeriod('tahunan')" id="btn-tahunan" class="period-tab px-3 py-1.5 rounded-lg transition-all duration-200 text-slate-600 hover:text-slate-900 font-medium">
                        Tahunan
                    </button>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="h-72 sm:h-80 pt-6">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ===== CHARTS ASYMMETRIC GRID ===== -->
    <div class="py-2 grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Chart Kecamatan (7 Cols) -->
        <div class="lg:col-span-7 space-y-3 p-6 rounded-2xl border border-slate-200/90 bg-white shadow-xs">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Sebaran Aduan per Kecamatan</h2>
                <p class="text-[11px] text-slate-500">Volume pengaduan masyarakat di 11 wilayah kecamatan Kota Padang.</p>
            </div>
            <div class="h-64 pt-4">
                <canvas id="kecamatanBarChart"></canvas>
            </div>
        </div>

        <!-- Chart Kategori (5 Cols) -->
        <div class="lg:col-span-5 space-y-3 p-6 rounded-2xl border border-slate-200/90 bg-white shadow-xs">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Komposisi Kategori Masalah</h2>
                <p class="text-[11px] text-slate-500">Proporsi jenis pengaduan lingkungan hidup yang masuk.</p>
            </div>
            <div class="h-64 flex items-center justify-center pt-2">
                <canvas id="kategoriPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ===== OPD WORKLOAD MATRIX ===== -->
    <div class="pt-6">
        <div class="space-y-1 mb-4">
            <h2 class="text-sm font-bold text-slate-900">Beban Disposisi OPD Pelaksana</h2>
            <p class="text-xs text-slate-500">Akumulasi pengaduan yang diteruskan ke masing-masing bidang/kedinasan.</p>
        </div>

        <div class="divide-y divide-slate-100 bg-white rounded-2xl border border-slate-200/80 p-2">
            @forelse($opdStats as $opd)
                <div class="py-3 px-4 flex items-center justify-between gap-4 text-xs hover:bg-slate-50/80 rounded-xl transition-colors">
                    <div>
                        <span class="font-bold text-slate-900 block">{{ $opd->nama_opd }}</span>
                        <span class="text-[11px] text-slate-500">{{ $opd->kontak ?? 'DLH Kota Padang' }}</span>
                    </div>
                    <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full text-[11px] tabular-nums">
                        {{ $opd->laporan_opds_count }} Penugasan
                    </span>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400">
                    Belum ada data disposisi OPD yang tercatat.
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data Multi-Periode dari Controller
        const timeSeriesData = {!! json_encode($timeSeriesData) !!};
        let trendChartInstance = null;

        // Inisialisasi Trend Chart (Default: Harian)
        const trendCtx = document.getElementById('trendChart')?.getContext('2d');
        if (trendCtx && window.Chart) {
            trendChartInstance = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: timeSeriesData.harian.labels,
                    datasets: [{
                        label: 'Pengaduan Masuk',
                        data: timeSeriesData.harian.data,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Pengaduan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                precision: 0,
                                font: { size: 10 },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        }

        // Global function switch period
        window.switchPeriod = function(period) {
            if (!trendChartInstance || !timeSeriesData[period]) return;

            // Update chart data
            trendChartInstance.data.labels = timeSeriesData[period].labels;
            trendChartInstance.data.datasets[0].data = timeSeriesData[period].data;
            trendChartInstance.update();

            // Update button tab visual active state
            document.querySelectorAll('.period-tab').forEach(btn => {
                btn.className = 'period-tab px-3 py-1.5 rounded-lg transition-all duration-200 text-slate-600 hover:text-slate-900 font-medium';
            });
            const activeBtn = document.getElementById('btn-' + period);
            if (activeBtn) {
                activeBtn.className = 'period-tab px-3 py-1.5 rounded-lg transition-all duration-200 bg-white text-emerald-800 shadow-2xs font-bold';
            }
        };

        // Kecamatan Bar Chart
        const kecCtx = document.getElementById('kecamatanBarChart')?.getContext('2d');
        if (kecCtx && window.Chart) {
            new Chart(kecCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartKecamatanLabels) !!},
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: {!! json_encode($chartKecamatanData) !!},
                        backgroundColor: '#059669',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                precision: 0,
                                font: { size: 10 },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        }

        // Kategori Doughnut Chart
        const katCtx = document.getElementById('kategoriPieChart')?.getContext('2d');
        if (katCtx && window.Chart) {
            new Chart(katCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartKategoriLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartKategoriData) !!},
                        backgroundColor: [
                            '#059669', '#2563eb', '#7c3aed', '#d97706', '#0284c7', '#e11d48', '#475569'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10 },
                                color: '#475569'
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
