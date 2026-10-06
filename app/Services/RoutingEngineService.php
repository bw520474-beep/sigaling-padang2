<?php

namespace App\Services;

use App\Models\Kategori;
use App\Models\Organization;
use App\Models\RoutingRule;
use App\Models\Subcategory;

class RoutingEngineService
{
    /**
     * Tentukan instansi, unit/bidang, prioritas, dan SLA berdasarkan aturan konfigurasi
     */
    public function resolveRouting(int $categoryId, ?int $subcategoryId = null, ?int $regionId = null): array
    {
        $query = RoutingRule::with(['organization', 'department'])
            ->where('is_active', true);

        $rule = null;

        // 1. Coba cari aturan paling spesifik: Subkategori + Wilayah
        if ($subcategoryId && $regionId) {
            $rule = (clone $query)
                ->where('subcategory_id', $subcategoryId)
                ->where('region_id', $regionId)
                ->first();
        }

        // 2. Coba cari aturan: Subkategori (Wilayah Global / null)
        if (!$rule && $subcategoryId) {
            $rule = (clone $query)
                ->where('subcategory_id', $subcategoryId)
                ->whereNull('region_id')
                ->first();
        }

        // 3. Coba cari aturan: Kategori + Wilayah
        if (!$rule && $regionId) {
            $rule = (clone $query)
                ->where('category_id', $categoryId)
                ->whereNull('subcategory_id')
                ->where('region_id', $regionId)
                ->first();
        }

        // 4. Coba cari aturan: Kategori (Wilayah Global / null)
        if (!$rule) {
            $rule = (clone $query)
                ->where('category_id', $categoryId)
                ->whereNull('subcategory_id')
                ->first();
        }

        // Jika aturan ditemukan
        if ($rule && $rule->organization) {
            return [
                'organization' => $rule->organization,
                'department' => $rule->department,
                'priority' => $rule->priority ?: 'Sedang',
                'sla_hours' => $rule->sla_hours ?: 72,
                'matched_rule_id' => $rule->id,
            ];
        }

        // Fallback jika belum ada rule khusus: arahkan ke DLH atau Organisasi pertama yang aktif
        $fallbackOrg = Organization::where('is_active', true)->first();

        return [
            'organization' => $fallbackOrg,
            'department' => null,
            'priority' => 'Sedang',
            'sla_hours' => 72,
            'matched_rule_id' => null,
        ];
    }

    /**
     * Asisten Pintar / Heuristic Suggestion untuk Klasifikasi Masalah dari Teks Deskripsi
     */
    public function suggestClassification(string $text): array
    {
        $textLower = strtolower($text);

        $keywordsMap = [
            // Jalan & Infrastruktur
            'jalan-rusak-berlubang' => ['jalan', 'lubang', 'berlubang', 'aspal', 'rusak parah', 'amblas', 'jembatan', 'trotoar'],
            // Drainase & Banjir
            'drainase-tersumbat-banjir' => ['drainase', 'parit', 'got', 'saluran', 'tersumbat', 'banjir', 'genangan', 'mampet', 'gorong'],
            // Sampah
            'tumpukan-sampah-liar' => ['sampah', 'berserakan', 'tumpukan', 'bau busuk', 'kotor', 'limbah plastik'],
            'jadwal-pengangkutan-terlambat' => ['truk sampah', 'pengangkutan', 'terlambat', 'jadwal sampah', 'armada'],
            'pembakaran-sampah-terbuka' => ['bakar sampah', 'pembakaran', 'asap sampah', 'sesak'],
            // Limbah & Pencemaran Air
            'limbah-cair-sungai' => ['limbah cair', 'sungai', 'pencemaran air', 'ikan mati', 'berbusa', 'air hitam'],
            'limbah-b3-medis' => ['limbah b3', 'medis', 'jarum', 'bahan kimia', 'oli bekas', 'beracun'],
            // Udara
            'asap-industri-cerobong' => ['asap pabrik', 'cerobong', 'debu pekat', 'polusi udara'],
            'bau-menyengat-kimia' => ['bau kimia', 'bau menyengat', 'gas', 'kandang'],
            // Pohon
            'pohon-tumbang-jalan' => ['pohon tumbang', 'tumbang', 'roboh', 'pohon patah', 'menghalangi jalan'],
            // Lalu Lintas & PJU
            'lampu-lalin-mati' => ['lampu merah', 'traffic light', 'lampu lalin', 'lampu lalu lintas', 'macet'],
            'lampu-pju-mati' => ['pju', 'lampu jalan', 'gelap', 'penerangan jalan', 'padam'],
            // Trantibum
            'gangguan-trantibum' => ['balap liar', 'tawuran', 'keamanan', 'ketertiban', 'gaduh', 'ribut'],
            // Kesehatan
            'rawan-dbd-fogging' => ['dbd', 'nyamuk', 'jentik', 'fogging', 'demam berdarah'],
            // Adminduk
            'layanan-adminduk' => ['ktp', 'kartu keluarga', 'kk', 'akta', 'pindah', 'capil'],
        ];

        $matchedSlug = null;
        $maxScore = 0;

        foreach ($keywordsMap as $slug => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (str_contains($textLower, $kw)) {
                    $score += 2;
                }
            }
            if ($score > $maxScore) {
                $maxScore = $score;
                $matchedSlug = $slug;
            }
        }

        if ($matchedSlug) {
            $subcategory = Subcategory::with('category')->where('slug', $matchedSlug)->first();
            if ($subcategory) {
                $routing = $this->resolveRouting($subcategory->category_id, $subcategory->id);
                return [
                    'matched' => true,
                    'category' => $subcategory->category,
                    'subcategory' => $subcategory,
                    'suggested_priority' => $subcategory->default_priority,
                    'routing' => $routing,
                ];
            }
        }

        return ['matched' => false];
    }
}
