<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitializePickupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'client_sid'=>'required|string',
            'location'=>'required|string',
            'current_province_id'=>'required|integer',
            'destination'=>'required|string',
            'licence_plate'=>'required|string',
            'is_vehicle_empty'=>'required|boolean',
            'date_requested'=>'sometimes|nullable|date',
            'distance'=>'required|numeric',
            'duration'=>'required|numeric'
        ];
    }
}
