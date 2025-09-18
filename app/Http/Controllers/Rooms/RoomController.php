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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
