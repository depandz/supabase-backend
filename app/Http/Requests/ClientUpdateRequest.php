<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
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
        //    'gender' => 'sometimes|nullable|in:male,female',
           'location' => 'sometimes|nullable|json',
           'email' => 'sometimes|nullable|string|max:100',
        //    'photo' => 'sometimes|nullable|string|image|mimes:jpg,jpeg,webp,bmp,png,gif,svg',
           'messaging_token' => 'sometimes|nullable|string|max:100',
        ];
    }
}
