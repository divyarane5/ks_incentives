<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoiningFormRequest extends FormRequest
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
            'joining_date' => 'required|date',
            'designation' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'email' => 'required|email',
            'dob' => 'required|date',
            'marital_status' => 'required',
            'pan_number' => 'required|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
            'aadhar_number' => 'required|numeric|digits:12',
            'gender' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_relation' => 'required',
            'emergency_contact_number' => 'required|numeric|digits:10',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required',
            'ifsc' => 'required',
        ];
    }
}
