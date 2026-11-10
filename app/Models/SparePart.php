<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'stock',
        'low_stock_threshold',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getOptionKeyAttribute(): string
    {
        return Str::slug($this->category . ' ' . $this->name);
    }

    public static function groupedOptions(bool $activeOnly = true): array
    {
        return self::query()
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (SparePart $part) => [
                'key' => $part->option_key,
                'category' => $part->category,
                'name' => $part->name,
                'description' => $part->description,
                'price' => (float) $part->price,
                'stock' => $part->stock,
                'is_active' => $part->is_active,
            ])
            ->groupBy('category')
            ->toArray();
    }

    public static function selectedAddons(array $keys, array $quantities = []): array
    {
        $options = collect(self::groupedOptions())
            ->flatten(1)
            ->keyBy('key');

        return collect($keys)
            ->unique()
            ->map(function (string $key) use ($options, $quantities) {
                $option = $options->get($key);

                if (! $option) {
                    return null;
                }

                $quantity = max(1, (int) ($quantities[$key] ?? 1));
                $quantity = $option['stock'] > 0 ? min($quantity, $option['stock']) : $quantity;

                return array_merge($option, [
                    'quantity' => $quantity,
                    'line_total' => $option['price'] * $quantity,
                ]);
            })
            ->filter()
            ->values()
            ->all();
    }
}
