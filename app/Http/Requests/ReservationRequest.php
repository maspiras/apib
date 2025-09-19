<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /* 'checkin' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id', */
            'checkin' => 'required|date_format:d/m/Y',
            'checkout' => 'required|date_format:d/m/Y',
            'adults' => 'integer:strict|max:300',
            'childs' => 'integer:strict|max:100',
            'pets' => 'integer:strict|max:200',
            'fullname' => 'required|string|max:255|min:3',
            'email' => 'email|max:255',
            'phone' => 'string|max:20',
        ];
    }

    /* public function messages(): array
    {
        return [
            //
        ];
    } */
}
