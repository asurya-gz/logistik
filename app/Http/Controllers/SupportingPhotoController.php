<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use App\Models\LogisticsSupportingPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportingPhotoController extends Controller
{
    public function store(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        // Lapangan dari field-report form
        if (session('field_user_id')) {
            $validated = $request->validate([
                'supporting_photos' => ['nullable', 'array', 'max:5'],
                'supporting_photos.*' => ['image', 'max:5120'],
                'supporting_catatan' => ['nullable', 'string', 'max:1000'],
            ]);
        } else {
            // Logistik atau Kantor
            abort_unless($user->canWriteLogistikNote() || $user->canVerify(), 403);
            $validated = $request->validate([
                'supporting_photos' => ['nullable', 'array', 'max:5'],
                'supporting_photos.*' => ['image', 'max:5120'],
                'supporting_catatan' => ['nullable', 'string', 'max:1000'],
            ]);
        }

        $uploaderId = $user->id ?? null;

        if (! empty($validated['supporting_photos'])) {
            foreach ($validated['supporting_photos'] as $file) {
                $path = $file->store('supporting-photos', 'public');
                LogisticsSupportingPhoto::create([
                    'logistics_id' => $logistics->id,
                    'uploaded_by' => $uploaderId,
                    'catatan' => $validated['supporting_catatan'] ?? null,
                    'photo_path' => $path,
                ]);
            }
        } elseif (! empty($validated['supporting_catatan'])) {
            // Catatan tanpa foto
            LogisticsSupportingPhoto::create([
                'logistics_id' => $logistics->id,
                'uploaded_by' => $uploaderId,
                'catatan' => $validated['supporting_catatan'],
                'photo_path' => '',
            ]);
        }

        return back()->with('success', 'Foto pendukung berhasil ditambahkan.');
    }

    public function destroy(Request $request, LogisticsSupportingPhoto $photo)
    {
        $user = $request->user();
        abort_unless($user->canWriteLogistikNote() || $user->canVerify(), 403);

        if ($photo->photo_path) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();

        return back()->with('success', 'Foto pendukung dihapus.');
    }
}
