<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
        $user_id = $this->request->get('user_id');
        $expense_id = $this->request->get('expense_id');
        $id = ($this->request->has('id')) ? $this->request->get('id') : '';
        return [
            'user_id' => 'required',
            'expense_id' => [
                'required',
                Rule::unique('indent_configurations')->where(function ($query) use($user_id, $expense_id, $id) {
                    $query = $query->where('user_id', $user_id)
                    ->where('expense_id', $expense_id);

                    if ($id != "") {
                        $query = $query->where('id', '!=', $id);
                    }

                    return $query;
                }),
            ],
            'monthly_limit' => 'nullable|numeric|gt:0',
            'intent_limit' => 'nullable|numeric|gt:0'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user field is required',
            'expense_id.required' => 'The expense field is required',
            'expense_id.unique' => 'The expense has already been taken with selected user.'
        ];
    }
}
