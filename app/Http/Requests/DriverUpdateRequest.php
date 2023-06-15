<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverUpdateRequest extends FormRequest
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
           'full_name' => 'sometimes|nullable|string|max:100',
           'phone_number' => 'sometimes|nullable|string|max:100',
           'location' => 'sometimes|nullable|json',
           'email' => 'sometimes|nullable|string|max:100',
            'messaging_token' => 'sometimes|nullable|string|max:100',
           'is_online' => 'sometimes|in:0,1',
        ];
    }
}
