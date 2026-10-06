@extends('layouts.app')

@section('title', 'Transparansi Publik & Akuntabilitas SLA — SIGALING PADANG')

@section('content')
<div class="bg-slate-50 min-h-screen pb-16">
    <!-- Header Hero Section (Apple Aesthetic: Pure, High Whitespace, Confident) -->
    <div class="border-b border-slate-200/80 bg-white/70 backdrop-blur-md sticky top-14 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-[11px] font-bold text-emerald-800 uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        Civic Open Data & Transparency
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        Transparansi & Akuntabilitas Publik
                    </h1>
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                        Pantau integritas dan kecepatan penyelesaian isu publik antar-instansi Pemerintah Kota Padang secara objektif dan real-time sesuai Service Level Agreement (SLA).
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('lapor') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs hover:shadow transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Laporkan Isu Kota</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-8">
        <!-- 1. Key Performance Indicators (Apple Clean Card Metric Grid) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pengaduan</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <i data-lucide="inbox" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($totalLaporan) }}</div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="text-emerald-600 font-semibold">100%</span> tercatat dalam audit trail
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Selesai Dituntaskan</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-extrabold tracking-tight text-emerald-700">{{ number_format($totalSelesai) }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    Solusi terverifikasi di lapangan
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Sedang Ditangani</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-extrabold tracking-tight text-amber-600">{{ number_format($totalProses) }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    Koordinasi armada & teknisi
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sky-700">Kepatuhan SLA</span>
                    <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center text-sky-700">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $slaRate }}%</div>
                <div class="text-xs text-slate-500 mt-1">
                    {{ $totalOverdue }} kasus melampaui tenggat batas
                </div>
            </div>
        </div>

        <!-- 2. Interactive Public Civic Map -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                        Peta Sebaran Laporan Masyarakat Terbuka
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan sebaran aduan kota Padang. Koordinat diperhalus untuk melindungi privasi kediaman pelapor.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 font-medium">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Proses
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-800 font-medium">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Menunggu
                    </span>
                </div>
            </div>
            
            <div id="transparencyMap" class="h-[420px] w-full bg-slate-100"></div>
        </div>

        <!-- 3. Two Columns: Instansi Collaboration & Category Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left: Beban & Kinerja Antar-Instansi (Collab Civic Engine) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-slate-600"></i>
                            Distribusi Penugasan Lintas Instansi
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Instansi aktif yang menerima disposisi dan penugasan kolaboratif</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($orgStats as $org)
                        @php
                            $percentage = $totalLaporan > 0 ? min(100, round(($org->assignments_count / $totalLaporan) * 100)) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold text-slate-800">{{ $org->name }} ({{ $org->code }})</span>
                                <span class="text-slate-500 font-medium">{{ $org->assignments_count }} Penugasan ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400">Belum ada data penugasan instansi.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Kategori Masalah Masyarakat -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-1">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-slate-600"></i>
                    Topik Masalah Terbanyak
                </h3>
                <p class="text-xs text-slate-500 mb-4">Klasifikasi permasalahan warga Kota Padang</p>

                <div class="space-y-3">
                    @forelse($kategoriStats->take(6) as $kat)
                        @php
                            $kPercent = $totalLaporan > 0 ? round(($kat->laporans_count / $totalLaporan) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-semibold text-slate-800">{{ $kat->nama_kategori }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900">{{ $kat->laporans_count }}</span>
                                <span class="text-[11px] text-slate-400">({{ $kPercent }}%)</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400">Belum ada kategori.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. Citizen Privacy & Open Governance Manifesto -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl text-white p-6 sm:p-8 shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="md:col-span-2 space-y-2">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs font-semibold">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        Perlindungan Data & Pelapor Terjamin
                    </div>
                    <h3 class="text-lg font-bold">Transparan untuk Masalah, Terisolasi untuk Identitas</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        SIGALING Padang menerapkan 3 level perlindungan: Publik, Rahasia (Confidential), dan Anonim Mutlak. 
                        Data pribadi pelapor disimpan di tabel terenkripsi terisolasi dan dilindungi dari intervensi lapangan untuk menjamin kenyamanan warga dalam menyuarakan perbaikan kota.
                    </p>
                </div>
                <div class="text-center md:text-right">
                    <a href="{{ route('alur') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-900 hover:bg-slate-100 text-xs font-bold transition-all shadow-sm">
                        <i data-lucide="file-text" class="w-4 h-4 text-emerald-600"></i>
                        <span>Pelajari SOP & SLA</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof L === 'undefined') return;

        // Inisialisasi Peta Transparansi (Pusat Kota Padang)
        const map = L.map('transparencyMap', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([-0.9471, 100.3686], 12);

        // Tile Basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors | SIGALING Padang'
        }).addTo(map);

        const points = @json($publicPoints);

        const statusColors = {
            'Selesai': '#10b981',
            'Dalam Penanganan': '#f59e0b',
            'Terverifikasi': '#3b82f6',
            'Menunggu Verifikasi': '#ef4444'
        };

        points.forEach(pt => {
            if (!pt.lat || !pt.lng) return;

            const color = statusColors[pt.status] || '#64748b';

            const marker = L.circleMarker([pt.lat, pt.lng], {
                radius: 6,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            marker.bindPopup(`
                <div style="font-family: system-ui, sans-serif; font-size: 12px; line-height: 1.4; padding: 2px;">
                    <div style="font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase;">${pt.kode}</div>
                    <div style="font-weight: 700; color: #0f172a; margin-top: 2px;">${pt.judul}</div>
                    <div style="color: #475569; margin-top: 4px;">Kategori: <b>${pt.kategori}</b></div>
                    <div style="color: #64748b; font-size: 11px;">Kecamatan: ${pt.kecamatan} &bull; ${pt.tanggal}</div>
                    <div style="margin-top: 6px;">
                        <span style="background: ${color}20; color: ${color}; font-weight: 700; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                            ${pt.status}
                        </span>
                    </div>
                </div>
            `);
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
