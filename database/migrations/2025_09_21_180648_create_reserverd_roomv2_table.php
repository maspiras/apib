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
        Schema::create('reserverd_roomv2', function (Blueprint $table) {
            Schema::enableForeignKeyConstraints();
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('room_id');
            $table->dateTime('checkin');
            $table->dateTime('checkout');

            $table->index('reservation_id', 'reservation_id');
            $table->index('room_id', 'room_id');
            $table->index('checkin', 'checkin');
            $table->index('checkout', 'checkout');
            //$table->unique(['room_id', 'checkin', 'checkout'], 'room_checkin_checkout_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserverd_roomv2');
    }
};
