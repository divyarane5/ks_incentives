<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndentRequest extends FormRequest
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
            'title' => 'required',
            'bill_mode' => 'required',
            'location_id' => 'required',
            'business_unit_id' => 'required',
            'expense_id.*' => 'required',
            'vendor_id.*' => 'required',
            'quantity.*' => 'required|numeric',
            'unit_price.*' => 'required|numeric',
            'payment_method_id.*' => 'required',
            'amount.*' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'location_id.required' => 'The location field is required',
            'business_unit_id.required' => 'The business unit field is required',
            'expense_id.*.required' => 'The expense field is required',
            'vendor_id.*.required' => 'The vendor field is required',
            'quantity.*.required' => 'The quantity field is required',
            'unit_price.*.required' => 'The unit price field is required',
            'payment_method_id.*.required' => 'The payment method field is required',
            'amount.*.required' => 'The amount field is required'
        ];
    }
}
