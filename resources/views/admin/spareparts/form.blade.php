<form class="panel form-panel" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <div class="form-grid">
        <label>Nama Suku Cadang
            <input class="input" name="name" value="{{ old('name', $sparePart->name ?? '') }}" required>
        </label>
        <label>Kategori
            <input class="input" name="category" value="{{ old('category', $sparePart->category ?? 'Suku Cadang') }}" required>
        </label>
        <label>Harga
            <input class="input" type="number" name="price" value="{{ old('price', $sparePart->price ?? 0) }}" required>
        </label>
        <label>Stok
            <input class="input" type="number" name="stock" value="{{ old('stock', $sparePart->stock ?? 0) }}" required>
        </label>
        <label>Batas Stok Minimum
            <input class="input" type="number" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', $sparePart->low_stock_threshold ?? 5) }}" required>
        </label>
    </div>
    <label>Deskripsi
        <textarea class="input textarea" name="description">{{ old('description', $sparePart->description ?? '') }}</textarea>
    </label>
    <label class="check-line">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $sparePart->is_active ?? true))> Tampilkan di katalog
    </label>
    <button class="btn primary" type="submit">Simpan Suku Cadang</button>
</form>
