<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemSuggestion;
use Illuminate\Http\Request;

class ItemSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ItemSuggestion::with(['suggester', 'branch', 'reviewer'])->latest();

        if (! $user->canManageItems()) {
            // Logistics: only see their own suggestions
            $query->where('suggested_by', $user->id);
        }

        // Filter by status for office
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $suggestions = $query->paginate(20);

        return view('item-suggestions.index', compact('suggestions'));
    }

    public function create()
    {
        abort_unless(request()->user()->canSuggestItems(), 403);

        return view('item-suggestions.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->canSuggestItems(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        ItemSuggestion::create([
            'name' => $validated['name'],
            'notes' => $validated['notes'],
            'suggested_by' => $user->id,
            'branch_id' => $user->branch_id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route($user->panelRouteName('item-suggestions.index'))
            ->with('success', 'Usulan barang berhasil dikirim.');
    }

    public function show(ItemSuggestion $suggestion)
    {
        return view('item-suggestions.show', compact('suggestion'));
    }

    public function approve(Request $request, ItemSuggestion $suggestion)
    {
        $user = $request->user();
        abort_unless($user->canManageItems(), 403);
        abort_if(! $suggestion->isPending(), 404);

        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:items,code'],
            'office_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Create the actual Item
        Item::create([
            'code' => $request->code,
            'name' => $suggestion->name,
            'description' => $suggestion->notes,
            'is_active' => true,
        ]);

        // Mark suggestion as approved
        $suggestion->update([
            'status' => 'approved',
            'office_notes' => $request->office_notes,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('superadmin.item-suggestions.index')
            ->with('success', "Barang \"{$suggestion->name}\" berhasil ditambahkan ke master barang.");
    }

    public function reject(Request $request, ItemSuggestion $suggestion)
    {
        $user = $request->user();
        abort_unless($user->canManageItems(), 403);
        abort_if(! $suggestion->isPending(), 404);

        $request->validate([
            'office_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $suggestion->update([
            'status' => 'rejected',
            'office_notes' => $request->office_notes,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('superadmin.item-suggestions.index')
            ->with('success', "Usulan barang \"{$suggestion->name}\" telah ditolak.");
    }
}
