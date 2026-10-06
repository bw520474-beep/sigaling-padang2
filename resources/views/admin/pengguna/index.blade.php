@extends('layouts.admin')

@section('title', 'Data Pengguna Warga - Admin SIGALING PADANG')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Pengguna Warga</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar akun warga dan admin yang terdaftar di sistem SIGALING PADANG.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pengguna</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">No. HP</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Total Aduan</th>
                        <th class="px-5 py-3.5">Terdaftar</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-900 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($u->nama, 0, 2)) }}
                                </div>
                                <span>{{ $u->nama }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $u->email }}</td>
                            <td class="px-5 py-3.5 text-slate-700">{{ $u->no_hp ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $roleBadge = match($u->role) {
                                        'admin' => 'bg-emerald-50 text-emerald-700',
                                        'petugas' => 'bg-purple-50 text-purple-700',
                                        default => 'bg-blue-50 text-blue-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $roleBadge }}">
                                    {{ ucfirst($u->role ?? 'Masyarakat') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-800">{{ $u->laporans_count ?? 0 }} Laporan</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ \Carbon\Carbon::parse($u->created_at)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if(Auth::id() !== $u->id)
                                    <form action="{{ route('admin.pengguna.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-xs">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
@endsection
