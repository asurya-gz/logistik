<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Logistics;
use App\Models\Upload;
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

    public function test_kantor_can_import_excel_and_create_item_snapshot_transaction(): void
    {
        Storage::fake('local');

        $branch = Branch::create([
            'name' => 'Cabang Import',
            'code' => 'IMP',
            'address' => 'Alamat Import',
        ]);

        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-import@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $item = Item::create([
            'code' => 'BRG-001',
            'name' => 'Laptop ThinkPad',
            'is_active' => true,
        ]);

        ItemPrice::create([
            'item_id' => $item->id,
            'branch_id' => $branch->id,
            'price' => 15000000,
            'effective_date' => now()->toDateString(),
            'created_by' => $user->id,
        ]);

        $fallbackItem = Item::create([
            'code' => 'LAPTOP-THINKPAD',
            'name' => 'Laptop ThinkPad',
            'is_active' => true,
        ]);

        ItemPrice::create([
            'item_id' => $fallbackItem->id,
            'branch_id' => $branch->id,
            'price' => 15000000,
            'effective_date' => now()->toDateString(),
            'created_by' => $user->id,
        ]);

        $filePath = storage_path('framework/testing/import-logistik.csv');
        $this->createCsv($filePath, [
            ['kode_barang', 'nama_barang', 'kategori', 'jumlah', 'tanggal', 'keterangan'],
            ['BRG-001', 'Laptop ThinkPad', 'masuk', 2, now()->toDateString(), 'Import stok showroom'],
        ]);

        $response = $this->actingAs($user)->post(route('superadmin.uploads.store'), [
            'branch_id' => $branch->id,
            'file' => new UploadedFile($filePath, 'import-logistik.csv', 'text/csv', null, true),
        ]);

        $response->assertRedirect(route('superadmin.uploads.index'));

        $this->assertDatabaseHas('logistics', [
            'branch_id' => $branch->id,
            'nama_barang' => 'Laptop ThinkPad',
            'jumlah' => 2,
            'unit_price_snapshot' => 15000000.00,
            'total_price' => 30000000.00,
            'keterangan' => 'Import stok showroom',
        ]);

        $this->assertDatabaseHas('uploads', [
            'branch_id' => $branch->id,
            'uploaded_by' => $user->id,
            'total_rows' => 1,
        ]);

        @unlink($filePath);
    }

    public function test_excel_import_rejects_file_with_only_invalid_rows(): void
    {
        Storage::fake('local');

        $branch = Branch::create([
            'name' => 'Cabang Invalid',
            'code' => 'INV',
            'address' => 'Alamat Invalid',
        ]);

        $user = User::create([
            'name' => 'Manajemen Kantor',
            'email' => 'kantor-invalid-import@test.local',
            'password' => 'password',
            'role' => User::ROLE_KANTOR,
        ]);

        $filePath = storage_path('framework/testing/import-invalid.csv');
        $this->createCsv($filePath, [
            ['kode_barang', 'nama_barang', 'kategori', 'jumlah', 'tanggal', 'keterangan'],
            ['', '', 'unknown', 0, '', 'Baris invalid'],
        ]);

        $response = $this->from(route('superadmin.uploads.index'))->actingAs($user)->post(route('superadmin.uploads.store'), [
            'branch_id' => $branch->id,
            'file' => new UploadedFile($filePath, 'import-invalid.csv', 'text/csv', null, true),
        ]);

        $response->assertRedirect(route('superadmin.uploads.index'));
        $response->assertSessionHasErrors('file');

        $this->assertDatabaseCount('logistics', 0);
        $this->assertDatabaseCount('uploads', 0);

        @unlink($filePath);
    }

    private function createCsv(string $path, array $rows): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $handle = fopen($path, 'w');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}
