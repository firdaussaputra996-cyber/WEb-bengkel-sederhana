<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spare_parts', function (Blueprint $table): void {
            $table->unsignedInteger('low_stock_threshold')->default(5);
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->foreignId('mechanic_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('booking_spare_part_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->timestamp('installed_at');
            $table->timestamps();
            $table->unique(['booking_id', 'spare_part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_spare_part_usages');
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('mechanic_id');
        });
        Schema::table('spare_parts', function (Blueprint $table): void {
            $table->dropColumn('low_stock_threshold');
        });
    }
};
