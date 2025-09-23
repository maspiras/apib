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

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request)
    {
        $datacleaned = $request->validated();  

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
        
        DB::beginTransaction();
        try {  
            //$this->reservationRepository->create($request->validated());
            $reservation = $this->reservationService->create($datacleaned) ;
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
