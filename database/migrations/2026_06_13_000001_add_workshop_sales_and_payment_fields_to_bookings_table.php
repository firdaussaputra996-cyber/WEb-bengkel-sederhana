<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_type')->default('Service')->after('service_id');
            $table->json('selected_addons')->nullable()->after('complaint');
            $table->decimal('addon_total', 12, 2)->default(0)->after('selected_addons');
            $table->decimal('estimated_total', 12, 2)->default(0)->after('addon_total');
            $table->string('payment_method')->nullable()->after('estimated_total');
            $table->string('payment_status')->default('Menunggu Pembayaran')->after('payment_method');
            $table->string('payment_reference')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_type',
                'selected_addons',
                'addon_total',
                'estimated_total',
                'payment_method',
                'payment_status',
                'payment_reference',
            ]);
        });
    }
};
