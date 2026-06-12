@extends('layouts.app')

@section('title', 'Manajemen Service - MotoCare M')

@section('content')
<div class="admin-heading row">
    <div>
        <p class="eyebrow">Service Catalog</p>
        <h1>DATA SERVICE</h1>
    </div>
    <a class="btn primary" href="{{ route('admin.services.create') }}">Tambah Service</a>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Nama</th><th>Kategori</th><th>Durasi</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->category }}</td>
                        <td>{{ $service->duration_minutes }} menit</td>
                        <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $service->is_active ? 'selesai' : 'menunggu' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="actions">
                            <a class="btn small outline" href="{{ route('admin.services.edit', $service) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn small danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $services->links() }}
</section>
@endsection
