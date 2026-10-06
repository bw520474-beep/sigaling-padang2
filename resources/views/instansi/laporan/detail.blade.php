@extends('layouts.instansi')

@section('title', 'Detail Penugasan ' . ($assignment->laporan->kode_laporan ?? '') . ' — SIGALING')

@section('content')
@php
    $laporan = $assignment->laporan;
@endphp

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('instansi.laporan.index') }}" class="text-xs text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Daftar Penugasan</span>
            </a>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $laporan->kode_laporan }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                    {{ $assignment->status === 'resolved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                    Status Tugas: {{ $assignment->status }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($assignment->status !== 'resolved')
                <button type="button" onclick="document.getElementById('modal-tugaskan').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    <span>Tugaskan Petugas</span>
                </button>
                <button type="button" onclick="document.getElementById('modal-kolaborasi').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                    <span>Tambah Instansi Pendukung</span>
                </button>
                <button type="button" onclick="document.getElementById('modal-selesai').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Setujui Selesai</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Case Details & Photos -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Dossier Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-4">
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Uraian Kasus Publik</span>
                    <h2 class="text-lg font-bold text-slate-900 mt-1">{{ $laporan->judul }}</h2>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed mt-2 bg-slate-50 p-4 rounded-xl border border-slate-200/70">
                        {{ $laporan->deskripsi }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block">Kategori</span>
                        <span class="font-bold text-slate-900">{{ $laporan->kategori->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block">Subkategori</span>
                        <span class="font-bold text-slate-900">{{ $laporan->subcategory->name ?? '-' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block">Prioritas</span>
                        <span class="font-bold text-slate-900">{{ $assignment->priority ?? 'Sedang' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block">Batas SLA</span>
                        <span class="font-bold text-slate-900">{{ $assignment->sla_due_at ? \Carbon\Carbon::parse($assignment->sla_due_at)->translatedFormat('d M, H:i') : '-' }}</span>
                    </div>
                </div>

                <div class="pt-2 text-xs space-y-1 text-slate-600">
                    <div class="flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5"></i>
                        <span>{{ $laporan->alamat }} (Kec. {{ $laporan->kecamatan ?? 'Padang' }})</span>
                    </div>
                </div>
            </div>

            <!-- Evidence Photos -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Dokumentasi Bukti</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-500 font-medium block mb-1.5">Foto Pengaduan Awal</span>
                        @if($laporan->fotoBuktiLaporan->isNotEmpty())
                            @foreach($laporan->fotoBuktiLaporan as $f)
                                <img src="{{ asset('uploads/laporan/' . $f->nama_file) }}" alt="Foto Awal" class="w-full h-48 object-cover rounded-xl border border-slate-200" onerror="this.src='https://images.unsplash.com/photo-1611288875785-5b432a514d87?w=600&auto=format&fit=crop&q=60'">
                            @endforeach
                        @else
                            <div class="h-40 rounded-xl bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-xs">
                                Tidak ada foto awal
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-emerald-800 font-semibold block mb-1.5">Foto Penanganan Petugas</span>
                        @if($laporan->fotoBuktiPenanganan->isNotEmpty())
                            @foreach($laporan->fotoBuktiPenanganan as $f)
                                <img src="{{ $f->url }}" alt="Foto Penanganan" class="w-full h-48 object-cover rounded-xl border border-slate-200">
                            @endforeach
                        @elseif($laporan->penanganans->whereNotNull('foto')->isNotEmpty())
                            @foreach($laporan->penanganans->whereNotNull('foto') as $p)
                                <img src="{{ $p->foto_url }}" alt="Foto Penanganan" class="w-full h-48 object-cover rounded-xl border border-slate-200">
                            @endforeach
                        @else
                            <div class="h-40 rounded-xl bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-xs">
                                Foto hasil penanganan belum diunggah
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Collaborative Multi-Agency Section -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Kolaborasi Lintas Instansi (Multi-Agency)</span>
                        <h3 class="text-sm font-bold text-slate-900 mt-0.5">Daftar Penugasan Instansi pada Kasus Ini</h3>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($laporan->assignments as $asg)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $asg->organization->name }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $asg->notes ?? 'Penugasan aktif' }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $asg->status === 'resolved' ? 'bg-emerald-50 text-emerald-800' : 'bg-blue-50 text-blue-800' }}">
                                {{ $asg->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Status, Reporter Protection & Audit Trail -->
        <div class="space-y-6">
            <!-- Reporter Protection Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Identitas Pelapor</span>
                    @if($laporan->protection_level === 'anonymous')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">Mode Anonim</span>
                    @elseif($laporan->protection_level === 'confidential')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Rahasia</span>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-50 text-slate-600">Publik</span>
                    @endif
                </div>

                <div class="text-xs space-y-2 pt-1">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama:</span>
                        <span class="font-bold text-slate-900">{{ $laporan->masked_reporter_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Kontak / HP:</span>
                        <span class="font-bold text-slate-900">{{ $laporan->masked_contact }}</span>
                    </div>
                    @if($laporan->protection_level === 'confidential')
                        <p class="text-[10px] text-amber-700 bg-amber-50 p-2 rounded-lg border border-amber-200 leading-relaxed">
                            🔒 <strong>Pemberitahuan Kerahasiaan:</strong> Anda memiliki hak verifikasi supervisor untuk melihat kontak demi koordinasi, namun dilarang membocorkan data ini ke pihak luar.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Petugas Bertugas -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs space-y-3">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Petugas Penanggung Jawab</span>
                @if($assignment->officer)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-sm">
                            {{ substr($assignment->officer->nama, 0, 1) }}
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">{{ $assignment->officer->nama }}</span>
                            <span class="text-slate-500 text-[11px]">{{ $assignment->officer->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 space-y-2">
                        <p>Belum ada petugas lapangan yang ditugaskan untuk laporan ini.</p>
                        <button type="button" onclick="document.getElementById('modal-tugaskan').classList.remove('hidden')" class="w-full py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs cursor-pointer">
                            Pilih Petugas Sekarang
                        </button>
                    </div>
                @endif
            </div>

            <!-- Mini Audit Trail -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs space-y-3">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Histori Aktivitas Kasus</span>
                <div class="space-y-3 max-h-64 overflow-y-auto text-xs">
                    @forelse($laporan->auditLogs as $log)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span class="font-semibold text-slate-700">{{ $log->actor_name ?: 'Sistem' }}</span>
                                <span class="tabular-nums">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M, H:i') }}</span>
                            </div>
                            <span class="font-mono text-[11px] font-semibold text-slate-800">{{ $log->action }}</span>
                        </div>
                    @empty
                        <span class="text-slate-400 text-xs">Belum ada catatan log.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tugaskan Petugas -->
<div id="modal-tugaskan" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Tugaskan Kasus ke Petugas Lapangan</h3>
            <button type="button" onclick="document.getElementById('modal-tugaskan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('instansi.laporan.tugaskan', $assignment->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Pilih Petugas Lapangan</label>
                <select name="officer_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Pilih Petugas yang Bertugas --</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ $assignment->officer_id == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->no_hp ?? $p->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Instruksi / Catatan Khusus untuk Petugas</label>
                <textarea name="notes" rows="3" placeholder="Contoh: Bawa armada pengangkut 1 unit, lakukan dokumentasi sebelum dan sesudah..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tugaskan').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 cursor-pointer">Kirim Penugasan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kolaborasi Lintas Instansi -->
<div id="modal-kolaborasi" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Kolaborasi Lintas Instansi (Multi-Agency)</h3>
            <button type="button" onclick="document.getElementById('modal-kolaborasi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('instansi.laporan.tambah-instansi', $assignment->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Instansi Mitra Pendukung</label>
                <select name="support_org_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800">
                    <option value="">-- Pilih Instansi Terkait --</option>
                    @foreach($otherOrganizations as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-medium text-slate-700 block mb-1">Uraian Bantuan / Bagian Pekerjaan</label>
                <textarea name="support_notes" rows="3" required placeholder="Contoh: Memohon Dishub menata rambu/lampu lalu lintas saat penambalan aspal oleh PUPR..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-kolaborasi').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-medium hover:bg-black cursor-pointer">Kirim Permintaan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Setujui Selesai -->
<div id="modal-selesai" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Verifikasi Penyelesaian Kasus</h3>
            <button type="button" onclick="document.getElementById('modal-selesai').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('instansi.laporan.selesai', $assignment->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-medium text-slate-700 block mb-1">Catatan Verifikasi Supervisor</label>
                <textarea name="completion_notes" rows="3" placeholder="Pekerjaan penanganan telah diperiksa dan dinyatakan selesai tuntas..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-slate-800"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-selesai').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-medium hover:bg-emerald-700 cursor-pointer">Tandai Kasus Selesai Tuntas</button>
            </div>
        </form>
    </div>
</div>
@endsection
