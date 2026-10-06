@extends('layouts.app')

@section('title', 'Tanya Jawab & Bantuan — SIGALING PADANG')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10">
        <div class="space-y-3 max-w-2xl">
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                Pusat Bantuan & Layanan
            </span>
            <h1 class="text-3xl sm:text-5xl font-semibold text-[#1d1d1f] tracking-tight">
                Pertanyaan yang Sering Diajukan
            </h1>
            <p class="text-xs sm:text-sm text-[#6e6e73] leading-relaxed">
                Jawaban praktis dan transparan seputar tata cara pengaduan, estimasi waktu penanganan (SLA), dan sistem pelacakan tiket SIGALING.
            </p>
        </div>
    </section>

    <!-- ===== FAQ WORKSPACE ===== -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24" x-data="{
        search: '',
        activeCat: 'all',
        activeItem: 1
    }">
        <!-- Search & Filter Controls -->
        <div class="space-y-4 pb-8 hairline-b">
            <div class="relative max-w-md">
                <i data-lucide="search" class="w-3.5 h-3.5 text-[#86868b] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" x-model="search" placeholder="Cari pertanyaan..." class="w-full pl-9 pr-3 py-1.5 rounded-full border border-[#e5e5ea] bg-white text-xs text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:border-[#1d1d1f]">
            </div>

            <!-- Segment Buttons (Green Active Style) -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <button type="button" @click="activeCat = 'all'" class="px-3.5 py-1.5 rounded-full font-semibold transition-all shadow-2xs" :class="activeCat === 'all' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 bg-white border border-slate-200 hover:text-emerald-700 hover:border-emerald-300'">
                    Semua
                </button>
                <button type="button" @click="activeCat = 'pelaporan'" class="px-3.5 py-1.5 rounded-full font-semibold transition-all shadow-2xs" :class="activeCat === 'pelaporan' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 bg-white border border-slate-200 hover:text-emerald-700 hover:border-emerald-300'">
                    Tata Cara
                </button>
                <button type="button" @click="activeCat = 'sla'" class="px-3.5 py-1.5 rounded-full font-semibold transition-all shadow-2xs" :class="activeCat === 'sla' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 bg-white border border-slate-200 hover:text-emerald-700 hover:border-emerald-300'">
                    Waktu Penanganan (SLA)
                </button>
                <button type="button" @click="activeCat = 'privasi'" class="px-3.5 py-1.5 rounded-full font-semibold transition-all shadow-2xs" :class="activeCat === 'privasi' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 bg-white border border-slate-200 hover:text-emerald-700 hover:border-emerald-300'">
                    Privasi & Keamanan Data
                </button>
            </div>
        </div>

        <!-- Accordion Feed (Hairline Divided) -->
        <div class="divide-y divide-[#e5e5ea] hairline-b">
            <!-- FAQ 1 -->
            <div x-show="(activeCat === 'all' || activeCat === 'pelaporan') && (!search || 'cara membuat laporan pengaduan lingkungan formulir foto'.includes(search.toLowerCase()))" class="py-5">
                <button type="button" @click="activeItem = (activeItem === 1 ? null : 1)" class="w-full text-left flex items-center justify-between gap-4 group">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs text-[#86868b] w-6">01</span>
                        <h3 class="text-sm font-medium text-[#1d1d1f] group-hover:text-[#0f5132] transition-colors">Bagaimana cara membuat laporan pengaduan lingkungan?</h3>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-[#86868b] transition-transform" :class="activeItem === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeItem === 1" x-collapse>
                    <div class="pt-3 pl-9 text-xs text-[#6e6e73] leading-relaxed max-w-3xl space-y-2">
                        <p>Langkah mudah menyampaikan pengaduan:</p>
                        <ol class="list-decimal pl-4 space-y-1">
                            <li>Buka menu <strong>"Laporkan"</strong> pada navigasi atas.</li>
                            <li>Pilih kategori permasalahan yang tepat (Sampah, Drainase, Pohon, dll).</li>
                            <li>Tentukan titik lokasi pada peta dan masukkan deskripsi masalah.</li>
                            <li>Lampirkan minimal 1 foto bukti kondisi nyata di lapangan.</li>
                            <li>Kirim laporan dan simpan <strong>Kode Laporan</strong> untuk melacak progres.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div x-show="(activeCat === 'all' || activeCat === 'pelaporan') && (!search || 'apakah wajib mendaftar login akun'.includes(search.toLowerCase()))" class="py-5">
                <button type="button" @click="activeItem = (activeItem === 2 ? null : 2)" class="w-full text-left flex items-center justify-between gap-4 group">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs text-[#86868b] w-6">02</span>
                        <h3 class="text-sm font-medium text-[#1d1d1f] group-hover:text-[#0f5132] transition-colors">Apakah saya wajib membuat akun untuk melapor?</h3>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-[#86868b] transition-transform" :class="activeItem === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeItem === 2" x-collapse>
                    <div class="pt-3 pl-9 text-xs text-[#6e6e73] leading-relaxed max-w-3xl">
                        <p><strong>Tidak wajib.</strong> Warga dapat melapor secara langsung tanpa mendaftar akun. Anda cukup menyimpan Kode Laporan (tiket) yang diterbitkan sistem untuk memantau proses verifikasi dan penanganan kapan saja.</p>
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div x-show="(activeCat === 'all' || activeCat === 'sla') && (!search || 'berapa lama waktu estimasi respon sla penanganan'.includes(search.toLowerCase()))" class="py-5">
                <button type="button" @click="activeItem = (activeItem === 3 ? null : 3)" class="w-full text-left flex items-center justify-between gap-4 group">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs text-[#86868b] w-6">03</span>
                        <h3 class="text-sm font-medium text-[#1d1d1f] group-hover:text-[#0f5132] transition-colors">Berapa lama standar waktu penyelesaian laporan (SLA)?</h3>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-[#86868b] transition-transform" :class="activeItem === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeItem === 3" x-collapse>
                    <div class="pt-3 pl-9 text-xs text-[#6e6e73] leading-relaxed max-w-3xl space-y-1">
                        <p>&bull; <strong>Verifikasi Administrasi Awal:</strong> Maksimal 24 jam setelah laporan dikirim.</p>
                        <p>&bull; <strong>Tindakan Lapangan Armada (Sampah & TPS):</strong> 1 &ndash; 2 hari kerja.</p>
                        <p>&bull; <strong>Pembersihan Drainase & Pemangkasan Pohon:</strong> 1 &ndash; 3 hari kerja tergantung tingkat keparahan.</p>
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div x-show="(activeCat === 'all' || activeCat === 'privasi') && (!search || 'apakah identitas data diri pelapor aman dirahasiakan'.includes(search.toLowerCase()))" class="py-5">
                <button type="button" @click="activeItem = (activeItem === 4 ? null : 4)" class="w-full text-left flex items-center justify-between gap-4 group">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs text-[#86868b] w-6">04</span>
                        <h3 class="text-sm font-medium text-[#1d1d1f] group-hover:text-[#0f5132] transition-colors">Apakah identitas dan data diri pelapor aman dan dirahasiakan?</h3>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-[#86868b] transition-transform" :class="activeItem === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeItem === 4" x-collapse>
                    <div class="pt-3 pl-9 text-xs text-[#6e6e73] leading-relaxed max-w-3xl">
                        <p><strong>Sangat aman.</strong> Data pribadi seperti nomor telepon hanya dapat diakses oleh tim admin verifikator DLH untuk kebutuhan konfirmasi titik lokasi. Identitas tidak akan dipublikasikan kepada pihak terlapor maupun pihak ketiga.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
