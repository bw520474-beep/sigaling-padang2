@extends('layouts.app')

@section('title', 'Profil Pengguna - SIGALING PADANG')

@section('content')
    <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white py-10 border-b border-emerald-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Profil Akun Saya</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">Kelola data informasi akun dan kata sandi Anda.</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar User Card -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs text-center">
                    <div class="w-20 h-20 rounded-2xl bg-emerald-100 text-emerald-800 font-extrabold text-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-200 shadow-sm">
                        {{ strtoupper(substr($user->nama ?? 'U', 0, 2)) }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $user->nama }}</h3>
                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $user->role ?? 'Masyarakat' }}
                    </span>

                    <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Total Laporan</span>
                            <span class="text-xl font-extrabold text-slate-900">{{ $laporans->count() }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Tuntas</span>
                            <span class="text-xl font-extrabold text-emerald-600">
                                {{ $laporans->where('status', 'Selesai')->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 text-white rounded-3xl p-6 border border-slate-800 text-xs space-y-3">
                    <h4 class="font-bold text-emerald-400 text-sm">Privasi & Keamanan</h4>
                    <p class="text-slate-400 leading-relaxed">
                        Data identitas dan nomor kontak Anda aman dan hanya digunakan untuk keperluan koordinasi tindak lanjut pengaduan lingkungan hidup Kota Padang.
                    </p>
                </div>
            </div>

            <!-- Forms Section -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Update Profil Form -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                    <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6">Ubah Data Diri</h3>

                    <form action="{{ route('profil.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('nama') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('no_hp') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Password Form -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                    <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6">Ganti Kata Sandi</h3>

                    <form action="{{ route('profil.password') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('current_password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                            <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-colors">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
