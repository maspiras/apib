<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Reservation;
class ReservedRoomv2 extends Model
{
    protected $table = 'reserved_roomv2';
    public $timestamps = false;
    protected $fillable = [
        'reservation_id', 'room_id', 'checkin', 'checkout'
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

}
