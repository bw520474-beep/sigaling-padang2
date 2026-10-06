@extends('layouts.petugas')

@section('title', 'Eksekusi Tugas Lapangan - Petugas SIGALING PADANG')

@section('content')
    <div class="mb-6">
        <a href="{{ route('petugas.tugas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-purple-700 mb-2 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Tugas
        </a>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="text-xs font-mono font-bold bg-slate-200 text-slate-700 px-2.5 py-1 rounded-md">
                    {{ $penanganan->laporan->kode_laporan ?? '-' }}
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $penanganan->laporan->judul ?? 'Detail Tugas' }}</h1>
            </div>

            @php
                $bClass = match($penanganan->status) {
                    'Menunggu' => 'bg-amber-100 text-amber-800',
                    'Dalam Proses' => 'bg-purple-100 text-purple-800',
                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                    default => 'bg-slate-100 text-slate-700',
                };
            @endphp
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold uppercase {{ $bClass }}">
                Status Saya: {{ $penanganan->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Col Left: Info Lokasi & Aduan (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Informasi Masalah dari Pelapor</h3>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Kategori Masalah:</span>
                        <span class="font-bold text-slate-800">{{ $penanganan->laporan->kategori->nama_kategori ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Kecamatan:</span>
                        <span class="font-bold text-slate-800">{{ $penanganan->laporan->kecamatan ?? 'Kota Padang' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Pelapor:</span>
                        <span class="font-bold text-slate-800">{{ $penanganan->laporan->nama_pelapor ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Kontak:</span>
                        <span class="font-bold text-slate-800">{{ $penanganan->laporan->kontak_pelapor ?? '-' }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 text-xs block mb-1">Alamat Titik Lokasi:</span>
                    <p class="text-xs bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-800 font-bold">
                        {{ $penanganan->laporan->alamat ?? '-' }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400 text-xs block mb-1">Deskripsi Kondisi Lapangan:</span>
                    <p class="text-xs bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-700 leading-relaxed">
                        {{ $penanganan->laporan->deskripsi ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Foto Bukti Pelapor -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Foto Bukti Awal dari Warga</h3>
                @if($penanganan->laporan && $penanganan->laporan->fotoBuktiLaporan->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($penanganan->laporan->fotoBuktiLaporan as $f)
                            <div class="h-44 rounded-xl overflow-hidden bg-slate-100">
                                <img src="{{ asset('uploads/laporan/' . $f->nama_file) }}" alt="Foto" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1611288875785-5b432a514d87?w=600&auto=format&fit=crop&q=60'">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">Tidak ada foto dari pelapor.</p>
                @endif
            </div>
        </div>

        <!-- Col Right: Form Update Progress (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Form Laporan Hasil Lapangan</h3>

                <form action="{{ route('petugas.tugas.progress', $penanganan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Penanganan</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="Menunggu" {{ $penanganan->status == 'Menunggu' ? 'selected' : '' }}>Menunggu (Belum Mulai)</option>
                            <option value="Dalam Proses" {{ $penanganan->status == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses (Sedang Dikerjakan)</option>
                            <option value="Selesai" {{ $penanganan->status == 'Selesai' ? 'selected' : '' }}>Selesai (Sudah Tuntas Dikerjakan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan Tindak Lanjut Lapangan <span class="text-rose-500">*</span></label>
                        <textarea name="keterangan" rows="4" required placeholder="Jelaskan tindakan yang telah diambil (misal: armada dump truck 04 telah mengangkut 2 ton sampah liar, saluran telah dikeruk, dll.)..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 leading-relaxed">{{ old('keterangan', $penanganan->keterangan) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Upload Bukti Foto Selesai (Setelah Dibersihkan)</label>
                        <input type="file" name="foto" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                        <span class="text-[10px] text-slate-400 mt-1 block">Foto wajib diunggah saat memilih status "Selesai".</span>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition-colors shadow-sm">
                        Simpan Progres Penanganan
                    </button>
                </form>
            </div>

            <!-- Current Penanganan Photo if exists -->
            @if($penanganan->foto)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                    <h4 class="font-bold text-xs text-slate-800">Foto Hasil Kerja Terunggah</h4>
                    <div class="h-48 rounded-xl overflow-hidden bg-slate-100">
                        <img src="{{ asset('uploads/penanganan/' . $penanganan->foto) }}" alt="Bukti Selesai" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
