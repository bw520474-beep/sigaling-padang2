@extends('layouts.app')

@section('title', 'Buat Laporan Permasalahan Publik — SIGALING')

@section('content')
    <!-- ===== APPLE EDITORIAL HEADER ===== -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">
        <div class="space-y-2.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-[11px] font-medium text-slate-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Global Civic Reporting &bull; Smart Public Issue Management</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-semibold text-[#1d1d1f] tracking-tight">
                Laporkan Masalah Publik
            </h1>
            <p class="text-sm text-[#6e6e73] max-w-2xl leading-relaxed">
                Cukup sampaikan permasalahan di lingkungan Anda. SIGALING secara cerdas menentukan instansi yang berwenang tanpa mengharuskan Anda memahami struktur birokrasi pemerintahan.
            </p>
        </div>
    </section>

    <!-- ===== MAIN FORM CONTAINER ===== -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24"
         x-data="civicReportForm({
            categories: {{ Js::from($kategoriList) }},
            selectedCategory: '{{ old('kategori_id', $selectedKategori ?? '') }}',
            selectedSubcategory: '{{ old('subcategory_id', $selectedSubkategori ?? '') }}',
            protectionLevel: '{{ old('protection_level', 'public') }}',
            judul: '{{ old('judul', '') }}',
            deskripsi: '{{ old('deskripsi', '') }}'
         })">

        <form action="{{ route('lapor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf

            <!-- ===== SMART CLASSIFIER LIVE BANNER ===== -->
            <div x-show="smartSuggestion.matched" 
                 x-transition:enter="transition ease-out duration-300 transform opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-50/90 via-sky-50/70 to-slate-50 border border-emerald-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-emerald-950 uppercase tracking-wider">Rekomendasi Cerdas SIGALING</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">Smart Classification</span>
                        </div>
                        <p class="text-xs text-slate-700 mt-1">
                            Permasalahan ini terdeteksi sebagai: <strong class="text-slate-900" x-text="smartSuggestion.subcategory?.name"></strong> 
                            &bull; Disposisi otomatis ke: <strong class="text-slate-900" x-text="smartSuggestion.routing?.organization?.name"></strong>
                        </p>
                    </div>
                </div>
                <button type="button" 
                        @click="applySuggestion()" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-medium transition-all shadow-xs flex-shrink-0 cursor-pointer flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Klasifikasi</span>
                </button>
            </div>

            <!-- STAGE 01: KLASIFIKASI & SUBKATEGORI -->
            <div class="hairline-t pt-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                    <div>
                        <span class="text-xs font-mono text-[#86868b] block">01 / Klasifikasi Masalah</span>
                        <h2 class="text-lg font-semibold text-[#1d1d1f] tracking-tight mt-0.5">Pilih Kategori & Isu Spesifik</h2>
                        <p class="text-xs text-[#86868b]">Pilih rumpun permasalahan agar disposisi instansi berjalan instan dan akurat.</p>
                    </div>

                    <!-- Search filter -->
                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[#86868b] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <input type="text" x-model="searchFilter" placeholder="Cari jenis isu..." 
                               class="w-full pl-9 pr-3 py-1.5 rounded-full border border-[#e5e5ea] text-xs bg-white text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:border-[#1d1d1f]">
                    </div>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="kategori_id" x-model="selectedCategory" required>
                <input type="hidden" name="subcategory_id" x-model="selectedSubcategory">

                <!-- 1. Category Selection Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <template x-for="cat in filteredCategories" :key="cat.id">
                        <button type="button"
                                @click="selectCategory(cat.id)"
                                class="p-3.5 rounded-xl text-left border transition-all cursor-pointer flex flex-col justify-between"
                                :class="selectedCategory == cat.id 
                                    ? 'border-[#1d1d1f] bg-slate-900 text-white shadow-xs' 
                                    : 'border-[#e5e5ea] bg-white hover:border-[#d2d2d7] text-slate-900'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold leading-tight line-clamp-1" x-text="cat.nama_kategori"></span>
                                <span class="w-2 h-2 rounded-full" :class="selectedCategory == cat.id ? 'bg-emerald-400' : 'bg-transparent'"></span>
                            </div>
                            <span class="text-[10px]" :class="selectedCategory == cat.id ? 'text-slate-300' : 'text-slate-500'" x-text="(cat.subcategories?.length || 0) + ' jenis isu'"></span>
                        </button>
                    </template>
                </div>

                @error('kategori_id')
                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror

                <!-- 2. Subcategory Pills / Details (Muncul saat Kategori dipilih) -->
                <div x-show="currentSubcategories.length > 0" 
                     x-transition 
                     class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-800">Spesifikasi Permasalahan (Subkategori)</span>
                        <span class="text-[11px] text-slate-500">Membantu router menentukan dinas & unit teknis</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <template x-for="sub in currentSubcategories" :key="sub.id">
                            <button type="button"
                                    @click="selectedSubcategory = sub.id"
                                    class="px-3.5 py-2 rounded-xl text-xs font-medium border transition-all cursor-pointer flex items-center gap-2"
                                    :class="selectedSubcategory == sub.id 
                                        ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' 
                                        : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="selectedSubcategory == sub.id ? 'bg-emerald-400' : 'bg-slate-300'"></span>
                                <span x-text="sub.name"></span>
                                <span class="text-[10px] opacity-70" x-text="'(' + sub.default_priority + ')'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- STAGE 02: RINCIAN PERMASALAHAN -->
            <div class="hairline-t pt-8 space-y-6">
                <div>
                    <span class="text-xs font-mono text-[#86868b] block">02 / Uraian Masalah</span>
                    <h2 class="text-lg font-semibold text-[#1d1d1f] tracking-tight mt-0.5">Judul & Fakta Lapangan</h2>
                    <p class="text-xs text-[#86868b]">Jelaskan pokok permasalahan secara ringkas dan objektif. Sistem akan menganalisis teks secara langsung.</p>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-[#1d1d1f]">Judul Ringkas Pengaduan <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="judul" @input="triggerSmartSuggestionDebounced()" required 
                               placeholder="Contoh: Jalan berlubang parah di dekat jembatan / Lampu merah mati" 
                               class="w-full px-4 py-2.5 rounded-xl border @error('judul') border-rose-500 @else border-[#e5e5ea] @enderror text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f] shadow-2xs">
                        @error('judul')
                            <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-[#1d1d1f]">Deskripsi Lengkap Kejadian <span class="text-rose-500">*</span></label>
                        <textarea name="deskripsi" x-model="deskripsi" @input="triggerSmartSuggestionDebounced()" rows="4" required 
                                  placeholder="Jelaskan kondisi detail, dampak terhadap keselamatan atau kenyamanan warga, dan sudah berapa lama masalah berlangsung..." 
                                  class="w-full px-4 py-2.5 rounded-xl border @error('deskripsi') border-rose-500 @else border-[#e5e5ea] @enderror text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f] leading-relaxed shadow-2xs"></textarea>
                        @error('deskripsi')
                            <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- STAGE 03: PERLINDUNGAN IDENTITAS PELAPOR -->
            <div class="hairline-t pt-8 space-y-6">
                <div>
                    <span class="text-xs font-mono text-[#86868b] block">03 / Privasi & Perlindungan Pelapor</span>
                    <h2 class="text-lg font-semibold text-[#1d1d1f] tracking-tight mt-0.5">Tingkat Perlindungan Identitas</h2>
                    <p class="text-xs text-[#86868b]">Pilih cara identitas Anda dikelola. Data identitas disimpan terpisah demi keamanan pelapor.</p>
                </div>

                <input type="hidden" name="protection_level" x-model="protectionLevel">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Option 1: Public -->
                    <button type="button" 
                            @click="protectionLevel = 'public'"
                            class="p-4 rounded-xl text-left border transition-all cursor-pointer flex flex-col justify-between"
                            :class="protectionLevel === 'public' ? 'border-slate-900 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300 text-slate-800'">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold">Publik</span>
                                <i data-lucide="globe" class="w-4 h-4" :class="protectionLevel === 'public' ? 'text-emerald-400' : 'text-slate-400'"></i>
                            </div>
                            <p class="text-[11px] leading-relaxed" :class="protectionLevel === 'public' ? 'text-slate-300' : 'text-slate-500'">
                                Identitas nama dapat ditampilkan sesuai kebijakan keterbukaan pengaduan umum.
                            </p>
                        </div>
                        <span class="text-[10px] mt-3 font-mono font-medium" :class="protectionLevel === 'public' ? 'text-emerald-300' : 'text-slate-400'">Standard Mode</span>
                    </button>

                    <!-- Option 2: Confidential -->
                    <button type="button" 
                            @click="protectionLevel = 'confidential'"
                            class="p-4 rounded-xl text-left border transition-all cursor-pointer flex flex-col justify-between"
                            :class="protectionLevel === 'confidential' ? 'border-slate-900 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300 text-slate-800'">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold">Rahasia (Confidential)</span>
                                <i data-lucide="lock" class="w-4 h-4" :class="protectionLevel === 'confidential' ? 'text-amber-400' : 'text-slate-400'"></i>
                            </div>
                            <p class="text-[11px] leading-relaxed" :class="protectionLevel === 'confidential' ? 'text-slate-300' : 'text-slate-500'">
                                Identitas hanya diketahui supervisor berwenang. Disembunyikan dari publik dan petugas biasa.
                            </p>
                        </div>
                        <span class="text-[10px] mt-3 font-mono font-medium" :class="protectionLevel === 'confidential' ? 'text-amber-300' : 'text-slate-400'">Protected Officer Access</span>
                    </button>

                    <!-- Option 3: Anonymous -->
                    <button type="button" 
                            @click="protectionLevel = 'anonymous'"
                            class="p-4 rounded-xl text-left border transition-all cursor-pointer flex flex-col justify-between"
                            :class="protectionLevel === 'anonymous' ? 'border-slate-900 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300 text-slate-800'">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold">Anonim (Anonymous)</span>
                                <i data-lucide="user-x" class="w-4 h-4" :class="protectionLevel === 'anonymous' ? 'text-sky-400' : 'text-slate-400'"></i>
                            </div>
                            <p class="text-[11px] leading-relaxed" :class="protectionLevel === 'anonymous' ? 'text-slate-300' : 'text-slate-500'">
                                Identitas sepenuhnya dirahasiakan dari seluruh dinas. Pantau laporan lewat Kode Laporan unik.
                            </p>
                        </div>
                        <span class="text-[10px] mt-3 font-mono font-medium" :class="protectionLevel === 'anonymous' ? 'text-sky-300' : 'text-slate-400'">Zero-Knowledge Tracking</span>
                    </button>
                </div>
            </div>

            <!-- STAGE 04: LOKASI & KOORDINAT PETA -->
            <div class="hairline-t pt-8 space-y-6">
                <div>
                    <span class="text-xs font-mono text-[#86868b] block">04 / Lokasi Kejadian</span>
                    <h2 class="text-lg font-semibold text-[#1d1d1f] tracking-tight mt-0.5">Wilayah & Koordinat GPS Presisi</h2>
                    <p class="text-xs text-[#86868b]">Geser penanda pada peta atau gunakan sensor GPS perangkat Anda.</p>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-[#1d1d1f]">Kecamatan / Wilayah Administrasi</label>
                            <select id="select-kecamatan" name="kecamatan" class="w-full px-3 py-2 rounded-xl border border-[#e5e5ea] text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                                <option value="">-- Pilih Wilayah / Kecamatan --</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->name }}" {{ old('kecamatan') == $d->name ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-medium text-[#1d1d1f]">Kelurahan / Area (Opsional)</label>
                            <input type="text" id="input-kelurahan" name="kelurahan" value="{{ old('kelurahan') }}" placeholder="Contoh: Korong Gadang / Belanti" class="w-full px-3 py-2 rounded-xl border border-[#e5e5ea] text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-[#1d1d1f]">Alamat Jalan / Patokan Lokasi Spesifik <span class="text-rose-500">*</span></label>
                        <input type="text" id="input-alamat" name="alamat" value="{{ old('alamat') }}" required placeholder="Contoh: Jl. Khatib Sulaiman No. 45, depan ruko fotocopy dekat SPBU" class="w-full px-4 py-2 rounded-xl border @error('alamat') border-rose-500 @else border-[#e5e5ea] @enderror text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                        @error('alamat')
                            <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Peta GPS Picker -->
                    <div class="pt-2">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="text-xs text-[#86868b]">Geser penanda ke titik kejadian atau deteksi otomatis:</span>
                            <div class="flex items-center gap-2">
                                <div class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-xs">
                                    <button type="button" id="picker-btn-street" onclick="switchPickerMapMode('street')" class="px-2 py-1 rounded font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1">
                                        <span>🗺️ Standar</span>
                                    </button>
                                    <button type="button" id="picker-btn-satellite" onclick="switchPickerMapMode('satellite')" class="px-2 py-1 rounded font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1">
                                        <span>🛰️ Satelit</span>
                                    </button>
                                </div>

                                <button type="button" id="btn-gps" onclick="getCurrentLocation()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold hover:bg-emerald-100 transition-all cursor-pointer">
                                    <i id="icon-gps" data-lucide="crosshair" class="w-3.5 h-3.5 text-emerald-700"></i>
                                    <span id="label-gps">Gunakan Lokasi Saya Sekarang</span>
                                </button>
                            </div>
                        </div>

                        <!-- Feedback banner GPS -->
                        <div id="gps-feedback" class="hidden mb-2 p-2.5 rounded-xl text-xs font-medium"></div>

                        <div id="picker-map" class="w-full h-64 rounded-xl border-2 border-slate-300 ring-1 ring-slate-200/80 overflow-hidden z-10"></div>

                        <div class="grid grid-cols-2 gap-4 mt-2 text-xs">
                            <div>
                                <span class="text-[11px] text-[#86868b] block mb-0.5">Latitude</span>
                                <input type="text" id="latitude" name="latitude" value="{{ old('latitude', '-0.9471') }}" readonly class="w-full px-3 py-1.5 bg-[#f5f5f7] border border-[#e5e5ea] rounded-lg font-mono text-xs text-[#1d1d1f]">
                            </div>
                            <div>
                                <span class="text-[11px] text-[#86868b] block mb-0.5">Longitude</span>
                                <input type="text" id="longitude" name="longitude" value="{{ old('longitude', '100.4172') }}" readonly class="w-full px-3 py-1.5 bg-[#f5f5f7] border border-[#e5e5ea] rounded-lg font-mono text-xs text-[#1d1d1f]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 05: BUKTI DOKUMENTASI & IDENTITAS TAMBAHAN -->
            <div class="hairline-t pt-8 space-y-6">
                <div>
                    <span class="text-xs font-mono text-[#86868b] block">05 / Bukti & Data Pelapor</span>
                    <h2 class="text-lg font-semibold text-[#1d1d1f] tracking-tight mt-0.5">Foto Dokumentasi & Kontak</h2>
                    <p class="text-xs text-[#86868b]">Dokumentasi visual mempercepat validasi oleh petugas penanganan lapangan.</p>
                </div>

                <div class="space-y-6">
                    <!-- Photo upload -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-[#1d1d1f]">Unggah Foto Bukti Kejadian</label>
                        <div class="p-6 border border-dashed border-[#d2d2d7] rounded-xl text-center bg-white">
                            <i data-lucide="image" class="w-6 h-6 text-[#86868b] mx-auto mb-2"></i>
                            <p class="text-xs font-medium text-[#1d1d1f]">Pilih Foto Dokumentasi</p>
                            <p class="text-[11px] text-[#86868b] mt-0.5">JPG, PNG, atau WEBP (maks. 5 MB)</p>
                            <input type="file" name="foto[]" multiple accept="image/*" class="mt-3 block w-full text-xs text-[#6e6e73] file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-[#1d1d1f] file:text-white hover:file:bg-black cursor-pointer">
                        </div>
                    </div>

                    <!-- Reporter info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-[#1d1d1f]">
                                Nama Pelapor 
                                <span x-show="protectionLevel !== 'anonymous'" class="text-rose-500">*</span>
                                <span x-show="protectionLevel === 'anonymous'" class="text-slate-400 font-normal">(Otomatis disamarkan)</span>
                            </label>
                            <input type="text" name="nama_pelapor" 
                                   :placeholder="protectionLevel === 'anonymous' ? 'Anonim (Dikosongkan / Dirahasiakan)' : 'Nama lengkap Anda'"
                                   :required="protectionLevel !== 'anonymous'"
                                   value="{{ old('nama_pelapor', $user->nama ?? '') }}" 
                                   class="w-full px-3 py-2 rounded-xl border @error('nama_pelapor') border-rose-500 @else border-[#e5e5ea] @enderror text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                            @error('nama_pelapor')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-medium text-[#1d1d1f]">Nomor WhatsApp / HP (Opsional)</label>
                            <input type="text" name="kontak_pelapor" value="{{ old('kontak_pelapor', $user->no_hp ?? '') }}" placeholder="Contoh: 081234567890" class="w-full px-3 py-2 rounded-xl border border-[#e5e5ea] text-xs bg-white text-[#1d1d1f] focus:outline-none focus:border-[#1d1d1f]">
                            <span class="text-[11px] text-[#86868b] block">Digunakan jika petugas membutuhkan titik temu di lokasi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMISSION BAR -->
            <div class="hairline-t pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-1">
                    <p class="text-xs text-[#86868b] max-w-md">
                        Laporan akan otomatis disalurkan ke sistem routing instansi terkait berdasarkan kewenangan wilayah.
                    </p>
                    <span class="text-[11px] text-emerald-700 font-medium flex items-center gap-1">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span>Privasi & perlindungan pelapor dijamin oleh sistem SIGALING.</span>
                    </span>
                </div>
                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-full bg-[#1d1d1f] hover:bg-black text-white text-xs font-semibold transition-all flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer shadow-sm">
                    <span>Kirim Pengaduan Publik</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function civicReportForm(initial) {
        return {
            categories: initial.categories || [],
            selectedCategory: initial.selectedCategory || '',
            selectedSubcategory: initial.selectedSubcategory || '',
            protectionLevel: initial.protectionLevel || 'public',
            judul: initial.judul || '',
            deskripsi: initial.deskripsi || '',
            searchFilter: '',
            debounceTimer: null,
            smartSuggestion: { matched: false },

            get filteredCategories() {
                if (!this.searchFilter) return this.categories;
                const q = this.searchFilter.toLowerCase();
                return this.categories.filter(c => 
                    c.nama_kategori.toLowerCase().includes(q) ||
                    (c.subcategories && c.subcategories.some(s => s.name.toLowerCase().includes(q)))
                );
            },

            get currentSubcategories() {
                const cat = this.categories.find(c => c.id == this.selectedCategory);
                return cat ? (cat.subcategories || []) : [];
            },

            selectCategory(catId) {
                this.selectedCategory = catId;
                const cat = this.categories.find(c => c.id == catId);
                if (cat && cat.subcategories && cat.subcategories.length > 0) {
                    this.selectedSubcategory = cat.subcategories[0].id;
                } else {
                    this.selectedSubcategory = '';
                }
            },

            triggerSmartSuggestionDebounced() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.fetchSuggestion();
                }, 400);
            },

            fetchSuggestion() {
                const fullText = (this.judul + ' ' + this.deskripsi).trim();
                if (fullText.length < 5) {
                    this.smartSuggestion = { matched: false };
                    return;
                }

                fetch('{{ route("api.suggest-routing") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ text: fullText })
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.matched) {
                        this.smartSuggestion = data;
                        if (window.lucide && typeof window.lucide.createIcons === 'function') {
                            setTimeout(() => window.lucide.createIcons(), 50);
                        }
                    } else {
                        this.smartSuggestion = { matched: false };
                    }
                })
                .catch(() => {
                    this.smartSuggestion = { matched: false };
                });
            },

            applySuggestion() {
                if (this.smartSuggestion && this.smartSuggestion.matched) {
                    this.selectedCategory = this.smartSuggestion.category.id;
                    this.selectedSubcategory = this.smartSuggestion.subcategory.id;
                    this.smartSuggestion.matched = false;
                }
            }
        };
    }

    let pickerMap;
    let pickerMarker;

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof L === 'undefined') return;

        if (L.Icon && L.Icon.Default && L.Icon.Default.prototype) {
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: '{{ asset("vendor/leaflet/images/marker-icon-2x.png") }}',
                iconUrl: '{{ asset("vendor/leaflet/images/marker-icon.png") }}',
                shadowUrl: '{{ asset("vendor/leaflet/images/marker-shadow.png") }}',
            });
        }

        const initLat = parseFloat(document.getElementById('latitude').value) || -0.9471;
        const initLng = parseFloat(document.getElementById('longitude').value) || 100.4172;

        pickerMap = L.map('picker-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([initLat, initLng], 13);

        let streetTileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        let satelliteTileLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '&copy; Google Hybrid Satellite'
        });

        streetTileLayer.addTo(pickerMap);

        const baseMaps = {
            "🗺️ Peta Standar": streetTileLayer,
            "🛰️ Satelit (Hybrid)": satelliteTileLayer
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(pickerMap);
        L.control.zoom({ position: 'bottomright' }).addTo(pickerMap);

        pickerMarker = L.marker([initLat, initLng], { draggable: true }).addTo(pickerMap);

        pickerMap.on('baselayerchange', function (e) {
            updatePickerSwitcherButtons(e.layer === satelliteTileLayer ? 'satellite' : 'street');
        });

        function updatePickerSwitcherButtons(mode) {
            const btnStreet = document.getElementById('picker-btn-street');
            const btnSat = document.getElementById('picker-btn-satellite');
            if (mode === 'satellite') {
                if (btnSat) btnSat.className = 'px-2 py-1 rounded font-bold bg-emerald-600 text-white shadow-2xs transition-all cursor-pointer flex items-center gap-1';
                if (btnStreet) btnStreet.className = 'px-2 py-1 rounded font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            } else {
                if (btnStreet) btnStreet.className = 'px-2 py-1 rounded font-bold bg-white text-slate-900 shadow-2xs transition-all cursor-pointer flex items-center gap-1';
                if (btnSat) btnSat.className = 'px-2 py-1 rounded font-medium text-slate-600 hover:text-slate-900 transition-all cursor-pointer flex items-center gap-1';
            }
        }

        window.switchPickerMapMode = function (mode) {
            if (!pickerMap || !streetTileLayer || !satelliteTileLayer) return;
            if (mode === 'satellite') {
                if (pickerMap.hasLayer(streetTileLayer)) pickerMap.removeLayer(streetTileLayer);
                if (!pickerMap.hasLayer(satelliteTileLayer)) satelliteTileLayer.addTo(pickerMap);
                updatePickerSwitcherButtons('satellite');
            } else {
                if (pickerMap.hasLayer(satelliteTileLayer)) pickerMap.removeLayer(satelliteTileLayer);
                if (!pickerMap.hasLayer(streetTileLayer)) streetTileLayer.addTo(pickerMap);
                updatePickerSwitcherButtons('street');
            }
        };

        setTimeout(() => pickerMap.invalidateSize(), 300);
        window.addEventListener('resize', () => pickerMap.invalidateSize());

        pickerMarker.on('dragend', function (e) {
            const coord = e.target.getLatLng();
            updateCoords(coord.lat, coord.lng);
            showGpsFeedback('success', 'Titik lokasi dipindahkan ke: ' + Number(coord.lat).toFixed(5) + ', ' + Number(coord.lng).toFixed(5));
        });

        pickerMap.on('click', function (e) {
            pickerMarker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
            showGpsFeedback('success', 'Titik lokasi ditentukan di: ' + Number(e.latlng.lat).toFixed(5) + ', ' + Number(e.latlng.lng).toFixed(5));
        });

        const selectKecEl = document.getElementById('select-kecamatan');
        if (selectKecEl) {
            selectKecEl.addEventListener('change', function () {
                const val = this.value;
                const kecamatanCoords = {
                    'Padang Barat': [-0.9350, 100.3550],
                    'Padang Timur': [-0.9400, 100.3750],
                    'Padang Utara': [-0.9050, 100.3550],
                    'Padang Selatan': [-0.9700, 100.3650],
                    'Koto Tangah': [-0.8400, 100.3400],
                    'Kuranji': [-0.9100, 100.4100],
                    'Nanggalo': [-0.9000, 100.3700],
                    'Lubuk Begalung': [-0.9850, 100.3950],
                    'Lubuk Kilangan': [-0.9650, 100.4500],
                    'Pauh': [-0.9150, 100.4600],
                    'Bungus Teluk Kabung': [-1.0700, 100.4100]
                };
                if (kecamatanCoords[val]) {
                    const [kLat, kLng] = kecamatanCoords[val];
                    pickerMap.flyTo([kLat, kLng], 14, { duration: 1.2 });
                    pickerMarker.setLatLng([kLat, kLng]);
                    updateCoords(kLat, kLng);
                    showGpsFeedback('success', 'Peta diarahkan ke pusat ' + val + '. Klik titik spesifik pada peta.');
                }
            });
        }
    });

    function updateCoords(lat, lng) {
        document.getElementById('latitude').value = Number(lat).toFixed(7);
        document.getElementById('longitude').value = Number(lng).toFixed(7);
    }

    function showGpsFeedback(type, message) {
        const feedback = document.getElementById('gps-feedback');
        if (!feedback) return;
        feedback.className = type === 'success' 
            ? 'mb-2 p-3 rounded-xl text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-start gap-2.5'
            : 'mb-2 p-3 rounded-xl text-xs font-medium bg-amber-50 text-amber-900 border border-amber-200 flex items-start gap-2.5';
        feedback.innerHTML = type === 'success' 
            ? '<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i><div class="flex-1 leading-relaxed">' + message + '</div>'
            : '<i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i><div class="flex-1 leading-relaxed">' + message + '</div>';
        feedback.classList.remove('hidden');
        if (window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
    }

    window.getCurrentLocation = function () {
        const btn = document.getElementById('btn-gps');
        const icon = document.getElementById('icon-gps');
        const label = document.getElementById('label-gps');

        if (!navigator.geolocation) {
            showGpsFeedback('error', 'Browser Anda tidak mendukung fitur deteksi lokasi GPS.');
            return;
        }

        if (btn) btn.disabled = true;
        if (label) label.textContent = 'Mendeteksi koordinat GPS presisi...';
        if (icon) icon.classList.add('animate-spin');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = Math.round(position.coords.accuracy || 0);

                if (pickerMap) pickerMap.flyTo([lat, lng], 17, { duration: 1.2 });
                if (pickerMarker) pickerMarker.setLatLng([lat, lng]);
                updateCoords(lat, lng);

                let feedbackMsg = 'Koordinat GPS akurat diperoleh (Akurasi ±' + accuracy + ' meter).';

                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        const inputAlamat = document.getElementById('input-alamat');
                        const detectedRoad = addr.road || addr.pedestrian || '';
                        if (inputAlamat && (!inputAlamat.value || inputAlamat.value.trim() === '')) {
                            inputAlamat.value = detectedRoad ? (detectedRoad + (addr.suburb ? ', ' + addr.suburb : '')) : (data.name || '');
                        }
                    }
                    showGpsFeedback('success', feedbackMsg);
                })
                .catch(() => showGpsFeedback('success', feedbackMsg))
                .finally(() => {
                    if (btn) btn.disabled = false;
                    if (label) label.textContent = 'Gunakan Lokasi Saya Sekarang';
                    if (icon) icon.classList.remove('animate-spin');
                });
            },
            function (error) {
                if (pickerMap) pickerMap.flyTo([-0.9471, 100.3543], 15, { duration: 1.2 });
                showGpsFeedback('warning', 'Akses lokasi terbatas. Silakan klik langsung titik jalan pada peta.');
                if (btn) btn.disabled = false;
                if (label) label.textContent = 'Gunakan Lokasi Saya Sekarang';
                if (icon) icon.classList.remove('animate-spin');
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
        );
    };
</script>
@endpush
