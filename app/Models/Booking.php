<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['Menunggu', 'Diproses', 'Selesai'];

    protected $fillable = [
        'booking_code',
        'user_id',
        'service_id',
        'service_date',
        'service_time',
        'motor_name',
        'plate_number',
        'complaint',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
