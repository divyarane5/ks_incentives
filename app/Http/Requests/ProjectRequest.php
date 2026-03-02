<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'developer_id' => 'required|exists:developers,id',

            // Ladders required
            'ladders' => 'required|array|min:1',

            'ladders.*.s_booking' => 'required|integer|min:0',
            'ladders.*.e_booking' => 'required|integer|gte:ladders.*.s_booking',
            'ladders.*.ladder' => 'required|numeric|min:0',

            'ladders.*.project_s_date' => 'required|date',
            'ladders.*.project_e_date' => 'required|date|after_or_equal:ladders.*.project_s_date',
        ];
    }
}
