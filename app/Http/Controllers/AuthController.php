<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Host;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            //'host_id' => $request['host_id'],
        ]);

        
        $token = $user->createToken('mobile-token')->plainTextToken;
       
        $user->assignRole('Admin');
        if($request->option == 'host'){
            Host::insert(['id' => $user->id,'user_id' => $user->id]);
        }
        
        /* User::where('id', $user->id)
              ->update(['host_id' => $user->id]); */

        event(new Registered($user));

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request['email'])->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // create new token
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // Logout (revoke current token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    // Get profile of logged-in user
    public function profile(Request $request)
    {
        $data = auth()->user()->toArray();
        $data['host_id'] = auth()->user()->host->id;        
        return response()->json($data);
    }
}