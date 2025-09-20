<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

use App\Models\Reservation;

use App\Repositories\Reservations\ReservationRepositoryInterface;
use App\Repositories\Reservations\ReservationRepository;

//use Illuminate\Http\Requests\FormRequest;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\v1\ReservationResource;

class ReservationController extends Controller
{
    protected $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository){
        $this->reservationRepository = $reservationRepository;
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
        $reservations = $this->reservationRepository->paginate(100);        
        return ApiResponse::paginated($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request)
    {
        $ref_number = substr(md5(time().'-'.auth()->user()->id), 0, 10);
         
        $datacleaned = $request->validated();  
        $checkin = Carbon::parse($request->checkin.' 2pm');
        $checkout = Carbon::parse($request->checkout.' 12pm');
        if($request->checkin == $request->checkout){
          $checkout = $checkout->addDays(1);  
        }        
        $diff = $checkin->diffInDays($checkout);
        
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
                        'subtotal' => 10, //$datacleaned['ratesperstay'], /* server compute level*/
                        'discount' => $datacleaned['discount'],
                        'tax' => $datacleaned['tax'],
                        'grandtotal' => 100, //$datacleaned['grandtotal'], /* server compute level*/ 
                        'currency_id' => auth()->user()->host->host_settings->currency_id,
                        'payment_type_id' => $datacleaned['typeofpayment'],
                        //'prepayment' => $datacleaned->prepayment,
                        'prepayment' => $datacleaned['prepayment'],
                        'payment_status_id' => 1, //$datacleaned['paymentstatus'], /* server process level*/
                        'balancepayment' => 10, //$datacleaned['balancepayment'], /* server compute level*/
                        'user_id' => auth()->user()->id,
                        'host_id' => auth()->user()->host->id,
                        'booking_status_id' => empty($datacleaned['prepayment']) ? 0 : 1,     
                        //'created_at' => now(),
                    );
        
        //return $data_reservation;
        //return new ReservationResource($data_reservation);

        DB::beginTransaction();
        try {  
            //$this->reservationRepository->create($request->validated());
            $reservation_id = $this->reservationRepository->insertGetId($data_reservation);
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Reservation created successfully!']);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Room creation failed!', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
