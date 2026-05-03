<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Logistics;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->canUseExcelUpload()) {
            abort(403);
        }

        $query = Upload::query()->with(['branch', 'uploader'])->latest('tanggal_upload');

        if (! $user->isFullAccess()) {
            $query->where('branch_id', $user->branch_id);
        }

        return view('uploads.index', [
            'uploads' => $query->paginate(10),
            'branches' => Branch::query()->orderBy('name')->get(),
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->canUseExcelUpload()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx'],
            'branch_id' => [$user->isFullAccess() ? 'required' : 'nullable', 'integer', 'exists:branches,id'],
        ]);

        $branchId = $user->isFullAccess() ? (int) $validated['branch_id'] : $user->branch_id;
        $file = $request->file('file');
        $rows = collect($this->parseSpreadsheet($file->getRealPath()))
            ->map(fn (array $row) => $this->normalizeRow($row))
            ->filter()
            ->values()
            ->all();

        if (count($rows) === 0) {
            return back()->withErrors(['file' => 'File tidak berisi baris valid yang dapat diimpor.'])->withInput();
        }

        $storedPath = $file->store('uploads', 'local');

        DB::transaction(function () use ($rows, $user, $branchId, $storedPath, $file): void {
            $imported = 0;

            foreach ($rows as $row) {
                $normalized = $this->normalizeRow($row);

                if (! $normalized) {
                    continue;
                }

                $item = Item::firstOrCreate(
                    ['code' => $normalized['item_code']],
                    [
                        'name' => $normalized['nama_barang'],
                        'description' => 'Dibuat otomatis dari upload Excel.',
                        'is_active' => true,
                    ]
                );

                if ($item->name !== $normalized['nama_barang']) {
                    $item->update(['name' => $normalized['nama_barang']]);
                }

                $unitPrice = $this->resolveUnitPrice($item->id, $branchId, $normalized['tanggal']);

                Logistics::create([
                    'item_id' => $item->id,
                    'nama_barang' => $normalized['nama_barang'],
                    'kategori' => $normalized['kategori'],
                    'jumlah' => $normalized['jumlah'],
                    'unit_price_snapshot' => $unitPrice,
                    'total_price' => $unitPrice !== null ? $unitPrice * $normalized['jumlah'] : null,
                    'tanggal' => $normalized['tanggal'],
                    'keterangan' => $normalized['keterangan'],
                    'status' => 'pending',
                    'branch_id' => $branchId,
                    'created_by' => $user->id,
                ]);

                $imported++;
            }

            Upload::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'uploaded_by' => $user->id,
                'branch_id' => $branchId,
                'tanggal_upload' => now(),
                'total_rows' => $imported,
            ]);
        });

        return redirect()->route($user->panelRouteName('uploads.index'))->with('success', 'File berhasil diunggah dan diproses.');
    }

    private function parseSpreadsheet(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return [];
        }

        $header = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));

        return array_map(function (array $row) use ($header) {
            $mapped = [];
            $values = array_values($row);

            foreach (array_values($header) as $index => $column) {
                $mapped[$column] = $values[$index] ?? null;
            }

            return $mapped;
        }, $rows);
    }

    private function normalizeRow(array $row): ?array
    {
        $namaBarang = trim((string) ($row['nama_barang'] ?? $row['nama barang'] ?? ''));
        $itemCode = trim((string) ($row['kode_barang'] ?? $row['kode barang'] ?? Str::of($namaBarang)->slug('-')->upper()));
        $kategori = strtolower(trim((string) ($row['kategori'] ?? '')));
        $jumlah = (int) ($row['jumlah'] ?? 0);
        $tanggalValue = $row['tanggal'] ?? '';
        $tanggal = is_numeric($tanggalValue)
            ? SpreadsheetDate::excelToDateTimeObject((float) $tanggalValue)->format('Y-m-d')
            : trim((string) $tanggalValue);

        if ($namaBarang === '' || ! in_array($kategori, ['masuk', 'keluar'], true) || $jumlah < 1 || $tanggal === '') {
            return null;
        }

        $parsedTanggal = strtotime($tanggal);

        if ($parsedTanggal === false) {
            return null;
        }

        return [
            'item_code' => $itemCode,
            'nama_barang' => $namaBarang,
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'tanggal' => date('Y-m-d', $parsedTanggal),
            'keterangan' => trim((string) ($row['keterangan'] ?? '')),
        ];
    }

    private function resolveUnitPrice(int $itemId, ?int $branchId, string $date): ?float
    {
        if ($branchId) {
            $branchPrice = ItemPrice::query()
                ->where('item_id', $itemId)
                ->where('branch_id', $branchId)
                ->effectiveOn($date)
                ->latest('effective_date')
                ->latest('id')
                ->value('price');

            if ($branchPrice !== null) {
                return (float) $branchPrice;
            }
        }

        $globalPrice = ItemPrice::query()
            ->where('item_id', $itemId)
            ->whereNull('branch_id')
            ->effectiveOn($date)
            ->latest('effective_date')
            ->latest('id')
            ->value('price');

        return $globalPrice !== null ? (float) $globalPrice : null;
    }
}
