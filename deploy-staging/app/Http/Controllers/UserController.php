<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index', [
            'users' => User::with('branch')->orderBy('name')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.form', [
            'userModel' => new User(),
            'branches' => Branch::orderBy('name')->get(),
            'roles' => User::roleOptions(),
            'mode' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'identity_number' => $data['identity_number'] ?: null,
            'branch_id' => $this->resolveBranchId($data),
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return redirect()->route('superadmin.users.edit', $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.form', [
            'userModel' => $user,
            'branches' => Branch::orderBy('name')->get(),
            'roles' => User::roleOptions(),
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $this->validatedData($request, $user);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'identity_number' => $data['identity_number'] ?: null,
            'branch_id' => $this->resolveBranchId($data),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Akun yang sedang dipakai tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $passwordRules,
            'role' => ['required', Rule::in(array_keys(User::roleOptions()))],
            'identity_number' => ['nullable', 'string', 'max:255', Rule::unique('users', 'identity_number')->ignore($user?->id)],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        if ($data['role'] !== User::ROLE_KANTOR && empty($data['branch_id'])) {
            throw ValidationException::withMessages([
                'branch_id' => 'Cabang wajib dipilih untuk role selain M. Kantor.',
            ]);
        }

        if ($data['role'] === User::ROLE_LAPANGAN && empty($data['identity_number'])) {
            throw ValidationException::withMessages([
                'identity_number' => 'Nomor identitas wajib diisi untuk M. Lapangan.',
            ]);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    private function resolveBranchId(array $data): ?int
    {
        return $data['role'] === User::ROLE_KANTOR
            ? null
            : (int) $data['branch_id'];
    }
}
