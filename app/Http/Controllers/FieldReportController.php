<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use App\Models\User;
use Illuminate\Http\Request;

class FieldReportController extends Controller
{
    private const SESSION_KEY = 'field_report_user_id';

    public function showIdentityForm(Request $request)
    {
        if ($this->resolveFieldUser($request)) {
            return redirect()->route('field-reports.create');
        }

        return view('field-reports.identity');
    }

    public function verifyIdentity(Request $request)
    {
        $data = $request->validate([
            'identity_number' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->where('role', User::ROLE_LAPANGAN)
            ->where('identity_number', $data['identity_number'])
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['identity_number' => 'Nomor identitas tidak terdaftar atau belum diizinkan.'])
                ->withInput();
        }

        $request->session()->put(self::SESSION_KEY, $user->id);
        $request->session()->regenerate();

        return redirect()->route('field-reports.create');
    }

    public function create(Request $request)
    {
        $fieldUser = $this->requireFieldUser($request);

        return view('field-reports.form', [
            'fieldUser' => $fieldUser,
        ]);
    }

    public function store(Request $request)
    {
        $fieldUser = $this->requireFieldUser($request);

        $data = $request->validate([
            'keterangan' => ['required', 'string'],
            'photo' => ['required', 'image', 'max:4096'],
        ]);

        Logistics::create([
            'nama_barang' => $fieldUser->name,
            'kategori' => 'masuk',
            'jumlah' => 1,
            'tanggal' => now()->toDateString(),
            'keterangan' => $data['keterangan'],
            'photo_path' => $request->file('photo')->store('logistics-photos', 'public'),
            'status' => 'pending',
            'branch_id' => $fieldUser->branch_id,
            'created_by' => $fieldUser->id,
        ]);

        return redirect()->route('field-reports.create')->with('success', 'Informasi lapangan berhasil dikirim.');
    }

    public function destroySession(Request $request)
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('field-reports.identity');
    }

    private function requireFieldUser(Request $request): User
    {
        $user = $this->resolveFieldUser($request);

        if (! $user) {
            abort(403);
        }

        return $user;
    }

    private function resolveFieldUser(Request $request): ?User
    {
        $userId = $request->session()->get(self::SESSION_KEY);

        if (! $userId) {
            return null;
        }

        return User::query()
            ->whereKey($userId)
            ->where('role', User::ROLE_LAPANGAN)
            ->first();
    }
}
