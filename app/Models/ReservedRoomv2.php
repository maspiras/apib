<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedRoomv2 extends Model
{
    protected $table = 'reserved_roomv2';
    public $timestamps = false;
    protected $fillable = [
        'reservation_id', 'room_id', 'checkin', 'checkout'
    ];
}
