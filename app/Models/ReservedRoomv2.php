<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedRoomv2 extends Model
{
    protected $table = 'reserverd_roomv2';
    public $timestamps = false;
    protected $fillable = [
        'reservation_id', 'room_id', 'checkin', 'checkout'
    ];
}
