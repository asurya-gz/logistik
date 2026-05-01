<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Logistics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_super_admin_can_access_branch_management(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.local',
            'password' => 'password',
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($user)->get('/branches');

        $response->assertOk();
        $response->assertSee('Manajemen Cabang');
    }

    public function test_branch_user_only_sees_own_branch_logistics(): void
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
            'code' => 'A',
            'address' => 'Alamat A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
            'code' => 'B',
            'address' => 'Alamat B',
        ]);

        $user = User::create([
            'name' => 'User Cabang',
            'email' => 'user@test.local',
            'password' => 'password',
            'role' => 'user_cabang',
            'branch_id' => $branchA->id,
        ]);

        Logistics::create([
            'nama_barang' => 'Barang Cabang A',
            'kategori' => 'masuk',
            'jumlah' => 3,
            'tanggal' => now()->toDateString(),
            'keterangan' => null,
            'status' => 'pending',
            'branch_id' => $branchA->id,
            'created_by' => $user->id,
        ]);

        Logistics::create([
            'nama_barang' => 'Barang Cabang B',
            'kategori' => 'keluar',
            'jumlah' => 2,
            'tanggal' => now()->toDateString(),
            'keterangan' => null,
            'status' => 'pending',
            'branch_id' => $branchB->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/logistics');

        $response->assertOk();
        $response->assertSee('Barang Cabang A');
        $response->assertDontSee('Barang Cabang B');
    }
}
