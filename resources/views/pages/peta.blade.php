@extends('layouts.app')

@section('title', 'Peta Sebaran Pengaduan — SIGALING PADANG')

@section('content')
    <!-- ===== HEADER & FILTER BAR ===== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 hairline-b">
            <div>
                <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                    Pemantauan Geospasial
                </span>
                <h1 class="text-2xl sm:text-4xl font-semibold text-[#1d1d1f] tracking-tight mt-1">
                    Peta Sebaran Pengaduan
                </h1>
                <p class="text-xs text-[#6e6e73] mt-1">
                    Titik koordinat penanganan masalah kebersihan dan lingkungan di 11 kecamatan Kota Padang.
                </p>
            </div>

            <!-- Minimal Filters -->
            <form method="GET" action="{{ route('peta') }}" class="flex flex-wrap items-center gap-2 text-xs">
                <select name="kategori" onchange="this.form.submit()" class="px-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="Dalam Penanganan" {{ request('status') == 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                @if(request('kategori') || request('status'))
                    <a href="{{ route('peta') }}" class="text-xs text-[#86868b] hover:text-[#1d1d1f] px-2 py-1">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>
    </section>

    <!-- ===== MAP & INCIDENT SPLIT WORKSPACE ===== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-[680px]">
            
            <!-- Map Container (8 Cols) -->
            <div class="lg:col-span-8 rounded-2xl overflow-hidden border-2 border-slate-300 ring-1 ring-slate-200/80 bg-white relative flex flex-col shadow-xs">
                <div id="sigaling-map" class="w-full h-full min-h-[480px] z-10"></div>

                <!-- Floating Mode Switcher: Standar vs Satelit -->
                <div class="absolute top-4 left-4 z-20 inline-flex p-0.5 rounded-xl bg-white/95 backdrop-blur-md border border-slate-300/80 shadow-md text-xs">
                    <button type="button" id="peta-btn-street" onclick="switchPetaMapMode('street')" class="px-2.5 py-1 rounded-lg font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1">
                        <span>🗺️ Standar</span>
                    </button>
                    <button type="button" id="peta-btn-satellite" onclick="switchPetaMapMode('satellite')" class="px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1">
                        <span>🛰️ Satelit</span>
                    </button>
                </div>

                <!-- Minimal Legend -->
                <div class="absolute bottom-4 left-4 z-20 bg-white/90 backdrop-blur-md rounded-xl p-3 border border-[#e5e5ea] text-xs space-y-1 hidden sm:block">
                    <span class="font-medium text-[#1d1d1f] block text-[11px] mb-1">Status Penanganan:</span>
                    <div class="flex items-center gap-2 text-[#6e6e73] text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-[#d97706]"></span>
                        <span>Menunggu Verifikasi</span>
                    </div>
                    <div class="flex items-center gap-2 text-[#6e6e73] text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-[#2563eb]"></span>
                        <span>Terverifikasi Disposisi</span>
                    </div>
                    <div class="flex items-center gap-2 text-[#6e6e73] text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-[#9333ea]"></span>
                        <span>Dalam Penanganan</span>
                    </div>
                    <div class="flex items-center gap-2 text-[#6e6e73] text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                        <span>Selesai Tuntas</span>
                    </div>
                </div>
            </div>

            <!-- Incident Drawer Sidebar (4 Cols) -->
            <div class="lg:col-span-4 rounded-2xl border border-[#e5e5ea] bg-white p-5 flex flex-col h-full overflow-hidden">
                <div class="flex items-center justify-between pb-3 hairline-b">
                    <span class="font-medium text-xs text-[#1d1d1f]">Daftar Titik ({{ $laporans->count() }})</span>
                    <span class="text-[11px] text-[#86868b]">Kota Padang</span>
                </div>

                <div class="flex-grow overflow-y-auto divide-y divide-[#e5e5ea] pr-1">
                    @forelse($laporans as $item)
                        @php
                            $badgeClass = match($item->status) {
                                'Menunggu Verifikasi' => 'badge-menunggu',
                                'Terverifikasi' => 'badge-terverifikasi',
                                'Dalam Penanganan' => 'badge-proses',
                                'Selesai' => 'badge-selesai',
                                default => 'badge-ditolak',
                            };
                        @endphp
                        <div class="py-3 cursor-pointer group hover:bg-[#f5f5f7] px-2 -mx-2 rounded-lg transition-colors" onclick="focusMarker({{ (float)$item->latitude }}, {{ (float)$item->longitude }}, '{{ $item->kode_laporan }}')">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="font-mono text-[10px] text-[#86868b] tabular-nums">{{ $item->kode_laporan }}</span>
                                <span class="badge-status {{ $badgeClass }} text-[10px]">{{ $item->status }}</span>
                            </div>
                            <h2 class="text-xs font-medium text-[#1d1d1f] group-hover:text-[#0f5132] transition-colors truncate">
                                {{ $item->judul }}
                            </h2>
                            <p class="text-[11px] text-[#86868b] truncate mt-0.5">
                                {{ $item->alamat ?? $item->kecamatan }}
                            </p>
                            <div class="mt-1 flex items-center justify-between text-[10px] text-[#86868b]">
                                <span>{{ $item->kategori->nama_kategori ?? 'Umum' }}</span>
                                <a href="{{ route('lapor.detail', $item->kode_laporan) }}" class="text-[#1d1d1f] hover:underline font-medium" onclick="event.stopPropagation()">
                                    Detail &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-xs text-[#86868b]">
                            Tidak ada pengaduan dengan titik koordinat pada filter ini.
                        </div>
                    @endforelse
                </div>

                <div class="pt-4 hairline-t mt-2">
                    <a href="{{ route('lapor') }}" class="w-full py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all flex items-center justify-center gap-1.5">
                        <i data-lucide="plus" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                        <span>Laporkan Titik Masalah</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    let map;
    let markers = [];

    document.addEventListener('DOMContentLoaded', function () {
        const padangLat = -0.9471;
        const padangLng = 100.4172;

        map = L.map('sigaling-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([padangLat, padangLng], 12);

        let streetTileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        let satelliteTileLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Hybrid Satellite'
        });

        // Default: peta standar
        streetTileLayer.addTo(map);

        // Leaflet Layer Control (Pojok Kanan Atas)
        const baseMaps = {
            "🗺️ Peta Standar": streetTileLayer,
            "🛰️ Satelit (Hybrid)": satelliteTileLayer
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Sinkronisasi tombol jika layer diganti lewat kontrol Leaflet
        map.on('baselayerchange', function (e) {
            updatePetaSwitcherButtons(e.layer === satelliteTileLayer ? 'satellite' : 'street');
        });

        function updatePetaSwitcherButtons(mode) {
            const btnStreet = document.getElementById('peta-btn-street');
            const btnSat = document.getElementById('peta-btn-satellite');
            if (mode === 'satellite') {
                if (btnSat) btnSat.className = 'px-2.5 py-1 rounded-lg font-bold bg-emerald-600 text-white shadow-2xs transition-all cursor-pointer flex items-center gap-1';
                if (btnStreet) btnStreet.className = 'px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            } else {
                if (btnStreet) btnStreet.className = 'px-2.5 py-1 rounded-lg font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1';
                if (btnSat) btnSat.className = 'px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            }
        }

        window.switchPetaMapMode = function (mode) {
            if (!map || !streetTileLayer || !satelliteTileLayer) return;
            if (mode === 'satellite') {
                if (map.hasLayer(streetTileLayer)) map.removeLayer(streetTileLayer);
                if (!map.hasLayer(satelliteTileLayer)) satelliteTileLayer.addTo(map);
                updatePetaSwitcherButtons('satellite');
            } else {
                if (map.hasLayer(satelliteTileLayer)) map.removeLayer(satelliteTileLayer);
                if (!map.hasLayer(streetTileLayer)) streetTileLayer.addTo(map);
                updatePetaSwitcherButtons('street');
            }
        };

        // Pastikan ukuran canvas peta terhitung akurat
        setTimeout(() => map.invalidateSize(), 300);
        window.addEventListener('resize', () => map.invalidateSize());

        const urlParams = new URLSearchParams(window.location.search);
        const geojsonUrl = '{{ route("peta.geojson") }}?' + urlParams.toString();

        fetch(geojsonUrl)
            .then(res => res.json())
            .then(res => {
                let items = [];
                if (res && Array.isArray(res.data)) {
                    items = res.data;
                } else if (res && Array.isArray(res.features)) {
                    items = res.features;
                }

                if (items.length > 0) {
                    const markersGroup = [];
                    items.forEach(item => {
                        const lat = item.latitude || (item.geometry && item.geometry.coordinates ? item.geometry.coordinates[1] : null);
                        const lng = item.longitude || (item.geometry && item.geometry.coordinates ? item.geometry.coordinates[0] : null);
                        if (!lat || !lng) return;

                        const status = item.status || (item.properties ? item.properties.status : '');
                        let pinColor = '#d97706'; // default kuning
                        if (status === 'Terverifikasi') pinColor = '#2563eb'; // biru
                        if (status === 'Dalam Penanganan') pinColor = '#9333ea'; // ungu
                        if (status === 'Selesai') pinColor = '#059669'; // hijau

                        const marker = L.circleMarker([lat, lng], {
                            radius: 8,
                            fillColor: pinColor,
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.95
                        }).addTo(map);

                        const judul = item.judul || (item.properties ? item.properties.judul : 'Pengaduan');
                        const kode = item.kode_laporan || (item.properties ? item.properties.kode_laporan : '');
                        const alamat = item.alamat || (item.properties ? item.properties.alamat : '');
                        const url = item.url || (kode ? '/laporan/' + kode : '#');

                        const popupHtml = `
                            <div style="font-family: -apple-system, BlinkMacSystemFont, sans-serif; min-width: 180px; padding: 2px;">
                                <div style="font-size: 10px; font-weight: 600; color: #86868b; margin-bottom: 2px; text-transform: uppercase;">
                                    ${kode} &bull; ${status}
                                </div>
                                <h4 style="font-size: 12px; font-weight: 600; color: #1d1d1f; margin: 0 0 3px 0;">${judul}</h4>
                                <p style="font-size: 11px; color: #6e6e73; margin: 0 0 5px 0;">${alamat || ''}</p>
                                <a href="${url}" style="font-size: 11px; font-weight: 600; color: #0f5132; text-decoration: none;">Buka Kasus &rarr;</a>
                            </div>
                        `;

                        marker.bindPopup(popupHtml);
                        markers.push({ lat: parseFloat(lat), lng: parseFloat(lng), kode: kode, marker: marker });
                        markersGroup.push(marker);
                    });

                    if (markersGroup.length > 0) {
                        const group = L.featureGroup(markersGroup);
                        try {
                            map.fitBounds(group.getBounds(), { padding: [40, 40], maxZoom: 15 });
                        } catch (e) {}
                    }
                }
            })
            .catch(err => console.warn('Peta geojson preview fallback:', err));
    });

    window.focusMarker = function(lat, lng, kode) {
        if (!map || !lat || !lng) return;
        map.setView([lat, lng], 16, { animate: true });
        
        let target = null;
        if (kode) {
            target = markers.find(m => m.kode === kode);
        }
        if (!target) {
            target = markers.find(m => Math.abs(m.lat - lat) < 0.0005 && Math.abs(m.lng - lng) < 0.0005);
        }

        if (target && target.marker) {
            target.marker.openPopup();
        }
    };
</script>
@endpush
