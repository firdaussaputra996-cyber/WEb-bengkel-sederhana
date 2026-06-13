<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('Suku Cadang');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('spare_parts')->insert([
            ['category' => 'Oli Mesin', 'name' => 'Federal Matic 10W-30', 'description' => 'Oli mesin matic untuk pemakaian harian.', 'price' => 65000, 'stock' => 12, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Oli Mesin', 'name' => 'Yamalube Super Matic', 'description' => 'Oli mesin matic Yamaha untuk performa stabil.', 'price' => 70000, 'stock' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Oli Mesin', 'name' => 'AHM MPX 2', 'description' => 'Oli motor matic Honda untuk perawatan berkala.', 'price' => 62000, 'stock' => 14, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Oli Mesin', 'name' => 'Motul Scooter LE 10W-30', 'description' => 'Oli scooter premium untuk perlindungan mesin.', 'price' => 95000, 'stock' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Suku Cadang', 'name' => 'Busi NGK', 'description' => 'Busi pengapian untuk penggantian rutin.', 'price' => 28000, 'stock' => 20, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Suku Cadang', 'name' => 'Kampas Rem Depan', 'description' => 'Kampas rem depan untuk pengereman harian.', 'price' => 85000, 'stock' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Suku Cadang', 'name' => 'Filter Udara', 'description' => 'Filter udara pengganti untuk menjaga asupan mesin.', 'price' => 55000, 'stock' => 11, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['category' => 'Suku Cadang', 'name' => 'V-Belt Matic', 'description' => 'V-belt motor matic untuk area transmisi CVT.', 'price' => 185000, 'stock' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
