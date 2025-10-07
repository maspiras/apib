<?php

namespace App\Services;

use App\Models\Reservation;
use App\Services\Contracts\ReservationServiceInterface;

use App\Repositories\Reservations\ReservationRepositoryInterface;
use App\Repositories\Reservations\ReservedRoomRepositoryInterface;
use App\Repositories\Reservations\PaymentRepositoryInterface;

use Carbon\CarbonPeriod;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Exception;

use App\Http\Resources\v1\ReservationResource;
//use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReservationService implements ReservationServiceInterface
{
    protected $reservationRepository, $reservedRoomRepository, $paymentRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository, ReservedRoomRepositoryInterface $reservedRoomRepository, PaymentRepositoryInterface $paymentRepository){
        $this->reservationRepository = $reservationRepository;
        $this->reservedRoomRepository = $reservedRoomRepository;
        $this->paymentRepository = $paymentRepository;        
    
    }

    public function getAll()
    {
        //return Reservation::all();
        //return $this->reservationRepository->paginate(100); 
        //return new ReservationResource($this->reservationRepository->paginate(100));       
        //return new ReservationResource(Reservation::all());       
        return ReservationResource::collection($this->reservationRepository->paginate(100));
    }

    //public function getById(int $id): ?Reservation
    public function getById($id)
    //public function getById(int $id): Reservation
    {
        /* try {
            $reservation = $this->reservationRepository->find($id);
            return new ReservationResource($reservation);
        } catch (ModelNotFoundException $e){
            throw new Exception('Reservation not found!');
        } */
        $reservation = $this->reservationRepository->find($id);
        
        if(!$reservation){
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Reservation not found!');
            //throw new Exception('Reservation not found!');
        }
        return new ReservationResource($reservation);
        
    }

    public function create(array $data): Reservation
    {
        $datacleaned = $data;
        
        $ref_number = substr(md5(time().'-'.auth()->user()->id), 0, 10);          
        $user = auth()->user();            
        
        $checkin = Carbon::parse($datacleaned['checkin']. '2pm');
        $checkout = Carbon::parse($datacleaned['checkout']. '12pm');

        if ($checkout->lessThanOrEqualTo($checkin)) {                
                throw new Exception("The check-out date must be after the check-in date.");
        }

        /* if($datacleaned['checkin'] == $datacleaned['checkout']){
          $checkout = $checkout->addDays(1);  
        } */        
        $diff = round($checkin->diffInDays($checkout));
        /* if($diff <= 1){
            $checkout = $checkout->addDays(1);  
        } */
        $rateperstay = $datacleaned['rateperday'] * $diff;        

        $grandtotal = $this->getReservationGrandTotal($rateperstay, $meals=0, $services=0);
        

        $payment_status = 1;
        
        
        $amount = 0;

        $discount = null;
        if($datacleaned['discountoption'] == 1){
            $discount = $datacleaned['discount'];
        }elseif($datacleaned['discountoption'] == 2){
            $discount = ($grandtotal * $datacleaned['discount']) / 100;
        }else{
            $discount = 0;
        }

        $net_total = $grandtotal - $discount;
        $balance = $net_total;

        if(!empty($datacleaned['prepayment'])){
            
            if($datacleaned['prepayment'] >= $net_total){
                 $payment_status = 3;
                 $balance = 0;
                 $amount = $net_total;
                 
                 
            }else{
                 
                 $balance = ($net_total - $datacleaned['prepayment']);
                 $payment_status = 2;
                 $amount = $datacleaned['prepayment'];
            }
         }

        $currency_id = $user->host->host_settings->currency_id;
        if(empty($user->host->host_settings->currency_id)){
        $currency_id = 251;
        }
        
        //return $datacleaned;
        //return new ReservationResource($request);
        //return ReservationResource::collection(($datacleaned));
        
       //return $datacleaned->additionalinformation;

        $data_reservation = array('ref_number' => $ref_number,
                        'checkin' => $checkin, //$datacleaned['checkin'],
                        'checkout' => $checkout, //$datacleaned['checkout'],
                        'adults' => $datacleaned['adults'],
                        'childs' => $datacleaned['childs'],
                        'pets' => $datacleaned['pets'],
                        'fullname' => $datacleaned['fullname'],
                        'phone' => $datacleaned['phone'],
                        'email' => $datacleaned['email'],
                        'additional_info' => $datacleaned['additionalinformation'],
                        //'rooms' => $datacleaned['rooms'],
                        'booking_source_id' => $datacleaned['bookingsource_id'],
                        'doorcode' => 0,
                        'rateperday' => $datacleaned['rateperday'],
                        'daystay' => $diff,
                        'meals_total' => 0, //$datacleaned['mealsamount'],
                        'additional_services_total' => 0, //$datacleaned['servicestotalamount'],
                        'subtotal' => $rateperstay,
                        'discount' => $discount,
                        'tax' => $datacleaned['tax'],
                        'grandtotal' => $grandtotal, 
                        'currency_id' => $currency_id,
                        'payment_type_id' => $datacleaned['typeofpayment'],
                        //'prepayment' => $datacleaned->prepayment,
                        'prepayment' => $datacleaned['prepayment'],
                        'payment_status_id' => $payment_status,
                        'balancepayment' => $balance, 
                        'user_id' => $user->id,
                        'host_id' => $user->host->id,
                        'booking_status_id' => empty($datacleaned['prepayment']) ? 0 : 1,     
                        /* 'created_at' => now(),
                        'updated_at' => now() */
                    );
        
        
        //return new ReservationResource($data_reservation);       
        
        $reservation = $this->reservationRepository->create($data_reservation); // create reservation
        
        /* if ($this->reservedRoomRepository->roomIsBooked($datacleaned['rooms'], $checkin, $checkout)) {
            throw new Exception("Room/s is/are not available for the selected dates.");
        } */
       $roomsBook = $this->reservedRoomRepository->roomIsBooked($datacleaned['rooms'], $checkin, $checkout);
       if(count($roomsBook) == 1){            
            throw new Exception("This ".$roomsBook[0]->room_name." is not available for the selected dates.");
        }elseif(count($roomsBook) > 1){
            $rooms= [];
            foreach($roomsBook as $room){
                $rooms[] = $room->room_name;
            }
            $roomlist = implode(", ", $rooms);
            throw new Exception("These rooms ($roomlist) are not available for the selected dates.");
        }
        
        $datareservedroom = [];
        foreach( $datacleaned['rooms'] as $bookedrooms){                    
            $datareservedroom[] = [
                                    'reservation_id' => $reservation->id, 
                                    'room_id' => $bookedrooms, 
                                    'checkin' => $checkin, 
                                    'checkout' => $checkout,
                                    'status_id' => 1, // reserved
                                ];
        }
        
        $reservation['rooms'] = $datacleaned['rooms']['room_id'] ?? $datacleaned['rooms'];
        $this->reservedRoomRepository->massiveInsert($datareservedroom); // assign rooms to reserved_rooms table

        $dataPayment = array(
                'ref_number' => $ref_number,
                'host_id' => $user->host->id,
                'user_id' => $user->id,
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'balance' => $balance,
                'currency_id' => $user->host->host_settings->currency_id,
                'payment_type_id' => $datacleaned['typeofpayment'],
                'action_type_id' => $amount >= $grandtotal? 3:1,
                'added_on' => now(),
            );  

        //(!$this->isEmpty($datacleaned['prepayment']) && $amount > 0){
        if(!empty($datacleaned['prepayment']) && $amount > 0){
            $this->paymentRepository->insert($dataPayment); // insert payment if there is prepayment
        }

        return $reservation;
       
    }

    //public function update(Reservation $model, array $data): Reservation
    //public function update(int $id, array $data): Reservation
    public function update(int $id, array $data)
    {
        $reservation = $this->reservationRepository->find($id);
        //return $reservation;
        //return $data->id;
        

        if (!$reservation || $reservation->booking_status_id == 2) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Reservation cannot found!');
        }

        /* if($reservation->booking_status_id == 2){
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Reservation cannot found!');
        } */

        //return $data->checkin;

        $user = auth()->user();            
        $datacleaned = $data;
        
        $checkin = Carbon::parse($datacleaned['checkin']. '2pm'); //->format('Y-m-d H:i');
        $checkout = Carbon::parse($datacleaned['checkout']. '12pm'); //->format('Y-m-d H:i');
        
        /* if ($checkout->lessThanOrEqualTo($checkin)) {                
            throw new Exception("The check-out date must be after the check-in date.");
        } */
        $diff = round($checkin->diffInDays($checkout));
        $rateperstay = $datacleaned['rateperday'] * $diff;        

        $grandtotal = $this->getReservationGrandTotal($rateperstay, $meals=0, $services=0);
        
        $discount = null;
        if($datacleaned['discountoption'] == 1){
            $discount = $datacleaned['discount'];
        }elseif($datacleaned['discountoption'] == 2){
            $discount = ($grandtotal * $datacleaned['discount']) / 100;
        }else{
            $discount = 0;
        }

        $payment_status = $reservation->payment_status_id;

        $net_total = $grandtotal - $discount;
        $oldbalance = $net_total - $reservation->prepayment;
        $balance = 0;
        $amount = 0;

        $prepayment = $reservation->prepayment;
        if(!empty($datacleaned['prepayment'])){
                
            if($datacleaned['prepayment'] >= $net_total){
                $payment_status = 3;
                $balance = 0;
                $amount = $net_total;
                $prepayment = $reservation->prepayment + $oldbalance;
           }else{

                /* $balance = $grandtotal - ($reservation->prepayment + $request->prepayment);                
                $amount = $request->prepayment; */
                
                $balance = $net_total - ($reservation->prepayment + $datacleaned['prepayment']);
                $amount = $datacleaned['prepayment'];

               // echo $request->prepayment. ' x '.$reservation->prepayment.' x ' .$oldbalance . ' x '.($reservation->prepayment + $request->prepayment).'<br>';
               
                if($datacleaned['prepayment'] >= $oldbalance){
                    $payment_status = 3;
                    $amount = $oldbalance;
                    $prepayment = $reservation->prepayment + $oldbalance;
                }else{
                    $payment_status = 2;
                    $prepayment = $reservation->prepayment + $datacleaned['prepayment'];
                }
                
                if($balance < 0){
                    $balance = 0;
                }

                
           }
           $booking_status_id =1;
        }else{
            $booking_status_id = $reservation->booking_status_id;
           # $balance = $reservation->balancepayment;
            $balance = $oldbalance;
            
        }

        $data_reservation = array(
                        'checkin' => $checkin->format('Y-m-d H:i'), //$datacleaned['checkin'],
                        'checkout' => $checkout->format('Y-m-d H:i'), //$datacleaned['checkout'],
                        'adults' => $datacleaned['adults'],
                        'childs' => $datacleaned['childs'],
                        'pets' => $datacleaned['pets'],
                        'fullname' => $datacleaned['fullname'],
                        'phone' => $datacleaned['phone'],
                        'email' => $datacleaned['email'],
                        'additional_info' => $datacleaned['additionalinformation'],
                        //'rooms' => $datacleaned['rooms'],
                        'booking_source_id' => $datacleaned['bookingsource_id'],
                        'doorcode' => 0,
                        'rateperday' => $datacleaned['rateperday'],
                        'daystay' => $diff,
                        'meals_total' => 0, //$datacleaned['mealsamount'],
                        'additional_services_total' => 0, //$datacleaned['servicestotalamount'],
                        'subtotal' => $rateperstay,
                        'discount' => $discount,
                        'tax' => $datacleaned['tax'],
                        'grandtotal' => $grandtotal, 
                        #'currency_id' => $user->host->host_settings->currency_id,
                        'payment_type_id' => $datacleaned['typeofpayment'],
                        //'prepayment' => $datacleaned->prepayment,
                        'prepayment' => $prepayment,
                        'payment_status_id' => $payment_status,
                        'balancepayment' => $balance, 
                        //'user_id' => $user->id,
                        //'host_id' => $user->host->id,
                        'booking_status_id' => $booking_status_id,
                    );
            
        //return $reservation->reservedRooms;  
        
        $changed = 0;

        $old_checkin = date('m/d/Y', strtotime($reservation->checkin));
        $new_checkin = $datacleaned['checkin'];
        $old_checkout = date('m/d/Y', strtotime($reservation->checkout));
        $new_checkout = $datacleaned['checkout'];
        if($new_checkin != $old_checkin){
            $changed = 1;
        }

        if($new_checkout != $old_checkout){
            $changed = 1;
        }

        $reservedRooms = $reservation->reservedRooms;

        $myOldReservedRooms = [];
        foreach($reservedRooms as $v){
            $myOldReservedRooms[] = $v->room_id;
        }
        $myOldReservedRooms = array_unique($myOldReservedRooms);
        
        if(empty($myOldReservedRooms)){
            $changed = 1;
        }else{
                //$diff = array_diff_assoc($myOldReservedRooms, $datacleaned['rooms']);
            $diff_rooms = array_diff($myOldReservedRooms, $datacleaned['rooms']);
            
            if ($diff_rooms) {                
                $changed = 1;                
            }
        }

        
        $roomsBook = $this->reservedRoomRepository->roomIsBooked($datacleaned['rooms'], $checkin, $checkout, $reservation->id);                                
            
        if(count($roomsBook) == 1){            
            throw new \Exception("This ".$roomsBook[0]->room_name." is not available for the selected dates.");
        }elseif(count($roomsBook) > 1){
            $rooms= [];
            foreach($roomsBook as $room){
                $rooms[] = $room->room_name;
            }
            $roomlist = implode(", ", $rooms);
            throw new Exception("These rooms ($roomlist) are not available for the selected dates.");
        }

        DB::beginTransaction();
        try {
            
            if($changed == 1){
                
                $this->reservedRoomRepository->massiveDelete($reservation->id); 
                $datareservedroom = [];
                foreach( $datacleaned['rooms'] as $bookedrooms){                    
                    $datareservedroom[] = [
                                            'reservation_id' => $reservation->id, 
                                            'room_id' => $bookedrooms, 
                                            'checkin' => $checkin, 
                                            'checkout' => $checkout,
                                            'status_id' => 1, // reserved
                                        ];
                }
                $this->reservedRoomRepository->massiveInsert($datareservedroom);
                //return $datareservedroom;
            }


            $this->reservationRepository->update($id, $data_reservation);
            DB::commit(); 
            // Optionally reload the updated reservation
            $updatedReservation = $this->reservationRepository->find($id);
            return $updatedReservation;
        
         } catch(\Exception $e) {
                DB::rollBack();

        }
        //return $reservation;
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

    /* public function isDateValid($date, $type){
        $patterns = [
            '/^\d{2}\/\d{2}\/\d{4}$/',                     // 12/25/2025
            '/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}\s?(AM|PM)$/i', // 12/25/2025 03:00 PM
        ];

        $matches = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $date)) {
                $matches = true;
                break;
            }
        }

        if (!$matches) {
            //return null; // 🚫 immediately reject malformed input
            //return 'Invalid checkin date format';
            throw new Exception("Invalid $type date format");
        }
    } */
}