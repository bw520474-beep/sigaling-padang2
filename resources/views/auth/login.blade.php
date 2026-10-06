@extends('layouts.app')

@section('title', 'Masuk ke Layanan — SIGALING PADANG')

@section('content')
    <div class="py-20 sm:py-28 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm space-y-8">
            <!-- Brand & Statement -->
            <div class="text-center space-y-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shadow-xs shadow-emerald-500/50 inline-block mb-1"></span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Masuk ke SIGALING</h1>
                <p class="text-xs text-slate-500">
                    Akses pemantauan pengaduan masyarakat Kota Padang.
                </p>
            </div>

            <!-- Login Form -->
            <div class="space-y-6">
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full px-3.5 py-2.5 rounded-xl border @error('email') border-rose-500 @else border-slate-200 @enderror text-xs bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('email')
                            <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Kata Sandi</label>
                            <a href="{{ route('alur') }}" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-medium">
                                Bantuan
                            </a>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border @error('password') border-rose-500 @else border-slate-200 @enderror text-xs bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('password')
                            <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs hover:shadow transition-all">
                            Masuk ke Akun
                        </button>
                    </div>
                </form>

                <!-- Demo Account Info Note -->
                <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-200/80 text-[11px] text-slate-600 space-y-1">
                    <span class="font-bold text-emerald-900 block">Akun Uji Coba:</span>
                    <p>Admin: <code class="font-mono text-emerald-800 font-bold bg-emerald-100/70 px-1.5 py-0.5 rounded">admin@sigaling.local</code> &bull; Sandi: <code class="font-mono text-emerald-800 font-bold bg-emerald-100/70 px-1.5 py-0.5 rounded">admin123</code></p>
                </div>
            </div>

            <!-- Footer Link -->
            <p class="text-center text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="text-emerald-700 font-bold hover:underline">Daftar Akun Baru</a>
            </p>
        </div>
    </div>
@endsection
