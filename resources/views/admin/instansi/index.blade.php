@extends('layouts.admin')

@section('title', 'Manajemen Instansi & Unit Kerja — Konsol Administrator')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Instansi & Bidang Pemerintahan</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola organisasi perangkat daerah (OPD), badan teknis, dan unit pelaksana kerja di lingkungan SIGALING.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-tambah-instansi').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold shadow-xs cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Daftarkan Instansi Baru</span>
        </button>
    </div>

    <!-- Organizations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($organizations as $org)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs space-y-4 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $org->code }}</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $org->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $org->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>{{ $org->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $org->name }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $org->description ?? 'Tidak ada deskripsi profil instansi.' }}</p>

                    <div class="pt-2 text-[11px] text-slate-600 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                            <span>{{ $org->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="mail" class="w-3 h-3 text-slate-400"></i>
                            <span>{{ $org->email ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Unit Kerja / Departments list -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Unit / Bidang Kerja ({{ $org->departments->count() }})</span>
                        <button type="button" 
                                onclick="openDeptModal('{{ $org->id }}', '{{ $org->name }}')" 
                                class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer flex items-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            <span>Tambah Bidang</span>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                        @forelse($org->departments as $dept)
                            <span class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-medium text-slate-700">
                                {{ $dept->name }}
                            </span>
                        @empty
                            <span class="text-[11px] text-slate-400 italic">Belum ada unit/bidang terdaftar.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-12 bg-white rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                Belum ada data instansi yang terdaftar.
            </div>
        @endforelse
    </div>

    @if($organizations->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $organizations->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Instansi -->
<div id="modal-tambah-instansi" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftarkan Instansi / OPD Baru</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-instansi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('admin.instansi.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Kode Instansi</label>
                    <input type="text" name="code" required placeholder="Contoh: DLH-PDG" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs uppercase focus:outline-none focus:border-slate-800">
                </div>
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Tipe Lembaga</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                        <option value="dinas">Dinas Daerah</option>
                        <option value="badan">Badan Teknis</option>
                        <option value="satpol_pp">Satpol PP</option>
                        <option value="inspektorat">Inspektorat</option>
                        <option value="kecamatan">Kantor Camat</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Nama Lengkap Instansi</label>
                <input type="text" name="name" required placeholder="Contoh: Dinas Lingkungan Hidup Kota Padang" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Kontak Telepon</label>
                    <input type="text" name="phone" placeholder="(0751) ..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                </div>
                <div>
                    <label class="font-medium text-slate-700 block mb-1">Email Resmi</label>
                    <input type="email" name="email" placeholder="dinas@padang.go.id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                </div>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Tupoksi & Deskripsi Kewenangan</label>
                <textarea name="description" rows="2" placeholder="Uraian bidang kewenangan penanganan pengaduan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tambah-instansi').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Simpan Instansi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Bidang Kerja -->
<div id="modal-tambah-bidang" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Tambah Unit / Bidang Kerja</h3>
                <span id="label-org-name" class="text-xs text-slate-500 font-medium"></span>
            </div>
            <button type="button" onclick="document.getElementById('modal-tambah-bidang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-tambah-bidang" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Kode Bidang</label>
                <input type="text" name="code" placeholder="Contoh: DLH-SAMPAH" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs uppercase focus:outline-none focus:border-slate-800">
            </div>
            <div>
                <label class="font-medium text-slate-700 block mb-1">Nama Unit / Bidang</label>
                <input type="text" name="name" required placeholder="Contoh: Bidang Pengelolaan Sampah & Kebersihan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
            </div>
            <div>
                <label class="font-medium text-slate-700 block mb-1">Uraian Tugas Teknis</label>
                <textarea name="description" rows="2" placeholder="Fokus penanganan masalah teknis..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tambah-bidang').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Simpan Bidang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeptModal(orgId, orgName) {
        document.getElementById('label-org-name').textContent = orgName;
        document.getElementById('form-tambah-bidang').action = "{{ url('admin/instansi') }}/" + orgId + "/department";
        document.getElementById('modal-tambah-bidang').classList.remove('hidden');
    }
</script>
@endsection
