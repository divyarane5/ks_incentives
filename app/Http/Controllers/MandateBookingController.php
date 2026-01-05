<?php

namespace App\Http\Controllers;
use App\Models\MandateBooking;
use App\Models\MandateProject;
use App\Models\ClientEnquiry;
use App\Models\ChannelPartner;
use Illuminate\Http\Request;
use App\Models\MandateBookingApplicant;
use Illuminate\Support\Facades\DB;


class MandateBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = MandateBooking::latest()->get();
        return view('mandate_bookings.index', compact('bookings'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('mandate_bookings.create', [
            'projects' => MandateProject::orderBy('project_name')->get(),
            'clientEnquiries' => ClientEnquiry::orderBy('customer_name')->get(),
            'channelPartners' => ChannelPartner::orderBy('firm_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            // ================= BOOKING =================
            $booking = MandateBooking::create([
                'booking_date' => $request->booking_date,
                'booking_source' => $request->booking_source,
                'channel_partner_id' => $request->channel_partner_id,

                'project_id' => $request->project_id,
                'tower' => $request->tower,
                'wing' => $request->wing,
                'unit_no' => $request->unit_no,
                'floor_no' => $request->floor_no,
                'property_type' => $request->property_type,
                'configuration' => $request->configuration,
                'rera_carpet_area' => $request->rera_carpet_area,
                'parking_count' => $request->parking_count,

                'total_agreement_value' => $request->total_agreement_value,
                'net_payable_amount' => $request->net_payable_amount,
                'booking_amount' => $request->booking_amount,
                'payment_mode' => $request->payment_mode,
                'bank_name' => $request->bank_name,
                'payment_date' => $request->payment_date,

                'broker_involved' => $request->broker_involved ?? 0,
                'broker_name' => $request->broker_name,
                'brokerage_percentage' => $request->brokerage_percentage,
                'brokerage_amount' => $request->brokerage_amount,

                'declaration_correctness' => $request->has('declaration_correctness'),
                'acceptance_terms' => $request->has('acceptance_terms'),
                'rbi_declaration' => $request->has('rbi_declaration'),

                'created_by' => auth()->id(),
            ]);

            // ================= SIGNATURES =================
            if ($request->hasFile('sales_signature')) {
                $booking->sales_signature =
                    $request->file('sales_signature')->store('signatures');
            }

            if ($request->hasFile('closing_signature')) {
                $booking->closing_signature =
                    $request->file('closing_signature')->store('signatures');
            }

            $booking->save();

            // ================= APPLICANTS =================
            foreach ($request->applicants as $applicant) {

                $row = MandateBookingApplicant::create([
                    'mandate_booking_id' => $booking->id,
                    'applicant_type' => $applicant['type'],
                    'client_enquiry_id' => $applicant['client_enquiry_id'] ?? null,

                    'first_name' => $applicant['first_name'],
                    'middle_name' => $applicant['middle_name'] ?? null,
                    'last_name' => $applicant['last_name'],
                    'mobile' => $applicant['mobile'],
                    'alternate_mobile' => $applicant['alt_mobile'] ?? null,
                    'email' => $applicant['email'] ?? null,
                    'pan_number' => $applicant['pan'] ?? null,
                ]);

                if (!empty($applicant['signature'])) {
                    $row->signature =
                        $applicant['signature']->store('signatures');
                    $row->save();
                }
            }
        });

        return redirect()
            ->route('mandate_bookings.index')
            ->with('success', 'Booking created successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
