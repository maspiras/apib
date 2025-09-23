<?php

namespace App\Repositories\Reservations;

use App\Models\ReservedRoomv2;
use App\Repositories\BaseRepository;
use App\Repositories\Reservations\ReservedRoomRepositoryInterface;

class ReservedRoomRepository extends BaseRepository implements ReservedRoomRepositoryInterface
{
    protected $model;

    public function __construct(ReservedRoomv2 $model)
    {
        $this->model = $model;
    }

    public function roomIsBooked(array $roomId, string $checkIn, string $checkOut)
    {
        return ReservedRoomv2::whereIn('reserved_roomv2.room_id', $roomId)
                        ->leftJoin('rooms', 'rooms.id', '=', 'reserved_roomv2.room_id')
                        ->where(function ($q) use ($checkIn, $checkOut) {
                          /* $q->whereBetween('checkin', [$checkIn, $checkOut])
                            ->orWhereBetween('checkout', [$checkIn, $checkOut]); */
                            $q->where('reserved_roomv2.checkin', '<', $checkOut)
                              ->where('reserved_roomv2.checkout', '>', $checkIn);
                      })
                    //->exists();
                      ->get();
    }
}
