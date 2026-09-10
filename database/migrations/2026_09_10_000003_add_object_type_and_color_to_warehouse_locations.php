<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_locations', 'location_type')) {
                $table->string('location_type', 30)->default('rack')->after('room_sector'); // 'room' atau 'rack'
            }
            if (!Schema::hasColumn('warehouse_locations', 'custom_color')) {
                $table->string('custom_color', 30)->nullable()->after('status_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_locations', 'location_type')) {
                $table->dropColumn('location_type');
            }
            if (Schema::hasColumn('warehouse_locations', 'custom_color')) {
                $table->dropColumn('custom_color');
            }
        });
    }
};
