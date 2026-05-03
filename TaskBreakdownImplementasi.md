# Task Breakdown Implementasi

## Tujuan

Dokumen ini memecah rencana implementasi tahap II menjadi daftar pekerjaan teknis yang lebih rinci agar mudah dieksekusi, diprioritaskan, dan dipantau progresnya.

Dokumen ini difokuskan pada area:

- perbaikan struktur data
- penambahan modul harga
- multi upload foto
- filter lanjutan
- penyesuaian dashboard
- penyesuaian alur role dan halaman

---

## Prinsip Eksekusi

1. Kerjakan fondasi data lebih dulu.
2. Hindari perubahan UI besar sebelum struktur backend siap.
3. Pastikan migrasi aman terhadap data lama.
4. Pisahkan task berdasarkan modul agar lebih mudah dites.
5. Utamakan fitur yang langsung menjawab feedback klien.

---

## Fase 1: Finalisasi Requirement Teknis

### Task 1.1

Nama:

- Finalisasi definisi proses bisnis harga

Deskripsi:

- Tentukan apakah harga berlaku global atau per cabang.
- Tentukan apakah satu barang bisa memiliki banyak harga aktif berdasarkan tanggal.
- Tentukan apakah transaksi menyimpan snapshot harga saat transaksi dibuat.

Output:

- keputusan aturan bisnis harga

Status:

- belum dikerjakan

### Task 1.2

Nama:

- Finalisasi definisi master barang

Deskripsi:

- Tentukan atribut barang minimum seperti kode, nama, deskripsi, status aktif.
- Tentukan apakah barang dikelola terpusat atau per cabang.

Output:

- daftar field master barang

Status:

- belum dikerjakan

### Task 1.3

Nama:

- Finalisasi pemisahan laporan lapangan dan transaksi logistik

Deskripsi:

- Putuskan apakah tabel `logistics` akan tetap dipakai untuk transaksi inti.
- Putuskan apakah laporan lapangan dipisah ke tabel sendiri seperti `field_reports`.

Output:

- keputusan arsitektur modul utama

Status:

- belum dikerjakan

---

## Fase 2: Persiapan Struktur Data

### Task 2.1

Nama:

- Buat migration tabel master barang

Deskripsi:

- Tambahkan tabel `items` atau `products`.
- Isi field minimal: kode barang, nama barang, deskripsi, status aktif.

File yang kemungkinan dibuat/diubah:

- `database/migrations/*create_items_table.php`
- `app/Models/Item.php`

Output:

- master barang tersedia di database

### Task 2.2

Nama:

- Buat migration tabel histori harga

Deskripsi:

- Tambahkan tabel `item_prices`.
- Simpan `item_id`, `branch_id`, `price`, `effective_date`, `created_by`, dan catatan.

File yang kemungkinan dibuat/diubah:

- `database/migrations/*create_item_prices_table.php`
- `app/Models/ItemPrice.php`

Output:

- sistem mampu menyimpan histori harga

### Task 2.3

Nama:

- Siapkan relasi harga pada model barang

Deskripsi:

- Tambahkan relasi `hasMany` dari barang ke histori harga.
- Tambahkan helper untuk mengambil harga aktif/latest.

File yang kemungkinan dibuat/diubah:

- `app/Models/Item.php`
- `app/Models/ItemPrice.php`

Output:

- model siap dipakai untuk logika harga

### Task 2.4

Nama:

- Refactor struktur tabel logistik

Deskripsi:

- Ubah data logistik agar mereferensikan barang lewat `item_id`.
- Tambahkan `unit_price_snapshot` dan `total_price`.
- Evaluasi perubahan `nama_barang`, `jumlah`, `tanggal`, dan `keterangan`.

File yang kemungkinan dibuat/diubah:

- `database/migrations/*alter_logistics_table.php`
- `app/Models/Logistics.php`

Output:

- tabel logistik lebih cocok untuk transaksi

### Task 2.5

Nama:

- Siapkan tabel multi foto

Deskripsi:

- Buat tabel `logistics_photos`.
- Setiap data logistik dapat memiliki banyak file foto.

File yang kemungkinan dibuat/diubah:

- `database/migrations/*create_logistics_photos_table.php`
- `app/Models/LogisticsPhoto.php`
- `app/Models/Logistics.php`

Output:

- relasi satu logistik ke banyak foto siap dipakai

### Task 2.6

Nama:

- Siapkan strategi migrasi data lama

Deskripsi:

- Petakan data lama yang memakai `nama_barang` ambigu.
- Putuskan data lama akan dipertahankan, dipindah, atau dinormalisasi.

Output:

- catatan migrasi data lama

---

## Fase 3: Implementasi Master Barang

### Task 3.1

Nama:

- Buat controller master barang

Deskripsi:

- Tambahkan CRUD untuk master barang.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/ItemController.php`
- `routes/web.php`

Output:

- modul barang tersedia

### Task 3.2

Nama:

- Buat view daftar barang

Deskripsi:

- Buat halaman listing barang dengan pencarian dasar.

File yang kemungkinan dibuat/diubah:

- `resources/views/items/index.blade.php`

Output:

- user dapat melihat daftar barang

### Task 3.3

Nama:

- Buat form tambah/edit barang

Deskripsi:

- Buat form untuk menambah dan mengubah barang.

File yang kemungkinan dibuat/diubah:

- `resources/views/items/form.blade.php`

Output:

- master barang dapat dikelola

### Task 3.4

Nama:

- Tambahkan validasi dan hak akses master barang

Deskripsi:

- Batasi pengelolaan barang hanya untuk role yang ditentukan.

File yang kemungkinan dibuat/diubah:

- `app/Models/User.php`
- middleware/route terkait
- `app/Http/Controllers/ItemController.php`

Output:

- akses modul barang terkontrol

---

## Fase 4: Implementasi Modul Harga

### Task 4.1

Nama:

- Buat controller pengelolaan harga

Deskripsi:

- Tambahkan endpoint untuk tambah, edit, dan lihat histori harga.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/ItemPriceController.php`
- `routes/web.php`

Output:

- modul harga tersedia

### Task 4.2

Nama:

- Buat halaman histori harga per barang

Deskripsi:

- Tampilkan histori harga dan tanggal berlakunya.

File yang kemungkinan dibuat/diubah:

- `resources/views/item-prices/index.blade.php`

Output:

- manajemen dapat melacak perubahan harga

### Task 4.3

Nama:

- Buat form set harga

Deskripsi:

- Sediakan form untuk menentukan harga baru dan tanggal berlakunya.

File yang kemungkinan dibuat/diubah:

- `resources/views/item-prices/form.blade.php`

Output:

- harga dapat diatur secara resmi

### Task 4.4

Nama:

- Tambahkan rule harga aktif

Deskripsi:

- Saat transaksi dibuat, sistem harus mengambil harga aktif berdasarkan barang, cabang, dan tanggal jika diperlukan.

File yang kemungkinan dibuat/diubah:

- service/helper terkait harga
- `app/Http/Controllers/LogisticsController.php`
- model harga/barang

Output:

- transaksi memakai harga yang benar

### Task 4.5

Nama:

- Batasi pengubahan harga hanya untuk M. Kantor

Deskripsi:

- Pastikan role logistik dan lapangan tidak bisa mengubah harga inti.

File yang kemungkinan dibuat/diubah:

- `app/Models/User.php`
- route middleware
- controller harga

Output:

- otoritas harga sesuai kebutuhan klien

---

## Fase 5: Refactor Modul Logistik

### Task 5.1

Nama:

- Ubah validasi input logistik

Deskripsi:

- Ganti input dari `nama_barang` menjadi `item_id`.
- Pastikan quantity, tanggal, harga snapshot, dan catatan tervalidasi.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/LogisticsController.php`

Output:

- input logistik sesuai model data baru

### Task 5.2

Nama:

- Ubah form logistik

Deskripsi:

- Form harus memuat pilihan barang, jumlah, tanggal, cabang, catatan, dan upload banyak foto.

File yang kemungkinan dibuat/diubah:

- `resources/views/logistics/form.blade.php`

Output:

- UI logistik lebih sesuai kebutuhan operasional

### Task 5.3

Nama:

- Ubah listing logistik

Deskripsi:

- Tampilkan nama barang, harga satuan, total harga, cabang, tanggal, status, dan jumlah foto.

File yang kemungkinan dibuat/diubah:

- `resources/views/logistics/index.blade.php`

Output:

- data logistik lebih informatif

### Task 5.4

Nama:

- Ubah relasi model logistik

Deskripsi:

- Tambahkan relasi ke item dan ke foto.

File yang kemungkinan dibuat/diubah:

- `app/Models/Logistics.php`
- `app/Models/Item.php`
- `app/Models/LogisticsPhoto.php`

Output:

- model siap dipakai di seluruh modul

---

## Fase 6: Multi Upload Foto

### Task 6.1

Nama:

- Ubah input file menjadi multiple

Deskripsi:

- Form harus mendukung upload sampai 10 foto.

File yang kemungkinan dibuat/diubah:

- `resources/views/logistics/form.blade.php`
- bila perlu `resources/views/field-reports/form.blade.php`

Output:

- user dapat memilih banyak foto sekaligus

### Task 6.2

Nama:

- Ubah validasi multi file di controller

Deskripsi:

- Tambahkan validasi array file maksimum 10.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/LogisticsController.php`
- `app/Http/Controllers/FieldReportController.php`

Output:

- upload banyak foto tervalidasi dengan aman

### Task 6.3

Nama:

- Simpan banyak foto ke tabel relasi

Deskripsi:

- Setiap file foto disimpan sebagai record terpisah.

File yang kemungkinan dibuat/diubah:

- controller logistik/lapangan
- model relasi foto

Output:

- sistem tidak lagi tergantung pada `photo_path` tunggal

### Task 6.4

Nama:

- Tampilkan foto dalam daftar atau detail

Deskripsi:

- Tampilkan jumlah foto dan preview/link galeri.

File yang kemungkinan dibuat/diubah:

- `resources/views/logistics/index.blade.php`
- view detail jika nanti dibuat

Output:

- dokumentasi lebih mudah diakses

---

## Fase 7: Filter Lanjutan

### Task 7.1

Nama:

- Tambahkan filter tanggal

Deskripsi:

- Tambahkan `date_from` dan `date_to` di listing logistik.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/LogisticsController.php`
- `resources/views/logistics/index.blade.php`

Output:

- data dapat dicari berdasarkan rentang tanggal

### Task 7.2

Nama:

- Tambahkan filter barang

Deskripsi:

- User dapat memilih barang tertentu pada listing.

File yang kemungkinan dibuat/diubah:

- controller logistik
- view logistik

Output:

- data lebih mudah dilacak per barang

### Task 7.3

Nama:

- Tambahkan filter harga

Deskripsi:

- Tambahkan filter harga minimum dan maksimum.

File yang kemungkinan dibuat/diubah:

- controller logistik
- view logistik

Output:

- pencarian nilai transaksi lebih fleksibel

### Task 7.4

Nama:

- Tambahkan filter kategori dan status

Deskripsi:

- Lengkapi filter yang sudah ada agar mendukung kombinasi pencarian.

File yang kemungkinan dibuat/diubah:

- controller logistik
- view logistik

Output:

- filter operasional lebih lengkap

### Task 7.5

Nama:

- Tambahkan filter serupa pada dashboard jika diperlukan

Deskripsi:

- Minimal dashboard mendukung cabang dan tanggal.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/DashboardController.php`
- `resources/views/dashboard.blade.php`

Output:

- dashboard lebih konsisten dengan kebutuhan pencarian

---

## Fase 8: Penyesuaian Dashboard

### Task 8.1

Nama:

- Tambahkan statistik nilai transaksi

Deskripsi:

- Tampilkan total nilai barang masuk dan keluar.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/DashboardController.php`
- `resources/views/dashboard.blade.php`

Output:

- dashboard punya insight nilai bisnis

### Task 8.2

Nama:

- Tambahkan ringkasan barang

Deskripsi:

- Tampilkan barang yang paling sering bergerak atau paling besar nilainya.

File yang kemungkinan dibuat/diubah:

- controller dashboard
- view dashboard

Output:

- user mendapat insight barang utama

### Task 8.3

Nama:

- Tambahkan ringkasan per cabang

Deskripsi:

- Tampilkan performa atau volume per cabang.

File yang kemungkinan dibuat/diubah:

- controller dashboard
- view dashboard

Output:

- manajemen lebih mudah membandingkan cabang

---

## Fase 9: Penyesuaian Upload Excel

### Task 9.1

Nama:

- Evaluasi ulang format template upload

Deskripsi:

- Ubah template agar mendukung item, quantity, tanggal, dan bila perlu harga.

File yang kemungkinan dibuat/diubah:

- `resources/views/uploads/index.blade.php`
- `app/Http/Controllers/UploadController.php`

Output:

- upload massal sesuai model data baru

### Task 9.2

Nama:

- Perbarui parser upload

Deskripsi:

- Sesuaikan logika mapping CSV/Excel ke item dan transaksi.

File yang kemungkinan dibuat/diubah:

- `app/Http/Controllers/UploadController.php`

Output:

- hasil upload lebih konsisten dengan struktur baru

---

## Fase 10: Role, Navigasi, dan Hak Akses

### Task 10.1

Nama:

- Tambahkan menu master barang

Deskripsi:

- Munculkan menu baru pada layout utama.

File yang kemungkinan dibuat/diubah:

- `resources/views/components/layouts/app.blade.php`

Output:

- modul barang dapat diakses dari navigasi

### Task 10.2

Nama:

- Tambahkan menu harga

Deskripsi:

- Sediakan akses ke pengelolaan harga untuk role kantor.

File yang kemungkinan dibuat/diubah:

- `resources/views/components/layouts/app.blade.php`
- `routes/web.php`

Output:

- modul harga bisa diakses sesuai role

### Task 10.3

Nama:

- Rapikan method capability di model user

Deskripsi:

- Tambahkan method seperti `canManageItems()` dan `canManagePrices()`.

File yang kemungkinan dibuat/diubah:

- `app/Models/User.php`

Output:

- rule akses lebih jelas dan mudah dipakai

---

## Fase 11: Testing dan Validasi

### Task 11.1

Nama:

- Tambahkan test untuk harga

Deskripsi:

- Uji pembuatan harga, histori harga, dan pembatasan role.

File yang kemungkinan dibuat/diubah:

- `tests/Feature/*`

Output:

- logika harga lebih aman

### Task 11.2

Nama:

- Tambahkan test untuk logistik baru

Deskripsi:

- Uji create/update/filter transaksi logistik.

File yang kemungkinan dibuat/diubah:

- `tests/Feature/*`

Output:

- alur utama lebih terjaga

### Task 11.3

Nama:

- Tambahkan test untuk multi upload foto

Deskripsi:

- Uji batas maksimum 10 foto dan penyimpanan relasinya.

File yang kemungkinan dibuat/diubah:

- `tests/Feature/*`

Output:

- fitur dokumentasi lebih aman

### Task 11.4

Nama:

- Lakukan validasi manual pada UI

Deskripsi:

- Cek flow role kantor, logistik, dan lapangan.
- Cek dashboard, filter, upload, dan navigasi.

Output:

- hasil QA manual

---

## Urutan Eksekusi yang Disarankan

1. Finalisasi requirement teknis
2. Buat master barang
3. Buat histori harga
4. Refactor struktur logistik
5. Implementasi multi foto
6. Implementasi filter lanjutan
7. Update dashboard
8. Update upload Excel
9. Rapikan role dan navigasi
10. Testing

---

## Prioritas Tinggi

- struktur harga
- hak akses manajemen harga
- multi upload foto
- filter tanggal, barang, harga

## Prioritas Menengah

- master barang
- refactor listing logistik
- dashboard nilai bisnis
- upload Excel baru

## Prioritas Rendah

- penyempurnaan tampilan
- statistik tambahan
- audit log lanjutan

---

## Catatan Implementasi

- Hindari langsung mengganti semua alur sekaligus tanpa migration plan.
- Jika data lama masih penting, siapkan mapping atau fallback sebelum refactor besar.
- Jika waktu implementasi terbatas, fokus dulu pada:
  - harga
  - multi foto
  - filter

Dengan begitu, revisi yang paling dirasakan klien bisa selesai lebih cepat meskipun refactor total dilakukan bertahap.
