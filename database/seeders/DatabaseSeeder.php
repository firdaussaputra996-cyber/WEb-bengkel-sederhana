<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin MotoCare',
            'email' => 'admin@motocare.test',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Raka Pratama',
            'email' => 'user@motocare.test',
            'role' => 'user',
        ]);

        $user->profile()->create([
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 17',
            'motor_brand' => 'Yamaha',
            'motor_type' => 'NMAX 155',
            'plate_number' => 'B 1234 MTR',
        ]);

        $services = collect([
            ['name' => 'Tune Up Injeksi', 'category' => 'Perawatan', 'duration_minutes' => 75, 'price' => 100000, 'description' => 'Pemeriksaan throttle body, busi, filter udara, dan setting performa mesin.'],
            ['name' => 'Ganti Oli Premium', 'category' => 'Oli', 'duration_minutes' => 30, 'price' => 70000, 'description' => 'Penggantian oli mesin berkualitas dengan inspeksi ringan area mesin.'],
            ['name' => 'Service CVT', 'category' => 'Transmisi', 'duration_minutes' => 90, 'price' => 145000, 'description' => 'Pembersihan CVT, pengecekan roller, v-belt, kampas ganda, dan performa tarikan.'],
            ['name' => 'Cek Rem & Suspensi', 'category' => 'Safety', 'duration_minutes' => 60, 'price' => 90000, 'description' => 'Pemeriksaan kampas rem, minyak rem, shockbreaker, bearing, dan kaki-kaki.'],
            ['name' => 'Diagnosa Kelistrikan', 'category' => 'Elektrikal', 'duration_minutes' => 60, 'price' => 105000, 'description' => 'Pengecekan aki, lampu, starter, soket, dan sistem pengisian.'],
            ['name' => 'Turun Mesin', 'category' => 'Mesin', 'duration_minutes' => 480, 'price' => 1500000, 'description' => 'Overhaul mesin menyeluruh untuk pengecekan kompresi, piston, klep, gasket, dan komponen internal.'],
        ])->map(fn ($service) => Service::create($service));

        Booking::create([
            'booking_code' => 'MC-' . now()->format('ymd') . '-0001',
            'user_id' => $user->id,
            'service_id' => $services->first()->id,
            'service_date' => now()->addDays(2)->toDateString(),
            'service_time' => '09:00',
            'motor_name' => 'Yamaha NMAX 155',
            'plate_number' => 'B 1234 MTR',
            'complaint' => 'Tarikan terasa berat dan mesin agak bergetar saat langsam.',
            'status' => 'Menunggu',
        ]);
    }
}
