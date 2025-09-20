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
        
        return $datacleaned;

        $data = array('ref_number' => $ref_number,
                        'checkin' => $datacleaned['checkin'],
                        'checkout' => $datacleaned['checkout'],
                        'adults' => $datacleaned['adults'],
                        'childs' => $datacleaned['childs'],
                        'pets' => $datacleaned['pets'],
                        'fullname' => $datacleaned['fullname'],
                        'phone' => $datacleaned['phone'],
                        'email' => $datacleaned['email'],
                        'additional_info' => $datacleaned['additionalinformation'],
                        'booking_source_id' => $datacleaned['bookingsource_id'],
                        'doorcode' => 0,
                        'rateperday' => $datacleaned['ratesperday'],
                        'daystay' => $datacleaned['daystay'], /* server compute level*/
                        'meals_total' => $datacleaned['mealsamount'],
                        'additional_services_total' => $datacleaned['servicestotalamount'],
                        'subtotal' => $datacleaned['ratesperstay'], /* server compute level*/
                        'discount' => $datacleaned['discount'],
                        //'tax' => $datacleaned->tax,
                        'grandtotal' => $datacleaned['grandtotal'], /* server compute level*/ 
                        'currency_id' => $datacleaned['currency'],
                        'payment_type_id' => $datacleaned['typeofpayment'],
                        //'prepayment' => $datacleaned->prepayment,
                        'prepayment' => $datacleaned['prepayment'],
                        'payment_status_id' => $datacleaned['paymentstatus'], /* server process level*/
                        'balancepayment' => $datacleaned['balancepayment'], /* server compute level*/
                        'user_id' => auth()->user()->id,
                        'host_id' => auth()->user()->host->id,
                        'booking_status_id' => empty($datacleaned['prepayment']) ? 0 : 1,     
                        'created_at' => now(),
                    );
        
        //return $data;
        

        /* DB::beginTransaction();
        try {  
            //$this->reservationRepository->create($request->validated());
            $reservation_id = $this->reservationRepository->insertGetId($data);
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Reservation created successfully!']);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Room creation failed!', ['error' => $e->getMessage()]);
        } */
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
