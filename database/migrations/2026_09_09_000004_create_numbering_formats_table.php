<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numbering_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('pattern', 255); // e.g. {COMPANY}/{DEPT}/{YEAR}/{ROMAN_MONTH}/{COUNTER}
            $table->integer('current_counter')->default(0);
            $table->integer('padding')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numbering_formats');
    }
};
