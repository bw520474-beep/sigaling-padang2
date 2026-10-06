# PROMPT REDESIGN WEBSITE SIGALING PADANG

## ROLE

Kamu bertindak sebagai **Senior Full-Stack Developer, UI/UX Designer, dan Product Designer** yang berpengalaman membangun aplikasi pemerintahan/public service yang profesional.

Saya memiliki website **SIGaling Padang** versi lama yang sebelumnya dibuat menggunakan:

* PHP Native
* MySQL
* Bootstrap/CSS
* JavaScript
* Leaflet.js
* Laragon

Saya ingin **merombak website tersebut secara menyeluruh** dan memigrasikannya menjadi aplikasi web modern menggunakan:

### Frontend

* Laravel Blade
* Tailwind CSS
* JavaScript
* Alpine.js jika diperlukan
* Leaflet.js untuk peta
* Chart.js jika diperlukan
* Lucide Icons atau icon library dengan gaya clean line-art

### Backend

* Laravel
* Laravel Routing
* Laravel Controller
* Laravel Middleware
* Laravel Validation
* Laravel REST/API jika memang diperlukan

### Database

* MySQL
* Development menggunakan Laragon

---

# 1. TUJUAN REDESIGN

SIGaling Padang adalah website pelayanan/informasi masyarakat yang berhubungan dengan:

* pelaporan masalah lingkungan
* informasi kondisi lingkungan
* lokasi laporan masyarakat
* status laporan
* peta lokasi laporan
* informasi dan statistik
* riwayat laporan masyarakat

Website harus terasa seperti **produk digital pemerintahan yang benar-benar digunakan masyarakat**, bukan seperti website hasil generator AI.

Prioritas utama:

1. Profesional
2. Modern
3. Premium tetapi sederhana
4. Human-centered
5. Mudah digunakan
6. Cepat
7. Responsive
8. Informasi mudah dipahami
9. Interaksi terasa natural
10. Tidak terlihat seperti template AI

---

# 2. JANGAN MENGUBAH SISTEM SECARA SEMBARANGAN

Sebelum melakukan perubahan:

1. Baca seluruh struktur project lama.
2. Identifikasi seluruh halaman.
3. Identifikasi database.
4. Identifikasi tabel.
5. Identifikasi relasi/data yang sudah digunakan.
6. Identifikasi fitur yang sudah berjalan.
7. Identifikasi endpoint/proses PHP lama.
8. Identifikasi fungsi Leaflet.
9. Identifikasi sistem login.
10. Identifikasi sistem laporan masyarakat.

Jangan langsung menghapus fitur lama.

Jika sebuah fitur sudah berjalan dengan baik, **pertahankan fungsi bisnisnya** dan hanya modernisasi implementasi serta tampilannya.

Migrasikan logic PHP Native ke struktur Laravel secara bertahap:

```text
PHP Native
    ↓
Laravel Routes
    ↓
Controllers
    ↓
Models
    ↓
Services jika diperlukan
    ↓
Blade Views
    ↓
Tailwind CSS
    ↓
JavaScript / Alpine.js
```

---

# 3. ARAH DESAIN

Gunakan konsep:

> **Modern Civic Technology**

Bukan:

> Futuristic Technology

Desain harus terlihat seperti produk digital profesional untuk pemerintah kota.

Inspirasi visual:

* modern civic platform
* public service dashboard
* premium editorial website
* modern mapping application
* clean environmental platform
* professional SaaS dashboard

Tetapi jangan meniru website tertentu secara langsung.

---

# 4. HAL YANG DILARANG

JANGAN gunakan:

* desain futuristik berlebihan
* efek 3D
* robot
* AI brain
* hologram
* glassmorphism berlebihan
* neon
* gradient mencolok
* background penuh pola
* garis-garis sirkuit
* elemen teknologi yang rumit
* floating card berlebihan
* terlalu banyak rounded rectangle
* kartu-kartu kecil yang memenuhi halaman
* ilustrasi AI generik
* stock image yang terasa palsu
* animasi berlebihan
* parallax berlebihan
* glowing effect
* efek glossy
* shadow terlalu tebal
* border berlebihan

Jangan membuat semua elemen berbentuk:

```text
[ rounded card ]
[ rounded card ]
[ rounded card ]
[ rounded card ]
```

karena akan terlihat seperti AI-generated UI.

Gunakan kombinasi:

* section terbuka
* editorial layout
* garis pemisah tipis
* typography
* whitespace
* image
* map
* card seperlunya
* asymmetric layout
* overlapping section

---

# 5. BENTUK DAN ICON

Gunakan bentuk UI yang lebih natural.

Rounded corner boleh digunakan, tetapi jangan berlebihan.

Gunakan radius yang konsisten seperti:

```text
rounded-xl
rounded-2xl
```

hanya pada komponen yang memang membutuhkan.

Untuk icon gunakan:

* Lucide Icons
* clean line icons
* consistent stroke width
* minimal
* organic
* simple

Icon harus memiliki gaya yang konsisten.

Hindari icon:

* 3D
* emoji sebagai icon utama
* colorful AI icons
* icon dengan terlalu banyak detail

Contoh gaya icon:

* MapPin
* Navigation
* Shield
* AlertTriangle
* ClipboardList
* Clock
* CheckCircle
* MessageSquare
* Route
* Map
* Users
* Leaf
* Camera
* Search

Gunakan stroke icon yang konsisten.

---

# 6. WARNA

Gunakan visual yang terinspirasi dari:

* lingkungan
* kota
* pelayanan publik
* alam
* peta

Palet warna harus tenang dan profesional.

Contoh arah warna:

```text
Primary:
Deep Green / Forest Green

Secondary:
Muted Green

Background:
Warm White / Off White

Text:
Dark Charcoal

Muted:
Slate / Gray

Accent:
Subtle Earth Tone
```

Jangan menggunakan terlalu banyak warna.

Gunakan satu warna utama sebagai identitas SIGaling.

Pastikan contrast accessibility tetap baik.

---

# 7. TYPOGRAPHY

Gunakan typography modern dan mudah dibaca.

Contoh:

```text
Inter
Manrope
Plus Jakarta Sans
DM Sans
```

Pilih maksimal 1–2 font.

Gunakan hierarchy yang jelas:

```text
Hero Heading
↓
Section Heading
↓
Subheading
↓
Body
↓
Caption
```

Hindari heading yang terlalu besar sampai memenuhi seluruh layar.

---

# 8. NAVBAR

Navbar lama harus **diganti menjadi navigation sidebar / side navigation**.

Desktop:

```text
┌───────────────────────┐
│ SIGaling              │
│ Padang                │
│                       │
│ Beranda               │
│ Peta Laporan          │
│ Laporan Saya          │
│ Riwayat               │
│ Informasi             │
│                       │
│ ─────────────────     │
│                       │
│ Profil                │
│ Keluar                │
└───────────────────────┘
```

Sidebar:

* fixed/sticky
* clean
* tidak terlalu lebar
* icon + label
* active state jelas
* tidak menggunakan efek neon
* hover halus
* transisi cepat
* dapat collapse menjadi icon-only jika diperlukan

Pada mobile:

Sidebar berubah menjadi:

* drawer
* hamburger button
* slide-in navigation

Jangan membuat navbar desktop dipaksa menjadi navbar mobile.

---

# 9. BERANDA / HOMEPAGE

Homepage harus terasa seperti landing page produk pemerintahan modern.

Struktur:

```text
SIDEBAR

HERO
↓
Quick Action
↓
Status / Informasi penting
↓
Peta laporan
↓
Pesan Masyarakat
↓
Statistik
↓
Cara kerja
↓
Informasi / edukasi
↓
Footer
```

---

# 10. HERO SECTION

Hero harus sederhana dan kuat.

Contoh konsep:

Judul:

> Laporkan, Pantau, dan Jaga Lingkungan Kota Padang.

Subheading:

> SIGaling membantu masyarakat menyampaikan laporan lingkungan dan memantau tindak lanjutnya secara transparan.

CTA:

```text
Buat Laporan
Lihat Peta
```

Jangan menggunakan hero dengan:

* robot
* globe 3D
* dashboard melayang
* efek hologram

Gunakan visual:

* foto lingkungan Kota Padang
* peta
* ilustrasi line-art sederhana
* bentuk organik
* typography

---

# 11. OVERLAPPING SECTION

Gunakan overlapping section secara elegan.

Contoh:

Hero:

```text
┌──────────────────────────────┐
│                              │
│        HERO                  │
│                              │
│                              │
└──────────────────────────────┘
             ┌────────────────────────┐
             │ Statistik / Quick Info  │
             └────────────────────────┘
```

Tetapi jangan membuat semua section overlap.

Gunakan hanya pada bagian penting seperti:

* hero → quick stats
* map → information panel
* featured report → content section

Overlap harus tetap responsive.

---

# 12. VELOCITY SCROLL / SCROLL ANIMATION

Tambahkan animasi saat user melakukan scrolling.

Gunakan animasi yang:

* subtle
* cepat
* smooth
* profesional

Contoh:

### Section reveal

Ketika section masuk viewport:

```text
opacity: 0 → 1
transform: translateY(30px) → translateY(0)
```

### Image reveal

```text
scale: 0.97 → 1
opacity: 0 → 1
```

### Text reveal

Heading muncul sedikit setelah section.

### Stagger

Untuk list:

```text
item 1
item 2
item 3
```

muncul dengan delay kecil.

Gunakan Intersection Observer atau library ringan.

Jangan membuat animasi terlalu lama.

Rekomendasi:

```text
duration: 400–700ms
ease: cubic-bezier(...)
```

---

# 13. SCROLL EXPERIENCE

Scrolling harus terasa seperti website profesional.

Gunakan:

* smooth reveal
* subtle movement
* overlapping content
* section transition
* sticky element jika relevan
* progress indicator jika diperlukan

Jangan menggunakan:

* parallax ekstrem
* elemen terbang
* spinning object
* infinite animation
* excessive motion

Pastikan:

```css
prefers-reduced-motion
```

didukung.

---

# 14. QUICK ACTION

Di halaman beranda buat area aksi utama:

```text
Apa yang ingin Anda lakukan?

[ Buat Laporan ]

[ Lihat Peta ]

[ Cek Laporan Saya ]

[ Riwayat ]
```

Tampilan tidak harus berupa 4 card besar.

Boleh menggunakan:

* horizontal action list
* icon + text
* compact cards
* editorial buttons

---

# 15. PESAN MASYARAKAT

Tambahkan section baru pada halaman beranda:

# Pesan Masyarakat

Section ini menampilkan pesan/feedback dari masyarakat.

Contoh:

```text
Pesan Masyarakat

"Terima kasih, laporan jalan rusak di
daerah kami sudah ditindaklanjuti."

— Warga Padang

────────────────────────────

"Fitur peta sangat membantu melihat
laporan di sekitar lingkungan kami."

— Masyarakat
```

Jangan membuatnya seperti testimonial marketing perusahaan.

Karena ini website pemerintahan, tampilannya harus terasa:

* autentik
* sederhana
* informatif

Jika data berasal dari sistem, tampilkan:

```text
Nama / inisial
Tanggal
Kategori
Lokasi jika relevan
Pesan
Status
```

Tambahkan filter jika datanya banyak.

Contoh:

```text
Semua
Lingkungan
Infrastruktur
Kebersihan
Drainase
Lainnya
```

---

# 16. RIWAYAT MASYARAKAT

Tambahkan menu:

> Riwayat

Halaman ini digunakan masyarakat untuk melihat seluruh laporan yang pernah mereka buat.

Contoh:

```text
Riwayat Laporan

12 Agustus 2026
Sampah menumpuk
Kecamatan Kuranji

Status:
✓ Diterima
✓ Diverifikasi
→ Dalam Penanganan
○ Selesai
```

Gunakan timeline status.

Contoh:

```text
Dikirim
   ↓
Diverifikasi
   ↓
Diproses
   ↓
Selesai
```

User harus bisa membuka detail laporan.

Detail:

```text
Nomor Laporan
Kategori
Tanggal
Lokasi
Foto
Deskripsi
Status
Petugas/instansi
Catatan tindak lanjut
Riwayat perubahan status
```

---

# 17. STATUS LAPORAN

Gunakan status yang jelas:

```text
Menunggu
Diverifikasi
Diproses
Selesai
Ditolak
```

Jangan hanya menggunakan warna.

Gabungkan:

* icon
* label
* warna
* progress

Pastikan pengguna langsung memahami status.

---

# 18. PETA

Leaflet tetap digunakan.

Peta harus menjadi salah satu fitur utama SIGaling.

Gunakan:

```text
Leaflet.js
OpenStreetMap
```

Fitur:

* marker laporan
* lokasi pengguna
* filter kategori
* filter status
* clustering jika marker banyak
* popup detail
* pencarian lokasi
* legenda
* tombol current location
* zoom controls

Jangan membuat peta terlalu penuh.

Gunakan marker yang sederhana.

---

# 19. PETA PADA MOBILE

Ini sangat penting.

Pada mobile:

* map tidak boleh memenuhi layar secara tidak proporsional
* control map harus kecil
* bottom sheet dapat digunakan
* daftar laporan dapat muncul dari bawah
* user tetap bisa scroll halaman
* jangan membuat map mengambil alih seluruh viewport

Contoh:

```text
┌──────────────────────────┐
│ Peta Laporan             │
│                          │
│      MAP                 │
│                          │
└──────────────────────────┘

[ Daftar laporan sekitar ]

───────────────────────────

Laporan #SG-2026-001
Sampah menumpuk
2 km dari lokasi Anda
```

---

# 20. RESPONSIVE DESIGN

Website wajib dibuat mobile-first.

Target:

```text
Mobile
Tablet
Laptop
Desktop
```

Breakpoints Tailwind harus digunakan dengan benar.

Pastikan tidak ada:

* horizontal overflow
* text terpotong
* button keluar layar
* map terlalu besar
* sidebar menutupi content
* modal melebihi layar

Perhatikan terutama:

```text
375px
390px
412px
768px
1024px
1280px
1440px
```

---

# 21. DASHBOARD MASYARAKAT

Jika terdapat login user, dashboard harus sederhana.

Contoh:

```text
Selamat datang kembali.

Ringkasan laporan Anda

03
Total Laporan

01
Dalam Proses

02
Selesai
```

Kemudian:

```text
Laporan Terbaru
```

Jangan membuat dashboard penuh widget.

Prioritaskan informasi yang benar-benar dibutuhkan.

---

# 22. ADMIN DASHBOARD

Admin/petugas membutuhkan dashboard berbeda.

Admin dapat melihat:

```text
Total Laporan
Laporan Baru
Laporan Diproses
Laporan Selesai
Laporan berdasarkan kategori
Laporan berdasarkan wilayah
```

Gunakan Chart.js jika memang diperlukan.

Admin juga dapat:

* melihat laporan
* memverifikasi laporan
* mengubah status
* memberikan catatan
* melihat lokasi pada peta
* melihat foto
* mengelola kategori
* mengelola user
* melihat statistik

Dashboard admin boleh lebih padat daripada homepage masyarakat.

---

# 23. INTERAKSI LAPORAN

Saat masyarakat membuat laporan:

```text
Pilih kategori
↓
Masukkan deskripsi
↓
Tentukan lokasi
↓
Upload foto
↓
Review
↓
Kirim laporan
```

Berikan feedback setiap tahap.

Contoh:

```text
✓ Lokasi berhasil ditentukan

✓ Foto berhasil diupload

✓ Laporan siap dikirim
```

Setelah submit:

```text
Laporan berhasil dikirim.

Nomor laporan:
SG-2026-000123
```

Berikan tombol:

```text
Lihat Status
Kembali ke Beranda
```

---

# 24. EMPTY STATE

Jangan menggunakan halaman kosong.

Contoh:

```text
Belum ada laporan.

Saat Anda membuat laporan,
laporan tersebut akan muncul di sini.

[ Buat Laporan ]
```

Gunakan icon line-art sederhana.

---

# 25. LOADING STATE

Gunakan skeleton/loading state yang sederhana.

Hindari spinner besar yang mengganggu.

Contoh:

```text
████████████
████████
████████████████
```

Gunakan skeleton hanya ketika memang diperlukan.

---

# 26. ERROR STATE

Buat error state yang manusiawi.

Contoh:

```text
Data belum dapat dimuat.

Periksa koneksi Anda dan coba lagi.

[ Coba Lagi ]
```

Jangan tampilkan error teknis Laravel kepada user.

---

# 27. ACCESSIBILITY

Pastikan:

* semantic HTML
* keyboard navigation
* focus state
* aria-label
* color contrast
* button memiliki label jelas
* icon-only button memiliki tooltip/aria-label
* form error mudah dipahami

---

# 28. PERFORMANCE

Prioritaskan performance.

Hindari:

* library yang tidak diperlukan
* animasi berat
* gambar terlalu besar
* JavaScript berlebihan
* dependency yang tidak digunakan

Gunakan:

* lazy loading
* optimized image
* debounce search
* pagination
* API pagination jika diperlukan

---

# 29. STRUKTUR LARAVEL

Gunakan struktur Laravel yang rapi.

Contoh:

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
│
├── Models/
│
└── Services/

resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── reports/
│   │   ├── history/
│   │   ├── map/
│   │   └── profile/
│   │
│   └── admin/
│
├── css/
│   └── app.css
│
└── js/
    └── app.js

routes/
├── web.php
└── api.php

database/
├── migrations/
├── seeders/
└── factories/
```

Gunakan Blade Components untuk komponen yang berulang.

Contoh:

```text
components/
├── sidebar.blade.php
├── button.blade.php
├── status-badge.blade.php
├── report-card.blade.php
├── empty-state.blade.php
├── modal.blade.php
└── map.blade.php
```

---

# 30. DATABASE

Jangan mengubah database tanpa alasan.

Audit database lama terlebih dahulu.

Jika perlu migrasi:

```text
Old Database
      ↓
Migration
      ↓
Laravel Models
      ↓
Eloquent
```

Gunakan:

* migrations
* seeders
* factories

Jangan menyimpan data penting secara hardcode di Blade.

---

# 31. SECURITY

Implementasikan:

* CSRF protection
* validation
* authentication
* authorization
* middleware
* secure file upload
* MIME validation
* file size validation
* sanitization
* rate limiting jika diperlukan

Masyarakat hanya boleh melihat laporan miliknya sendiri jika laporan tersebut bersifat private.

Admin memiliki akses sesuai role.

---

# 32. FILE UPLOAD

Untuk foto laporan:

Validasi:

```text
jpg
jpeg
png
webp
```

Batasi ukuran file.

Jangan langsung menyimpan file sembarangan di public directory.

Gunakan Laravel Storage.

---

# 33. ANIMASI COMPONENT

Buat reusable animation class/component.

Contoh:

```text
reveal
fade-up
fade-in
scale-in
stagger
```

Gunakan JavaScript hanya ketika diperlukan.

Jangan menambahkan animasi ke setiap elemen.

Prinsip:

> Content first, motion second.

---

# 34. MICRO INTERACTION

Tambahkan interaksi kecil:

* button hover
* sidebar active state
* map marker interaction
* filter transition
* modal transition
* toast notification
* form validation
* status update
* loading feedback

Semua harus subtle.

---

# 35. FOOTER

Footer sederhana.

Berisi:

```text
SIGaling Padang

Platform pelaporan dan informasi
lingkungan masyarakat Kota Padang.

Navigasi
Beranda
Peta
Laporan
Riwayat

Informasi
Tentang SIGaling
Kontak
Kebijakan Privasi

© 2026 SIGaling Padang
```

Jangan membuat footer terlalu besar.

---

# 36. KUALITAS VISUAL

Saya ingin hasil akhirnya terasa seperti website yang dibuat oleh:

> Tim product design profesional + frontend engineer berpengalaman.

Bukan:

> AI-generated landing page.

Karena itu:

* gunakan whitespace
* jangan semua menjadi card
* gunakan visual hierarchy
* gunakan grid yang konsisten
* gunakan typography sebagai elemen desain
* gunakan image/map secara fungsional
* gunakan icon secara natural
* hindari dekorasi yang tidak memiliki fungsi

---

# 37. TAHAP PENGERJAAN

Jangan langsung mengubah semua file.

Kerjakan secara bertahap.

### STEP 1 — AUDIT

Baca project lama.

Identifikasi:

* halaman
* database
* fitur
* role
* flow
* API
* JavaScript
* map

Kemudian buat ringkasan arsitektur.

### STEP 2 — MIGRASI

Siapkan:

* Laravel
* database connection
* migrations
* models
* controllers
* routes
* authentication

### STEP 3 — DESIGN SYSTEM

Buat:

* colors
* typography
* spacing
* button
* input
* badge
* card
* sidebar
* modal
* toast

### STEP 4 — LAYOUT

Buat:

* sidebar
* responsive mobile drawer
* global layout
* page container

### STEP 5 — HOMEPAGE

Implementasikan:

* hero
* quick action
* overlapping section
* map
* pesan masyarakat
* statistik
* informasi

### STEP 6 — REPORT

Implementasikan:

* create report
* upload photo
* location
* validation
* submit
* success state

### STEP 7 — HISTORY

Implementasikan:

* riwayat masyarakat
* filter
* detail laporan
* timeline status

### STEP 8 — MAP

Implementasikan:

* marker
* filtering
* popup
* location
* responsive map

### STEP 9 — ADMIN

Implementasikan:

* dashboard
* report management
* status
* map
* statistics

### STEP 10 — POLISH

Lakukan:

* responsive testing
* accessibility
* performance
* animation
* empty states
* error states
* loading states

---

# 38. PENTING — JANGAN MERUSAK FITUR

Sebelum menghapus kode lama, pastikan fungsi tersebut sudah memiliki pengganti.

Jangan:

* menghapus database tanpa backup
* menghapus fitur lama tanpa pengganti
* mengganti struktur database secara sembarangan
* membuat dummy data sebagai pengganti database asli
* membuat tombol yang tidak berfungsi
* membuat UI yang terlihat bagus tetapi backend tidak bekerja

Semua tombol utama harus benar-benar berfungsi.

---

# 39. HASIL AKHIR YANG DIHARAPKAN

Saya ingin SIGaling Padang memiliki karakter:

```text
Professional
        +
Modern
        +
Human
        +
Civic
        +
Environmental
        +
Minimal
        +
Interactive
```

Bukan:

```text
Futuristic
3D
Neon
AI Slop
Glassmorphism
Robot
Cyberpunk
```

Website harus terlihat **realistis sebagai platform layanan publik Kota Padang** dan cukup matang untuk dipresentasikan sebagai project magang.

---

# 40. OUTPUT YANG HARUS KAMU LAKUKAN

Sebelum coding:

1. Audit project.
2. Jelaskan struktur project lama.
3. Jelaskan fitur yang ditemukan.
4. Jelaskan rencana migrasi.
5. Jelaskan struktur Laravel baru.
6. Jelaskan perubahan UI/UX.
7. Jelaskan database yang dipertahankan/diubah.

Setelah itu mulai implementasi secara bertahap.

Jangan hanya membuat mockup.

**Implementasikan website yang benar-benar berjalan.**

Prioritaskan:

> FUNCTIONALITY → UX → VISUAL → ANIMATION

dan bukan sebaliknya.
