<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReimbursementRequest extends FormRequest
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
            'client_name' => 'required',
            'project_name' => 'required',
            'visit_attended_of_id' => 'required',
            'amount' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'visit_attended_of_id.required' => "Who's visit attended is required field"
        ];
    }
}
