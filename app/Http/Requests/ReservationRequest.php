<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; //Laravel 11
//use Illuminate\Http\Request\FormRequest; //Laravel 12
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
            /* 'checkin' => ['required', 'date_format:m/d/Y h:i A'],   //'required|date_format:m/d/Y h:i A', 
            'checkout' => ['required', 'date_format:m/d/Y h:i A'],  //'required|date_format:m/d/Y h:i A', */
            
            'checkin' => ['required', new MultipleDateFormat],
            'checkout' => ['required', new MultipleDateFormat],
           
            'adults' => ['required', 'integer', 'max:300'], //]'integer:strict|max:300',
            'childs' => ['nullable', 'integer', 'max:100'], 
            'pets' => ['nullable', 'integer', 'max:200'],                        
            'fullname' => ['required','string','max:255','min:3'],            
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable','email','max:255'],
            'additionalinformation' => ['nullable','string','max:1000'],
            'rooms' => ['required','array'], // Ensure the input is an array
            
            // Advanced validation to ensure rooms belong to the user's host
            
            'rooms.*' => [
                    'required','numeric','distinct',// 'unique:rooms,id',
                     Rule::exists('rooms', 'id')
                        ->where(function ($query) {
                            $query->where('host_id', auth()->user()->host->id);
                        }), 
                    
                ],
            'bookingsource_id' => ['required', 'integer'],
            //'rateperstay' => ['required','numeric','min:1','decimal:0,2'], 
            'rateperday' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'], //'required|regex:/^\d+(\.\d{1,2})?$/',
            'typeofpayment' => ['required', 'integer'], 
            'discount' => ['nullable','numeric','min:0','decimal:0,2'],
            'discountoption' => ['nullable','numeric','min:0','decimal:0,2'],
            'tax' => ['nullable','numeric','min:0','decimal:0,2'],
            'prepayment' => ['nullable','numeric','min:0','decimal:0,2'],
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
            if($this->normalizeDate($this->checkout)){
                
                $checkin = Carbon::parse($this->checkin);
                $checkout = Carbon::parse($this->checkout);
                
                if ($checkout->lessThanOrEqualTo($checkin)) {
                    $validator->errors()->add(
                        'checkout',
                        'The check-out date must be after the check-in date.'
                    );
                }
            }
            
        });
    }

    protected function prepareForValidation() // Will be called before the validation process starts
    {
        /* // Trim strings
        $this->merge([
            'fullname' => $this->fullname ? trim($this->fullname) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'email' => $this->email ? trim($this->email) : null,
            'additionalinformation' => $this->additionalinformation ? trim($this->additionalinformation) : null,
        ]); */
        if ($this->has('checkin')) {
            $this->merge([
                'checkin' => $this->normalizeDate($this->checkin),
                //'checkin' => $this->checkin,
            ]);            
        }

        if ($this->has('checkout')) {
            $this->merge([
                'checkout' => $this->normalizeDate($this->checkout),
                //'checkout' => $this->checkout,
            ]);            
        }
    }

    private function normalizeDate($date)
    {
        //$formats = ['m/d/Y h:i A'];
        /* foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                continue;
                //throw new Exception("Invalid Format");
            }
        } */
       // Acceptable formats
        $formats = [
            //'m/d/Y h:i A', // 12/25/2025 03:00 PM
            'm/d/Y',       // 12/25/2025
        ];

        // Strict regex whitelist
        $patterns = [
            '/^\d{2}\/\d{2}\/\d{4}$/',                     // date only
            //'/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}\s?(AM|PM)$/i', // date + time
        ];

        $validPattern = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $date)) {
                $validPattern = true;
                break;
            }
        }

        if (!$validPattern) {
            return false; // 🚫 immediately reject junk like "add..add"
        }
        return $date; // keep as-is to let validation fail
    }
    
}
