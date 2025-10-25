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
use Illuminate\Support\Facades\Auth;

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
            if (!empty($request->timezone)) {
                $timezone = $request->timezone;
            }

            $user->assignRole('Admin');
            //if($request->option == 'host'){
            Host::insert(['id' => $user->id, 'user_id' => $user->id]);
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
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(500, 'Registration failed!', ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        //$token = $user->createToken('flutterApp')->plainTextToken;
        $token = $user->createToken('mobile-token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
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
        //$data['timenow'] = date('Y-m-d H:i:s a');
        return ApiResponse::success($data);
    }
}
