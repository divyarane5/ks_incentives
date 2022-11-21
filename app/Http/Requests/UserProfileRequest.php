<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'dob' => 'nullable|date|before:-18 years',
            'photo' => 'image'
        ];
    }

    public function messages()
    {
        return [
            'dob.before' => 'The date of birth must be a date before 18 years.',
            'dob.date' => 'The date of birth is not a valid date.'
        ];
    }
}
