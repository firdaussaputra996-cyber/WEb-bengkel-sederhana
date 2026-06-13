<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['Menunggu', 'Diproses', 'Selesai'];
    public const BOOKING_TYPES = ['Service', 'Beli Suku Cadang', 'Service + Suku Cadang'];
    public const PAYMENT_METHODS = ['QRIS', 'Transfer Bank', 'E-Wallet'];
    public const PAYMENT_STATUSES = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Lunas'];
    public const ADDONS = [
        'Oli Mesin' => [
            ['name' => 'Federal Matic 10W-30', 'price' => 65000],
            ['name' => 'Yamalube Super Matic', 'price' => 70000],
            ['name' => 'AHM MPX 2', 'price' => 62000],
            ['name' => 'Motul Scooter LE 10W-30', 'price' => 95000],
        ],
        'Suku Cadang' => [
            ['name' => 'Busi NGK', 'price' => 28000],
            ['name' => 'Kampas Rem Depan', 'price' => 85000],
            ['name' => 'Filter Udara', 'price' => 55000],
            ['name' => 'V-Belt Matic', 'price' => 185000],
        ],
    ];

    protected $fillable = [
        'booking_code',
        'user_id',
        'service_id',
        'booking_type',
        'service_date',
        'service_time',
        'motor_name',
        'plate_number',
        'complaint',
        'selected_addons',
        'addon_total',
        'estimated_total',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_proof',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'selected_addons' => 'array',
            'addon_total' => 'decimal:2',
            'estimated_total' => 'decimal:2',
        ];
    }

    public static function addonOptions(): array
    {
        return collect(self::ADDONS)
            ->flatMap(fn (array $items, string $category) => collect($items)->map(fn (array $item) => [
                'key' => self::addonKey($category, $item['name']),
                'category' => $category,
                'name' => $item['name'],
                'price' => $item['price'],
            ]))
            ->values()
            ->all();
    }

    public static function selectedAddons(array $keys): array
    {
        $options = collect(self::addonOptions())->keyBy('key');

        return collect($keys)
            ->unique()
            ->map(fn (string $key) => $options->get($key))
            ->filter()
            ->values()
            ->all();
    }

    private static function addonKey(string $category, string $name): string
    {
        return str($category . ' ' . $name)->slug()->toString();
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
