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

    public function roomIsBooked(array $roomId, string $checkIn, string $checkOut, int $reservationId = null)
    {
        return ReservedRoomv2::whereIn('reserved_roomv2.room_id', $roomId)
                        ->leftJoin('rooms', 'rooms.id', '=', 'reserved_roomv2.room_id')
                        ->where(function ($q) use ($checkIn, $checkOut, $reservationId) {
                          /* $q->whereBetween('checkin', [$checkIn, $checkOut])
                            ->orWhereBetween('checkout', [$checkIn, $checkOut]); */
                            $q->where('reserved_roomv2.checkin', '<', $checkOut)
                              ->where('reserved_roomv2.checkout', '>', $checkIn);
                            /* if(!is_null($reservationId)){
                                $q->where('reserved_roomv2.reservation_id', '!=', $reservationId);
                            } */
                      })
                    //->exists();
                      ->get();
    }

    public function massiveDelete(int $reservationId)
    {      
        return $this->model->where('reservation_id', $reservationId)
        ->chunk(500, function ($reservedRooms) {
              foreach ($reservedRooms as $room) {
                  $room->delete();
              }
          });        
    }
    
}
