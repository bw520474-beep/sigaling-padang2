<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $laporan_id
 * @property string $nama_file
 * @property string|null $jenis
 * @property string|null $created_at
 * @property-read string $url
 * @property-read \App\Models\Laporan $laporan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereNamaFile($value)
 */
	class FotoLaporan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_kategori
 * @property string|null $ikon
 * @property string|null $deskripsi
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Laporan> $laporans
 * @property-read int|null $laporans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereIkon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereNamaKategori($value)
 */
	class Kategori extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $kode_laporan
 * @property int|null $user_id
 * @property string $nama_pelapor
 * @property string|null $kontak_pelapor
 * @property int $kategori_id
 * @property string $judul
 * @property string $deskripsi
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $alamat
 * @property string|null $kecamatan
 * @property string|null $kelurahan
 * @property string|null $status
 * @property string|null $prioritas
 * @property \Illuminate\Support\Carbon|null $tanggal_laporan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanOpd> $allLaporanOpd
 * @property-read int|null $all_laporan_opd_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoBuktiLaporan
 * @property-read int|null $foto_bukti_laporan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoBuktiPenanganan
 * @property-read int|null $foto_bukti_penanganan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoLaporan
 * @property-read int|null $foto_laporan_count
 * @property-read int $status_step
 * @property-read \App\Models\Kategori $kategori
 * @property-read \App\Models\LaporanOpd|null $laporanOpd
 * @property-read \App\Models\Penanganan|null $latestPenanganan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Penanganan> $penanganans
 * @property-read int|null $penanganans_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKecamatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKelurahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKodeLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKontakPelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereNamaPelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan wherePrioritas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereTanggalLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereUserId($value)
 */
	class Laporan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $laporan_id
 * @property int $opd_id
 * @property int|null $petugas_id
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $tanggal_diteruskan
 * @property-read \App\Models\Laporan $laporan
 * @property-read \App\Models\Opd $opd
 * @property-read \App\Models\User|null $petugas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereOpdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd wherePetugasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereTanggalDiteruskan($value)
 */
	class LaporanOpd extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_opd
 * @property string|null $deskripsi
 * @property string|null $kontak
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanOpd> $laporanOpds
 * @property-read int|null $laporan_opds_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereNamaOpd($value)
 */
	class Opd extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $laporan_id
 * @property int $petugas_id
 * @property string|null $status
 * @property string|null $keterangan
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $tanggal_penanganan
 * @property-read string|null $foto_url
 * @property-read \App\Models\Laporan $laporan
 * @property-read \App\Models\User $petugas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan wherePetugasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereTanggalPenanganan($value)
 */
	class Penanganan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string|null $email
 * @property string|null $password
 * @property string|null $no_hp
 * @property string|null $role
 * @property string|null $remember_token
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanOpd> $laporanOpds
 * @property-read int|null $laporan_opds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Laporan> $laporans
 * @property-read int|null $laporans_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Penanganan> $penanganans
 * @property-read int|null $penanganans_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 */
	class User extends \Eloquent {}
}

