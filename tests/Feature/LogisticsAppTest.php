<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemPrice;
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
        $response->assertSee('QR Code');
    }

    public function test_kantor_can_see_and_download_generated_user_barcode(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Barcode',
            'code' => 'BCD',
            'address' => 'Alamat Barcode',
        ]);

        $manager = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-barcode@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $fieldUser = User::create([
            'name' => 'Petugas Barcode',
            'email' => 'lapangan-barcode@test.local',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'LPG-123-XY',
            'branch_id' => $branch->id,
        ]);

        $indexResponse = $this->actingAs($manager)->get(route('superadmin.users.index'));

        $indexResponse->assertOk();
        $indexResponse->assertSee('Unduh QR Code');
        $indexResponse->assertSee(route('superadmin.users.barcode', $fieldUser), false);
        $indexResponse->assertSee('LPG-123-XY');

        $downloadResponse = $this->actingAs($manager)->get(route('superadmin.users.barcode', $fieldUser));

        $downloadResponse->assertOk();
        $downloadResponse->assertHeader('content-disposition', 'attachment; filename="qr-code-lpg-123-xy.svg"');
        $this->assertStringContainsString('<svg', $downloadResponse->getContent());
        $this->assertStringContainsString('LPG-123-XY', $downloadResponse->getContent());
    }

    public function test_identity_preview_uses_role_branch_and_unique_index(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Jakarta',
            'code' => 'JKT',
            'address' => 'Alamat Jakarta',
        ]);

        $manager = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-preview@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        User::create([
            'name' => 'Lapangan Lama',
            'email' => 'lapangan-lama@test.local',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'LPG-JKT-001',
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($manager)->getJson(route('superadmin.users.identity-preview', [
            'role' => User::ROLE_LAPANGAN,
            'branch_id' => $branch->id,
        ]));

        $response->assertOk();
        $response->assertJson([
            'identity_number' => 'LPG-JKT-002',
        ]);
    }

    public function test_kantor_can_create_user_with_generated_identity_number(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Bandung',
            'code' => 'BDG',
            'address' => 'Alamat Bandung',
        ]);

        $manager = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-generate@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        User::create([
            'name' => 'Petugas Sebelumnya',
            'email' => 'sebelumnya@test.local',
            'password' => 'password',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'LPG-BDG-001',
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($manager)->post(route('superadmin.users.store'), [
            'name' => 'Petugas Baru',
            'password' => 'password123',
            'role' => User::ROLE_LAPANGAN,
            'identity_number' => 'MANUAL-OVERRIDE',
            'branch_id' => $branch->id,
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => null,
            'identity_number' => 'LPG-BDG-002',
            'branch_id' => $branch->id,
        ]);
    }

    public function test_lapangan_user_can_be_created_without_email(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Surabaya',
            'code' => 'SBY',
            'address' => 'Alamat Surabaya',
        ]);

        $manager = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-lapangan@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $response = $this->actingAs($manager)->post(route('superadmin.users.store'), [
            'name' => 'Petugas Tanpa Email',
            'password' => 'password123',
            'role' => User::ROLE_LAPANGAN,
            'branch_id' => $branch->id,
        ]);

        $response->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Petugas Tanpa Email',
            'email' => null,
            'identity_number' => 'LPG-SBY-001',
        ]);
    }

    public function test_non_lapangan_user_still_requires_email(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Semarang',
            'code' => 'SMG',
            'address' => 'Alamat Semarang',
        ]);

        $manager = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-require-email@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $response = $this->actingAs($manager)->from(route('superadmin.users.create'))->post(route('superadmin.users.store'), [
            'name' => 'Officer Tanpa Email',
            'password' => 'password123',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $branch->id,
        ]);

        $response->assertRedirect(route('superadmin.users.create'));
        $response->assertSessionHasErrors('email');
    }

    public function test_kantor_can_access_item_and_price_management(): void
    {
        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-item-price@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $item = Item::create([
            'code' => 'BRG-001',
            'name' => 'Barang Uji',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.items.index'))
            ->assertOk()
            ->assertSee('Master Barang');

        $this->actingAs($user)
            ->get(route('superadmin.prices.create'))
            ->assertOk()
            ->assertSee($item->name);
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

    public function test_kantor_dashboard_shows_value_metrics_and_date_filter(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Dashboard',
            'code' => 'DSH',
            'address' => 'Alamat Dashboard',
        ]);

        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-dashboard@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $item = Item::create([
            'code' => 'DSH-001',
            'name' => 'Monitor LED',
            'is_active' => true,
        ]);

        Logistics::create([
            'item_id' => $item->id,
            'nama_barang' => $item->name,
            'kategori' => 'masuk',
            'jumlah' => 2,
            'unit_price_snapshot' => 1000000,
            'total_price' => 2000000,
            'tanggal' => '2026-05-02',
            'keterangan' => 'Transaksi masuk',
            'status' => 'approved',
            'branch_id' => $branch->id,
            'created_by' => $user->id,
        ]);

        Logistics::create([
            'item_id' => $item->id,
            'nama_barang' => $item->name,
            'kategori' => 'keluar',
            'jumlah' => 1,
            'unit_price_snapshot' => 500000,
            'total_price' => 500000,
            'tanggal' => '2026-05-03',
            'keterangan' => 'Transaksi keluar',
            'status' => 'pending',
            'branch_id' => $branch->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.dashboard', [
            'branch_id' => $branch->id,
            'date_from' => '2026-05-03',
            'date_to' => '2026-05-03',
        ]));

        $response->assertOk();
        $response->assertViewHas('stats', fn (array $stats) => $stats['total'] === 1
            && (float) $stats['totalValue'] === 500000.0
            && (float) $stats['incomingValue'] === 0.0
            && (float) $stats['outgoingValue'] === 500000.0);
        $response->assertViewHas('branchSummaries', fn ($summaries) => $summaries->count() === 1);
        $response->assertSee('Nilai Total');
    }

    public function test_logistik_dashboard_is_limited_to_own_branch_summary(): void
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
            'code' => 'DA',
            'address' => 'Alamat A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
            'code' => 'DB',
            'address' => 'Alamat B',
        ]);

        $user = User::create([
            'name' => 'Officer Logistik',
            'email' => 'logistik-dashboard@test.local',
            'password' => 'password',
            'role' => User::ROLE_LOGISTIK,
            'branch_id' => $branchA->id,
        ]);

        $item = Item::create([
            'code' => 'CAB-001',
            'name' => 'Printer',
            'is_active' => true,
        ]);

        Logistics::create([
            'item_id' => $item->id,
            'nama_barang' => $item->name,
            'kategori' => 'masuk',
            'jumlah' => 3,
            'unit_price_snapshot' => 700000,
            'total_price' => 2100000,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Cabang A',
            'status' => 'approved',
            'branch_id' => $branchA->id,
            'created_by' => $user->id,
        ]);

        Logistics::create([
            'item_id' => $item->id,
            'nama_barang' => $item->name,
            'kategori' => 'masuk',
            'jumlah' => 5,
            'unit_price_snapshot' => 700000,
            'total_price' => 3500000,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Cabang B',
            'status' => 'approved',
            'branch_id' => $branchB->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('branchSummaries', fn ($summaries) => $summaries->count() === 1
            && $summaries->first()['branch'] === 'Cabang A');
        $response->assertSee('Ringkasan Cabang Anda');
        $response->assertDontSee('Cabang B');
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
            'photos' => [
                UploadedFile::fake()->image('lapangan-1.jpg'),
                UploadedFile::fake()->image('lapangan-2.jpg'),
            ],
        ]);

        $response->assertRedirect(route('field-reports.create'));

        $this->assertDatabaseHas('logistics', [
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'nama_barang' => 'Petugas Lapangan',
            'keterangan' => 'Foto kondisi barang sudah diunggah dari lokasi.',
        ]);
        $this->assertDatabaseCount('logistics_photos', 2);
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
