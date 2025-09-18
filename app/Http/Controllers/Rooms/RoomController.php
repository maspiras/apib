<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::where('host_id', auth()->user()->host->id)->paginate(10);
        return ApiResponse::paginated($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255|min:3',
            'room_desc' => 'string|max:1000|nullable',
            
        ]);

        if ($validator->fails()) {            
            return ApiResponse::error(422, 'Room creation failed!', ['error' => $validator->errors()]);
        }

        $data = [
            'room_name' => $request->room_name,
            'description' => $request->room_desc,
            'room_status_id' => 1,
            'host_id' => auth()->user()->host->id
        ];

        DB::beginTransaction();
        try {  
            Room::create($data);
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Room created successfully']);
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
        $room = Room::where('id', $id)->where('host_id', auth()->user()->host->id)->first();
        if(!$room){
            return ApiResponse::error(404, 'Room not found');
        }
        return ApiResponse::success($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::where('id', $id)->where('host_id', auth()->user()->host->id)->first();
        if(!$room){
            return ApiResponse::error(404, 'Room not found');
        }
        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255|min:3',
            'room_desc' => 'string|max:1000|nullable',
            'room_status_id' => 'required|integer|in:1,2,3' // assuming 1,2,3 are valid status IDs
        ]);
        if ($validator->fails()) {            
            return ApiResponse::error(422, 'Room update failed!', ['error' => $validator->errors()]);
        }
        $data = [
            'room_name' => $request->room_name,
            'description' => $request->room_desc,
            'room_status_id' => $request->room_status_id
        ];
        DB::beginTransaction();
        try {  
            $room->update($data);
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Room updated successfully']);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Room update failed!', ['error' => $e->getMessage()]);
        }       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::where('id', $id)->where('host_id', auth()->user()->host->id)->first();
        if(!$room){
            return ApiResponse::error(404, 'Room not found');
        }
        DB::beginTransaction();
        try {  
            $room->delete();
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Room deleted successfully']);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Room deletion failed!', ['error' => $e->getMessage()]);
        }
    }
}
