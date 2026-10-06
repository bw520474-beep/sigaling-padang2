<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CivicPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. SEED REGIONS (Wilayah Berjenjang)
        $countryId = DB::table('regions')->insertGetId([
            'parent_id' => null,
            'code' => 'ID',
            'name' => 'Republik Indonesia',
            'type' => 'country',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provinceId = DB::table('regions')->insertGetId([
            'parent_id' => $countryId,
            'code' => 'SB',
            'name' => 'Sumatera Barat',
            'type' => 'province',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cityId = DB::table('regions')->insertGetId([
            'parent_id' => $provinceId,
            'code' => 'PDG',
            'name' => 'Kota Padang',
            'type' => 'city',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $districts = [
            ['code' => 'PDG-01', 'name' => 'Bungus Teluk Kabung'],
            ['code' => 'PDG-02', 'name' => 'Koto Tangah'],
            ['code' => 'PDG-03', 'name' => 'Kuranji'],
            ['code' => 'PDG-04', 'name' => 'Lubuk Begalung'],
            ['code' => 'PDG-05', 'name' => 'Lubuk Kilangan'],
            ['code' => 'PDG-06', 'name' => 'Nanggalo'],
            ['code' => 'PDG-07', 'name' => 'Padang Barat'],
            ['code' => 'PDG-08', 'name' => 'Padang Selatan'],
            ['code' => 'PDG-09', 'name' => 'Padang Timur'],
            ['code' => 'PDG-10', 'name' => 'Padang Utara'],
            ['code' => 'PDG-11', 'name' => 'Pauh'],
        ];

        foreach ($districts as $d) {
            DB::table('regions')->insertOrIgnore([
                'parent_id' => $cityId,
                'code' => $d['code'],
                'name' => $d['name'],
                'type' => 'district',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. SEED ORGANIZATIONS (Disinkronkan dengan OPD yang ada)
        // Cek OPD yang ada di tabel legacy 'opd'
        $existingOpds = DB::table('opd')->get()->keyBy('id');

        $orgDefs = [
            1 => [
                'code' => 'DLH-PDG',
                'name' => 'Dinas Lingkungan Hidup Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas pengelolaan kebersihan persampahan, pengendalian pencemaran air, udara, dan kelestarian lingkungan hidup.',
                'phone' => '(0751) 7051234',
                'email' => 'dlh@padang.go.id',
                'address' => 'Jl. Bagindo Aziz Chan No. 1, Aie Pacah, Kota Padang',
            ],
            2 => [
                'code' => 'PUPR-PDG',
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas infrastruktur jalan kota, jembatan, saluran drainase induk, dan tata ruang wilayah.',
                'phone' => '(0751) 7055678',
                'email' => 'pupr@padang.go.id',
                'address' => 'Jl. Jenderal Sudirman No. 12, Padang Barat, Kota Padang',
            ],
            3 => [
                'code' => 'DISHUT-SB',
                'name' => 'Dinas Kehutanan Provinsi Sumatera Barat',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas konservasi hutan lindung, penghijauan kawasan, dan penanganan flora kawasan hutan.',
                'phone' => '(0751) 7054321',
                'email' => 'kehutanan@sumbarprov.go.id',
                'address' => 'Jl. Raden Saleh No. 8, Padang Barat, Kota Padang',
            ],
            4 => [
                'code' => 'DINKES-PDG',
                'name' => 'Dinas Kesehatan Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas fasilitas kesehatan masyarakat, pencegahan wabah penyakit, sanitasi, dan layanan Puskesmas.',
                'phone' => '(0751) 7059876',
                'email' => 'dinkes@padang.go.id',
                'address' => 'Jl. Perintis Kemerdekaan No. 3, Padang Timur, Kota Padang',
            ],
            5 => [
                'code' => 'SATPOLPP-PDG',
                'name' => 'Satuan Polisi Pamong Praja Kota Padang',
                'type' => 'satpol_pp',
                'description' => 'Bertanggung jawab atas penegakan peraturan daerah (Perda), ketenteraman, dan ketertiban umum masyarakat.',
                'phone' => '(0751) 7058888',
                'email' => 'satpolpp@padang.go.id',
                'address' => 'Jl. Tan Malaka No. 5, Padang Barat, Kota Padang',
            ],
            6 => [
                'code' => 'DISHUB-PDG',
                'name' => 'Dinas Perhubungan Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas manajemen lalu lintas, lampu lalu lintas (APILL), penerangan jalan umum (PJU), dan marka/rambu jalan.',
                'phone' => '(0751) 7051111',
                'email' => 'dishub@padang.go.id',
                'address' => 'Jl. Khatib Sulaiman No. 25, Padang Utara, Kota Padang',
            ],
            7 => [
                'code' => 'DISDUKCAPIL-PDG',
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas layanan administrasi kependudukan (KTP-el, KK, Akta Kelahiran, KIA, Surat Pindah).',
                'phone' => '(0751) 7052222',
                'email' => 'disdukcapil@padang.go.id',
                'address' => 'Jl. Sudirman No. 1, Padang Barat, Kota Padang',
            ],
            8 => [
                'code' => 'DINSOS-PDG',
                'name' => 'Dinas Sosial Kota Padang',
                'type' => 'dinas',
                'description' => 'Bertanggung jawab atas bantuan sosial, penanganan penyandang masalah kesejahteraan sosial (PMKS), lansia terlantar, dan disabilitas.',
                'phone' => '(0751) 7053333',
                'email' => 'dinsos@padang.go.id',
                'address' => 'Jl. Khatib Sulaiman No. 40, Padang Utara, Kota Padang',
            ],
            9 => [
                'code' => 'BPBD-PDG',
                'name' => 'Badan Penanggulangan Bencana Daerah Kota Padang',
                'type' => 'badan',
                'description' => 'Bertanggung jawab atas mitigasi, kesiapsiagaan, evakuasi, dan tanggap darurat bencana alam.',
                'phone' => '(0751) 7054444',
                'email' => 'bpbd@padang.go.id',
                'address' => 'Jl. Bypass KM 12, Sungai Sapih, Kuranji, Kota Padang',
            ],
        ];

        $orgIdMap = [];

        foreach ($orgDefs as $opdKey => $def) {
            // Pastikan OPD legacy juga ada
            $opdRecord = DB::table('opd')->where('id', $opdKey)->first();
            if (!$opdRecord) {
                DB::table('opd')->insert([
                    'id' => $opdKey,
                    'nama_opd' => $def['name'],
                    'deskripsi' => $def['description'],
                    'kontak' => $def['phone'],
                    'created_at' => now(),
                ]);
            }

            // Simpan / update di organizations
            $orgId = DB::table('organizations')->updateOrInsert(
                ['code' => $def['code']],
                [
                    'opd_id' => $opdKey,
                    'region_id' => $cityId,
                    'name' => $def['name'],
                    'type' => $def['type'],
                    'description' => $def['description'],
                    'phone' => $def['phone'],
                    'email' => $def['email'],
                    'address' => $def['address'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $orgRecord = DB::table('organizations')->where('code', $def['code'])->first();
            $orgIdMap[$def['code']] = $orgRecord->id;
        }

        // 3. SEED DEPARTMENTS (Unit/Bidang di setiap Instansi)
        $departmentsData = [
            // PUPR
            ['org' => 'PUPR-PDG', 'code' => 'PUPR-JALAN', 'name' => 'Bidang Bina Marga & Jembatan', 'desc' => 'Perbaikan jalan berlubang, aspal rusak, dan fasilitas jembatan.'],
            ['org' => 'PUPR-PDG', 'code' => 'PUPR-DRAINASE', 'name' => 'Bidang Sumber Daya Air & Drainase', 'desc' => 'Normalisasi saluran drainase primer, gorong-gorong, dan pencegahan banjir.'],
            ['org' => 'PUPR-PDG', 'code' => 'PUPR-CIPTA', 'name' => 'Bidang Cipta Karya & Penataan Ruang', 'desc' => 'Fasilitas gedung umum, trotoar, dan penataan kawasan.'],

            // DLH
            ['org' => 'DLH-PDG', 'code' => 'DLH-SAMPAH', 'name' => 'Bidang Pengelolaan Sampah & Kebersihan', 'desc' => 'Pengangkutan sampah, penanganan TPS liar, dan kebersihan jalan protokol.'],
            ['org' => 'DLH-PDG', 'code' => 'DLH-PENCEMARAN', 'name' => 'Bidang Pengendalian Pencemaran Lingkungan', 'desc' => 'Pengawasan pencemaran air sungai, limbah cair, dan polusi udara industri.'],
            ['org' => 'DLH-PDG', 'code' => 'DLH-TATA', 'name' => 'Bidang Tata Lingkungan & Pertamanan', 'desc' => 'Perawatan pohon peneduh jalan, taman kota, dan ruang terbuka hijau.'],

            // Dishub
            ['org' => 'DISHUB-PDG', 'code' => 'DISHUB-LALIN', 'name' => 'Bidang Lalu Lintas & Rekayasa Jalan', 'desc' => 'Pengaturan rambu, marka jalan, dan perbaikan traffic light (APILL).'],
            ['org' => 'DISHUB-PDG', 'code' => 'DISHUB-PJU', 'name' => 'Bidang Penerangan Jalan Umum (PJU)', 'desc' => 'Perbaikan dan pemeliharaan lampu penerangan jalan umum yang mati/rusak.'],

            // Satpol PP
            ['org' => 'SATPOLPP-PDG', 'code' => 'SATPOLPP-TRANTIB', 'name' => 'Bidang Ketenteraman & Ketertiban Umum', 'desc' => 'Penertiban gangguan trantibum, penertiban PKL liar, dan ketertiban jalan.'],

            // Disdukcapil
            ['org' => 'DISDUKCAPIL-PDG', 'code' => 'DISDUK-DAFTAR', 'name' => 'Bidang Pelayanan Pendaftaran Penduduk', 'desc' => 'Layanan KTP-el, Kartu Keluarga, dan mutasi penduduk.'],

            // Dinkes
            ['org' => 'DINKES-PDG', 'code' => 'DINKES-P2P', 'name' => 'Bidang Pencegahan & Pengendalian Penyakit', 'desc' => 'Penanganan sarang nyamuk DBD, fogging, dan pencegahan wabah infeksi.'],

            // Dinsos
            ['org' => 'DINSOS-PDG', 'code' => 'DINSOS-REHAB', 'name' => 'Bidang Perlindungan & Rehabilitasi Sosial', 'desc' => 'Penanganan PMKS, lansia terlantar, anak jalanan, dan bantuan kemiskinan.'],
        ];

        $deptIdMap = [];
        foreach ($departmentsData as $d) {
            $orgId = $orgIdMap[$d['org']] ?? null;
            if ($orgId) {
                DB::table('departments')->updateOrInsert(
                    ['code' => $d['code']],
                    [
                        'organization_id' => $orgId,
                        'name' => $d['name'],
                        'description' => $d['desc'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $deptRecord = DB::table('departments')->where('code', $d['code'])->first();
                $deptIdMap[$d['code']] = $deptRecord->id;
            }
        }

        // 4. SEED SUBCATEGORIES (Memperluas Taksonomi Masalah Publik)
        // Kategori ID mapping:
        // 1: Sampah, 2: Drainase Tersumbat, 3: Pencemaran Air, 4: Pencemaran Udara,
        // 5: Pohon Tumbang, 6: Pembakaran Sampah, 7: Limbah, 8: Kebisingan, 9: Sungai, 10: Permasalahan Lainnya

        $subcategoriesData = [
            // Kategori 1: Sampah
            ['cat' => 1, 'name' => 'Tumpukan Sampah Liar', 'slug' => 'tumpukan-sampah-liar', 'icon' => 'trash-2', 'priority' => 'Sedang', 'desc' => 'Tumpukan sampah berserakan di bahu jalan, lahan kosong, atau fasilitas umum.'],
            ['cat' => 1, 'name' => 'Jadwal Pengangkutan Terlambat', 'slug' => 'jadwal-pengangkutan-terlambat', 'icon' => 'truck', 'priority' => 'Sedang', 'desc' => 'Armada pengangkut sampah tidak kunjung datang melewati jadwal reguler.'],
            ['cat' => 1, 'name' => 'TPS Liar / Kontainer Penuh Meluap', 'slug' => 'tps-liar-penuh', 'icon' => 'package', 'priority' => 'Sedang', 'desc' => 'Bak kontainer TPS sampah umum sudah meluap hingga tumpah ke badan jalan.'],

            // Kategori 2: Drainase Tersumbat
            ['cat' => 2, 'name' => 'Drainase Tersumbat & Genangan Air', 'slug' => 'drainase-tersumbat-banjir', 'icon' => 'droplets', 'priority' => 'Tinggi', 'desc' => 'Saluran drainase parit mampet oleh sedimentasi lumpur atau sampah hingga memicu genangan saat hujan.'],
            ['cat' => 2, 'name' => 'Gorong-Gorong Amblas / Patah', 'slug' => 'gorong-gorong-amblas', 'icon' => 'alert-triangle', 'priority' => 'Tinggi', 'desc' => 'Konstruksi gorong-gorong drainase amblas atau jebol membahayakan lintasan warga.'],

            // Kategori 3: Pencemaran Air
            ['cat' => 3, 'name' => 'Pembuangan Limbah Cair ke Sungai', 'slug' => 'limbah-cair-sungai', 'icon' => 'waves', 'priority' => 'Tinggi', 'desc' => 'Pembuangan limbah kimia/cair tanpa filter ke badan air sungai atau muara.'],
            ['cat' => 3, 'name' => 'Pencemaran Sumur Warga', 'slug' => 'pencemaran-sumur-warga', 'icon' => 'droplet', 'priority' => 'Tinggi', 'desc' => 'Air sumur warga berubah warna, berminyak, atau berbau akibat resapan limbah sekitar.'],

            // Kategori 4: Pencemaran Udara
            ['cat' => 4, 'name' => 'Polusi Asap Industri / Cerobong', 'slug' => 'asap-industri-cerobong', 'icon' => 'cloud-rain', 'priority' => 'Tinggi', 'desc' => 'Asap pekat dari aktivitas operasional pabrik atau cerobong pembakaran industri.'],
            ['cat' => 4, 'name' => 'Bau Menyengat / Gas Kimia', 'slug' => 'bau-menyengat-kimia', 'icon' => 'wind', 'priority' => 'Sedang', 'desc' => 'Polusi bau busuk menyengat dari tempat usaha, kandang, atau pembuangan limbah.'],

            // Kategori 5: Pohon Tumbang/Rusak
            ['cat' => 5, 'name' => 'Pohon Tumbang Menghalangi Jalan', 'slug' => 'pohon-tumbang-jalan', 'icon' => 'tree-pine', 'priority' => 'Darurat', 'desc' => 'Pohon roboh menutupi akses jalan raya, menimpa kabel, atau membahayakan warga.'],
            ['cat' => 5, 'name' => 'Dahan Lapuk Rawan Jatuh', 'slug' => 'dahan-lapuk-rawan', 'icon' => 'shield-alert', 'priority' => 'Sedang', 'desc' => 'Dahan pohon rindang lapuk yang rawan patah menimpa pengendara di bawahnya.'],

            // Kategori 6: Pembakaran Sampah
            ['cat' => 6, 'name' => 'Pembakaran Sampah Terbuka', 'slug' => 'pembakaran-sampah-terbuka', 'icon' => 'flame', 'priority' => 'Tinggi', 'desc' => 'Pembakaran sampah plastik atau limbah yang menghasilkan asap tebal dan sesak nafas.'],

            // Kategori 7: Limbah
            ['cat' => 7, 'name' => 'Pembuangan Limbah B3 / Medis Ilegal', 'slug' => 'limbah-b3-medis', 'icon' => 'biohazard', 'priority' => 'Darurat', 'desc' => 'Pembuangan jarum suntik, botol bahan kimia berbahaya, atau oli bekas di sembarang tempat.'],

            // Kategori 8: Kebisingan
            ['cat' => 8, 'name' => 'Kebisingan Mesin / Usaha di Pemukiman', 'slug' => 'kebisingan-usaha-pemukiman', 'icon' => 'volume-2', 'priority' => 'Sedang', 'desc' => 'Suara bising dari aktivitas mesin industri atau bengkel di lingkungan pemukiman padat.'],

            // Kategori 9: Sungai
            ['cat' => 9, 'name' => 'Pendangkalan Sungai & Sampah Menumpuk', 'slug' => 'pendangkalan-sungai', 'icon' => 'compass', 'priority' => 'Sedang', 'desc' => 'Aliran sungai tersumbat sedimen dan tumpukan sampah menghambat debit air.'],

            // Kategori 10: Permasalahan Lainnya (Infrastruktur Jalan, PJU, Adminduk, Kesehatan, Ketertiban)
            ['cat' => 10, 'name' => 'Jalan Berlubang / Aspal Rusak Parah', 'slug' => 'jalan-rusak-berlubang', 'icon' => 'alert-circle', 'priority' => 'Tinggi', 'desc' => 'Lubang jalan raya yang dalam dan membahayakan keselamatan pengendara.'],
            ['cat' => 10, 'name' => 'Lampu Lalu Lintas (Traffic Light) Mati', 'slug' => 'lampu-lalin-mati', 'icon' => 'traffic-cone', 'priority' => 'Tinggi', 'desc' => 'Lampu pengatur lalu lintas mati atau mengalami gangguan sinyal.'],
            ['cat' => 10, 'name' => 'Lampu Penerangan Jalan (PJU) Mati', 'slug' => 'lampu-pju-mati', 'icon' => 'lightbulb', 'priority' => 'Sedang', 'desc' => 'Lampu jalan umum padam mengakibatkan kawasan gelap dan rawan kriminalitas.'],
            ['cat' => 10, 'name' => 'Gangguan Ketertiban / Balap Liar', 'slug' => 'gangguan-trantibum', 'icon' => 'shield', 'priority' => 'Tinggi', 'desc' => 'Gangguan keamanan lingkungan, tawuran, atau aktivitas balap liar di jalan raya.'],
            ['cat' => 10, 'name' => 'Sarang Jentik Nyamuk / Rawan DBD', 'slug' => 'rawan-dbd-fogging', 'icon' => 'activity', 'priority' => 'Tinggi', 'desc' => 'Permintaan fogging atau pembersihan genangan air sarang nyamuk penular DBD.'],
            ['cat' => 10, 'name' => 'Keluhan Layanan Adminduk / KTP', 'slug' => 'layanan-adminduk', 'icon' => 'id-card', 'priority' => 'Rendah', 'desc' => 'Kendala pengurusan dokumen kependudukan seperti KTP-el, KK, atau Akta.'],
        ];

        $subcatIdMap = [];
        foreach ($subcategoriesData as $sc) {
            DB::table('subcategories')->updateOrInsert(
                ['slug' => $sc['slug']],
                [
                    'category_id' => $sc['cat'],
                    'name' => $sc['name'],
                    'icon' => $sc['icon'],
                    'default_priority' => $sc['priority'],
                    'description' => $sc['desc'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $subcatRecord = DB::table('subcategories')->where('slug', $sc['slug'])->first();
            $subcatIdMap[$sc['slug']] = $subcatRecord->id;
        }

        // 5. SEED SMART ROUTING RULES (Aturan Pemetaan Tanpa Hardcode)
        $rulesData = [
            // Sampah -> DLH Bidang Sampah
            ['subcat' => 'tumpukan-sampah-liar', 'cat' => 1, 'org' => 'DLH-PDG', 'dept' => 'DLH-SAMPAH', 'prio' => 'Sedang', 'sla' => 48],
            ['subcat' => 'jadwal-pengangkutan-terlambat', 'cat' => 1, 'org' => 'DLH-PDG', 'dept' => 'DLH-SAMPAH', 'prio' => 'Sedang', 'sla' => 24],
            ['subcat' => 'tps-liar-penuh', 'cat' => 1, 'org' => 'DLH-PDG', 'dept' => 'DLH-SAMPAH', 'prio' => 'Sedang', 'sla' => 36],
            ['subcat' => 'pembakaran-sampah-terbuka', 'cat' => 6, 'org' => 'DLH-PDG', 'dept' => 'DLH-SAMPAH', 'prio' => 'Tinggi', 'sla' => 12],

            // Drainase -> PUPR Drainase
            ['subcat' => 'drainase-tersumbat-banjir', 'cat' => 2, 'org' => 'PUPR-PDG', 'dept' => 'PUPR-DRAINASE', 'prio' => 'Tinggi', 'sla' => 48],
            ['subcat' => 'gorong-gorong-amblas', 'cat' => 2, 'org' => 'PUPR-PDG', 'dept' => 'PUPR-DRAINASE', 'prio' => 'Tinggi', 'sla' => 48],

            // Pencemaran Air & Limbah -> DLH Pencemaran
            ['subcat' => 'limbah-cair-sungai', 'cat' => 3, 'org' => 'DLH-PDG', 'dept' => 'DLH-PENCEMARAN', 'prio' => 'Tinggi', 'sla' => 24],
            ['subcat' => 'pencemaran-sumur-warga', 'cat' => 3, 'org' => 'DLH-PDG', 'dept' => 'DLH-PENCEMARAN', 'prio' => 'Tinggi', 'sla' => 24],
            ['subcat' => 'limbah-b3-medis', 'cat' => 7, 'org' => 'DLH-PDG', 'dept' => 'DLH-PENCEMARAN', 'prio' => 'Darurat', 'sla' => 12],

            // Pencemaran Udara -> DLH Pencemaran
            ['subcat' => 'asap-industri-cerobong', 'cat' => 4, 'org' => 'DLH-PDG', 'dept' => 'DLH-PENCEMARAN', 'prio' => 'Tinggi', 'sla' => 24],
            ['subcat' => 'bau-menyengat-kimia', 'cat' => 4, 'org' => 'DLH-PDG', 'dept' => 'DLH-PENCEMARAN', 'prio' => 'Sedang', 'sla' => 48],

            // Pohon Tumbang -> DLH Pertamanan
            ['subcat' => 'pohon-tumbang-jalan', 'cat' => 5, 'org' => 'DLH-PDG', 'dept' => 'DLH-TATA', 'prio' => 'Darurat', 'sla' => 6],
            ['subcat' => 'dahan-lapuk-rawan', 'cat' => 5, 'org' => 'DLH-PDG', 'dept' => 'DLH-TATA', 'prio' => 'Sedang', 'sla' => 72],

            // Sungai -> DLH / PUPR
            ['subcat' => 'pendangkalan-sungai', 'cat' => 9, 'org' => 'PUPR-PDG', 'dept' => 'PUPR-DRAINASE', 'prio' => 'Sedang', 'sla' => 96],

            // Jalan Rusak -> PUPR Jalan
            ['subcat' => 'jalan-rusak-berlubang', 'cat' => 10, 'org' => 'PUPR-PDG', 'dept' => 'PUPR-JALAN', 'prio' => 'Tinggi', 'sla' => 72],

            // Lampu Lalu Lintas -> Dishub Lalin
            ['subcat' => 'lampu-lalin-mati', 'cat' => 10, 'org' => 'DISHUB-PDG', 'dept' => 'DISHUB-LALIN', 'prio' => 'Tinggi', 'sla' => 24],

            // Lampu Penerangan Jalan -> Dishub PJU
            ['subcat' => 'lampu-pju-mati', 'cat' => 10, 'org' => 'DISHUB-PDG', 'dept' => 'DISHUB-PJU', 'prio' => 'Sedang', 'sla' => 48],

            // Gangguan Trantibum -> Satpol PP
            ['subcat' => 'gangguan-trantibum', 'cat' => 10, 'org' => 'SATPOLPP-PDG', 'dept' => 'SATPOLPP-TRANTIB', 'prio' => 'Tinggi', 'sla' => 12],

            // Rawan DBD -> Dinkes
            ['subcat' => 'rawan-dbd-fogging', 'cat' => 10, 'org' => 'DINKES-PDG', 'dept' => 'DINKES-P2P', 'prio' => 'Tinggi', 'sla' => 48],

            // Layanan Adminduk -> Disdukcapil
            ['subcat' => 'layanan-adminduk', 'cat' => 10, 'org' => 'DISDUKCAPIL-PDG', 'dept' => 'DISDUK-DAFTAR', 'prio' => 'Rendah', 'sla' => 72],
        ];

        foreach ($rulesData as $r) {
            $orgId = $orgIdMap[$r['org']] ?? null;
            $deptId = $deptIdMap[$r['dept']] ?? null;
            $subcatId = $subcatIdMap[$r['subcat']] ?? null;

            if ($orgId && $subcatId) {
                DB::table('routing_rules')->updateOrInsert(
                    [
                        'category_id' => $r['cat'],
                        'subcategory_id' => $subcatId,
                    ],
                    [
                        'region_id' => null, // Berlaku global / default
                        'organization_id' => $orgId,
                        'department_id' => $deptId,
                        'priority' => $r['prio'],
                        'sla_hours' => $r['sla'],
                        'notes' => 'Aturan routing standar hasil rekomendasi civic governance.',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 6. SEED SLA RULES
        $slaLevels = [
            ['priority' => 'Darurat', 'response' => 2, 'resolution' => 12, 'warning' => 75],
            ['priority' => 'Tinggi', 'response' => 6, 'resolution' => 24, 'warning' => 75],
            ['priority' => 'Sedang', 'response' => 24, 'resolution' => 72, 'warning' => 75],
            ['priority' => 'Rendah', 'response' => 48, 'resolution' => 120, 'warning' => 75],
        ];

        foreach ($slaLevels as $sla) {
            DB::table('sla_rules')->updateOrInsert(
                ['priority' => $sla['priority'], 'category_id' => null, 'subcategory_id' => null],
                [
                    'response_time_hours' => $sla['response'],
                    'resolution_time_hours' => $sla['resolution'],
                    'warning_threshold_percent' => $sla['warning'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 7. SEED USERS BERDASARKAN 6 PERAN
        $usersToSeed = [
            // 1. Super Administrator Global
            [
                'email' => 'admin@sigaling.local',
                'nama' => 'Super Administrator SIGALING',
                'role' => 'superadmin',
                'opd_id' => null,
                'no_hp' => '081234567890',
            ],
            // 2. Admin Instansi (OPD)
            [
                'email' => 'admin.pupr@sigaling.local',
                'nama' => 'Admin Instansi Dinas PUPR',
                'role' => 'admin_instansi',
                'opd_id' => 2,
                'no_hp' => '081234567891',
            ],
            [
                'email' => 'admin.dlh@sigaling.local',
                'nama' => 'Admin Instansi Dinas Lingkungan Hidup',
                'role' => 'admin_instansi',
                'opd_id' => 1,
                'no_hp' => '081234567892',
            ],
            [
                'email' => 'admin.dishub@sigaling.local',
                'nama' => 'Admin Instansi Dinas Perhubungan',
                'role' => 'admin_instansi',
                'opd_id' => 6,
                'no_hp' => '081234567893',
            ],
            // 3. Supervisor Instansi
            [
                'email' => 'supervisor.dlh@sigaling.local',
                'nama' => 'Supervisor Kebersihan & Lingkungan DLH',
                'role' => 'supervisor',
                'opd_id' => 1,
                'no_hp' => '081234567894',
            ],
            [
                'email' => 'supervisor.pupr@sigaling.local',
                'nama' => 'Supervisor Jalan & Drainase PUPR',
                'role' => 'supervisor',
                'opd_id' => 2,
                'no_hp' => '081234567895',
            ],
            [
                'email' => 'supervisor.dishub@sigaling.local',
                'nama' => 'Supervisor Keselamatan Dishub',
                'role' => 'supervisor',
                'opd_id' => 6,
                'no_hp' => '081234567896',
            ],
            // 4. Petugas Lapangan
            [
                'email' => 'petugas.dishub@sigaling.local',
                'nama' => 'Randi Petugas Dishub',
                'role' => 'petugas',
                'opd_id' => 6,
                'no_hp' => '081234567897',
            ],
            // 5. Auditor / Pengawas
            [
                'email' => 'auditor@sigaling.local',
                'nama' => 'Inspektorat / Pengawas Publik SIGALING',
                'role' => 'auditor',
                'opd_id' => null,
                'no_hp' => '081234567898',
            ],
        ];

        foreach ($usersToSeed as $u) {
            $user = User::where('email', $u['email'])->first();
            if ($user) {
                $user->role = $u['role'];
                $user->opd_id = $u['opd_id'];
                $user->nama = $u['nama'];
                $user->save();
            } else {
                User::create([
                    'nama' => $u['nama'],
                    'email' => $u['email'],
                    'password' => 'password', // akan di-hash oleh casts
                    'role' => $u['role'],
                    'opd_id' => $u['opd_id'],
                    'no_hp' => $u['no_hp'],
                ]);
            }
        }

        // Update laporan lama agar memiliki data reporter_identities & report_assignments
        $laporans = DB::table('laporan')->get();
        foreach ($laporans as $lap) {
            // Cek apakah sudah ada reporter_identities
            $hasIdentity = DB::table('reporter_identities')->where('report_id', $lap->id)->exists();
            if (!$hasIdentity) {
                DB::table('reporter_identities')->insert([
                    'report_id' => $lap->id,
                    'user_id' => $lap->user_id,
                    'reporter_name' => $lap->nama_pelapor ?: 'Warga Masyarakat',
                    'contact' => $lap->kontak_pelapor ?: '08123456789',
                    'id_card_number' => null,
                    'protection_level' => 'public',
                    'address_detail' => $lap->alamat,
                    'created_at' => $lap->created_at ?: now(),
                    'updated_at' => $lap->created_at ?: now(),
                ]);
            }

            // Sync ke report_assignments jika ada laporan_opd
            $laporanOpdList = DB::table('laporan_opd')->where('laporan_id', $lap->id)->get();
            foreach ($laporanOpdList as $lopd) {
                $orgRecord = DB::table('organizations')->where('opd_id', $lopd->opd_id)->first();
                $orgId = $orgRecord ? $orgRecord->id : 1;

                $hasAssignment = DB::table('report_assignments')
                    ->where('report_id', $lap->id)
                    ->where('organization_id', $orgId)
                    ->exists();

                if (!$hasAssignment) {
                    $status = 'received';
                    if ($lap->status === 'Dalam Penanganan') $status = 'in_progress';
                    elseif ($lap->status === 'Selesai') $status = 'resolved';
                    elseif ($lap->status === 'Ditolak') $status = 'rejected';

                    DB::table('report_assignments')->insert([
                        'report_id' => $lap->id,
                        'organization_id' => $orgId,
                        'department_id' => null,
                        'supervisor_id' => null,
                        'officer_id' => $lopd->petugas_id,
                        'status' => $status,
                        'priority' => $lap->prioritas ?: 'Sedang',
                        'sla_due_at' => now()->addDays(3),
                        'notes' => $lopd->catatan,
                        'created_at' => $lopd->tanggal_diteruskan ?: now(),
                        'updated_at' => $lopd->tanggal_diteruskan ?: now(),
                    ]);
                }
            }
        }
    }
}
