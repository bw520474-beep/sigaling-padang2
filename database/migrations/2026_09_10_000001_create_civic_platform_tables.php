<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Wilayah / Regions (Multi-wilayah hierarkis)
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('code', 50)->unique();
                $table->string('name', 255);
                $table->string('type', 50)->default('city'); // country, province, city, district, village
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Instansi / Organisasi (Multi-organisasi/OPD)
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->integer('opd_id')->nullable()->index(); // Bridge ke OPD legacy
                $table->unsignedBigInteger('region_id')->nullable()->index();
                $table->string('code', 50)->unique();
                $table->string('name', 255);
                $table->string('type', 50)->default('dinas'); // dinas, badan, satpol_pp, bumn, dll
                $table->text('description')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('address')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Bidang / Unit Kerja / Departments
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('code', 50)->nullable();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Subkategori Permasalahan (Klasifikasi Berjenjang)
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->integer('category_id')->index(); // Mengacu ke tabel kategori (int)
                $table->string('name', 255);
                $table->string('slug', 255)->nullable();
                $table->string('icon', 100)->nullable();
                $table->string('default_priority', 20)->default('Sedang'); // Rendah, Sedang, Tinggi, Darurat
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Smart Routing Rules (Aturan Penentuan Instansi)
        if (!Schema::hasTable('routing_rules')) {
            Schema::create('routing_rules', function (Blueprint $table) {
                $table->id();
                $table->integer('category_id')->index();
                $table->unsignedBigInteger('subcategory_id')->nullable()->index();
                $table->unsignedBigInteger('region_id')->nullable()->index();
                $table->unsignedBigInteger('organization_id')->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->string('priority', 20)->default('Sedang');
                $table->integer('sla_hours')->default(72);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 6. Perlindungan Identitas Pelapor (Pemisahan Data Identitas)
        if (!Schema::hasTable('reporter_identities')) {
            Schema::create('reporter_identities', function (Blueprint $table) {
                $table->id();
                $table->integer('report_id')->index();
                $table->integer('user_id')->nullable()->index();
                $table->string('reporter_name', 255);
                $table->string('contact', 255)->nullable();
                $table->string('id_card_number', 50)->nullable();
                $table->enum('protection_level', ['public', 'confidential', 'anonymous'])->default('public');
                $table->text('address_detail')->nullable();
                $table->timestamps();
            });
        }

        // 7. Multi-Agency Assignments (Satu Laporan Bisa Melibatkan Banyak Instansi)
        if (!Schema::hasTable('report_assignments')) {
            Schema::create('report_assignments', function (Blueprint $table) {
                $table->id();
                $table->integer('report_id')->index();
                $table->unsignedBigInteger('organization_id')->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->integer('supervisor_id')->nullable()->index();
                $table->integer('officer_id')->nullable()->index();
                $table->string('status', 50)->default('received'); 
                // received, verification, assigned, in_progress, investigation, action, resolved, rejected, need_more_info
                $table->string('priority', 20)->default('Sedang');
                $table->dateTime('sla_due_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('completion_notes')->nullable();
                $table->string('completion_evidence', 255)->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // 8. SLA Rules (Service Level Agreement Target)
        if (!Schema::hasTable('sla_rules')) {
            Schema::create('sla_rules', function (Blueprint $table) {
                $table->id();
                $table->integer('category_id')->nullable()->index();
                $table->unsignedBigInteger('subcategory_id')->nullable()->index();
                $table->string('priority', 20)->default('Sedang');
                $table->integer('response_time_hours')->default(24);
                $table->integer('resolution_time_hours')->default(72);
                $table->integer('warning_threshold_percent')->default(75);
                $table->timestamps();
            });
        }

        // 9. Audit Logs (Catatan Audit Trail Aktivitas)
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('actor_id')->nullable()->index();
                $table->string('actor_role', 50)->nullable();
                $table->string('actor_name', 255)->nullable();
                $table->string('action', 100);
                $table->string('entity_type', 100);
                $table->string('entity_id', 100);
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 10. Modifikasi Kolom pada Tabel Users (Perluasan Role & Department)
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) DEFAULT 'masyarakat'");
        } catch (\Exception $e) {
            // Abaikan jika sudah varchar
        }

        if (!Schema::hasColumn('users', 'department_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')->nullable()->after('opd_id');
            });
        }

        // 11. Modifikasi Kolom pada Tabel Laporan (Subkategori, Protection Level, SLA)
        Schema::table('laporan', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('kategori_id');
            }
            if (!Schema::hasColumn('laporan', 'protection_level')) {
                $table->string('protection_level', 20)->default('public')->after('prioritas');
            }
            if (!Schema::hasColumn('laporan', 'sla_due_at')) {
                $table->dateTime('sla_due_at')->nullable()->after('protection_level');
            }
            if (!Schema::hasColumn('laporan', 'sla_status')) {
                $table->string('sla_status', 20)->default('on_track')->after('sla_due_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('sla_rules');
        Schema::dropIfExists('report_assignments');
        Schema::dropIfExists('reporter_identities');
        Schema::dropIfExists('routing_rules');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('regions');
    }
};
