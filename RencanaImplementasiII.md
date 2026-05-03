# Rencana Implementasi II

## Tujuan

Dokumen ini merangkum arah pengembangan lanjutan sistem logistik berdasarkan:

- kondisi sistem saat ini
- feedback klien dari demo
- gap antara kebutuhan operasional logistik dan implementasi yang sudah ada

Fokus utama tahap ini adalah menggeser sistem dari sekadar **laporan lapangan multi cabang** menjadi sistem yang lebih relevan untuk **pengelolaan barang, harga, dokumentasi, dan pencarian data**.

---

## Ringkasan Kondisi Sistem Saat Ini

Saat ini sistem sudah memiliki:

- autentikasi dan role pengguna
- pemisahan akses berdasarkan cabang
- input data logistik
- upload data massal
- verifikasi data
- dashboard ringkas

Namun secara struktur, modul `logistics` masih mencampur beberapa kebutuhan sekaligus:

- data barang
- laporan lapangan
- foto dokumentasi
- status verifikasi
- identitas pelapor

Akibatnya, sistem sudah cukup baik untuk demo, tetapi belum sepenuhnya sesuai untuk kebutuhan logistik yang menekankan:

- harga yang dinamis
- kontrol harga oleh manajemen kantor
- dokumentasi foto lebih dari satu
- filter data yang lebih spesifik

---

## Tujuan Revisi Tahap II

1. Menambahkan dukungan harga sebagai komponen inti sistem.
2. Memisahkan data barang, laporan, dan dokumentasi agar struktur data lebih rapi.
3. Menyediakan pengaturan harga oleh manajemen kantor.
4. Menambahkan dukungan upload foto hingga 10 file.
5. Memperluas filter data agar sesuai kebutuhan operasional.
6. Menyesuaikan dashboard agar lebih bernilai secara bisnis.

---

## Kebutuhan Utama dari Klien

### 1. Harga adalah elemen penting

Sistem harus mendukung harga karena di logistik harga dianggap sebagai salah satu data inti.

### 2. Harga tidak selalu tetap

Harga dapat berubah sewaktu-waktu, sehingga sistem tidak boleh hanya menyimpan satu harga statis tanpa histori.

### 3. Harga diatur oleh manajemen kantor

Pihak kantor harus memiliki otoritas untuk menentukan atau mengubah harga.

### 4. Foto maksimal 10 file

Setiap data/log transaksi perlu dapat menyimpan banyak dokumentasi foto.

### 5. Filter harus lebih spesifik

Minimal perlu filter berdasarkan:

- cabang
- tanggal
- barang
- harga
- status

---

## Analisis Gap

### Gap 1: Belum ada struktur harga

Saat ini tabel utama belum memiliki field harga, histori harga, atau pengelola harga.

Dampak:

- sistem belum bisa mencatat nilai barang secara akurat
- tidak ada jejak perubahan harga
- kebutuhan manajemen kantor belum terakomodasi

### Gap 2: Data barang masih bercampur dengan laporan lapangan

Field seperti `nama_barang` masih dipakai secara ambigu antara nama barang dan identitas pelapor.

Dampak:

- data sulit dikembangkan
- logika bisnis menjadi rancu
- laporan dan transaksi sulit dipisahkan

### Gap 3: Foto masih tunggal

Saat ini sistem hanya menyimpan satu `photo_path` untuk satu data.

Dampak:

- kebutuhan dokumentasi lapangan belum terpenuhi
- user harus memilih satu foto saja

### Gap 4: Filter listing dan dashboard masih terbatas

Filter saat ini dominan pada cabang dan status.

Dampak:

- pencarian data operasional kurang efisien
- monitoring data belum cukup detail

### Gap 5: Dashboard belum berbasis nilai bisnis

Dashboard masih fokus pada jumlah data dan status approval.

Dampak:

- belum membantu analisis harga
- belum menampilkan nilai transaksi
- belum menunjukkan insight per barang

---

## Strategi Implementasi

Implementasi disarankan dilakukan bertahap agar risiko perubahan data dan UI tetap terkendali.

### Tahap 1: Rapikan model data

Target:

- pisahkan konsep barang, transaksi/logistik, dan dokumentasi
- siapkan pondasi untuk harga dinamis

Rencana:

- buat master data barang
- ubah tabel logistik agar fokus ke transaksi/laporan
- siapkan tabel relasi foto
- siapkan tabel histori harga

Output:

- struktur data lebih jelas
- lebih mudah menambahkan fitur bisnis ke depan

### Tahap 2: Tambahkan modul harga

Target:

- manajemen kantor dapat mengatur harga
- harga memiliki histori perubahan

Rencana:

- tambah menu pengelolaan harga
- tambah validasi harga
- simpan tanggal berlaku harga
- simpan siapa yang membuat atau mengubah harga

Output:

- harga dapat dikelola resmi oleh pihak kantor
- perubahan harga dapat dilacak

### Tahap 3: Ubah dokumentasi foto menjadi multi upload

Target:

- satu data dapat memiliki sampai 10 foto

Rencana:

- ubah form upload
- ubah validasi file
- simpan banyak file ke tabel relasi
- tampilkan galeri foto pada detail/listing

Output:

- dokumentasi lapangan lebih lengkap

### Tahap 4: Tambahkan filter lanjutan

Target:

- user dapat mencari data dengan lebih cepat dan spesifik

Rencana:

- filter tanggal dari-sampai
- filter barang
- filter harga
- filter cabang
- filter status
- filter kategori masuk/keluar

Output:

- listing lebih usable untuk kebutuhan operasional harian

### Tahap 5: Revisi dashboard

Target:

- dashboard lebih relevan untuk manajemen

Rencana:

- tampilkan total nilai barang masuk
- tampilkan total nilai barang keluar
- tampilkan ringkasan per cabang
- tampilkan barang yang paling sering bergerak
- tampilkan aktivitas terbaru yang lebih informatif

Output:

- dashboard lebih berguna sebagai alat monitoring bisnis

---

## Rekomendasi Perubahan Struktur Data

### 1. Tabel `items` atau `products`

Tujuan:

- menyimpan master barang

Field minimum:

- `id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

### 2. Tabel `item_prices`

Tujuan:

- menyimpan histori harga barang

Field minimum:

- `id`
- `item_id`
- `branch_id` nullable jika harga global
- `price`
- `effective_date`
- `created_by`
- `notes`
- `created_at`
- `updated_at`

### 3. Penyesuaian tabel `logistics`

Tujuan:

- fokus ke transaksi atau pergerakan barang

Field yang disarankan:

- `item_id`
- `quantity`
- `transaction_date`
- `unit_price_snapshot`
- `total_price`
- `branch_id`
- `status`
- `notes`
- `created_by`

Catatan:

- jika tetap mempertahankan alur laporan lapangan, pertimbangkan memisahkan menjadi tabel `field_reports`

### 4. Tabel `logistics_photos`

Tujuan:

- menyimpan banyak foto untuk satu data logistik

Field minimum:

- `id`
- `logistics_id`
- `photo_path`
- `sort_order`
- `created_at`
- `updated_at`

---

## Penyesuaian Role dan Hak Akses

### M. Kantor

- kelola harga
- lihat semua cabang
- atur data master barang
- verifikasi data
- lihat dashboard global

### Officer / M. Logistik

- input transaksi logistik
- lihat data cabang terkait
- tambah catatan operasional
- tidak mengubah harga utama jika aturan bisnis mengharuskan kantor sebagai pengendali harga

### M. Lapangan

- kirim laporan lapangan
- unggah dokumentasi
- akses terbatas ke cabang masing-masing

---

## Penyesuaian UI/UX

### Form input logistik

Perlu diubah agar field lebih jelas:

- pilih barang
- jumlah
- tanggal
- cabang
- harga aktif atau snapshot harga
- keterangan
- multi foto

### Listing logistik

Kolom perlu ditambah atau diperjelas:

- nama barang
- jumlah
- harga satuan
- total harga
- tanggal
- cabang
- status
- jumlah foto

### Dashboard

Perlu menampilkan data yang lebih manajerial:

- total transaksi
- total nilai barang
- ringkasan per cabang
- tren transaksi
- barang dominan

---

## Prioritas Implementasi

### Prioritas Tinggi

1. Tambah struktur harga
2. Tambah hak akses manajemen kantor untuk harga
3. Ubah foto tunggal menjadi multi foto
4. Tambah filter lanjutan

### Prioritas Menengah

1. Refactor struktur data logistik
2. Tambah master barang
3. Sesuaikan upload Excel
4. Revisi dashboard

### Prioritas Rendah

1. Penyempurnaan tampilan laporan
2. Optimasi statistik tambahan
3. Penyempurnaan histori audit

---

## Risiko Implementasi

### 1. Perubahan struktur data dapat memengaruhi data lama

Mitigasi:

- gunakan migration bertahap
- sediakan fallback atau mapping data lama

### 2. UI lama bisa tidak lagi cocok dengan struktur baru

Mitigasi:

- ubah form dan listing secara paralel dengan migration

### 3. Data lama ambigu

Contoh:

- `nama_barang` mungkin berisi nama barang atau nama pelapor

Mitigasi:

- lakukan pembersihan data
- tentukan aturan migrasi manual jika perlu

### 4. Scope implementasi membesar

Mitigasi:

- pecah ke beberapa milestone
- selesaikan fitur inti lebih dulu

---

## Milestone yang Disarankan

### Milestone 1

- finalisasi requirement revisi
- finalisasi model data baru
- finalisasi aturan harga

### Milestone 2

- implementasi migration baru
- implementasi master barang
- implementasi histori harga

### Milestone 3

- implementasi perubahan form logistik
- implementasi multi upload foto
- implementasi filter lanjutan

### Milestone 4

- implementasi dashboard baru
- penyesuaian upload massal
- testing dan validasi

---

## Output Akhir yang Diharapkan

Setelah tahap ini selesai, sistem diharapkan:

- lebih sesuai dengan kebutuhan logistik nyata
- mendukung harga yang dinamis
- memungkinkan kantor mengelola harga
- mendukung dokumentasi foto yang lebih lengkap
- mempermudah pencarian data operasional
- memberi dashboard yang lebih berguna untuk pengambilan keputusan

---

## Kesimpulan

Implementasi tahap II sebaiknya difokuskan pada pembenahan fondasi data dan penambahan fitur inti bisnis, bukan hanya kosmetik tampilan. Arah yang paling tepat adalah membangun sistem yang lebih tegas membedakan:

- barang
- harga
- transaksi/logistik
- laporan lapangan
- dokumentasi

Dengan pendekatan bertahap, sistem yang sudah ada tetap bisa dikembangkan tanpa harus dibangun ulang dari nol, tetapi hasil akhirnya akan jauh lebih dekat dengan kebutuhan klien.
