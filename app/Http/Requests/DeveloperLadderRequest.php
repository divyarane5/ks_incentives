<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeveloperLadderRequest extends FormRequest
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
            'developer_id' => 'required',
            'aop' => 'required',
            'ladder' => 'required',
            'aop_s_date' => 'required',
            'aop_e_date' => 'required'
        ];
    }
}
