<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Logistics;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $selectedBranchId = $request->integer('branch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $branchOptions = Branch::query()->orderBy('name')->get();

        $logisticsQuery = Logistics::query()
            ->with(['branch', 'creator', 'item', 'photos'])
            ->visibleTo($user);

        $uploadsQuery = Upload::query()
            ->with(['branch', 'uploader']);

        if (! $user->isFullAccess()) {
            $uploadsQuery->where('branch_id', $user->branch_id);
        }

        if ($selectedBranchId) {
            $logisticsQuery->where('branch_id', $selectedBranchId);
            $uploadsQuery->where('branch_id', $selectedBranchId);
        }

        if ($dateFrom) {
            $logisticsQuery->whereDate('tanggal', '>=', $dateFrom);
            $uploadsQuery->whereDate('tanggal_upload', '>=', $dateFrom);
        }

        if ($dateTo) {
            $logisticsQuery->whereDate('tanggal', '<=', $dateTo);
            $uploadsQuery->whereDate('tanggal_upload', '<=', $dateTo);
        }

        $logistics = $logisticsQuery->get();
        $uploads = $uploadsQuery->latest('tanggal_upload')->take(5)->get();

        $incoming = $logistics->filter(fn (Logistics $item) => strtolower($item->kategori) === 'masuk');
        $outgoing = $logistics->filter(fn (Logistics $item) => strtolower($item->kategori) === 'keluar');

        // Today stats
        $today = $logistics->filter(fn (Logistics $item) => $item->tanggal?->isToday() ?? false);
        $todayApproved = $today->where('status', 'approved')->count();
        $todayPending = $today->where('status', 'pending')->count();

        // Approval rate
        $verified = $logistics->whereIn('status', ['approved', 'rejected']);
        $approvalRate = $verified->count() > 0
            ? round(($verified->where('status', 'approved')->count() / $verified->count()) * 100)
            : 0;

        // Total photos
        $totalPhotos = $logistics->sum(fn (Logistics $item) => $item->photos->count());

        // Latest pending (butuh tindakan)
        $pendingItems = $logistics
            ->where('status', 'pending')
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $stats = [
            'total' => $logistics->count(),
            'pending' => $logistics->where('status', 'pending')->count(),
            'approved' => $logistics->where('status', 'approved')->count(),
            'rejected' => $logistics->where('status', 'rejected')->count(),
            'totalValue' => (float) $logistics->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
            'incomingValue' => (float) $incoming->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
            'outgoingValue' => (float) $outgoing->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
        ];

        $movementChart = [
            'incomingValue' => $stats['incomingValue'],
            'outgoingValue' => $stats['outgoingValue'],
            'incomingQty' => $incoming->sum('jumlah'),
            'outgoingQty' => $outgoing->sum('jumlah'),
            'incomingReports' => $incoming->count(),
            'outgoingReports' => $outgoing->count(),
        ];

        $topItems = $logistics
            ->groupBy(fn (Logistics $item) => $item->item?->name ?? $item->nama_barang)
            ->map(function (Collection $group, string $name) {
                return [
                    'name' => $name,
                    'transactions' => $group->count(),
                    'quantity' => $group->sum('jumlah'),
                    'value' => (float) $group->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
                ];
            })
            ->sortByDesc('quantity')
            ->take(5)
            ->values();

        $branchSummaries = $logistics
            ->groupBy('branch_id')
            ->map(function (Collection $group) {
                $branch = $group->first()?->branch;

                return [
                    'branch' => $branch?->name ?? 'Tanpa Cabang',
                    'transactions' => $group->count(),
                    'quantity' => $group->sum('jumlah'),
                    'value' => (float) $group->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
                    'pending' => $group->where('status', 'pending')->count(),
                ];
            })
            ->sortByDesc('value')
            ->values();

        $recentActivities = Collection::make()
            ->merge($logistics->sortByDesc('created_at')->take(5)->map(function (Logistics $item) {
                $valueLabel = $item->total_price !== null
                    ? ' - Rp ' . number_format((float) $item->total_price, 0, ',', '.')
                    : '';

                return [
                    'title' => 'Transaksi ' . ($item->item?->name ?? $item->nama_barang),
                    'meta' => "{$item->branch->name} - {$item->kategori} {$item->jumlah} pcs{$valueLabel}",
                    'time' => $item->created_at,
                ];
            }))
            ->merge($uploads->map(function (Upload $item) {
                return [
                    'title' => "Upload file {$item->file_name}",
                    'meta' => "{$item->branch->name} - {$item->total_rows} baris",
                    'time' => $item->tanggal_upload,
                ];
            }))
            ->sortByDesc('time')
            ->take(8);

        return view('dashboard', [
            'branchOptions' => $branchOptions,
            'selectedBranchId' => $selectedBranchId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'stats' => $stats,
            'movementChart' => $movementChart,
            'topItems' => $topItems,
            'branchSummaries' => $branchSummaries,
            'recentActivities' => $recentActivities,
            'isGlobalView' => $user->isFullAccess(),
            'todayCount' => $today->count(),
            'todayValue' => (float) $today->sum(fn (Logistics $item) => (float) ($item->total_price ?? 0)),
            'todayApproved' => $todayApproved,
            'todayPending' => $todayPending,
            'approvalRate' => $approvalRate,
            'totalPhotos' => $totalPhotos,
            'pendingItems' => $pendingItems,
        ]);
    }
}
