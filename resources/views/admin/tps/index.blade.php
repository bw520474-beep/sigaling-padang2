@extends('layouts.admin')

@section('title', 'Kelola Titik TPS - Admin SIGALING PADANG')

@section('content')
<div class="space-y-6">
    <!-- HEADER BAR: High-impact Nike/Puma typography & sharp Apple action buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] uppercase tracking-widest font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Master Data DLH</span>
                <span class="text-xs font-bold text-emerald-600">Kota Padang</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Manajemen Titik TPS & Kontainer
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pengelolaan koordinat GPS, kapasitas tampung, dan jadwal pengangkutan 11 kecamatan di Kota Padang.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tps.index') }}" target="_blank" class="px-4 py-2.5 rounded-full border border-slate-300 hover:border-slate-400 text-slate-700 hover:text-slate-900 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="map" class="w-3.5 h-3.5"></i>
                <span>Peta Publik</span>
            </a>

            <a href="{{ route('admin.tps.create') }}" class="px-5 py-2.5 rounded-full bg-slate-900 hover:bg-black text-white text-xs font-extrabold tracking-tight transition-all flex items-center gap-2 shadow-sm hover:shadow-md">
                <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                <span>Tambah Titik TPS Baru</span>
            </a>
        </div>
    </div>

    <!-- METRICS STRIP: Nike Sport/Tech KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-slate-100 rounded-full group-hover:scale-110 transition-transform"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block relative z-10">Total Titik Terdata</span>
            <div class="flex items-baseline gap-2 mt-1 relative z-10">
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalTps }}</span>
                <span class="text-xs font-bold text-slate-500">Titik TPS</span>
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">11 Kecamatan se-Kota Padang</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block relative z-10">Operasional Aktif</span>
            <div class="flex items-baseline gap-2 mt-1 relative z-10">
                <span class="text-3xl font-black text-emerald-600 tracking-tight">{{ $totalAktif }}</span>
                <span class="text-xs font-bold text-emerald-700">Beroperasi</span>
            </div>
            <span class="text-[11px] text-emerald-600/80 mt-1 block">Rutin diangkut armada DLH</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-amber-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block relative z-10">Perlu Pengangkutan</span>
            <div class="flex items-baseline gap-2 mt-1 relative z-10">
                <span class="text-3xl font-black text-amber-600 tracking-tight">{{ $totalPenuh }}</span>
                <span class="text-xs font-bold text-amber-700">Penuh/Kritis</span>
            </div>
            <span class="text-[11px] text-amber-600/80 mt-1 block">Perlu prioritas armada truk</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-cyan-50 rounded-full group-hover:scale-110 transition-transform"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block relative z-10">Fasilitas TPS 3R</span>
            <div class="flex items-baseline gap-2 mt-1 relative z-10">
                <span class="text-3xl font-black text-cyan-600 tracking-tight">{{ $total3R }}</span>
                <span class="text-xs font-bold text-cyan-700">Unit 3R</span>
            </div>
            <span class="text-[11px] text-cyan-600/80 mt-1 block">Pemilahan & bank sampah</span>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR (Apple-style clean segmented controls) -->
    <div class="bg-white rounded-3xl p-4 border border-slate-200/90 shadow-2xs space-y-3">
        <form action="{{ route('admin.tps.index') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="w-full md:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                <!-- Search -->
                <div class="relative w-full sm:w-72">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, kode, jalan, kelurahan..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <!-- Select Kecamatan -->
                <div class="w-full sm:w-48">
                    <select name="kecamatan" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-slate-900">
                        <option value="">Semua Kecamatan (11)</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec }}" {{ $selectedKecamatan === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Status -->
                <div class="w-full sm:w-36">
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50/50 text-slate-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-slate-900">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ $statusFilter === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Hampir Penuh" {{ $statusFilter === 'Hampir Penuh' ? 'selected' : '' }}>Hampir Penuh</option>
                        <option value="Penuh" {{ $statusFilter === 'Penuh' ? 'selected' : '' }}>Penuh</option>
                        <option value="Perbaikan" {{ $statusFilter === 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                @if($search || $selectedKecamatan || $statusFilter)
                    <a href="{{ route('admin.tps.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 px-2 py-1">
                        Reset
                    </a>
                @endif
            </div>

            <div class="w-full md:w-auto text-right text-xs text-slate-400">
                Menampilkan <strong>{{ $tpsList->count() }}</strong> dari <strong>{{ $tpsList->total() }}</strong> titik
            </div>
        </form>
    </div>

    <!-- DATA TABLE: High-Contrast Puma / Nike Sports Minimalist Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-slate-300 uppercase tracking-widest font-extrabold text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Kode & Nama TPS</th>
                        <th class="px-5 py-4">Kecamatan / Kelurahan</th>
                        <th class="px-5 py-4">Alamat & GPS</th>
                        <th class="px-5 py-4">Jenis & Kapasitas</th>
                        <th class="px-5 py-4">Jadwal Angkut</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tpsList as $tps)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Kode & Nama -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[10px] font-black px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $tps->kode_tps }}
                                    </span>
                                </div>
                                <span class="font-extrabold text-sm text-slate-900 block mt-1 group-hover:text-emerald-700 transition-colors">
                                    {{ $tps->nama_tps }}
                                </span>
                            </td>

                            <!-- Kecamatan / Kelurahan -->
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 block">Kec. {{ $tps->kecamatan }}</span>
                                <span class="text-[11px] text-slate-500">Kel. {{ $tps->kelurahan ?? '-' }}</span>
                            </td>

                            <!-- Alamat & GPS -->
                            <td class="px-5 py-4 max-w-xs">
                                <span class="text-slate-700 line-clamp-1 font-medium block" title="{{ $tps->alamat }}">{{ $tps->alamat }}</span>
                                <a href="{{ $tps->google_maps_url }}" target="_blank" class="text-[10px] font-mono text-emerald-600 hover:underline inline-flex items-center gap-0.5 mt-0.5">
                                    <span>{{ number_format($tps->latitude, 5) }}, {{ number_format($tps->longitude, 5) }}</span>
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            </td>

                            <!-- Jenis & Kapasitas -->
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800 block">{{ $tps->jenis_tps }}</span>
                                <span class="text-[11px] text-slate-500">{{ $tps->kapasitas ?? 'Standar' }}</span>
                            </td>

                            <!-- Jadwal -->
                            <td class="px-5 py-4">
                                <span class="text-slate-700 font-medium block">{{ $tps->jadwal_angkut ?? 'Harian' }}</span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $tps->status_badge_class }} inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $tps->status_dot_class }}"></span>
                                    <span>{{ $tps->status }}</span>
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.tps.edit', $tps->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Edit TPS">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <form action="{{ route('admin.tps.destroy', $tps->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus titik TPS {{ $tps->nama_tps }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Hapus TPS">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <i data-lucide="map-pin-off" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                <span class="font-bold text-slate-600 block">Belum ada data titik TPS</span>
                                <span class="text-xs text-slate-400">Klik tombol "Tambah Titik TPS Baru" untuk merekam lokasi pertama.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR -->
        @if($tpsList->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tpsList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
