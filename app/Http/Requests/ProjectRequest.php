<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:projects,name,' . $this->route('project'),
            'developer_id' => 'required|exists:developers,id',

            // ✅ Optional fields
            'base_brokerage_percent' => 'nullable|numeric|min:0',
            'rera_number' => 'nullable|string|max:255',

            // ✅ Ladders OPTIONAL
            'ladders' => 'nullable|array',

            // ✅ Validate only if present (not mandatory)
            'ladders.*.s_booking' => 'nullable|integer|min:0',
            'ladders.*.e_booking' => 'nullable|integer|gte:ladders.*.s_booking',
            'ladders.*.ladder' => 'nullable|numeric|min:0',

            'ladders.*.project_s_date' => 'nullable|date',
            'ladders.*.project_e_date' => 'nullable|date|after_or_equal:ladders.*.project_s_date',
        ];
    }
}