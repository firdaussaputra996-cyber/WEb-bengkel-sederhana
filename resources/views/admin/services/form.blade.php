<form class="panel form-panel" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <div class="form-grid">
        <label>Nama Service
            <input class="input" name="name" value="{{ old('name', $service->name ?? '') }}" required>
        </label>
        <label>Kategori
            <input class="input" name="category" value="{{ old('category', $service->category ?? '') }}" required>
        </label>
        <label>Durasi Menit
            <input class="input" type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? 60) }}" required>
        </label>
        <label>Harga
            <input class="input" type="number" name="price" value="{{ old('price', $service->price ?? 0) }}" required>
        </label>
    </div>
    <label>Deskripsi
        <textarea class="input textarea" name="description">{{ old('description', $service->description ?? '') }}</textarea>
    </label>
    <label class="check-line">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true))> Aktif
    </label>
    <button class="btn primary" type="submit">Simpan Service</button>
</form>
