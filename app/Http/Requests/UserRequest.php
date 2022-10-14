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
        $validationArr =  [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'employee_code' => 'required|unique:users,employee_code'.(($this->request->has('id')) ? ','.$this->request->get('id') : ''),
                'email' => 'email|unique:users,email'.(($this->request->has('id')) ? ','.$this->request->get('id') : ''),
                'entity' => 'required',
                'location_id' => 'required',
                'department_id' => 'required',
                'designation_id' => 'required',
                'joining_date' => 'required',
                'dob' => 'nullable|date|before:-18 years',
                'photo' => 'image',
                'role_id' => 'required'
            ];
        if ($this->isMethod('post')) {
            $validationArr['password'] = 'required|min:8';
        }
        return $validationArr;
    }

    public function messages()
    {
        return [
            'location_id.required' => 'The location field is required',
            'department_id.required' => 'The department field is required',
            'designation_id.required' => 'The designation field is required',
            'joining_date.required' => 'The date of joining field is required',
            'dob.before' => 'The date of birth must be a date before 18 years.',
            'dob.date' => 'The date of birth is not a valid date.'
        ];
    }
}
