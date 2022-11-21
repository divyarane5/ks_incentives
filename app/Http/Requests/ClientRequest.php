<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'template_id' => 'required',
            'sales_person' => 'required',
            'client_name' => 'required',
            'client_email' => 'required|email',
            'subject_name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'template_id' => 'The template field is required'
        ];
    }
}
