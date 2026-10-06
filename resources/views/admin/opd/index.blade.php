@extends('layouts.admin')

@section('title', 'OPD Pelaksana - Admin SIGALING PADANG')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Organisasi Perangkat Daerah (OPD)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar instansi kedinasan teknis pelaksana penanganan pengaduan lingkungan hidup.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- List OPD (Col 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Daftar OPD Pelaksana ({{ $opds->count() }})</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">Nama Instansi</th>
                            <th class="px-5 py-3.5">Deskripsi / Wewenang</th>
                            <th class="px-5 py-3.5">Kontak</th>
                            <th class="px-5 py-3.5">Disposisi</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($opds as $opd)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    {{ $opd->nama_opd }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $opd->deskripsi ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-700">
                                    {{ $opd->kontak ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">
                                        {{ $opd->laporan_opds_count ?? 0 }} Tugas
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form action="{{ route('admin.opd.destroy', $opd->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus OPD ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Form (Col 4) -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs h-fit space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Tambah OPD Baru</h3>

            <form action="{{ route('admin.opd.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama OPD / Dinas</label>
                    <input type="text" name="nama_opd" required placeholder="Contoh: Dinas PUPR Kota Padang" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kontak / Call Center</label>
                    <input type="text" name="kontak" placeholder="Contoh: (0751) 123456" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Ruang Lingkup</label>
                    <textarea name="deskripsi" rows="3" placeholder="Wewenang penanganan dinas ini..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                    Simpan OPD Baru
                </button>
            </form>
        </div>
    </div>
@endsection
