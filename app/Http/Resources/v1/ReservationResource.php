<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
               
            'checkin' => $this->checkin,
            'checkout' => $this->checkout,            
            'adults' => $this->adults,
            'childs' => $this->childs,
            'pets' => $this->pets,
            'fullname' => $this->fullname,
            'phone' => $this->phone, 
        ];
    }
}
