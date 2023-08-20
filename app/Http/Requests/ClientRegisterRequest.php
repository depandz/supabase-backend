<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
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
            'phone_number' => 'required|regex:/^\+213[567]\d{8}$/',
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'sometimes|nullable|email|string',
            'gender'=>'sometimes|nullable|in:male,female'
        ];
    }
}
