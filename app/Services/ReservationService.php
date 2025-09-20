<?php

namespace App\Services;

use App\Models\Reservation;
use App\Services\Contracts\ReservationServiceInterface;

class ReservationService implements ReservationServiceInterface
{
    public function getAll()
    {
        return Reservation::all();
    }

    public function getById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function update(Reservation $model, array $data): Reservation
    {
        $model->update($data);
        return $model;
    }

    public function delete(Reservation $model): bool
    {
        return $model->delete();
    }

    public function getReservationGrandTotal($rate, $meals=0, $services=0){
        if(!empty($services)){            
            $services = str_replace(',','', $services);
        }
        if(!empty($meals)){
            $meals = str_replace(',','', $meals);
        } 
        
        return $rate + $meals + $services;
    }
}