<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Logistics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogisticsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_kantor_can_access_branch_management(): void
    {
        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.branches.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Cabang');
    }

    public function test_kantor_can_access_user_management(): void
    {
        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-user@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.users.index'));

        $response->assertOk();
        $response->assertSee('Manajemen User');
    }

    public function test_logistik_cannot_access_user_management(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'code' => 'A1',
            'address' => 'Alamat A',
        ]);

        $user = User::create([
            'name' => 'Officer Logistik',
            'email' => 'logistik@test.local',
            'password' => 'password',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.users.index'));

        $response->assertForbidden();
    }

    public function test_logistik_only_sees_own_branch_logistics(): void
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
            'name' => 'Officer Logistik',
            'email' => 'logistik-branch@test.local',
            'password' => 'password',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $branchA->id,
        ]);

        Logistics::create([
            'nama_barang' => 'Pelapor Cabang A',
            'kategori' => 'masuk',
            'jumlah' => 1,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Laporan Cabang A',
            'status' => 'pending',
            'branch_id' => $branchA->id,
            'created_by' => $user->id,
        ]);

        Logistics::create([
            'nama_barang' => 'Pelapor Cabang B',
            'kategori' => 'masuk',
            'jumlah' => 1,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Laporan Cabang B',
            'status' => 'pending',
            'branch_id' => $branchB->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin.logistics.index'));

        $response->assertOk();
        $response->assertSee('Laporan Cabang A');
        $response->assertDontSee('Laporan Cabang B');
    }

    public function test_lapangan_can_submit_simple_information(): void
    {
        Storage::fake('public');

        $branch = Branch::create([
            'name' => 'Cabang A',
            'code' => 'A1',
            'address' => 'Alamat A',
        ]);

        $user = User::create([
            'name' => 'Petugas Lapangan',
            'email' => 'lapangan@test.local',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'LPG-001',
            'branch_id' => $branch->id,
        ]);

        $this->post(route('field-reports.verify'), [
            'identity_number' => 'LPG-001',
        ])->assertRedirect(route('field-reports.create'));

        $response = $this->post(route('field-reports.store'), [
            'keterangan' => 'Foto kondisi barang sudah diunggah dari lokasi.',
            'photo' => UploadedFile::fake()->image('lapangan.jpg'),
        ]);

        $response->assertRedirect(route('field-reports.create'));

        $this->assertDatabaseHas('logistics', [
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'nama_barang' => 'Petugas Lapangan',
            'keterangan' => 'Foto kondisi barang sudah diunggah dari lokasi.',
        ]);
    }

    public function test_logistik_can_add_office_note_without_editing_information(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'code' => 'A1',
            'address' => 'Alamat A',
        ]);

        $fieldUser = User::create([
            'name' => 'Petugas Lapangan',
            'email' => 'lapangan-note@test.local',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'LPG-002',
            'branch_id' => $branch->id,
        ]);

        $logisticsUser = User::create([
            'name' => 'Officer Logistik',
            'email' => 'logistik-note@test.local',
            'password' => 'password',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $branch->id,
        ]);

        $item = Logistics::create([
            'nama_barang' => $fieldUser->name,
            'kategori' => 'masuk',
            'jumlah' => 1,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Dokumentasi lapangan awal.',
            'status' => 'pending',
            'branch_id' => $branch->id,
            'created_by' => $fieldUser->id,
        ]);

        $response = $this->actingAs($logisticsUser)->patch(route('admin.logistics.office-note', $item), [
            'office_note' => 'Mohon kirim update susulan besok pagi.',
        ]);

        $response->assertRedirect(route('admin.logistics.index'));

        $this->assertDatabaseHas('logistics', [
            'id' => $item->id,
            'office_note' => 'Mohon kirim update susulan besok pagi.',
            'keterangan' => 'Dokumentasi lapangan awal.',
        ]);
    }

    public function test_unknown_identity_cannot_open_field_form(): void
    {
        $response = $this->post(route('field-reports.verify'), [
            'identity_number' => 'UNKNOWN-001',
        ]);

        $response->assertSessionHasErrors('identity_number');
    }
}
