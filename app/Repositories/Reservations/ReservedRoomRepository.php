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
        return ReservedRoomv2::whereIn('room_id', $roomId)
                        ->where(function ($q) use ($checkIn, $checkOut) {
                          /* $q->whereBetween('checkin', [$checkIn, $checkOut])
                            ->orWhereBetween('checkout', [$checkIn, $checkOut]); */
                            $q->where('checkin', '<', $checkOut)
                              ->where('checkout', '>', $checkIn);
                      })->exists();
    }
}
