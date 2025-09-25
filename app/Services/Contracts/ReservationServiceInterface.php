<?php

namespace App\Services\Contracts;

use App\Models\Reservation;

interface ReservationServiceInterface
{
    public function getAll();
    //public function getById(int $id): ?Reservation;
    public function getById(int $id);
    public function create(array $data): Reservation;
    //public function update(Reservation $model, array $data): Reservation;
    //public function update(int $id, array $data): Reservation;
    public function update(int $id, array $data);
    public function delete(Reservation $model): bool;
}