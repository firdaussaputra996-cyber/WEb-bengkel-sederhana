@extends('layouts.app')

@section('title', 'Booking - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Booking Form</p>
        <h1>{{ strtoupper($selectedBookingType) }}</h1>
    </div>
</section>

<section class="section container narrow">
    <form class="panel form-panel" method="POST" action="{{ route('user.booking.store') }}">
        @csrf
        @php($oldAddons = old('selected_addons', $preselectedAddons ?? []))
        <div class="booking-type-summary">
            <div>
                <span>Jenis Booking</span>
                <strong>{{ $selectedBookingType }}</strong>
            </div>
            <a class="text-link" href="{{ route('user.booking.create') }}">Ganti Pilihan</a>
        </div>
        <input type="hidden" name="booking_type" value="{{ $selectedBookingType }}">
        <div class="form-grid">
            @if($selectedBookingType === 'Beli Suku Cadang')
                <input type="hidden" name="service_id" value="{{ old('service_id', $selectedServiceId) }}" data-service-select data-price="0">
                <div class="readonly-field">
                    <span>Layanan</span>
                    <strong>Pembelian Suku Cadang</strong>
                </div>
            @else
                <label>Jenis Layanan
                    <select class="input" name="service_id" data-service-select required>
                        <option value="">Pilih layanan</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ (int) $service->price }}" @selected(old('service_id', $selectedServiceId) == $service->id)>{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </label>
            @endif
            <label>Tanggal Kunjungan
                <input class="input" type="date" name="service_date" value="{{ old('service_date') }}" required>
            </label>
            <label>Jam
                <input class="input" type="time" name="service_time" value="{{ old('service_time') }}">
            </label>
            @if($selectedBookingType === 'Beli Suku Cadang')
                <input type="hidden" name="motor_name" value="">
            @else
                <label>Nama Motor
                    <input class="input" type="text" name="motor_name" value="{{ old('motor_name', trim(($profile->motor_brand ?? '') . ' ' . ($profile->motor_type ?? ''))) }}" required>
                </label>
            @endif
            <input type="hidden" name="plate_number" value="">
        </div>
        <label>{{ $selectedBookingType === 'Beli Suku Cadang' ? 'Catatan Kebutuhan' : 'Keluhan Motor' }}
            <textarea class="input textarea" name="complaint" required>{{ old('complaint') }}</textarea>
        </label>

        <div class="form-section">
            <div class="panel-head">
                <h2>OLI & SUKU CADANG</h2>
            </div>
            <div class="addon-grid">
                @foreach($addonGroups as $category => $items)
                    <div class="addon-group">
                        <h3>{{ $category }}</h3>
                        @foreach($items as $item)
                            <label class="addon-option">
                                <input type="checkbox" name="selected_addons[]" value="{{ $item['key'] }}" data-addon-price="{{ $item['price'] }}" @checked(in_array($item['key'], $oldAddons, true))>
                                <span>
                                    <strong>{{ $item['name'] }}</strong>
                                    <small>Rp {{ number_format($item['price'], 0, ',', '.') }} · Stok {{ $item['stock'] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-grid">
            <label>Metode Pembayaran Online
                <select class="input" name="payment_method" required>
                    <option value="">Pilih pembayaran</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </label>
            <label>Referensi Pembayaran
                <input class="input" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="No. transaksi bila sudah bayar">
            </label>
        </div>

        <div class="total-summary">
            <span>Estimasi Total</span>
            <strong data-estimated-total>Rp 0</strong>
        </div>
        <button class="btn primary" type="submit">Kirim Booking</button>
    </form>
</section>
@endsection

@push('scripts')
<script>
    const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    const serviceSelect = document.querySelector('[data-service-select]');
    const addonInputs = document.querySelectorAll('[data-addon-price]');
    const totalTarget = document.querySelector('[data-estimated-total]');

    function updateEstimatedTotal() {
        const servicePrice = Number(serviceSelect?.selectedOptions?.[0]?.dataset.price || serviceSelect?.dataset.price || 0);
        const addonTotal = Array.from(addonInputs)
            .filter((input) => input.checked)
            .reduce((total, input) => total + Number(input.dataset.addonPrice || 0), 0);

        if (totalTarget) {
            totalTarget.textContent = rupiah.format(servicePrice + addonTotal);
        }
    }

    serviceSelect?.addEventListener('change', updateEstimatedTotal);
    addonInputs.forEach((input) => input.addEventListener('change', updateEstimatedTotal));
    updateEstimatedTotal();
</script>
@endpush
