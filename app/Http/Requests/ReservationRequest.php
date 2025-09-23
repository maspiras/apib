<?php

namespace App\Http\Requests;

//use Illuminate\Foundation\Http\FormRequest; Laravel 11
use Illuminate\Http\Request\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;

use App\Rules\MultipleDateFormat;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true;
        //dd(auth()->user(), auth()->check());        
        //return auth()->check() && auth()->user()->hasRole('Admin'); 
        //return auth('sanctum')->check(); // User must be authenticated via Sanctum
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [            
            'checkin' => ['required', 'date_format:m/d/Y h:i A'],   //'required|date_format:m/d/Y h:i A', 
            'checkout' => ['required', 'date_format:m/d/Y h:i A'],  //'required|date_format:m/d/Y h:i A',
            
            /* 'checkin' => ['required', new MultipleDateFormat],
            'checkout' => ['required', new MultipleDateFormat], */
           
            'adults' => ['required', 'integer:strict', 'max:300'], //]'integer:strict|max:300',
            'childs' => ['nullable', 'integer:strict', 'max:100'], 
            'pets' => ['nullable', 'integer:strict', 'max:200'],                        
            'fullname' => ['required','string','max:255','min:3'],            
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable','email','max:255'],
            'additionalinformation' => ['nullable','string','max:1000'],
            'rooms' => ['required','array'], // Ensure the input is an array
            
            // Advanced validation to ensure rooms belong to the user's host
            
            'rooms.*' => [
                    'required','numeric:strict','distinct',// 'unique:rooms,id',
                     Rule::exists('rooms', 'id')
                        ->where(function ($query) {
                            $query->where('host_id', auth()->user()->host->id);
                        }), 
                    
                ],
            'bookingsource_id' => ['required', 'integer'],
            'rateperstay' => ['required','numeric:strict','min:1','decimal:0,2'], 
            'rateperday' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'], //'required|regex:/^\d+(\.\d{1,2})?$/',
            'typeofpayment' => ['required', 'integer'], 
            'discount' => ['nullable','numeric:strict','min:0','decimal:0,2'],
            'discountoption' => ['nullable','numeric:strict','min:0','decimal:0,2'],
            'tax' => ['nullable','numeric:strict','min:0','decimal:0,2'],
            'prepayment' => ['nullable','numeric:strict','min:0','decimal:0,2'],
        ];
    }

    public function messages(): array
    {
        return [
            
            'fullname.required' => 'Full name is required',
            'fullname.string' => 'Full name must be a string',
            'fullname.max' => 'Full name must not exceed 255 characters',
            'fullname.min' => 'Full name must be at least 3 characters',
            'email.email' => 'Email format is invalid',
            'email.max' => 'Email must not exceed 255 characters',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must not exceed 20 characters',   
            //'rooms.*.unique' => 'One or more selected rooms are invalid',
            'rooms.*.exists' => 'selected room/s do not exist or not available',
            
            //'checkin' => 'required|date_format:Y-m-d|date_format:m/d/Y'   
            /* 'items.required' => 'Please add at least one item.',
            'items.*.name.required' => 'The name for item :position is required.',
            'items.*.name.string' => 'The name for item :position must be a string.',
            'items.*.name.max' => 'The name for item :position cannot exceed :max characters.',
            'items.*.quantity.required' => 'The quantity for item :position is required.',
            'items.*.quantity.integer' => 'The quantity for item :position must be a whole number.',
            'items.*.quantity.min' => 'The quantity for item :position must be at least :min.', */     
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /* throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422)); */

        throw new HttpResponseException(ApiResponse::error(422, 'Validation failed!', $validator->errors()));
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
            $checkin = Carbon::parse($this->checkin);
            $checkout = Carbon::parse($this->checkout);
            
            if ($checkout->lessThanOrEqualTo($checkin)) {
                    $validator->errors()->add(
                        'check_out',
                        'The check-out date must be after the check-in date.'
                    );
                }
        });
    }
    
}
