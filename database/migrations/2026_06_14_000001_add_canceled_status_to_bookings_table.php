<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY status ENUM('Menunggu', 'Diproses', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY status ENUM('Menunggu', 'Diproses', 'Selesai') DEFAULT 'Menunggu'");
    }
};
