<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Host;
use App\Models\Hosts_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    // Register new user
    public function register(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            //'host_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            //return response()->json(['errors' => $validator->errors()], 422);
            return ApiResponse::error(422, 'Registration failed!', ['error' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {  
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                //'host_id' => $request['host_id'],
            ]);

        
        $token = $user->createToken('mobile-token')->plainTextToken;
        $timezone = 'America/Los_Angeles';
        if(!empty($request->timezone)){
            $timezone = $request->timezone;
        }
       
        $user->assignRole('Admin');
        //if($request->option == 'host'){
            Host::insert(['id' => $user->id,'user_id' => $user->id]);
            Hosts_Setting::insert(['host_id' => $user->id, 'currency_id' => 251, 'timezone' => $timezone]);
        //}
        
        /* User::where('id', $user->id)
              ->update(['host_id' => $user->id]); */

        event(new Registered($user));

        /* return response()->json([
            'user' => $user,
            'token' => $token
        ], 201); */
            DB::commit(); 
            return ApiResponse::success([], ['message' => 'Successfully registered', 'user' => $user, 'token' => $token]);
        } catch(\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Registration failed!', ['error' => $e->getMessage()]);
        }
    }

    // Login existing user
    public function login(Request $request)
    {
        /* $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]); */

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            //return response()->json(['errors' => $validator->errors()], 422);
            return ApiResponse::error(422, 'Login failed!', ['error' => $validator->errors()]);
        }

        $user = User::where('email', $request['email'])->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            /* return response()->json([
                'message' => 'Invalid credentials'
            ], 401); */
            return ApiResponse::error(401, 'Invalid credentials!', ['error' => $validator->errors()]);
        }

        // create new token
        $token = $user->createToken('mobile-token')->plainTextToken;

        /* return response()->json([
            'user' => $user,
            'token' => $token
        ], 200); */
        $data = $user->toArray();
        $data['host_id'] = $user->host->id;
        $data['token'] = $token;
        return ApiResponse::success($data, ['message' => 'You are logged in']);
    }

    // Logout (revoke current token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return ApiResponse::success([], ['message' => 'Logged out successfully']);
    }

    // Get profile of logged-in user
    public function profile(Request $request)
    {
        $data = auth()->user()->toArray();
        $data['host_id'] = auth()->user()->host->id;        
        //return response()->json($data);
        return ApiResponse::success($data);
    }
}