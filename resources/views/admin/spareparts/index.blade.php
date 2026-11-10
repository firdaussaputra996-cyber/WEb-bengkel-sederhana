@extends('layouts.app')

@section('title', 'Manajemen Suku Cadang - MotoCare M')

@section('content')
<div class="admin-heading row">
    <div>
        <p class="eyebrow">Parts Inventory</p>
        <h1>DATA SUKU CADANG</h1>
    </div>
    <a class="btn primary" href="{{ route('admin.spareparts.create') }}">Tambah Suku Cadang</a>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Batas Minimum</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($spareParts as $part)
                    <tr>
                        <td>{{ $part->name }}</td>
                        <td>{{ $part->category }}</td>
                        <td>Rp {{ number_format($part->price, 0, ',', '.') }}</td>
                        <td>{{ $part->stock }}</td>
                        <td>{{ $part->low_stock_threshold }}</td>
                        <td><span class="badge {{ $part->is_active ? 'selesai' : 'menunggu' }}">{{ $part->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="actions">
                            <a class="btn small outline" href="{{ route('admin.spareparts.edit', $part) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.spareparts.destroy', $part) }}" onsubmit="return confirm('Hapus suku cadang ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn small danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada suku cadang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $spareParts->links() }}
</section>
@endsection
