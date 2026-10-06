@extends('layouts.admin')

@section('title', 'Smart Routing Rules — Konsol Administrator')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Smart Routing Engine Rules</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">Anti-Hardcode</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Konfigurasi pemetaan dinamis: Kategori &bull; Subkategori &bull; Wilayah &rarr; Instansi Berwenang &bull; Bidang Kerja &bull; Target SLA.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-tambah-rule').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold shadow-xs cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Aturan Routing Baru</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Subkategori Masalah</th>
                        <th class="px-5 py-3">Wilayah</th>
                        <th class="px-5 py-3">Instansi Tujuan</th>
                        <th class="px-5 py-3">Bidang / Unit Kerja</th>
                        <th class="px-5 py-3">Prioritas</th>
                        <th class="px-5 py-3">Target SLA</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $r)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $r->category->nama_kategori ?? '-' }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">
                                @if($r->subcategory)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>{{ $r->subcategory->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Semua Subkategori</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $r->region ? $r->region->name : 'Global (Semua Wilayah)' }}
                            </td>
                            <td class="px-5 py-3 font-bold text-slate-900">
                                {{ $r->organization ? $r->organization->name : '-' }}
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                {{ $r->department ? $r->department->name : 'Disposisi Umum Instansi' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $r->priority === 'Darurat' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($r->priority === 'Tinggi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $r->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono font-semibold text-slate-900">{{ $r->sla_hours }} Jam</td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('admin.routing.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus aturan routing ini?')" class="inline">
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
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">Belum ada aturan routing yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rules->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $rules->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Aturan Routing -->
<div id="modal-tambah-rule" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Konfigurasi Aturan Smart Routing Baru</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-rule').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('admin.routing.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Kategori Masalah</label>
                <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Subkategori Spesifik</label>
                <select name="subcategory_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Berlaku untuk Semua Subkategori --</option>
                    @foreach($subcategories as $sc)
                        <option value="{{ $sc->id }}">{{ $sc->name }} ({{ $sc->category->nama_kategori ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Wilayah Spesifik (Opsional)</label>
                <select name="region_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Berlaku Global / Seluruh Wilayah --</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}">{{ $reg->name }} ({{ strtoupper($reg->type) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Instansi Penanggung Jawab</label>
                    <select name="organization_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($organizations as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Unit / Bidang Kerja</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                        <option value="">-- Otomatis / Disposisi Umum --</option>
                        @foreach($organizations as $o)
                            @foreach($o->departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $o->code }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Tingkat Prioritas</label>
                    <select name="priority" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Darurat">Darurat</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Target SLA (Jam)</label>
                    <input type="number" name="sla_hours" value="72" min="1" max="720" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tambah-rule').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Simpan Aturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
