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
            'name' => 'Super Admin',
            'email' => 'superadmin@logistik.test',
            'password' => 'password',
            'role' => 'super_admin',
        ]);

        $branchAdmin = User::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jakarta@logistik.test',
            'password' => 'password',
            'role' => 'admin_cabang',
            'branch_id' => $jakarta->id,
        ]);

        Logistics::insert([
            [
                'nama_barang' => 'Laptop Operasional',
                'kategori' => 'masuk',
                'jumlah' => 12,
                'tanggal' => now()->subDays(3)->toDateString(),
                'keterangan' => 'Pengadaan awal pelatihan',
                'status' => 'approved',
                'branch_id' => $jakarta->id,
                'created_by' => $branchAdmin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Printer Gudang',
                'kategori' => 'keluar',
                'jumlah' => 2,
                'tanggal' => now()->subDay()->toDateString(),
                'keterangan' => 'Distribusi ke area packing',
                'status' => 'pending',
                'branch_id' => $jakarta->id,
                'created_by' => $branchAdmin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_barang' => 'Scanner Barcode',
                'kategori' => 'masuk',
                'jumlah' => 6,
                'tanggal' => now()->subDays(2)->toDateString(),
                'keterangan' => 'Cabang Bandung',
                'status' => 'rejected',
                'branch_id' => $bandung->id,
                'created_by' => $superAdmin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
