<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->integer('rotation_angle')->default(0)->after('orientation');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn('rotation_angle');
        });
    }
};
