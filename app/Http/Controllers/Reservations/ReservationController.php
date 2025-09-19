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

use Illuminate\Http\Requests\ReservationRequest;

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
        //$reservations = $this->reservationRepository->create($request->all());                

        /* $data = [
            'room_name' => $request->room_name,
            'description' => $request->room_desc,
            'room_status_id' => 1,
            'host_id' => auth()->user()->host->id
        ]; */

        /* DB::beginTransaction();
        try {  
            Room::create($data);
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
