<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Halaman Beranda Utama - SIGALING Padang
     */
    public function index()
    {
        // 1. Statistik Ringkas
        $totalLaporan = Laporan::count();
        $laporanTerverifikasi = Laporan::where('status', 'Terverifikasi')->count();
        $laporanProses = Laporan::where('status', 'Dalam Penanganan')->count();
        $laporanSelesai = Laporan::where('status', 'Selesai')->count();

        // 2. Kategori Masalah Lingkungan
        $kategoris = Kategori::withCount('laporans')->orderBy('nama_kategori', 'asc')->get();

        // 3. Laporan Publik Terbaru (dengan foto dan kategori)
        $laporanTerbaru = Laporan::with(['kategori', 'fotoBuktiLaporan'])
            ->whereIn('status', ['Terverifikasi', 'Dalam Penanganan', 'Selesai'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 4. Pesan & Feedback Masyarakat Kota Padang (Autentik & Terpilih)
        $pesanMasyarakat = [
            [
                'nama' => 'Hendra Saputra',
                'lokasi' => 'Kec. Kuranji',
                'kategori' => 'Sampah Liar & TPS',
                'pesan' => 'Tumpukan sampah liar di bypass dekat perumahan sudah diangkut bersih oleh armada DLH dalam waktu 2 hari setelah dilaporkan. Terima kasih respon cepatnya!',
                'tanggal' => '26 Agustus 2026',
                'status' => 'Selesai',
            ],
            [
                'nama' => 'Nurul Aini',
                'lokasi' => 'Kec. Padang Barat',
                'kategori' => 'Drainase & Saluran Air',
                'pesan' => 'Fitur peta sangat memudahkan kami melihat progres laporan parit tersumbat di lingkungan kami. Sangat transparan dan membantu.',
                'tanggal' => '24 Agustus 2026',
                'status' => 'Selesai',
            ],
            [
                'nama' => 'Syamsul Rizal',
                'lokasi' => 'Kec. Koto Tangah',
                'kategori' => 'Pohon Rawan Tumbang',
                'pesan' => 'Dahan beringin tua yang lapuk dekat jalan utama langsung dipangkas oleh tim dinas terkait sebelum musim hujan badai datang.',
                'tanggal' => '21 Agustus 2026',
                'status' => 'Selesai',
            ],
            [
                'nama' => 'Rina Marlina',
                'lokasi' => 'Kec. Lubuk Begalung',
                'kategori' => 'Pencemaran Air & Sungai',
                'pesan' => 'Sistem pelacakan dengan kode laporan sangat jelas, kita bisa tahu posisi pengaduan sedang ditangani oleh OPD mana.',
                'tanggal' => '18 Agustus 2026',
                'status' => 'Dalam Penanganan',
            ],
        ];

        return view('pages.home', compact(
            'totalLaporan',
            'laporanTerverifikasi',
            'laporanProses',
            'laporanSelesai',
            'kategoris',
            'laporanTerbaru',
            'pesanMasyarakat'
        ));
    }

    /**
     * Halaman Alur & SOP Pelaporan
     */
    public function alur()
    {
        return view('pages.alur');
    }

    /**
     * Halaman Edukasi Lingkungan, 3R & Bank Sampah
     */
    public function edukasi()
    {
        return view('pages.edukasi');
    }

    /**
     * Halaman Jadwal Operasional Pengangkutan Sampah Kota Padang
     */
    public function jadwalSampah()
    {
        $jadwalKecamatan = [
            [
                'kecamatan' => 'Kuranji',
                'hari' => 'Senin, Rabu, Jumat',
                'jam' => '05:30 - 08:30 WIB & 16:00 - 18:30 WIB',
                'titik' => 'Jl. Bypass KM 6-10, Korong Gadang, Pasar Ambacang, Gn. Sarik, Anduring',
                'armada' => 'Compactor 04 & Dump Truck 08 DLH Padang',
            ],
            [
                'kecamatan' => 'Padang Barat',
                'hari' => 'Setiap Hari (Senin - Minggu)',
                'jam' => '05:00 - 07:30 WIB & 18:00 - 21:00 WIB',
                'titik' => 'Kawasan Pantai Purus, Jl. Samudera, Olo Ladang, Padang Pasir, Ujung Gurun',
                'armada' => 'Compactor 01, 02 & Petugas Gerobak Motor',
            ],
            [
                'kecamatan' => 'Koto Tangah',
                'hari' => 'Selasa, Kamis, Sabtu',
                'jam' => '06:00 - 09:00 WIB & 15:30 - 18:00 WIB',
                'titik' => 'Lubuk Buaya, Tabing, Batipuh Panjang, Air Pacah, Sungai Bangek',
                'armada' => 'Dump Truck 11 & Arm Roll 05 DLH Padang',
            ],
            [
                'kecamatan' => 'Padang Timur',
                'hari' => 'Senin, Rabu, Sabtu',
                'jam' => '05:30 - 08:30 WIB & 16:30 - 19:00 WIB',
                'titik' => 'Jati, Sawahan, Simpang Haru, Kubu Marapalam, Ganting Parak Gadang',
                'armada' => 'Compactor 03 & Dump Truck 06',
            ],
            [
                'kecamatan' => 'Lubuk Begalung',
                'hari' => 'Selasa, Jumat, Minggu',
                'jam' => '06:00 - 09:00 WIB & 16:00 - 18:30 WIB',
                'titik' => 'Pengambiran, Banuaran, Gurun Laweh, Gates Nan XX, Simpang Gaung',
                'armada' => 'Dump Truck 09 & Arm Roll 03',
            ],
            [
                'kecamatan' => 'Padang Selatan',
                'hari' => 'Setiap Hari',
                'jam' => '05:00 - 08:00 WIB & 17:00 - 20:00 WIB',
                'titik' => 'Batang Arau, Seberang Padang, Teluk Bayur, Bukit Gado-Gado, Rawang',
                'armada' => 'Compactor 05 & Armada Sungai Batang Arau',
            ],
        ];

        return view('pages.jadwal-sampah', compact('jadwalKecamatan'));
    }

    /**
     * Halaman Pusat Bantuan & Tanya Jawab (FAQ)
     */
    public function faq()
    {
        return view('pages.faq');
    }
}
