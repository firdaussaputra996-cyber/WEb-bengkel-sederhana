<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Layanan</th>
                <th>Tanggal</th>
                <th>Motor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_code }}</td>
                    <td>{{ $booking->service->name }}</td>
                    <td>{{ $booking->service_date->format('d M Y') }} {{ $booking->service_time }}</td>
                    <td>{{ $booking->motor_name }}</td>
                    <td><span class="badge {{ strtolower($booking->status) }}">{{ $booking->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
