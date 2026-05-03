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
        $branchOptions = Branch::orderBy('name')->get();

        $logisticsQuery = Logistics::query()
            ->with(['branch', 'creator'])
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

        $logistics = $logisticsQuery->get();
        $uploads = $uploadsQuery->latest('tanggal_upload')->take(5)->get();

        $stats = [
            'total' => $logistics->count(),
            'pending' => $logistics->where('status', 'pending')->count(),
            'approved' => $logistics->where('status', 'approved')->count(),
            'rejected' => $logistics->where('status', 'rejected')->count(),
        ];

        $chart = [
            'masuk' => $logistics->filter(fn (Logistics $item) => strtolower($item->kategori) === 'masuk')->sum('jumlah'),
            'keluar' => $logistics->filter(fn (Logistics $item) => strtolower($item->kategori) === 'keluar')->sum('jumlah'),
        ];

        $recentActivities = Collection::make()
            ->merge($logistics->sortByDesc('created_at')->take(5)->map(function (Logistics $item) {
                return [
                    'title' => "Data logistik {$item->nama_barang}",
                    'meta' => "{$item->branch->name} - status " . ucfirst($item->status),
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
            'stats' => $stats,
            'chart' => $chart,
            'recentActivities' => $recentActivities,
            'isGlobalView' => $user->isFullAccess(),
        ]);
    }
}
