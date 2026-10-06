@extends('layouts.admin')

@section('title', 'Tambah Titik TPS Baru - Admin SIGALING PADANG')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto pb-12">
    <!-- TOP BAR: Navigation & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.tps.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Data TPS</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-emerald-600">Formulir Perekaman</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Tambah Titik TPS Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Tentukan koordinat geografis presisi dengan klik peta atau seret penanda pin.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tps.index') }}" class="px-4 py-2 rounded-full border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                Batal
            </a>
            <button type="submit" form="form-create-tps" class="px-6 py-2 rounded-full bg-slate-900 hover:bg-black text-white text-xs font-extrabold tracking-tight transition-all shadow-sm hover:shadow-md flex items-center gap-1.5">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Titik TPS</span>
            </button>
        </div>
    </div>

    <!-- MAIN FORM GRID -->
    <form id="form-create-tps" action="{{ route('admin.tps.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- LEFT COLUMN: Field Inputs (Col 7) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Card 1: Identitas & Wilayah -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <i data-lucide="info" class="w-4 h-4 text-emerald-600"></i>
                        <h2 class="text-sm font-extrabold text-slate-900">Identitas & Wilayah Administrasi</h2>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Titik TPS <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_tps" value="{{ old('nama_tps') }}" required placeholder="Contoh: TPS GOR H. Agus Salim" class="w-full px-3.5 py-2.5 rounded-xl border @error('nama_tps') border-rose-500 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">
                            @error('nama_tps')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                                <select id="admin-select-kecamatan" name="kecamatan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">
                                    <option value="">-- Pilih Kecamatan (11) --</option>
                                    @foreach($kecamatans as $kec)
                                        <option value="{{ $kec }}" {{ old('kecamatan') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Kelurahan</label>
                                <input type="text" id="admin-input-kelurahan" name="kelurahan" value="{{ old('kelurahan') }}" placeholder="Contoh: Rimbo Kaluang" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap / Patokan <span class="text-rose-500">*</span></label>
                            <textarea id="admin-input-alamat" name="alamat" rows="2" required placeholder="Jl. Rimbo Kaluang No. 14, Kompleks Parkir Barat GOR H. Agus Salim" class="w-full px-3.5 py-2.5 rounded-xl border @error('alamat') border-rose-500 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium leading-relaxed">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 2: Karakteristik Operasional & Kapasitas -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <i data-lucide="truck" class="w-4 h-4 text-emerald-600"></i>
                        <h2 class="text-sm font-extrabold text-slate-900">Operasional, Armada, & Kapasitas</h2>
                    </div>

                    <div class="space-y-4 text-xs">
                        <!-- Jenis TPS -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-2">Jenis Fasilitas TPS <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @php
                                    $jenisList = ['Kontainer Sampah', 'TPS 3R', 'Transfer Depo', 'Bak Beton'];
                                    $oldJenis = old('jenis_tps', 'Kontainer Sampah');
                                @endphp
                                @foreach($jenisList as $jns)
                                    <label class="flex items-center justify-center p-2.5 rounded-xl border cursor-pointer transition-all text-center text-[11px] font-bold has-checked:bg-slate-900 has-checked:text-white has-checked:border-slate-900 border-slate-200 text-slate-700 hover:bg-slate-50">
                                        <input type="radio" name="jenis_tps" value="{{ $jns }}" class="sr-only" {{ $oldJenis === $jns ? 'checked' : '' }}>
                                        <span>{{ $jns }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Estimasi Kapasitas Tampung</label>
                                <input type="text" name="kapasitas" value="{{ old('kapasitas', '6 m³ (1 Kontainer)') }}" placeholder="Contoh: 12 m³ (2 Kontainer Armroll)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Jadwal Pengangkutan Armada DLH</label>
                                <input type="text" name="jadwal_angkut" value="{{ old('jadwal_angkut', 'Setiap Hari (06:00 WIB)') }}" placeholder="Contoh: Setiap Hari (06:00 & 17:00 WIB)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">
                            </div>
                        </div>

                        <!-- Status Operasional Segmented -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-2">Status Operasional Awal <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @php
                                    $statusList = [
                                        ['key' => 'Aktif', 'color' => 'has-checked:bg-emerald-600 has-checked:text-white has-checked:border-emerald-600'],
                                        ['key' => 'Hampir Penuh', 'color' => 'has-checked:bg-amber-600 has-checked:text-white has-checked:border-amber-600'],
                                        ['key' => 'Penuh', 'color' => 'has-checked:bg-rose-600 has-checked:text-white has-checked:border-rose-600'],
                                        ['key' => 'Perbaikan', 'color' => 'has-checked:bg-slate-700 has-checked:text-white has-checked:border-slate-700'],
                                    ];
                                    $oldStatus = old('status', 'Aktif');
                                @endphp
                                @foreach($statusList as $st)
                                    <label class="flex items-center justify-center p-2.5 rounded-xl border cursor-pointer transition-all text-center text-[11px] font-bold border-slate-200 text-slate-700 hover:bg-slate-50 {{ $st['color'] }}">
                                        <input type="radio" name="status" value="{{ $st['key'] }}" class="sr-only" {{ $oldStatus === $st['key'] ? 'checked' : '' }}>
                                        <span>{{ $st['key'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan khusus akses jalan truk compactor / nomor armada..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-900 font-medium">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Interactive Leaflet Map Pin-Picker (Col 5) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-4 sticky top-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                            <h2 class="text-sm font-extrabold text-slate-900">Pin Peta Koordinat GPS</h2>
                        </div>
                        <button type="button" onclick="getAdminCurrentLocation()" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg hover:bg-emerald-100 transition-colors cursor-pointer">
                            <i id="admin-icon-gps" data-lucide="crosshair" class="w-3.5 h-3.5"></i>
                            <span id="admin-label-gps">Titik Saya</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-normal">
                        Klik pada peta atau seret penanda merah untuk menetapkan posisi presisi titik kontainer.
                    </p>

                    <!-- Peta Container -->
                    <div id="admin-picker-map" class="w-full h-80 rounded-2xl border-2 border-slate-300 ring-1 ring-slate-200/80 overflow-hidden z-10"></div>

                    <!-- Lat / Long Readouts -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[10px] uppercase tracking-wider font-mono font-bold text-slate-400 mb-1">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', '-0.9471000') }}" required readonly class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-wider font-mono font-bold text-slate-400 mb-1">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', '100.3800000') }}" required readonly class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs font-bold text-slate-900">
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl text-[11px] text-slate-500 border border-slate-100 flex items-start gap-2">
                        <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"></i>
                        <span>Sistem otomatis mendeteksi nama jalan dan kecamatan terdekat dari titik peta yang Anda klik.</span>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-full bg-slate-900 hover:bg-black text-white text-xs font-black tracking-tight transition-all shadow-sm hover:shadow flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Titik TPS Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let adminMap;
    let adminMarker;

    const padangKecList = [
        'Padang Barat', 'Padang Timur', 'Padang Utara', 'Padang Selatan',
        'Koto Tangah', 'Kuranji', 'Nanggalo', 'Lubuk Begalung', 'Lubuk Kilangan', 'Pauh', 'Bungus Teluk Kabung'
    ];

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof L === 'undefined') return;

        const initLat = parseFloat(document.getElementById('latitude').value) || -0.9471;
        const initLng = parseFloat(document.getElementById('longitude').value) || 100.3800;

        adminMap = L.map('admin-picker-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([initLat, initLng], 13);

        const streetTileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        const satelliteTileLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Hybrid Satellite'
        });

        streetTileLayer.addTo(adminMap);

        const baseMaps = {
            "🗺️ Peta Standar": streetTileLayer,
            "🛰️ Satelit (Hybrid)": satelliteTileLayer
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(adminMap);

        L.control.zoom({ position: 'bottomright' }).addTo(adminMap);

        adminMarker = L.marker([initLat, initLng], { draggable: true }).addTo(adminMap);

        setTimeout(() => adminMap.invalidateSize(), 300);
        window.addEventListener('resize', () => adminMap.invalidateSize());

        adminMarker.on('dragend', function (e) {
            const coord = e.target.getLatLng();
            setAdminCoords(coord.lat, coord.lng);
            reverseGeocodeAdmin(coord.lat, coord.lng);
        });

        adminMap.on('click', function (e) {
            adminMarker.setLatLng(e.latlng);
            setAdminCoords(e.latlng.lat, e.latlng.lng);
            reverseGeocodeAdmin(e.latlng.lat, e.latlng.lng);
        });
    });

    function setAdminCoords(lat, lng) {
        document.getElementById('latitude').value = Number(lat).toFixed(7);
        document.getElementById('longitude').value = Number(lng).toFixed(7);
    }

    function reverseGeocodeAdmin(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.address) {
                    const addr = data.address;
                    const district = addr.city_district || addr.suburb || addr.municipality || '';
                    
                    for (let k of padangKecList) {
                        if (district.toLowerCase().includes(k.toLowerCase()) || (data.display_name && data.display_name.toLowerCase().includes(k.toLowerCase()))) {
                            const selectKec = document.getElementById('admin-select-kecamatan');
                            if (selectKec && !selectKec.value) {
                                selectKec.value = k;
                            }
                            break;
                        }
                    }

                    const inputKel = document.getElementById('admin-input-kelurahan');
                    const kel = addr.village || addr.quarter || addr.suburb || '';
                    if (inputKel && kel && !inputKel.value) {
                        inputKel.value = kel;
                    }

                    const inputAlamat = document.getElementById('admin-input-alamat');
                    const road = addr.road || addr.pedestrian || addr.neighbourhood || '';
                    if (inputAlamat && !inputAlamat.value) {
                        inputAlamat.value = road ? (road + (kel ? ', ' + kel : '')) : (data.name || '');
                    }
                }
            })
            .catch(() => {});
    }

    window.getAdminCurrentLocation = function () {
        const icon = document.getElementById('admin-icon-gps');
        const label = document.getElementById('admin-label-gps');

        if (!navigator.geolocation) {
            alert('Browser tidak mendukung GPS.');
            return;
        }

        if (label) label.textContent = 'Mendeteksi...';
        if (icon) icon.classList.add('animate-spin');

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                if (adminMap) adminMap.flyTo([lat, lng], 17, { duration: 1 });
                if (adminMarker) adminMarker.setLatLng([lat, lng]);
                setAdminCoords(lat, lng);
                reverseGeocodeAdmin(lat, lng);

                if (label) label.textContent = 'Titik Saya';
                if (icon) icon.classList.remove('animate-spin');
            },
            function (err) {
                alert('Gagal mendeteksi lokasi GPS: ' + (err.message || 'Izin ditolak.'));
                if (label) label.textContent = 'Titik Saya';
                if (icon) icon.classList.remove('animate-spin');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };
</script>
@endpush
