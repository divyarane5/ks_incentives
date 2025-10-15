<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        // Get the user ID from the route (for update)
        $userId = $this->route('user') ?? null;

        $rules = [
            // Basic Information
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'last_name'  => 'required|regex:/^[\pL\s\-]+$/u',
            'employee_code' => 'required|unique:users,employee_code,' . $userId,
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'entity' => 'required',
            'work_location_id' => 'required',
            
            // Employment Details
            'department_id' => 'required',
            'designation_id' => 'required',
            'joining_date' => 'required|date',
            'role_id' => 'required',

            // Optional Fields
            'dob' => 'nullable|date|before:-18 years',
            'photo' => 'nullable|image',
        ];

        // Password required only on create
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|min:8';
        }

        return $rules;
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'employee_code.required' => 'The employee code field is required.',
            'employee_code.unique' => 'The employee code has already been taken.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'entity.required' => 'The company field is required.',
            'work_location_id.required' => 'The location field is required.',
            'department_id.required' => 'The department field is required.',
            'designation_id.required' => 'The designation field is required.',
            'joining_date.required' => 'The date of joining field is required.',
            'dob.before' => 'The date of birth must be at least 18 years ago.',
            'dob.date' => 'The date of birth is not a valid date.',
            'role_id.required' => 'The role field is required.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
