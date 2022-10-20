<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndentConfigurationRequest extends FormRequest
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
            'user_id' => 'required',
            'expense_id' => 'required',
            'monthly_limit' => 'nullable|numeric|gt:0',
            'intent_limit' => 'nullable|numeric|gt:0'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user field is required',
            'expense_id.required' => 'The expense field is required',
        ];
    }
}
