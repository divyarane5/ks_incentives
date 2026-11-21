<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            // The fields marked * on the booking form.
            'booking_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'developer_id' => 'required|exists:developers,id',
            'project_id' => 'required|exists:projects,id',
            'agreement_value' => 'required|numeric|min:0',
            'sales_user_id' => 'required|exists:users,id',
            'booking_amount' => 'nullable|numeric|min:0',
            'additional_kicker' => 'nullable|numeric',
            'passback' => 'nullable|numeric',
            'registration_date' => 'nullable|date',
        ];
    }
}
