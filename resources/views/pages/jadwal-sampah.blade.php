@extends('layouts.app')

@section('title', 'Jadwal Armada Pengangkutan — SIGALING PADANG')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
        <div class="space-y-3">
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                Operasional Rutin DLH Kota Padang
            </span>
            <h1 class="text-3xl sm:text-5xl font-semibold text-[#1d1d1f] tracking-tight">
                Jadwal Armada Sampah
            </h1>
            <p class="text-xs sm:text-sm text-[#6e6e73] max-w-2xl leading-relaxed">
                Ketahui rute dan waktu operasional armada compactor serta dump truck di kecamatan Anda untuk mencegah penumpukan sampah liar di bahu jalan.
            </p>
        </div>

        <!-- Perda Advisory Note -->
        <div class="mt-8 p-4 rounded-xl bg-white border border-[#e5e5ea] text-xs text-[#6e6e73] leading-relaxed flex items-start gap-3">
            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706] mt-1.5 flex-shrink-0"></span>
            <div>
                <strong class="font-semibold text-[#1d1d1f]">Waktu Buang Sampah Warga (Perda No. 21/2012):</strong>
                Warga diharapkan meletakkan sampah ke TPS mulai pukul <strong>17.00 WIB hingga 05.00 WIB</strong> agar terangkut bersih pada ritase pagi hari.
            </div>
        </div>
    </section>

    <!-- ===== CLEAN SCHEDULE MATRIX ===== -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="divide-y divide-[#e5e5ea] hairline-y">
            @foreach($jadwalKecamatan as $item)
                <div class="py-6 flex flex-col md:flex-row md:items-start justify-between gap-6 hover:bg-white px-3 -mx-3 rounded-xl transition-colors">
                    <div class="md:w-1/4 space-y-1">
                        <span class="text-xs font-mono text-[#86868b] block">0{{ $loop->iteration }}</span>
                        <h2 class="text-lg font-semibold text-[#1d1d1f]">{{ $item['kecamatan'] }}</h2>
                        <span class="text-xs text-[#0f5132] font-medium block">{{ $item['hari'] }}</span>
                    </div>

                    <div class="md:w-1/2 space-y-2 text-xs">
                        <div>
                            <span class="text-[11px] text-[#86868b] block mb-0.5">Jam Pengangkutan</span>
                            <span class="font-medium text-[#1d1d1f] tabular-nums">{{ $item['jam'] }}</span>
                        </div>

                        <div>
                            <span class="text-[11px] text-[#86868b] block mb-0.5">Titik Rute & Wilayah Lintasan</span>
                            <p class="text-[#6e6e73] leading-relaxed">{{ $item['titik'] }}</p>
                        </div>
                    </div>

                    <div class="md:w-1/4 text-xs text-[#86868b]">
                        <span class="text-[11px] text-[#86868b] block mb-0.5">Armada Bertugas</span>
                        <span class="font-medium text-[#1d1d1f] block">{{ $item['armada'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
