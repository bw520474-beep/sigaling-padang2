<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pengaduan Masyarakat - Kota Padang</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background-color: #f3f4f6;
            padding: 24px;
            font-size: 12pt;
            line-height: 1.4;
        }

        /* Container Dokumen (Lembar Kertas A4) */
        .paper {
            background-color: #ffffff;
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 25mm 20mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Top Action Bar (Non-Printable) */
        .action-bar {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .btn-print {
            background-color: #059669;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background-color: #047857;
        }

        .btn-back {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        /* Kop Surat Resmi */
        .kop-surat {
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }

        .kop-logo {
            position: absolute;
            left: 5px;
            top: 5px;
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-header {
            margin-left: 75px;
            margin-right: 75px;
        }

        .kop-instansi-1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-instansi-2 {
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 9.5pt;
            font-style: italic;
            color: #333333;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 20px 0 15px 0;
        }

        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .doc-title p {
            font-size: 10pt;
            margin-top: 4px;
        }

        /* Metadata & Filter Information */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Summary Cards Table */
        .summary-box {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 18px;
            font-family: system-ui, -apple-system, sans-serif;
            text-align: center;
        }

        .sum-item {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px;
            background-color: #f9fafb;
        }

        .sum-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            display: block;
        }

        .sum-val {
            font-size: 14pt;
            font-weight: 900;
            color: #111827;
            display: block;
            margin-top: 2px;
        }

        /* Data Table */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 30px;
        }

        .table-data th {
            border: 1px solid #000000;
            padding: 6px 5px;
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .table-data td {
            border: 1px solid #000000;
            padding: 5px 6px;
            vertical-align: top;
        }

        .table-data tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .ttd-box {
            float: right;
            width: 240px;
            text-align: center;
            font-size: 11pt;
        }

        .ttd-space {
            height: 70px;
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Media Style */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 0;
            }

            @page {
                size: A4 landscape;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Printable Action Bar -->
    <div class="action-bar">
        <a href="{{ route('admin.laporan.rekap', request()->all()) }}" class="btn-back">
            &larr; Kembali ke Filter
        </a>
        <button onclick="window.print()" class="btn-print">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Cetak Dokumen Sekarang</span>
        </button>
    </div>

    <!-- Halaman Dokumen Lembar Kertas -->
    <div class="paper">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <!-- Lambang Pemko Padang / DLH -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo Padang" class="kop-logo" onerror="this.src='{{ asset('images/hero/hero-bg.png') }}'; this.style.borderRadius='50%'">
            
            <div class="kop-header">
                <div class="kop-instansi-1">PEMERINTAH KOTA PADANG</div>
                <div class="kop-instansi-2">DINAS LINGKUNGAN HIDUP</div>
                <div class="kop-alamat">
                    Jl. Bagindo Aziz Chan No. 1, Aie Pacah, Kec. Koto Tangah, Kota Padang, Sumatera Barat<br>
                    Laman Resmi: dlh.padang.go.id &bull; Sistem Informasi Pengaduan Lingkungan (SIGALING PADANG)
                </div>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h2>REKAPITULASI PENGADUAN MASYARAKAT</h2>
            <p>SISTEM INFORMASI PENGADUAN & PENANGANAN LINGKUNGAN HIDUP KOTA PADANG</p>
        </div>

        <!-- Parameter Metadata -->
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Periode</strong></td>
                <td width="2%">:</td>
                <td width="38%">
                    @if(request('tgl_mulai') || request('tgl_selesai'))
                        {{ request('tgl_mulai') ? \Carbon\Carbon::parse(request('tgl_mulai'))->translatedFormat('d F Y') : 'Awal' }}
                        s/d
                        {{ request('tgl_selesai') ? \Carbon\Carbon::parse(request('tgl_selesai'))->translatedFormat('d F Y') : 'Sekarang' }}
                    @else
                        Semua Periode / Riwayat Tercatat
                    @endif
                </td>
                <td width="15%"><strong>Wilayah Kecamatan</strong></td>
                <td width="2%">:</td>
                <td width="28%">{{ request('kecamatan') ? request('kecamatan') : 'Seluruh Kecamatan (11)' }}</td>
            </tr>
            <tr>
                <td><strong>Status Laporan</strong></td>
                <td>:</td>
                <td>{{ request('status') ? request('status') : 'Semua Status' }}</td>
                <td><strong>Instansi OPD</strong></td>
                <td>:</td>
                <td>{{ $selectedOpd ? $selectedOpd->nama_opd : 'Seluruh OPD Terkait' }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Pada</strong></td>
                <td>:</td>
                <td>{{ now()->translatedFormat('l, d F Y - H:i') }} WIB</td>
                <td><strong>Total Data</strong></td>
                <td>:</td>
                <td><strong>{{ $totalLaporan }} Pengaduan</strong></td>
            </tr>
        </table>

        <!-- Ringkasan Statistik Angka -->
        <div class="summary-box">
            <div class="sum-item">
                <span class="sum-label">Total Aduan</span>
                <span class="sum-val">{{ $totalLaporan }}</span>
            </div>
            <div class="sum-item">
                <span class="sum-label">Menunggu</span>
                <span class="sum-val">{{ $totalMenunggu }}</span>
            </div>
            <div class="sum-item">
                <span class="sum-label">Dalam Proses</span>
                <span class="sum-val">{{ $totalProses }}</span>
            </div>
            <div class="sum-item">
                <span class="sum-label">Selesai Tuntas</span>
                <span class="sum-val">{{ $totalSelesai }}</span>
            </div>
            <div class="sum-item">
                <span class="sum-label">Ditolak</span>
                <span class="sum-val">{{ $totalDitolak }}</span>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Rinci -->
        <table class="table-data">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Kode Tiket</th>
                    <th width="10%">Tanggal</th>
                    <th width="13%">Kategori</th>
                    <th width="17%">Judul Pengaduan</th>
                    <th width="15%">Lokasi & Kecamatan</th>
                    <th width="12%">Nama Pelapor</th>
                    <th width="10%">OPD Pelaksana</th>
                    <th width="8%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="font-bold text-center">{{ $item->kode_laporan }}</td>
                        <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>
                            <strong>{{ $item->kecamatan ?? 'Kota Padang' }}</strong><br>
                            <small>{{ $item->alamat }}</small>
                        </td>
                        <td>
                            {{ $item->nama_pelapor }}<br>
                            <small>{{ $item->kontak_pelapor }}</small>
                        </td>
                        <td>{{ $item->laporanOpd->opd->nama_opd ?? '-' }}</td>
                        <td class="text-center font-bold">{{ $item->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px;">
                            Tidak ada data pengaduan yang sesuai dengan parameter filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Tanda Tangan Pejabat -->
        <div class="ttd-wrapper">
            <div class="ttd-box">
                Padang, {{ now()->translatedFormat('d F Y') }}<br>
                Kepala Dinas Lingkungan Hidup<br>
                Kota Padang
                <div class="ttd-space"></div>
                <div class="ttd-name">Ir. H. Feri Mulyani Hamid, M.Biomed</div>
                <div>NIP. 19680320 199603 1 002</div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

</body>
</html>
