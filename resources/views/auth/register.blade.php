@extends('layouts.app')

@section('title', 'Daftar Akun Warga - SIGALING PADANG')

@section('content')
    <div class="py-16 sm:py-24 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Brand Logo & Intro -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-700 to-emerald-500 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-600/20">
                    <i data-lucide="leaf" class="w-7 h-7"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Pendaftaran Akun Warga</h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Buat akun untuk mempermudah pelaporan dan riwayat aduan lingkungan Anda.
                </p>
            </div>

            <!-- Register Form Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm space-y-6">
                <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                            <input type="text" name="nama" value="{{ old('nama') }}" required autofocus placeholder="Contoh: Rahmat Hidayat" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('nama') border-rose-500 @else border-slate-200 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        @error('nama') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-500 @else border-slate-200 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('no_hp') border-rose-500 @else border-slate-200 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        @error('no_hp') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                            <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('password') border-rose-500 @else border-slate-200 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Daftar Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Login link -->
            <p class="text-center text-xs text-slate-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:underline">Masuk ke Akun</a>
            </p>
        </div>
    </div>
@endsection
