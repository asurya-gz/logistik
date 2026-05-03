# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
## Sistem Manajemen Logistik Multi Cabang (Versi Pelatihan)

---

## 1. Pendahuluan

### 1.1 Tujuan
Dokumen ini mendefinisikan kebutuhan sistem manajemen logistik berbasis web yang mendukung pengelolaan data logistik untuk beberapa cabang, termasuk fitur upload data, verifikasi, dan dashboard monitoring.

### 1.2 Ruang Lingkup
Sistem ini digunakan untuk:
- Mengelola data logistik (barang masuk/keluar)
- Mendukung banyak cabang (multi-branch)
- Upload data logistik (file)
- Proses verifikasi data (approval workflow)
- Monitoring melalui dashboard

### 1.3 Definisi Singkat
- Cabang: Unit/lokasi operasional
- Verifikasi: Proses approval data
- Dashboard: Tampilan ringkasan data

---

## 2. Deskripsi Umum Sistem

### 2.1 Perspektif Sistem
Aplikasi berbasis web (browser-based), standalone, tanpa integrasi eksternal.

### 2.2 Karakteristik Pengguna

| Role | Deskripsi |
|------|----------|
| User Cabang | Input & upload data logistik |
| Admin Cabang | Verifikasi data cabang |
| Super Admin | Akses seluruh cabang |

### 2.3 Batasan Sistem
- Tidak ada integrasi API eksternal
- Tidak mendukung multi gudang dalam 1 cabang
- Fokus pada kebutuhan pelatihan

---

## 3. Kebutuhan Fungsional

### 3.1 Modul Autentikasi
- Login menggunakan email & password
- Sistem mengenali role user
- Sistem mengaitkan user dengan cabang

### 3.2 Modul Manajemen Cabang

#### Fitur:
- Tambah cabang
- Edit cabang
- Hapus cabang
- Lihat daftar cabang

#### Akses:
- Hanya Super Admin

### 3.3 Modul Manajemen Logistik

#### Fitur:
- Tambah data logistik
- Edit data logistik
- Hapus data logistik
- Lihat daftar data logistik

#### Field:
- Nama barang
- Kategori
- Jumlah
- Tanggal
- Keterangan
- Status (Pending / Approved / Rejected)
- Cabang

#### Behavior:
- Data otomatis terhubung ke cabang user
- User hanya dapat melihat data cabangnya

### 3.4 Modul Upload Data

#### Fitur:
- Upload file (Excel/CSV)
- Sistem membaca dan menyimpan data
- Validasi format file

#### Behavior:
- Data hasil upload masuk ke cabang user

### 3.5 Modul Verifikasi Data

#### Fitur:
- Melihat data berstatus Pending
- Approve data
- Reject data
- Menambahkan catatan saat reject

#### Akses:
- Admin Cabang: hanya cabangnya
- Super Admin: semua cabang

### 3.6 Modul Dashboard

#### Menampilkan:
- Total data logistik
- Jumlah data:
  - Pending
  - Approved
  - Rejected
- Grafik:
  - Barang masuk vs keluar
- Aktivitas terbaru

#### Fitur tambahan:
- Filter berdasarkan cabang
- Global view (Super Admin)

---

## 4. Kebutuhan Non-Fungsional

### 4.1 Usability
- UI sederhana dan mudah digunakan
- Responsif (desktop/laptop)

### 4.2 Performance
- Mendukung ±100 transaksi data per hari

### 4.3 Security
- Authentication (login)
- Role-based access control
- Isolasi data antar cabang

### 4.4 Reliability
- Data tersimpan di database
- Tidak ada kehilangan data saat proses normal

---

## 5. Struktur Data (High-Level)

### 5.1 Tabel: Users
- id
- name
- email
- password
- role
- branch_id (nullable untuk super admin)

### 5.2 Tabel: Branches
- id
- name
- code
- address
- created_at

### 5.3 Tabel: Logistics
- id
- nama_barang
- kategori
- jumlah
- tanggal
- keterangan
- status (pending/approved/rejected)
- branch_id
- created_by

### 5.4 Tabel: Uploads
- id
- file_path
- uploaded_by
- branch_id
- tanggal_upload

### 5.5 Tabel: Verification
- id
- logistics_id
- status
- note
- verified_by
- tanggal_verifikasi

---

## 6. Workflow Sistem

### 6.1 Alur Utama
1. User login
2. Sistem mendeteksi role & cabang
3. User input / upload data
4. Data masuk status Pending
5. Admin melakukan verifikasi
   - Approve / Reject
6. Data tampil di dashboard

---

## 7. Aturan Bisnis (Business Rules)

- Setiap data logistik wajib memiliki cabang
- User hanya dapat mengakses data cabangnya
- Verifikasi hanya dapat dilakukan oleh Admin/Super Admin
- Data yang sudah di-approve tidak dapat diedit (opsional)

---

## 8. Asumsi

- Sistem digunakan untuk pelatihan
- Jumlah user terbatas
- Data tidak kompleks

---

## 9. Teknologi yang Digunakan

- Backend: Laravel
- Frontend: Blade / (opsional Vue dengan Inertia)
- Database: MySQL
- Authentication: Laravel Auth (Sanctum/Breeze/Jetstream)

---

## 10. Kesimpulan

Sistem ini adalah aplikasi web manajemen logistik multi cabang dengan fitur input data, upload file, verifikasi (approval workflow), dan dashboard monitoring berbasis role.

