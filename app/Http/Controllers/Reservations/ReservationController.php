<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Services\Contracts\ReservationServiceInterface;

//use Illuminate\Http\Requests\FormRequest;
use App\Http\Requests\ReservationRequest;
use Carbon\Carbon;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationServiceInterface $reservationService){        
        $this->reservationService = $reservationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        #$reservations = Reservation::where('host_id', auth()->user()->host->id)->paginate(100);
        #return ApiResponse::paginated($reservations);
        //return ApiResponse::paginated($this->reservationRepository->all()); 
        //return response()->json($this->reservationRepository->all());
        /* $reservations = $this->reservationRepository->paginate(100);        
        return ApiResponse::paginated($reservations); */
        //return ApiResponse::paginated($this->reservationService->getAll());
        return ApiResponse::paginated($this->reservationService->getAll());
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

    /**
     * Store a newly created resource in storage.
     */
    //public function store(ReservationRequest $request)
    public function store(Request $request)
    {
        $datacleaned = $request->all();
        
        $ref_number = substr(md5(time().'-'.auth()->user()->id), 0, 10);          
        $user = auth()->user();            
        
        $checkin = Carbon::parse($datacleaned['check_in']. '2pm');
        $checkout = Carbon::parse($datacleaned['check_out']. '12pm');

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
                        'check_in' => $checkin, //$datacleaned['checkin'],
                        'check_out' => $checkout, //$datacleaned['checkout'],
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
        return $data_reservation;
        //$checkin = Carbon::parse($request->checkin);
        // Step 1: Accepted regex formats
        /* $patterns = [
            '/^\d{2}\/\d{2}\/\d{4}$/',                     // 12/25/2025
            '/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}\s?(AM|PM)$/i', // 12/25/2025 03:00 PM
        ];

        $matches = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $request->checkin)) {
                $matches = true;
                break;
            }
        }

        if (!$matches) {
            //return null; // 🚫 immediately reject malformed input
            return 'Invalid checkin date format';
        } */

        //return $request->checkin;
        //$datacleaned = $request->validated();  

        /* $checkin = Carbon::parse($datacleaned['checkin']);
        $checkout = Carbon::parse($datacleaned['checkout']);
        return array('result' => $checkout->lessThanOrEqualTo($checkin)); */
        
        //$format = 'm/d/Y h:i A';
        /* $date = Carbon::createFromFormat($format, $datacleaned['checkout']);
        return $date['date'];//->format($format); */
        //return Carbon::parse('10/09/2025 asd...');
        /* carbon = Carbon::parse('10/09/2025 aaa.sd..');
        try{
            return $carbon;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        } */
        /* if($carbon !== false && empty(Carbon::getLastErrors()['errors'])){
            return $carbon;
        }

        throw new \InvalidArgumentException("Invalidat date format {$carbon}"); */

        $datacleaned = $request->validated();
        
        DB::beginTransaction();
        try {  
            //$this->reservationRepository->create($request->validated());
            $reservation = $this->reservationService->create($datacleaned) ;
            //$reservation = $this->reservationService->create($request) ;
            //$reservation = $this->reservationService->create($request->all()) ;
            //$reservation_id = $this->reservationRepository->insertGetId($data_reservation);
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Reservation created successfully!']);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Reservation creation failed!', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        /* try {              
            $reservation = $this->reservationService->getById($id) ;            
            return ApiResponse::success($reservation, ['message' => 'Reservation Information']);
        } catch(\Exception $e) {
        #} catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            
            return ApiResponse::error(500, 'No Reservation', ['error' => 'Reservation not found!']);
        } */
        $reservation = $this->reservationService->getById($id) ;            
        return ApiResponse::success($reservation, ['message' => 'Reservation Information']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservationRequest $request, string $id)
    {
        /* try {              
            //$reservation = $this->reservationService->getById($id) ;            
            $reservation = $this->reservationService->update($id, $request->all()) ;    
            return ApiResponse::success($reservation, ['message' => 'Reservation Updated Successfully']);
        } catch(\Exception $e) {
        #} catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            
            //return ApiResponse::error(500, 'No Reservation', ['error' => 'Reservation not found!']);
            return ApiResponse::error(500, 'No Reservation', ['error' => $e->getMessage()]);
        } */
       $datacleaned = $request->validated();
       $reservation = $this->reservationService->update($id,$datacleaned) ;
       //return $reservation;
       return ApiResponse::success($reservation, ['message' => 'Reservation updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
