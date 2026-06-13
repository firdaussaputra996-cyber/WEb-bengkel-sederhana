<form class="panel form-panel" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    @php
        $storedAddons = collect($booking->selected_addons ?? [])
            ->map(fn ($item) => \Illuminate\Support\Str::slug(($item['category'] ?? '') . ' ' . ($item['name'] ?? '')))
            ->filter()
            ->all();
        $selectedAddons = old('selected_addons', $storedAddons);
        $storedQuantities = collect($booking->selected_addons ?? [])
            ->mapWithKeys(fn ($item) => [\Illuminate\Support\Str::slug(($item['category'] ?? '') . ' ' . ($item['name'] ?? '')) => $item['quantity'] ?? 1])
            ->all();
        $selectedQuantities = old('addon_quantities', $storedQuantities);
    @endphp
    <div class="form-grid">
        <label>Pelanggan
            <select class="input" name="user_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $booking->user_id ?? '') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Layanan
            <select class="input" name="service_id" required>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id', $booking->service_id ?? '') == $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Jenis Booking
            <select class="input" name="booking_type" required>
                @foreach($bookingTypes as $type)
                    <option value="{{ $type }}" @selected(old('booking_type', $booking->booking_type ?? 'Service') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </label>
        <label>Tanggal
            <input class="input" type="date" name="service_date" value="{{ old('service_date', optional($booking?->service_date)->format('Y-m-d')) }}" required>
        </label>
        <label>Jam
            <input class="input" type="time" name="service_time" value="{{ old('service_time', $booking->service_time ?? '') }}">
        </label>
        <label>Motor
            <input class="input" name="motor_name" value="{{ old('motor_name', $booking->motor_name ?? '') }}" required>
        </label>
        <label>Plat Nomor
            <input class="input" name="plate_number" value="{{ old('plate_number', $booking->plate_number ?? '') }}">
        </label>
        <label>Status
            <select class="input" name="status" required>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $booking->status ?? 'Menunggu') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label>Status Pembayaran
            <select class="input" name="payment_status" required>
                @foreach($paymentStatuses as $paymentStatus)
                    <option value="{{ $paymentStatus }}" @selected(old('payment_status', $booking->payment_status ?? 'Menunggu Pembayaran') === $paymentStatus)>{{ $paymentStatus }}</option>
                @endforeach
            </select>
        </label>
        <label>Metode Pembayaran
            <select class="input" name="payment_method">
                <option value="">Belum dipilih</option>
                @foreach($paymentMethods as $methodName)
                    <option value="{{ $methodName }}" @selected(old('payment_method', $booking->payment_method ?? '') === $methodName)>{{ $methodName }}</option>
                @endforeach
            </select>
        </label>
        <label>Referensi Pembayaran
            <input class="input" name="payment_reference" value="{{ old('payment_reference', $booking->payment_reference ?? '') }}">
        </label>
    </div>
    @if(! empty($booking?->payment_proof))
        <div class="proof-panel">
            <span>Bukti Pembayaran</span>
            <a class="text-link" href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" rel="noopener">Lihat Bukti</a>
        </div>
    @endif
    <label>Keluhan
        <textarea class="input textarea" name="complaint" required>{{ old('complaint', $booking->complaint ?? '') }}</textarea>
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
                            <input type="checkbox" name="selected_addons[]" value="{{ $item['key'] }}" @checked(in_array($item['key'], $selectedAddons, true))>
                            <span>
                                <strong>{{ $item['name'] }}</strong>
                                <small>Rp {{ number_format($item['price'], 0, ',', '.') }} · Stok {{ $item['stock'] }}</small>
                            </span>
                            <input class="input compact quantity-input" type="number" name="addon_quantities[{{ $item['key'] }}]" value="{{ $selectedQuantities[$item['key']] ?? 1 }}" min="1" max="{{ max(1, $item['stock']) }}" aria-label="Jumlah {{ $item['name'] }}">
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <label>Catatan Admin
        <textarea class="input textarea" name="admin_note">{{ old('admin_note', $booking->admin_note ?? '') }}</textarea>
    </label>
    <button class="btn primary" type="submit">Simpan Booking</button>
</form>
