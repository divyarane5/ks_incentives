<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'       => 'required|string|max:255',
            'contact_no'          => 'required|string|max:15',
            'alternate_no'        => 'nullable|string|max:15',
            'email'               => 'nullable|email|max:255',
            'profession'          => 'nullable|string|max:255',
            'company_name'        => 'nullable|string|max:255',
            'address'             => 'nullable|string|max:500',
            'pin_code'            => 'nullable|string|max:10',
            'residential_status'  => 'nullable|string|max:100',
            'nri_country'         => 'nullable|string|max:100',
            'channel_partner_id'  => 'nullable|exists:channel_partners,id',
            'closing_manager_id'  => 'nullable|exists:users,id',
            'property_type'       => 'nullable|string|max:255',
            'budget'              => 'nullable|string|max:255',
            'purchase_purpose'    => 'nullable|string|max:255',
            'funding_source'      => 'nullable|string|max:255',
            'team_call_received'  => 'sometimes|boolean',
            'source_of_visit'     => 'nullable|array',
            'reference_name'      => 'nullable|string|max:255',
            'reference_contact'   => 'nullable|string|max:20',
            'feedback'            => 'nullable|string|max:1000',
        ];
    }
}
