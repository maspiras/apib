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
    //public function store(Request $request)
    {
        
        //return $request->all();

        $datacleaned = $request->validated();
        
        DB::beginTransaction();
        try {  
            //$this->reservationRepository->create($request->validated());
            $reservation = $this->reservationService->create($datacleaned) ;
            //$reservation = $this->reservationService->create($request) ;
            //$reservation = $this->reservationService->create($request->all()) ;
            //$reservation_id = $this->reservationRepository->insertGetId($data_reservation);
            DB::commit(); 
            return ApiResponse::success($reservation, ['message' => 'Reservation created successfully!']);
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
