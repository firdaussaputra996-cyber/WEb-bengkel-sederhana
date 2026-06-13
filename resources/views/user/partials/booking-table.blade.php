@php($showPaymentActions = $showPaymentActions ?? false)
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Layanan</th>
                <th>Tanggal</th>
                <th>Motor</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Status</th>
                @if($showPaymentActions)
                    <th>Aksi Bayar</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_code }}</td>
                    <td>{{ $booking->service->name }}</td>
                    <td>{{ $booking->service_date->format('d M Y') }} {{ $booking->service_time }}</td>
                    <td>{{ $booking->motor_name }}</td>
                    <td>Rp {{ number_format($booking->estimated_total ?: $booking->service->price, 0, ',', '.') }}</td>
                    <td>{{ $booking->payment_status ?? 'Menunggu Pembayaran' }}</td>
                    <td><span class="badge {{ strtolower($booking->status) }}">{{ $booking->status }}</span></td>
                    @if($showPaymentActions)
                        <td>
                            @if($booking->payment_proof)
                                <a class="text-link" href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" rel="noopener">Lihat Bukti</a>
                            @endif
                            @if($booking->payment_status !== 'Lunas')
                                <form class="proof-upload" method="POST" action="{{ route('user.booking.payment-proof', $booking) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input class="input compact" type="file" name="payment_proof" accept="image/*" required>
                                    <button class="btn small outline" type="submit">{{ $booking->payment_proof ? 'Upload Ulang' : 'Upload Bukti' }}</button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $showPaymentActions ? 8 : 7 }}" class="empty">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
