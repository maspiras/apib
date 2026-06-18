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
        Schema::create('reserved_room_status', function (Blueprint $table) {            
            $table->increments('status_id');
            $table->string('status_code', 50)->unique();
            $table->string('status_name', 100);
            $table->string('status_color', 7); // Hex color code
            $table->tinyText('description')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_room_status');
    }
};
