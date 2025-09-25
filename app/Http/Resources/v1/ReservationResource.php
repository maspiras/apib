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
            'id' => $this->id,
            'checkin' => $this->checkin,
            'checkout' => $this->checkout,
            'adults' => $this->adults,
            'childs' => $this->childs,
            'pets' => $this->pets,
            'fullname' => $this->fullname,            
            'phone' => $this->phone,
            'email' => $this->email,            
            'additionalinformation' => $this->additional_info,
            'bookingsource_id' => $this->bookingsource_id,
            'room' => $this->rooms,
            'ratesperday' => $this->rateperday,            
            'daystay' => $this->daystay, 
            'ratesperstay' => $this->subtotal,
            'payment_type_id' => $this->payment_type_id,            
            'grandtotal' => $this->grandtotal,
            'discount' => $this->discount,            
            'prepayment' => $this->prepayment,            
            'balance' => $this->balancepayment,
            'payment_status_id' => $this->payment_status_id,
        ];
    }
}
