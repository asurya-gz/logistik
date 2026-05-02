<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Logistics;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jakarta = Branch::create([
            'name' => 'Cabang Jakarta',
            'code' => 'JKT',
            'address' => 'Jl. Sudirman No. 10, Jakarta',
        ]);

        $bandung = Branch::create([
            'name' => 'Cabang Bandung',
            'code' => 'BDG',
            'address' => 'Jl. Asia Afrika No. 25, Bandung',
        ]);

        $superAdmin = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor@logistik.test',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $logisticsManager = User::create([
            'name' => 'Manajemen Logistik Jakarta',
            'email' => 'logistik.jakarta@logistik.test',
            'password' => 'password',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $jakarta->id,
        ]);

        $fieldOfficer = User::create([
            'name' => 'Manajemen Lapangan Jakarta',
            'email' => 'lapangan.jakarta@logistik.test',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'branch_id' => $jakarta->id,
        ]);

        Logistics::insert([
            [
                'nama_barang' => $fieldOfficer->name,
                'kategori' => 'masuk',
                'jumlah' => 1,
                'tanggal' => now()->subDays(3)->toDateString(),
                'keterangan' => 'Foto material sudah diterima di lokasi proyek dan siap dicek lanjutan.',
                'office_note' => 'Cek kelengkapan material tahap 1.',
                'status' => 'approved',
                'branch_id' => $jakarta->id,
                'created_by' => $fieldOfficer->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => $fieldOfficer->name,
                'kategori' => 'masuk',
                'jumlah' => 1,
                'tanggal' => now()->subDay()->toDateString(),
                'keterangan' => 'Update kondisi lapangan setelah pengiriman sesi sore.',
                'office_note' => null,
                'status' => 'pending',
                'branch_id' => $jakarta->id,
                'created_by' => $logisticsManager->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => $superAdmin->name,
                'kategori' => 'masuk',
                'jumlah' => 1,
                'tanggal' => now()->subDays(2)->toDateString(),
                'keterangan' => 'Dokumentasi inspeksi pusat untuk cabang Bandung.',
                'office_note' => 'Perlu tindak lanjut pada area bongkar muat.',
                'status' => 'rejected',
                'branch_id' => $bandung->id,
                'created_by' => $superAdmin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
