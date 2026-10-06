@extends('layouts.admin')

@section('title', 'Manajemen Wilayah — Konsol Administrator')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Wilayah Administratif</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola hierarki yurisdiksi wilayah (Negara, Provinsi, Kota/Kabupaten, Kecamatan, Kelurahan) untuk routing pengaduan publik.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-tambah-wilayah').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold shadow-xs cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Wilayah</span>
        </button>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form action="{{ route('admin.wilayah.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode wilayah..." class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-slate-50/50 focus:outline-none focus:border-slate-800 w-full sm:w-64">
                <select name="type" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50/50 focus:outline-none">
                    <option value="">Semua Tingkatan</option>
                    <option value="country" {{ request('type') == 'country' ? 'selected' : '' }}>Negara</option>
                    <option value="province" {{ request('type') == 'province' ? 'selected' : '' }}>Provinsi</option>
                    <option value="city" {{ request('type') == 'city' ? 'selected' : '' }}>Kota / Kab</option>
                    <option value="district" {{ request('type') == 'district' ? 'selected' : '' }}>Kecamatan</option>
                    <option value="village" {{ request('type') == 'village' ? 'selected' : '' }}>Kelurahan</option>
                </select>
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium cursor-pointer">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Nama Wilayah</th>
                        <th class="px-5 py-3">Tingkat</th>
                        <th class="px-5 py-3">Induk Wilayah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($regions as $r)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-mono font-semibold text-slate-900">{{ $r->code }}</td>
                            <td class="px-5 py-3 font-medium text-slate-900">{{ $r->name }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase 
                                    {{ $r->type === 'district' ? 'bg-blue-50 text-blue-700' : ($r->type === 'city' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $r->type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $r->parent ? $r->parent->name : '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $r->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $r->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                    <span>{{ $r->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('admin.wilayah.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus wilayah ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada data wilayah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($regions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $regions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Wilayah -->
<div id="modal-tambah-wilayah" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Tambah Wilayah Administratif</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-wilayah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('admin.wilayah.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Kode Unik Wilayah</label>
                <input type="text" name="code" required placeholder="Contoh: PDG-12" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs uppercase focus:outline-none focus:border-slate-800">
            </div>
            <div>
                <label class="font-medium text-slate-700 block mb-1">Nama Wilayah</label>
                <input type="text" name="name" required placeholder="Contoh: Kecamatan Pauh" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
            </div>
            <div>
                <label class="font-medium text-slate-700 block mb-1">Tingkatan</label>
                <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="district">Kecamatan (District)</option>
                    <option value="city">Kota / Kabupaten (City)</option>
                    <option value="province">Provinsi (Province)</option>
                    <option value="village">Kelurahan (Village)</option>
                </select>
            </div>
            <div>
                <label class="font-medium text-slate-700 block mb-1">Induk Wilayah (Parent)</label>
                <select name="parent_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Tanpa Induk / Tingkat Tertinggi --</option>
                    @foreach($parentCandidates as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ strtoupper($p->type) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tambah-wilayah').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Simpan Wilayah</button>
            </div>
        </form>
    </div>
</div>
@endsection
