<x-layouts.app :title="$mode === 'create' ? 'Set Harga' : 'Edit Harga'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Set Harga Baru' : 'Edit Harga' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route('superadmin.prices.store') : route('superadmin.prices.update', $price) }}" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <div class="grid grid-2">
                <div>
                    <label for="item_id">Barang</label>
                    <select id="item_id" name="item_id" required>
                        <option value="">Pilih barang</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected((int) old('item_id', $price->item_id) === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('item_id') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="branch_id">Cabang</label>
                    <select id="branch_id" name="branch_id">
                        <option value="">Global</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((int) old('branch_id', $price->branch_id) === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="price">Harga</label>
                    <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $price->price) }}" required>
                    @error('price') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="effective_date">Tanggal Berlaku</label>
                    <input id="effective_date" name="effective_date" type="date" value="{{ old('effective_date', optional($price->effective_date)->format('Y-m-d') ?? $price->effective_date) }}" required>
                    @error('effective_date') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label for="notes">Catatan</label>
                <textarea id="notes" name="notes">{{ old('notes', $price->notes) }}</textarea>
                @error('notes') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route('superadmin.prices.index') }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
