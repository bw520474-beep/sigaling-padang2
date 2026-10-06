@extends('layouts.app')

@section('title', 'Titik Lokasi TPS 11 Kecamatan — SIGALING PADANG')

@section('content')
<div class="space-y-8 pb-16">
    <!-- APPLE HERO HEADER -->
    <div class="rounded-3xl bg-gradient-to-b from-[#fbfbfd] to-[#f5f5f7] p-8 sm:p-12 border border-[#d2d2d7] shadow-xs text-center relative overflow-hidden">
        <div class="max-w-3xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-[#1d1d1f] text-xs font-semibold border border-[#d2d2d7] shadow-2xs">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Direktori Resmi DLH Kota Padang</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-semibold tracking-tight text-[#1d1d1f]">
                Titik Lokasi TPS Kota Padang
            </h1>
            <p class="text-sm sm:text-base text-[#6e6e73] max-w-2xl mx-auto leading-relaxed">
                Pemetaan resmi Tempat Penampungan Sementara (TPS), kontainer armroll, dan unit TPS 3R di 11 kecamatan Kota Padang untuk mempermudah pembuangan sampah tertib dan terjadwal.
            </p>
        </div>

        <!-- APPLE KPI METRICS STRIP -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-8 pt-6 border-t border-[#e5e5ea]">
            <div class="bg-white rounded-2xl p-4 border border-[#e5e5ea] shadow-2xs text-center transition-all hover:border-[#d2d2d7]">
                <span class="text-[11px] uppercase tracking-wider text-[#86868b] font-medium block">Total Titik TPS</span>
                <span class="text-2xl sm:text-3xl font-semibold text-[#1d1d1f] mt-0.5 block">{{ $totalTps }}</span>
                <span class="text-[11px] text-[#6e6e73]">Terdata se-Kota Padang</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-[#e5e5ea] shadow-2xs text-center transition-all hover:border-[#d2d2d7]">
                <span class="text-[11px] uppercase tracking-wider text-[#86868b] font-medium block">Kecamatan Terjangkau</span>
                <span class="text-2xl sm:text-3xl font-semibold text-[#1d1d1f] mt-0.5 block">{{ $totalKecamatan }}</span>
                <span class="text-[11px] text-[#6e6e73]">100% Kecamatan</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-[#e5e5ea] shadow-2xs text-center transition-all hover:border-[#d2d2d7]">
                <span class="text-[11px] uppercase tracking-wider text-[#86868b] font-medium block">Operasional Aktif</span>
                <span class="text-2xl sm:text-3xl font-semibold text-emerald-700 mt-0.5 block">{{ $totalAktif }}</span>
                <span class="text-[11px] text-emerald-700/80">Angkut rutin harian</span>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-[#e5e5ea] shadow-2xs text-center transition-all hover:border-[#d2d2d7]">
                <span class="text-[11px] uppercase tracking-wider text-[#86868b] font-medium block">Unit TPS 3R</span>
                <span class="text-2xl sm:text-3xl font-semibold text-teal-700 mt-0.5 block">{{ $total3R }}</span>
                <span class="text-[11px] text-teal-700/80">Daur ulang & pilah</span>
            </div>
        </div>
    </div>

    <!-- APPLE SEARCH & 11 KECAMATAN FILTER BAR -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Search Input -->
            <form action="{{ route('tps.index') }}" method="GET" class="flex-1 max-w-md relative">
                @if($selectedKecamatan)
                    <input type="hidden" name="kecamatan" value="{{ $selectedKecamatan }}">
                @endif
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama TPS, jalan, kelurahan..." class="w-full pl-10 pr-4 py-2.5 rounded-full border border-[#d2d2d7] text-xs bg-white text-[#1d1d1f] placeholder:text-[#86868b] focus:outline-none focus:border-[#1d1d1f] focus:ring-1 focus:ring-[#1d1d1f] shadow-2xs transition-all">
                <i data-lucide="search" class="w-4 h-4 text-[#86868b] absolute left-3.5 top-3"></i>
                @if($search)
                    <a href="{{ route('tps.index', ['kecamatan' => $selectedKecamatan]) }}" class="absolute right-3 top-3 text-[#86868b] hover:text-[#1d1d1f] text-xs">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>

            <!-- Action: Nearest TPS finder -->
            <div class="flex items-center gap-2">
                <button type="button" id="btn-tps-terdekat" onclick="findNearestTps()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-all shadow-xs hover:shadow cursor-pointer">
                    <i id="icon-nearest" data-lucide="navigation" class="w-3.5 h-3.5"></i>
                    <span id="label-nearest">Deteksi TPS Terdekat Saya</span>
                </button>
            </div>
        </div>

        <!-- 11 KECAMATAN PILL FILTERS (Apple Segmented Control Style) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs">
            <a href="{{ route('tps.index', array_filter(['q' => $search, 'status' => $statusFilter])) }}" 
               class="px-3.5 py-1.5 rounded-full whitespace-nowrap font-medium transition-all flex items-center gap-1.5 {{ empty($selectedKecamatan) ? 'bg-[#1d1d1f] text-white font-semibold shadow-xs' : 'bg-white hover:bg-[#e8e8ed] text-[#1d1d1f] border border-[#d2d2d7]' }}">
                <span>Semua Kecamatan</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ empty($selectedKecamatan) ? 'bg-white/20 text-white' : 'bg-[#f5f5f7] text-[#6e6e73] border border-[#e5e5ea]' }}">{{ $totalTps }}</span>
            </a>

            @foreach($kecamatans as $kec)
                @php
                    $count = $kecamatanCounts[$kec] ?? 0;
                    $isActive = ($selectedKecamatan === $kec);
                @endphp
                <a href="{{ route('tps.index', array_filter(['kecamatan' => $kec, 'q' => $search, 'status' => $statusFilter])) }}" 
                   class="px-3.5 py-1.5 rounded-full whitespace-nowrap font-medium transition-all flex items-center gap-1.5 {{ $isActive ? 'bg-[#1d1d1f] text-white font-semibold shadow-xs' : 'bg-white hover:bg-[#e8e8ed] text-[#1d1d1f] border border-[#d2d2d7]' }}">
                    <span>{{ $kec }}</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-[#f5f5f7] text-[#6e6e73] border border-[#e5e5ea]' }}">{{ $count }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- MAP CONTAINER (DIKECILKAN DARI SAMPING: max-w-2xl mx-auto DENGAN GARIS TEPI TEGAS) -->
    <div class="max-w-2xl mx-auto w-full bg-white rounded-3xl p-4 sm:p-5 border border-[#d2d2d7] shadow-xs space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 px-1">
            <div class="flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4 text-emerald-600"></i>
                <h2 class="text-sm font-semibold text-[#1d1d1f]">
                    Peta Titik TPS {{ $selectedKecamatan ? '— ' . $selectedKecamatan : 'Kota Padang' }}
                </h2>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Mode Switcher Peta: Standar vs Satelit -->
                <div class="inline-flex p-0.5 rounded-xl bg-[#f5f5f7] border border-[#e5e5ea] text-xs">
                    <button type="button" id="tps-btn-street" onclick="switchTpsMapMode('street')" class="px-2.5 py-1 rounded-lg font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1">
                        <span>🗺️ Standar</span>
                    </button>
                    <button type="button" id="tps-btn-satellite" onclick="switchTpsMapMode('satellite')" class="px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1">
                        <span>🛰️ Satelit</span>
                    </button>
                </div>

                <div class="hidden sm:flex items-center gap-2.5 text-xs text-[#6e6e73]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span>Aktif</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                        <span>Penuh</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Peta diperkecil dari samping (max-w-2xl mx-auto) dengan garis tepi tegas (border-2 border-slate-300) -->
        <div id="tps-map" class="w-full h-[300px] sm:h-[340px] rounded-2xl border-2 border-slate-300 shadow-2xs ring-1 ring-slate-200/80 overflow-hidden z-10"></div>
    </div>

    <!-- DAFTAR TITIK TPS DENGAN TATA LETAK LEBIH RAPI (APPLE STYLE 2-COLUMN GRID) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2">
                <h3 class="text-base sm:text-lg font-semibold text-[#1d1d1f]">
                    Daftar Titik Penampungan TPS
                </h3>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-[#f5f5f7] text-[#6e6e73] border border-[#e5e5ea]">
                    {{ $tpsList->count() }} Lokasi
                </span>
            </div>
            <span class="text-xs text-[#86868b] flex items-center gap-1.5 font-medium">
                <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Gulir untuk menjelajah semua titik</span>
            </span>
        </div>

        @if($tpsList->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-[#d2d2d7] shadow-xs">
                <i data-lucide="map-pin-off" class="w-10 h-10 text-[#86868b] mx-auto mb-3"></i>
                <h4 class="font-semibold text-sm text-[#1d1d1f]">Tidak ada titik TPS yang cocok</h4>
                <p class="text-xs text-[#6e6e73] mt-1">Coba sesuaikan kata kunci pencarian atau pilih kecamatan lain.</p>
                <a href="{{ route('tps.index') }}" class="inline-block mt-4 px-5 py-2 rounded-full bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors">
                    Reset Filter
                </a>
            </div>
        @else
            <!-- Scrolling Container untuk Daftar Titik TPS -->
            <div id="tps-scroll-container" class="max-h-[540px] sm:max-h-[580px] overflow-y-auto pr-2.5 pb-2 rounded-2xl border border-slate-200/80 bg-slate-50/50 p-3 sm:p-4 shadow-2xs space-y-4" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
                    @foreach($tpsList as $tps)
                        <div id="tps-card-{{ $tps->id }}" class="tps-card bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-400/80 transition-all duration-200 flex flex-col justify-between group scroll-mt-6">
                            <div class="space-y-3">
                                <!-- Header Baris Kartu -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-md bg-[#f5f5f7] text-[#1d1d1f] border border-[#e5e5ea]">
                                            {{ $tps->kode_tps }}
                                        </span>
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            {{ $tps->jenis_tps }}
                                        </span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $tps->status_badge_class }} flex items-center gap-1.5 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tps->status_dot_class }}"></span>
                                        <span>{{ $tps->status }}</span>
                                    </span>
                                </div>

                                <!-- Judul & Wilayah -->
                                <div>
                                    <h4 class="text-[15px] font-semibold text-[#1d1d1f] group-hover:text-emerald-700 transition-colors">
                                        {{ $tps->nama_tps }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 text-xs text-[#6e6e73] font-medium mt-0.5">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-emerald-600"></i>
                                        <span>Kecamatan {{ $tps->kecamatan }}{{ $tps->kelurahan ? ' • Kel. ' . $tps->kelurahan : '' }}</span>
                                    </div>
                                </div>

                                <!-- Alamat Jalan Lengkap -->
                                <p class="text-xs text-[#3a3a3c] leading-relaxed">
                                    {{ $tps->alamat }}
                                </p>

                                <!-- Kotak Spesifikasi Operasional Apple Style -->
                                <div class="bg-[#f5f5f7] rounded-xl p-3 border border-[#e5e5ea] grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-[#86868b] font-medium block">Jadwal Armada</span>
                                        <span class="font-medium text-[11px] text-[#1d1d1f] mt-0.5 block truncate" title="{{ $tps->jadwal_angkut ?? 'Harian' }}">
                                            {{ $tps->jadwal_angkut ?? 'Harian' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-[#86868b] font-medium block">Kapasitas</span>
                                        <span class="font-medium text-[11px] text-[#1d1d1f] mt-0.5 block truncate" title="{{ $tps->kapasitas ?? '6 m³' }}">
                                            {{ $tps->kapasitas ?? '6 m³' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris Aksi Kartu -->
                            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <button type="button" onclick="focusTps({{ $tps->id }}, {{ $tps->latitude }}, {{ $tps->longitude }})" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="crosshair" class="w-3.5 h-3.5"></i>
                                    <span>Lihat di Peta</span>
                                </button>

                                <a href="{{ $tps->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors shadow-2xs">
                                    <span>Petunjuk Arah</span>
                                    <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let tpsMap;
    let markersLayer;
    let markersMap = {};
    let userLocationMarker = null;
    let streetTileLayer;
    let satelliteTileLayer;

    let rawTpsData = @json($tpsList->values());
    let tpsData = Array.isArray(rawTpsData) ? rawTpsData : (rawTpsData ? Object.values(rawTpsData) : []);

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof L === 'undefined') return;

        // Inisialisasi Peta Kota Padang
        const defaultLat = -0.9471;
        const defaultLng = 100.3800;

        tpsMap = L.map('tps-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([defaultLat, defaultLng], 12);

        // Basemap 1: OpenStreetMap (Standar)
        streetTileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        // Basemap 2: Google Hybrid (Satelit + Label Jalan)
        satelliteTileLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Hybrid Satellite'
        });

        // Default: Tampilkan peta standar
        streetTileLayer.addTo(tpsMap);

        // Leaflet Layer Control (Pojok Kanan Atas)
        const baseMaps = {
            "🗺️ Peta Standar": streetTileLayer,
            "🛰️ Satelit (Hybrid)": satelliteTileLayer
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(tpsMap);

        // Zoom control
        L.control.zoom({ position: 'bottomright' }).addTo(tpsMap);

        markersLayer = L.layerGroup().addTo(tpsMap);

        // Sinkronisasi tombol jika layer diganti lewat kontrol Leaflet
        tpsMap.on('baselayerchange', function (e) {
            updateSwitcherButtons(e.layer === satelliteTileLayer ? 'satellite' : 'street');
        });

        // Render data titik TPS
        if (Array.isArray(tpsData) && tpsData.length > 0) {
            renderTpsMarkers(tpsData);
        } else {
            fetchTpsFromApi();
        }

        // Multiple invalidation untuk memastikan tiles dan marker langsung pas
        setTimeout(() => { if (tpsMap) tpsMap.invalidateSize(); }, 150);
        setTimeout(() => { if (tpsMap) tpsMap.invalidateSize(); }, 450);
        setTimeout(() => { if (tpsMap) tpsMap.invalidateSize(); }, 1000);
        window.addEventListener('resize', () => { if (tpsMap) tpsMap.invalidateSize(); });
    });

    function updateSwitcherButtons(mode) {
        const btnStreet = document.getElementById('tps-btn-street');
        const btnSat = document.getElementById('tps-btn-satellite');
        if (mode === 'satellite') {
            if (btnSat) {
                btnSat.className = 'px-2.5 py-1 rounded-lg font-bold bg-emerald-600 text-white shadow-2xs transition-all cursor-pointer flex items-center gap-1';
            }
            if (btnStreet) {
                btnStreet.className = 'px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            }
        } else {
            if (btnStreet) {
                btnStreet.className = 'px-2.5 py-1 rounded-lg font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1';
            }
            if (btnSat) {
                btnSat.className = 'px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            }
        }
    }

    window.switchTpsMapMode = function (mode) {
        if (!tpsMap || !streetTileLayer || !satelliteTileLayer) return;
        if (mode === 'satellite') {
            if (tpsMap.hasLayer(streetTileLayer)) tpsMap.removeLayer(streetTileLayer);
            if (!tpsMap.hasLayer(satelliteTileLayer)) satelliteTileLayer.addTo(tpsMap);
            updateSwitcherButtons('satellite');
        } else {
            if (tpsMap.hasLayer(satelliteTileLayer)) tpsMap.removeLayer(satelliteTileLayer);
            if (!tpsMap.hasLayer(streetTileLayer)) streetTileLayer.addTo(tpsMap);
            updateSwitcherButtons('street');
        }
    };

    function fetchTpsFromApi() {
        fetch('{{ route("tps.geojson") }}' + window.location.search)
            .then(res => res.json())
            .then(data => {
                if (data && Array.isArray(data.features)) {
                    tpsData = data.features.map(f => ({
                        id: f.properties.id,
                        nama_tps: f.properties.nama_tps,
                        kode_tps: f.properties.kode_tps,
                        kecamatan: f.properties.kecamatan,
                        kelurahan: f.properties.kelurahan,
                        alamat: f.properties.alamat,
                        jenis_tps: f.properties.jenis_tps,
                        kapasitas: f.properties.kapasitas,
                        jadwal_angkut: f.properties.jadwal_angkut,
                        status: f.properties.status,
                        latitude: f.geometry.coordinates[1],
                        longitude: f.geometry.coordinates[0]
                    }));
                    renderTpsMarkers(tpsData);
                }
            })
            .catch(err => console.error('Error fetching TPS geojson:', err));
    }

    function getPinColor(status) {
        switch(status) {
            case 'Aktif': return '#10b981'; // emerald
            case 'Hampir Penuh': return '#f59e0b'; // amber
            case 'Penuh': return '#ef4444'; // rose
            case 'Perbaikan': return '#64748b'; // slate
            default: return '#10b981';
        }
    }

    function renderTpsMarkers(items) {
        if (!markersLayer || !tpsMap) return;
        markersLayer.clearLayers();
        markersMap = {};

        if (!items || items.length === 0) return;

        const bounds = [];

        items.forEach(tps => {
            const lat = parseFloat(tps.latitude);
            const lng = parseFloat(tps.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            bounds.push([lat, lng]);

            const color = getPinColor(tps.status);

            // Vector CircleMarker: Selalu muncul secara visual tanpa ketergantungan file gambar atau font
            const marker = L.circleMarker([lat, lng], {
                radius: 8,
                fillColor: color,
                color: '#ffffff',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            });

            // Tooltip cepat saat cursor diarahkan ke marker
            marker.bindTooltip(`<strong>${tps.nama_tps}</strong><br><span style="font-size:10px; color:${color}; font-weight:bold;">${tps.status}</span>`, {
                direction: 'top',
                offset: [0, -6]
            });

            const kelurahanLabel = tps.kelurahan ? ` • Kel. ${tps.kelurahan}` : '';
            const popupContent = `
                <div style="font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 12px; min-width: 220px; max-width: 260px; line-height: 1.4;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px;">
                        <span style="font-size: 10px; font-family: monospace; font-weight: bold; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569;">${tps.kode_tps || 'TPS'}</span>
                        <span style="font-size: 10px; font-weight: bold; color: ${color};">${tps.status}</span>
                    </div>
                    <div style="font-weight: 700; font-size: 13px; color: #1d1d1f; margin-bottom: 2px;">${tps.nama_tps}</div>
                    <div style="font-size: 11px; color: #059669; font-weight: 600; margin-bottom: 4px;">Kec. ${tps.kecamatan}${kelurahanLabel}</div>
                    <div style="font-size: 11px; color: #6e6e73; margin-bottom: 8px;">${tps.alamat}</div>
                    
                    <div style="border-top: 1px solid #e5e5ea; padding-top: 6px; margin-bottom: 8px; font-size: 11px; color: #1d1d1f;">
                        <div>⏱ <strong>Jadwal:</strong> ${tps.jadwal_angkut || 'Harian'}</div>
                        <div>📦 <strong>Kapasitas:</strong> ${tps.kapasitas || '6 m³'}</div>
                    </div>

                    <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" rel="noopener noreferrer" 
                       style="display: block; text-align: center; background: #059669; color: white; padding: 7px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 11px;">
                        Petunjuk Arah (Google Maps) ↗
                    </a>
                </div>
            `;

            marker.bindPopup(popupContent);
            marker.addTo(markersLayer);
            markersMap[tps.id] = marker;
        });

        if (bounds.length > 0 && tpsMap) {
            tpsMap.fitBounds(bounds, { padding: [35, 35], maxZoom: 14 });
        }
    }

    window.focusTps = function (id, lat, lng) {
        if (!tpsMap) return;
        tpsMap.flyTo([lat, lng], 16, { duration: 1.2 });
        if (markersMap[id]) {
            setTimeout(() => {
                markersMap[id].openPopup();
            }, 1200);
        }

        // Scroll ke peta secara halus
        const mapEl = document.getElementById('tps-map');
        if (mapEl) {
            mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    window.findNearestTps = function () {
        const btn = document.getElementById('btn-tps-terdekat');
        const icon = document.getElementById('icon-nearest');
        const label = document.getElementById('label-nearest');

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung geolokasi.');
            return;
        }

        if (btn) btn.disabled = true;
        if (label) label.textContent = 'Mencari lokasi Anda...';
        if (icon) icon.classList.add('animate-spin');

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const userLat = pos.coords.latitude;
                const userLng = pos.coords.longitude;

                if (userLocationMarker) {
                    tpsMap.removeLayer(userLocationMarker);
                }

                userLocationMarker = L.circleMarker([userLat, userLng], {
                    radius: 8,
                    fillColor: '#2563eb',
                    color: '#ffffff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(tpsMap);
                userLocationMarker.bindPopup('<strong style="font-size:12px;">Posisi Anda Saat Ini</strong>').openPopup();

                let nearest = null;
                let minDistance = Infinity;

                tpsData.forEach(item => {
                    const lat = parseFloat(item.latitude);
                    const lng = parseFloat(item.longitude);
                    if (isNaN(lat) || isNaN(lng)) return;

                    const d = getDistanceFromLatLonInKm(userLat, userLng, lat, lng);
                    item.distance = d;

                    if (d < minDistance) {
                        minDistance = d;
                        nearest = item;
                    }
                });

                if (nearest) {
                    tpsMap.flyTo([nearest.latitude, nearest.longitude], 16, { duration: 1.5 });
                    setTimeout(() => {
                        if (markersMap[nearest.id]) {
                            markersMap[nearest.id].openPopup();
                        }
                    }, 1500);

                    const card = document.getElementById('tps-card-' + nearest.id);
                    if (card) {
                        card.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500');
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    alert(`TPS terdekat ditemukan: "${nearest.nama_tps}" di Kec. ${nearest.kecamatan} (Jarak ±${minDistance.toFixed(2)} km).`);
                }

                if (btn) btn.disabled = false;
                if (label) label.textContent = 'Deteksi TPS Terdekat Saya';
                if (icon) icon.classList.remove('animate-spin');
            },
            function (err) {
                // Fallback cerdas: Pusatkan peta ke Kota Padang jika GPS browser dibatasi
                if (tpsMap) {
                    tpsMap.flyTo([-0.9471, 100.3543], 14, { duration: 1.2 });
                }

                let msg = '📍 Akses GPS perangkat dibatasi oleh browser karena koneksi lokal (HTTP).\n\nSolusi Praktis:\nCukup klik tombol pilihan Kecamatan di atas atau sentuh langsung titik-titik TPS pada peta untuk melihat detail lokasi dan petunjuk arah!';
                if (err.code === 2) {
                    msg = 'Sinyal GPS atau informasi lokasi perangkat tidak tersedia. Silakan pilih Kecamatan di atas.';
                } else if (err.code === 3) {
                    msg = 'Waktu permintaan lokasi GPS habis. Silakan pilih Kecamatan di atas.';
                }
                alert(msg);
                if (btn) btn.disabled = false;
                if (label) label.textContent = 'Deteksi TPS Terdekat Saya';
                if (icon) icon.classList.remove('animate-spin');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam KM
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }
</script>
@endpush
