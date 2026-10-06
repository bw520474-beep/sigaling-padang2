@extends('layouts.app')

@section('title', 'Edukasi 3R & Kelestarian — SIGALING PADANG')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
        <div class="space-y-3 max-w-3xl">
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                Edukasi & Konservasi Lingkungan
            </span>
            <h1 class="text-3xl sm:text-5xl font-semibold text-[#1d1d1f] tracking-tight">
                Pilah Sampah dari Rumah
            </h1>
            <p class="text-xs sm:text-sm text-[#6e6e73] leading-relaxed">
                Menjaga kebersihan Kota Padang berawal dari pemilahan sampah di tingkat rumah tangga. Terapkan prinsip 3R dan manfaatkan jejaring Bank Sampah unit kelurahan.
            </p>
        </div>
    </section>

    <!-- ===== 3R PRINCIPLES (EDITORIAL 3-COLUMNS) ===== -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 hairline-y py-12">
            <!-- Reduce -->
            <div class="space-y-4">
                <span class="text-xs font-mono text-[#86868b] block">Prinsip 01</span>
                <h2 class="text-xl font-semibold text-[#1d1d1f]">Reduce &bull; Mengurangi</h2>
                <p class="text-xs text-[#6e6e73] leading-relaxed">
                    Mencegah terbentuknya timbunan sampah baru sejak awal konsumsi. Prioritaskan barang dengan kemasan minim dan pakai ulang.
                </p>
                <ul class="space-y-2 text-xs text-[#1d1d1f] pt-2">
                    <li class="flex items-center gap-2">&bull; Bawa kantong belanja kain saat ke pasar</li>
                    <li class="flex items-center gap-2">&bull; Gunakan wadah dan tumbler isi ulang</li>
                    <li class="flex items-center gap-2">&bull; Tolak kantong plastik sekali pakai</li>
                </ul>
            </div>

            <!-- Reuse -->
            <div class="space-y-4">
                <span class="text-xs font-mono text-[#86868b] block">Prinsip 02</span>
                <h2 class="text-xl font-semibold text-[#1d1d1f]">Reuse &bull; Memakai Ulang</h2>
                <p class="text-xs text-[#6e6e73] leading-relaxed">
                    Mengoptimalkan usia pakai suatu barang sebelum memutuskan untuk membuangnya ke wadah pembuangan akhir.
                </p>
                <ul class="space-y-2 text-xs text-[#1d1d1f] pt-2">
                    <li class="flex items-center gap-2">&bull; Gunakan botol kaca untuk wadah serbaguna</li>
                    <li class="flex items-center gap-2">&bull; Donasikan pakaian layak pakai</li>
                    <li class="flex items-center gap-2">&bull; Manfaatkan kardus bekas untuk penyimpanan</li>
                </ul>
            </div>

            <!-- Recycle -->
            <div class="space-y-4">
                <span class="text-xs font-mono text-[#86868b] block">Prinsip 03</span>
                <h2 class="text-xl font-semibold text-[#1d1d1f]">Recycle &bull; Mendaur Ulang</h2>
                <p class="text-xs text-[#6e6e73] leading-relaxed">
                    Memilah material anorganik bernilai ekonomis (plastik PET, kertas, logam) untuk disalurkan ke Bank Sampah unit kelurahan.
                </p>
                <ul class="space-y-2 text-xs text-[#1d1d1f] pt-2">
                    <li class="flex items-center gap-2">&bull; Pisahkan sampah basah organik & kering</li>
                    <li class="flex items-center gap-2">&bull; Tabung sampah terpilah ke Bank Sampah</li>
                    <li class="flex items-center gap-2">&bull; Olah sisa sayuran menjadi kompos rumahan</li>
                </ul>
            </div>
        </div>

        <!-- Bank Sampah & Gerakan Komunitas -->
        <div class="pt-16 grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <div class="md:col-span-5 space-y-2">
                <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">Jejaring Kelurahan</span>
                <h3 class="text-2xl font-semibold text-[#1d1d1f] tracking-tight">Bank Sampah Unit Kota Padang</h3>
                <p class="text-xs text-[#6e6e73] leading-relaxed">
                    Dinas Lingkungan Hidup membina lebih dari 80 unit Bank Sampah aktif di seluruh kecamatan. Setiap kilogram sampah terpilah memiliki nilai tukar rupiah dan mengurangi beban TPA Air Dingin.
                </p>
            </div>

            <div class="md:col-span-7 space-y-3 text-xs text-[#1d1d1f]">
                <div class="p-4 rounded-xl bg-white border border-[#e5e5ea] flex items-start justify-between gap-4">
                    <div>
                        <strong class="font-medium block">1. Bersihkan & Keringkan</strong>
                        <span class="text-[#6e6e73] text-[11px]">Pastikan botol plastik dan kaleng dalam kondisi bersih sebelum disetor.</span>
                    </div>
                    <span class="font-mono text-[#86868b]">Tahap 1</span>
                </div>

                <div class="p-4 rounded-xl bg-white border border-[#e5e5ea] flex items-start justify-between gap-4">
                    <div>
                        <strong class="font-medium block">2. Timbang di Bank Sampah Terdekat</strong>
                        <span class="text-[#6e6e73] text-[11px]">Bawa sampah kering terpilah pada hari operasional penimbangan kelurahan.</span>
                    </div>
                    <span class="font-mono text-[#86868b]">Tahap 2</span>
                </div>

                <div class="p-4 rounded-xl bg-white border border-[#e5e5ea] flex items-start justify-between gap-4">
                    <div>
                        <strong class="font-medium block">3. Catat di Buku Tabungan</strong>
                        <span class="text-[#6e6e73] text-[11px]">Saldo tabungan dapat dicairkan atau ditukarkan dengan kebutuhan pokok.</span>
                    </div>
                    <span class="font-mono text-[#86868b]">Tahap 3</span>
                </div>
            </div>
        </div>
    </div>
@endsection
