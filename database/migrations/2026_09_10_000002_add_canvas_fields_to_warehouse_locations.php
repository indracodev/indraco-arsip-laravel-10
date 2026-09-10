<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->string('room_sector', 50)->nullable()->after('warehouse_id');
            $table->integer('canvas_x')->default(0)->after('room_sector');
            $table->integer('canvas_y')->default(0)->after('canvas_x');
            $table->integer('canvas_width')->default(60)->after('canvas_y');
            $table->integer('canvas_height')->default(120)->after('canvas_width');
            $table->string('orientation', 20)->default('vertical')->after('canvas_height');
            $table->foreignId('assigned_department_id')->nullable()->after('box_capacity')->constrained('departments')->nullOnDelete();
            $table->boolean('is_booked')->default(false)->after('assigned_department_id');
            $table->foreignId('booked_by_user_id')->nullable()->after('is_booked')->constrained('users')->nullOnDelete();
            $table->text('booking_notes')->nullable()->after('booked_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropForeign(['assigned_department_id']);
            $table->dropForeign(['booked_by_user_id']);
            $table->dropColumn([
                'room_sector',
                'canvas_x',
                'canvas_y',
                'canvas_width',
                'canvas_height',
                'orientation',
                'assigned_department_id',
                'is_booked',
                'booked_by_user_id',
                'booking_notes',
            ]);
        });
    }
};
