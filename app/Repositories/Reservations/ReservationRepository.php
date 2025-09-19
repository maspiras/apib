<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Repositories\BaseRepository;

class ReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    protected $model;

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $reservation = $this->find($id);
        $reservation->update($data);
        return $reservation;
    }

    public function delete($id)
    {
        $reservation = $this->find($id);
        return $reservation->delete();
    }
}