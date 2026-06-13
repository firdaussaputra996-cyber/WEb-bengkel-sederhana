<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('services')->where('name', 'Pembelian Suku Cadang')->exists()) {
            DB::table('services')->insert([
                'name' => 'Pembelian Suku Cadang',
                'category' => 'Sparepart',
                'description' => 'Reservasi pembelian oli atau suku cadang tanpa pekerjaan service utama.',
                'duration_minutes' => 15,
                'price' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('services')
            ->where('name', 'Pembelian Suku Cadang')
            ->where('category', 'Sparepart')
            ->delete();
    }
};
