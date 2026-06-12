<form class="panel form-panel" method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
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
    </div>
    <label>Keluhan
        <textarea class="input textarea" name="complaint" required>{{ old('complaint', $booking->complaint ?? '') }}</textarea>
    </label>
    <label>Catatan Admin
        <textarea class="input textarea" name="admin_note">{{ old('admin_note', $booking->admin_note ?? '') }}</textarea>
    </label>
    <button class="btn primary" type="submit">Simpan Booking</button>
</form>
