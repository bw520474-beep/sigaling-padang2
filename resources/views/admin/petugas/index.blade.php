@extends('layouts.admin')

@section('title', 'Petugas Lapangan - Admin SIGALING PADANG')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Akun Petugas Lapangan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun petugas tim satgas kebersihan dan dinas teknis.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Table List (Col 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Daftar Petugas Terdaftar ({{ $petugasList->count() }})</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">Nama Petugas</th>
                            <th class="px-5 py-3.5">Instansi OPD</th>
                            <th class="px-5 py-3.5">Email</th>
                            <th class="px-5 py-3.5">No. HP</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($petugasList as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-900 flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($p->nama, 0, 2)) }}
                                    </div>
                                    <span>{{ $p->nama }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($p->opd)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            {{ $p->opd->nama_opd }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] text-slate-400 bg-slate-100">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 font-mono text-[11px]">{{ $p->email }}</td>
                                <td class="px-5 py-3.5 text-slate-700">{{ $p->no_hp ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <form action="{{ route('admin.petugas.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus petugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs">
                                    Belum ada akun petugas lapangan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Form (Col 4) -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs h-fit space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">Tambah Akun Petugas</h3>

            <form action="{{ route('admin.petugas.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Contoh: Danu Satgas DLH" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Instansi OPD Penempatan <span class="text-rose-500">*</span></label>
                    <select name="opd_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs bg-white text-slate-800">
                        <option value="">-- Pilih Instansi OPD --</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Login</label>
                    <input type="email" name="email" required placeholder="petugas@sigaling.local" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" placeholder="081234567890" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Default</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors cursor-pointer shadow-xs">
                    Daftarkan Petugas
                </button>
            </form>
        </div>
    </div>
@endsection
