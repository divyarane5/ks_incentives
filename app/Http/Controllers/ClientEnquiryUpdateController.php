<?php

namespace App\Http\Controllers;
use App\Models\ClientEnquiry;
use App\Models\ClientEnquiryUpdate;
use Illuminate\Http\Request;

class ClientEnquiryUpdateController extends Controller
{
    
    public function create($id)
    {
        $enquiry = ClientEnquiry::findOrFail($id);
        return view('client_enquiries.update-form', compact('enquiry'));
    }

    public function store(Request $request, $enquiryId)
    {
        // Validate input
        $request->validate([
            'feedback' => 'nullable|string',
            'status' => 'nullable|string',
            'revisit_scheduled' => 'nullable|date',
            'revisit_done' => 'nullable|date',
            'followup_date' => 'nullable|date',
        ]);

        // Create new update
        ClientEnquiryUpdate::create([
            'client_enquiry_id' => $enquiryId,
            'feedback' => $request->feedback,
            'status' => $request->status,
            'revisit_scheduled' => $request->revisit_scheduled,
            'revisit_done' => $request->revisit_done,
            'followup_date' => $request->followup_date,
        ]);

        return redirect()->back()->with('success', 'Update added successfully.');

    }


}
