<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

//use Illuminate\Validation\Rules;

use App\Models\User;
use App\Models\Host;
use App\Models\Hosts_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)//: Response
    {
        /* $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent(); */
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
        Auth::login($user);
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
}
