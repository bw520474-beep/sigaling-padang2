<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tps', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tps', 30)->unique()->nullable();
            $table->string('nama_tps', 150);
            $table->string('kecamatan', 60);
            $table->string('kelurahan', 80)->nullable();
            $table->text('alamat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('jenis_tps', 50)->default('Kontainer Sampah'); // Kontainer Sampah, TPS 3R, Transfer Depo, Bak Beton
            $table->string('kapasitas', 80)->nullable(); // contoh: 6 m³ (1 Kontainer Armroll)
            $table->string('jadwal_angkut', 120)->nullable(); // contoh: Setiap Hari (06:00 & 17:00 WIB)
            $table->enum('status', ['Aktif', 'Hampir Penuh', 'Penuh', 'Perbaikan'])->default('Aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Indexes for fast geo & district queries
            $table->index('kecamatan');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tps');
    }
};
