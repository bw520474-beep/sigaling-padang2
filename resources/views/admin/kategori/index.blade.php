@extends('layouts.admin')

@section('title', 'Kategori Masalah - Admin SIGALING PADANG')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kategori Permasalahan Lingkungan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola jenis kategori aduan lingkungan hidup DLH Kota Padang.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- List Table (Col 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Daftar Kategori Terdaftar ({{ $kategoris->count() }})</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">No</th>
                            <th class="px-5 py-3.5">Nama Kategori</th>
                            <th class="px-5 py-3.5">Deskripsi</th>
                            <th class="px-5 py-3.5">Total Aduan</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kategoris as $index => $kat)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-900 block">{{ $kat->nama_kategori }}</span>
                                    <span class="text-[10px] text-slate-400">Ikon: {{ $kat->ikon }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $kat->deskripsi ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        {{ $kat->laporans_count }} Aduan
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form action="{{ route('admin.kategori.destroy', $kat->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
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
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Tambah Kategori Baru</h3>

            <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" required placeholder="Contoh: Pencemaran Limbah B3" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Ikon (Lucide)</label>
                    <input type="text" name="ikon" placeholder="Contoh: trash-2, alert-triangle" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" placeholder="Penjelasan jenis aduan ini..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors">
                    Simpan Kategori Baru
                </button>
            </form>
        </div>
    </div>
@endsection
