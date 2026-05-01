<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Logistics;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedBranchId = $request->integer('branch_id');
        $selectedStatus = $request->string('status')->toString();

        $query = Logistics::query()
            ->with(['branch', 'creator'])
            ->visibleTo($user)
            ->latest('tanggal');

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        return view('logistics.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'branches' => Branch::orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'selectedStatus' => $selectedStatus,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('logistics.form', [
            'item' => new Logistics(['tanggal' => now()->toDateString(), 'status' => 'pending']),
            'branches' => Branch::orderBy('name')->get(),
            'mode' => 'create',
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request, $user);
        $data['branch_id'] = $user->isSuperAdmin() ? $data['branch_id'] : $user->branch_id;
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';

        Logistics::create($data);

        return redirect()->route('logistics.index')->with('success', 'Data logistik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistics $logistics)
    {
        return redirect()->route('logistics.edit', $logistics);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Logistics $logistics)
    {
        $this->authorizeAccess($request->user(), $logistics);

        return view('logistics.form', [
            'item' => $logistics,
            'branches' => Branch::orderBy('name')->get(),
            'mode' => 'edit',
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logistics $logistics)
    {
        $user = $request->user();
        $this->authorizeAccess($user, $logistics);

        if ($logistics->status === 'approved') {
            return back()->with('error', 'Data yang sudah disetujui tidak dapat diedit.');
        }

        $data = $this->validatedData($request, $user);
        $data['branch_id'] = $user->isSuperAdmin() ? $data['branch_id'] : $user->branch_id;
        $data['status'] = 'pending';

        $logistics->update($data);

        return redirect()->route('logistics.index')->with('success', 'Data logistik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Logistics $logistics)
    {
        $this->authorizeAccess($request->user(), $logistics);

        if ($logistics->status === 'approved') {
            return back()->with('error', 'Data yang sudah disetujui tidak dapat dihapus.');
        }

        $logistics->delete();

        return redirect()->route('logistics.index')->with('success', 'Data logistik berhasil dihapus.');
    }

    private function validatedData(Request $request, $user): array
    {
        return $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(['masuk', 'keluar'])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'branch_id' => [$user->isSuperAdmin() ? 'required' : 'nullable', 'integer', 'exists:branches,id'],
        ]);
    }

    private function authorizeAccess($user, Logistics $logistics): void
    {
        if (! $user->isSuperAdmin() && $user->branch_id !== $logistics->branch_id) {
            abort(403);
        }
    }
}
