@extends('layouts.app')

@section('title', 'Alur Penanganan & SOP — SIGALING PADANG')

@section('content')
    <!-- ===== EDITORIAL HEADER ===== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
        <div class="space-y-3 max-w-3xl">
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block">
                Standar Operasional Prosedur (SOP)
            </span>
            <h1 class="text-3xl sm:text-5xl font-semibold text-[#1d1d1f] tracking-tight">
                Alur Penanganan Pengaduan
            </h1>
            <p class="text-xs sm:text-sm text-[#6e6e73] leading-relaxed">
                Setiap aduan yang masuk ke SIGALING diproses melalui tahapan baku yang terukur, transparan, dan dapat dipantau langsung oleh masyarakat Kota Padang.
            </p>
        </div>
    </section>

    <!-- ===== 4-STAGE COLUMN & TABLE SOP PROGRESSION ===== -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-12">
        
        <!-- 1. ALUR TAMPILAN KOLOM BERSEKAT TEGAS -->
        <div>
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block mb-3">Struktur 4 Kolom Berjenjang</span>
            <div class="border border-[#d2d2d7] rounded-2xl overflow-hidden bg-white shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-[#d2d2d7]">
                    
                    <!-- Kolom 1 -->
                    <div class="p-6 space-y-3 flex flex-col justify-between hover:bg-[#fbfbfd] transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-semibold text-[#86868b]">01 / TAHAP</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#f5f5f7] text-[#1d1d1f]">Masyarakat</span>
                            </div>
                            <h3 class="text-base font-semibold text-[#1d1d1f]">Warga Mengirim Aduan</h3>
                            <p class="text-xs text-[#6e6e73] mt-2 leading-relaxed">
                                Pengisian formulir online disertai foto bukti nyata dan pemilihan titik koordinat presisi pada peta GPS.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#d2d2d7] flex items-center justify-between text-[11px] text-[#86868b]">
                            <span>SLA Output:</span>
                            <strong class="font-mono text-[#1d1d1f]">Tiket Real-time</strong>
                        </div>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="p-6 space-y-3 flex flex-col justify-between hover:bg-[#fbfbfd] transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-semibold text-[#86868b]">02 / TAHAP</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#eff6ff] text-[#1e40af]">Admin DLH</span>
                            </div>
                            <h3 class="text-base font-semibold text-[#1d1d1f]">Verifikasi & Validasi</h3>
                            <p class="text-xs text-[#6e6e73] mt-2 leading-relaxed">
                                Pemeriksaan integritas data, audit foto dokumentasi bebas manipulasi, dan penetapan skala urgensi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#d2d2d7] flex items-center justify-between text-[11px] text-[#86868b]">
                            <span>SLA Output:</span>
                            <strong class="font-mono text-[#1e40af]">&lt; 24 Jam Kerja</strong>
                        </div>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="p-6 space-y-3 flex flex-col justify-between hover:bg-[#fbfbfd] transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-semibold text-[#86868b]">03 / TAHAP</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#f3e8ff] text-[#6b21a8]">OPD Teknis</span>
                            </div>
                            <h3 class="text-base font-semibold text-[#1d1d1f]">Disposisi & Aksi Lapangan</h3>
                            <p class="text-xs text-[#6e6e73] mt-2 leading-relaxed">
                                Pengerahan armada compactor, dump truck, atau petugas kebersihan langsung ke titik koordinat aduan.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#d2d2d7] flex items-center justify-between text-[11px] text-[#86868b]">
                            <span>SLA Output:</span>
                            <strong class="font-mono text-[#6b21a8]">1 &ndash; 3 Hari</strong>
                        </div>
                    </div>

                    <!-- Kolom 4 -->
                    <div class="p-6 space-y-3 flex flex-col justify-between hover:bg-[#fbfbfd] transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-semibold text-[#86868b]">04 / TAHAP</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#ecfdf5] text-[#065f46]">Penyelesaian</span>
                            </div>
                            <h3 class="text-base font-semibold text-[#1d1d1f]">Pembuktian & Selesai</h3>
                            <p class="text-xs text-[#6e6e73] mt-2 leading-relaxed">
                                Petugas mengunggah dokumentasi foto kondisi setelah bersih. Kasus ditutup tuntas dan terbuka untuk publik.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#d2d2d7] flex items-center justify-between text-[11px] text-[#86868b]">
                            <span>SLA Output:</span>
                            <strong class="font-mono text-[#065f46]">Foto Tuntas</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- 2. ALUR TAMPILAN TABEL MATRIKS SOP LENGKAP -->
        <div>
            <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider block mb-3">Matriks Standar Operasional Prosedur (SOP)</span>
            <div class="border border-[#d2d2d7] rounded-xl overflow-hidden bg-white shadow-xs">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#f5f5f7] border-b border-[#d2d2d7] text-[11px] font-semibold text-[#86868b] uppercase tracking-wider">
                            <th class="py-3.5 px-5">Tahapan Alur</th>
                            <th class="py-3.5 px-5">Penanggung Jawab</th>
                            <th class="py-3.5 px-5">Aktivitas Teknis & Dokumen</th>
                            <th class="py-3.5 px-5 text-right">Batas Waktu (SLA)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2d2d7] text-[#1d1d1f]">
                        <tr class="hover:bg-[#fbfbfd]">
                            <td class="py-3.5 px-5 font-semibold text-[#1d1d1f]">01. Pelaporan Daring</td>
                            <td class="py-3.5 px-5 text-[#6e6e73]">Warga Kota Padang</td>
                            <td class="py-3.5 px-5">Input formulir elektronik, lampiran bukti visual, dan penentuan koordinat GPS presisi</td>
                            <td class="py-3.5 px-5 text-right font-mono tabular-nums text-[#0f5132] font-semibold">Real-time</td>
                        </tr>
                        <tr class="hover:bg-[#fbfbfd]">
                            <td class="py-3.5 px-5 font-semibold text-[#1d1d1f]">02. Verifikasi & Audit</td>
                            <td class="py-3.5 px-5 text-[#6e6e73]">Admin Verifikator DLH</td>
                            <td class="py-3.5 px-5">Pemeriksaan integritas data, validasi foto, cek duplikasi laporan, dan penetapan status</td>
                            <td class="py-3.5 px-5 text-right font-mono tabular-nums text-[#1e40af] font-semibold">&le; 24 Jam Kerja</td>
                        </tr>
                        <tr class="hover:bg-[#fbfbfd]">
                            <td class="py-3.5 px-5 font-semibold text-[#1d1d1f]">03. Disposisi & Lapangan</td>
                            <td class="py-3.5 px-5 text-[#6e6e73]">Bidang Teknis DLH / UPT</td>
                            <td class="py-3.5 px-5">Pengerahan armada truk compactor, petugas kebersihan, atau koordinasi lintas instansi</td>
                            <td class="py-3.5 px-5 text-right font-mono tabular-nums text-[#6b21a8] font-semibold">1 &ndash; 3 Hari</td>
                        </tr>
                        <tr class="hover:bg-[#fbfbfd]">
                            <td class="py-3.5 px-5 font-semibold text-[#1d1d1f]">04. Pembuktian & Penutupan</td>
                            <td class="py-3.5 px-5 text-[#6e6e73]">Petugas Lapangan & Sistem</td>
                            <td class="py-3.5 px-5">Pengunggahan foto hasil bersih tuntas, validasi penutupan tiket, dan transparansi publik</td>
                            <td class="py-3.5 px-5 text-right font-mono tabular-nums text-[#065f46] font-semibold">Status Tuntas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Standar Pelayanan Komitmen -->
        <div class="pt-8 hairline-t space-y-4 max-w-2xl">
            <h3 class="text-base font-semibold text-[#1d1d1f]">Prinsip Keterbukaan Informasi</h3>
            <p class="text-xs text-[#6e6e73] leading-relaxed">
                Seluruh data penanganan aduan di platform SIGALING terintegrasi langsung dengan instrumen monitoring kinerja dinas. Identitas pelapor dilindungi sesuai peraturan perundang-undangan keterbukaan informasi dan privasi data publik.
            </p>
        </div>
    </div>
@endsection
