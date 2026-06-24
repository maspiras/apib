<?php

namespace App\Modules\Property\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $propertyId = $this->route('property');

        return [

            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('properties', 'slug')
                    ->ignore($propertyId),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'property_type' => [
                'sometimes',
                'string',
                Rule::in([
                    'hotel',
                    'resort',
                    'hostel',
                    'apartment',
                    'villa',
                    'guesthouse',
                ]),
            ],

            'address_line_1' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'province' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'country' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'latitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-180,180',
            ],
        ];
    }
}