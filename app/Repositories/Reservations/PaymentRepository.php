<?php

namespace App\Repositories\Reservations;

use App\Models\Payment;
use App\Repositories\BaseRepository;
use App\Repositories\Reservations\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    
}
