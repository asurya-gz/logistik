# Checklist Eksekusi

## Tujuan

Checklist ini digunakan untuk memantau progres implementasi revisi sistem logistik tahap II secara praktis dan terstruktur.

## Legenda Status

- `[x]` Selesai
- `[-]` Parsial / sudah mulai tetapi belum tuntas
- `[ ]` Belum dikerjakan

---

## 1. Finalisasi Requirement

- [ ] Tetapkan aturan bisnis harga: global atau per cabang
- [ ] Tetapkan aturan tanggal berlaku harga
- [ ] Tetapkan apakah transaksi menyimpan snapshot harga
- [ ] Tetapkan struktur master barang
- [ ] Tetapkan apakah laporan lapangan dipisah dari transaksi logistik
- [ ] Tetapkan role yang boleh mengelola barang
- [ ] Tetapkan role yang boleh mengelola harga

---

## 2. Persiapan Struktur Data

- [x] Buat migration tabel master barang
- [x] Buat model master barang
- [x] Buat migration tabel histori harga
- [x] Buat model histori harga
- [x] Buat migration perubahan tabel logistik
- [x] Tambahkan relasi `item_id` pada logistik
- [x] Tambahkan field `unit_price_snapshot`
- [x] Tambahkan field `total_price`
- [x] Buat migration tabel multi foto logistik
- [x] Buat model foto logistik
- [ ] Siapkan catatan migrasi data lama

---

## 3. Implementasi Master Barang

- [x] Buat controller master barang
- [x] Tambahkan route master barang
- [x] Buat halaman daftar barang
- [x] Buat halaman form tambah barang
- [x] Buat halaman form edit barang
- [x] Tambahkan validasi input barang
- [x] Tambahkan pembatasan akses master barang

---

## 4. Implementasi Modul Harga

- [x] Buat controller pengelolaan harga
- [x] Tambahkan route pengelolaan harga
- [x] Buat halaman histori harga
- [x] Buat halaman form set harga
- [x] Tambahkan validasi input harga
- [x] Implementasikan rule harga aktif
- [x] Implementasikan snapshot harga pada transaksi
- [x] Batasi pengelolaan harga hanya untuk M. Kantor

---

## 5. Refactor Modul Logistik

- [x] Ubah validasi input logistik dari `nama_barang` ke `item_id`
- [x] Ubah form logistik agar memakai pilihan barang
- [x] Tambahkan field jumlah pada form
- [x] Tambahkan field tanggal pada form
- [x] Tambahkan field harga/snapshot harga pada form atau proses backend
- [x] Tambahkan dukungan multi foto pada form
- [x] Ubah listing logistik agar menampilkan nama barang
- [x] Ubah listing logistik agar menampilkan harga satuan
- [x] Ubah listing logistik agar menampilkan total harga
- [x] Ubah relasi model logistik ke item
- [x] Ubah relasi model logistik ke foto

---

## 6. Multi Upload Foto

- [x] Ubah input file menjadi multiple
- [x] Batasi upload maksimal 10 foto
- [x] Tambahkan validasi array file
- [x] Simpan semua file ke tabel relasi foto
- [x] Tampilkan jumlah foto pada listing
- [x] Tampilkan link atau preview galeri foto
- [x] Sesuaikan alur upload lapangan jika masih memakai modul yang sama

---

## 7. Filter Lanjutan

- [x] Tambahkan filter cabang
- [x] Tambahkan filter status
- [ ] Tambahkan filter kategori masuk/keluar
- [x] Tambahkan filter tanggal mulai
- [x] Tambahkan filter tanggal akhir
- [x] Tambahkan filter barang
- [x] Tambahkan filter harga minimum
- [x] Tambahkan filter harga maksimum
- [x] Pastikan filter bisa dipakai bersamaan
- [ ] Pastikan query listing tetap efisien

---

## 8. Revisi Dashboard

- [x] Tambahkan statistik total nilai transaksi
- [x] Tambahkan statistik barang masuk berdasarkan nilai
- [x] Tambahkan statistik barang keluar berdasarkan nilai
- [x] Tambahkan ringkasan barang yang paling sering bergerak
- [x] Tambahkan ringkasan per cabang
- [x] Tambahkan filter dashboard berdasarkan tanggal
- [x] Pastikan dashboard tetap relevan untuk role kantor dan cabang

---

## 9. Penyesuaian Upload Excel

- [x] Evaluasi template upload lama
- [x] Sesuaikan format kolom upload baru
- [x] Ubah parser upload agar mendukung item
- [x] Ubah parser upload agar mendukung quantity
- [x] Ubah parser upload agar mendukung tanggal
- [x] Ubah parser upload agar mendukung harga jika diperlukan
- [x] Validasi ulang hasil upload ke struktur baru

---

## 10. Role dan Navigasi

- [x] Tambahkan menu master barang di navigasi
- [x] Tambahkan menu harga di navigasi
- [x] Tambahkan capability `canManageItems()`
- [x] Tambahkan capability `canManagePrices()`
- [-] Pastikan role kantor bisa akses seluruh modul yang diperlukan
- [x] Pastikan role logistik tidak bisa ubah harga utama
- [-] Pastikan role lapangan hanya akses fitur yang sesuai

---

## 11. Testing

- [-] Buat test fitur master barang
- [ ] Buat test fitur histori harga
- [x] Buat test pembatasan akses harga
- [ ] Buat test create transaksi logistik
- [ ] Buat test update transaksi logistik
- [ ] Buat test filter logistik
- [x] Buat test multi upload foto
- [ ] Buat test batas maksimum 10 foto
- [x] Buat test dashboard setelah revisi
- [x] Buat test upload Excel sesuai struktur baru

---

## 12. Validasi Manual

- [ ] Uji login role kantor
- [ ] Uji login role logistik
- [ ] Uji login role lapangan
- [ ] Uji tambah barang
- [ ] Uji set harga
- [ ] Uji create transaksi logistik
- [ ] Uji upload multi foto
- [ ] Uji filter kombinasi
- [ ] Uji verifikasi data
- [ ] Uji dashboard setelah perubahan
- [ ] Uji upload Excel

---

## 13. Prioritas Eksekusi Cepat

Jika waktu terbatas, dahulukan:

- [x] Struktur harga
- [x] Hak akses harga oleh M. Kantor
- [x] Multi upload foto maksimal 10
- [x] Filter tanggal, barang, dan harga

---

## 14. Status Ringkas Proyek

- [ ] Requirement final disepakati
- [x] Struktur data baru siap
- [x] Modul barang selesai
- [x] Modul harga selesai
- [x] Modul logistik baru selesai
- [x] Multi foto selesai
- [-] Filter lanjutan selesai
- [x] Dashboard revisi selesai
- [x] Upload Excel revisi selesai
- [-] Testing selesai
- [ ] Siap demo ulang ke klien
