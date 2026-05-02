<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Logistics;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedBranchId = $request->integer('branch_id');

        $query = Logistics::query()
            ->with(['branch', 'creator'])
            ->where('status', 'pending')
            ->latest('tanggal');

        if (! $user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        return view('verifications.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'branches' => Branch::orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'user' => $user,
        ]);
    }

    public function update(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canVerify()) {
            abort(403);
        }

        if (! $user->isSuperAdmin() && $user->branch_id !== $logistics->branch_id) {
            abort(403);
        }

        if ($logistics->status !== 'pending') {
            return back()->with('error', 'Hanya data berstatus pending yang bisa diverifikasi.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'note' => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'rejected' && blank($data['note'] ?? null)) {
            return back()->withErrors(['note' => 'Catatan wajib diisi saat reject.']);
        }

        DB::transaction(function () use ($data, $logistics, $user): void {
            $logistics->update([
                'status' => $data['status'],
            ]);

            Verification::create([
                'logistics_id' => $logistics->id,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'verified_by' => $user->id,
                'tanggal_verifikasi' => now(),
            ]);
        });

        return redirect()->route($user->panelRouteName('verifications.index'))->with('success', 'Data berhasil diverifikasi.');
    }
}
