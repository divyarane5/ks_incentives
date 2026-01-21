<?php

namespace App\Http\Controllers;
use App\Models\MandateBooking;
use App\Models\MandateProject;
use App\Models\ClientEnquiry;
use App\Models\ChannelPartner;
use Illuminate\Http\Request;
use App\Models\MandateBookingApplicant;
use App\Models\MandateBookingAddress;
use App\Models\MandateBookingBrokerage;
use Illuminate\Support\Facades\DB;


class MandateBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('mandate_bookings as b')
                ->leftJoin('mandate_projects as p', 'p.id', '=', 'b.project_id')
                ->leftJoin('mandate_booking_finances as f', 'f.booking_id', '=', 'b.id')
                ->leftJoin('mandate_booking_brokerages as br', 'br.booking_id', '=', 'b.id')
                ->select([
                    'b.id',
                    'b.booking_date',
                    'b.booking_status',
                    'b.booking_source',
                    DB::raw('COALESCE(p.project_name, "—") as project_name'),
                    'f.agreement_value',
                    'f.is_registered',
                    'br.total_paid',
                    'br.payment_percent',
                    'br.is_eligible',
                    'br.status as brokerage_status',
                ]);

            // Filters
            if ($request->filled('project_id')) {
                $query->where('b.project_id', $request->project_id);
            }
            if ($request->filled('status')) {
                $query->where('b.booking_status', $request->status);
            }
            if ($request->filled('is_registered')) {
                $query->where('f.is_registered', $request->is_registered);
            }
            if ($request->filled('is_eligible')) {
                $request->is_eligible === 'null' 
                    ? $query->whereNull('br.is_eligible') 
                    : $query->where('br.is_eligible', $request->is_eligible);
            }
            if ($request->filled('brokerage_status')) {
                $query->where('br.status', $request->brokerage_status);
            }
            if ($request->filled('booking_source')) {
                $query->where('b.booking_source', $request->booking_source);
            }
            if ($request->filled('booking_date_from') && $request->filled('booking_date_to')) {
                $query->whereBetween('b.booking_date', [$request->booking_date_from, $request->booking_date_to]);
            } elseif ($request->filled('booking_date_from')) {
                $query->where('b.booking_date', '>=', $request->booking_date_from);
            } elseif ($request->filled('booking_date_to')) {
                $query->where('b.booking_date', '<=', $request->booking_date_to);
            }

            return datatables()->of($query)
                ->addColumn('registered', function($row) {
                    return $row->is_registered == 1
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-warning">No</span>';
                })
                ->addColumn('brokerage_eligible', function($row) {
                    if ($row->is_eligible === null) return '<span class="badge bg-secondary">Not Evaluated</span>';
                    return $row->is_eligible == 1
                        ? '<span class="badge bg-success">Eligible</span>'
                        : '<span class="badge bg-danger">Not Eligible</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('mandate_bookings-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('mandate_bookings.show', $row->id).'">
                                        <i class="bx bx-show me-1"></i> View
                                    </a>';
                    }
                    if (auth()->user()->can('mandate_bookings-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('mandate_bookings.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>';
                    }
                    if (auth()->user()->can('mandate_bookings-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteLocation('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('mandate_bookings.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().method_field('delete').'
                                    </form>';
                    }
                    return $actions ? '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">'.$actions.'</div>
                                    </div>' : '';
                })
                ->rawColumns(['registered','brokerage_eligible','status','action'])
                ->make(true);
        }

        $projects = MandateProject::where('status', 1)->get(['id','project_name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding',
            'Mailers/SMS','Online Ad','Call Center','Walk in','Exhibition',
            'Insert','Existing Client','Property Portal'
        ];

        return view('mandate_bookings.index', compact('projects','sources'));
    }

    public function updateStatus(Request $request)
    {
        $bookingId = $request->id;

        $brokerage = MandateBookingBrokerage::firstOrCreate(
            ['booking_id' => $bookingId]
        );

        // Handle file uploads
        if ($request->hasFile('bill_copy')) {
            $file = $request->file('bill_copy');
            $filename = 'bill_'.$bookingId.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/bill_copies', $filename);
            $brokerage->bill_copy = $filename;
        }

        if ($request->hasFile('acceptance_copy')) {
            $file = $request->file('acceptance_copy');
            $filename = 'acceptance_'.$bookingId.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/acceptance_copies', $filename);
            $brokerage->acceptance_copy = $filename;
        }

        // ✅ Update brokerage_status from request if provided
        if ($request->filled('brokerage_status')) {
            $status = $request->brokerage_status;

            // Only allow approved if files exist
            if ($status === 'approved' && (! $brokerage->bill_copy || ! $brokerage->acceptance_copy)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot approve before uploading Bill and Acceptance copies.',
                    'status' => $brokerage->status
                ]);
            }
            if ($status === 'paid' && $brokerage->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update to paid before approval.',
                    'status'  => $brokerage->status
                ]);
            }

            $brokerage->status = $status;

            if ($status === 'approved' && ! $brokerage->eligible_at) {
                $brokerage->eligible_at = now();
            }

            if ($status === 'paid') {
                $brokerage->paid_at = now();
            }
        }

        // Optionally update booking status
        if ($request->filled('booking_status')) {
            $booking = MandateBooking::find($bookingId);
            if ($booking) {
                $booking->booking_status = $request->booking_status;
                $booking->save();
            }
        }

        $brokerage->save();

        return response()->json([
            'success' => true,
            'status' => $brokerage->status
        ]);
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
        DB::beginTransaction();

        try {

            /* =========================
            * STEP 1: VALIDATION
            * ========================= */
            $validated = $request->validate([
                'booking_date' => 'required|date',
                'project_id'   => 'required|exists:mandate_projects,id',

                // Applicants
                'applicants.primary.first_name' => 'required|string|max:100',
                'applicants.primary.mobile'     => 'required|digits:10',

                // Finance
                'finance.unit_value' => 'required|numeric|min:1',

                // Payments
                'payments'            => 'required|array|min:1',
                'payments.*.mode'     => 'required|string',
                'payments.*.amount'   => 'required|numeric|min:1',
                'payments.*.proof'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            /* =========================
            * STEP 2: BOOKING
            * ========================= */
            $bookingFormPath = $request->file('booking_form_file')
                ? $request->file('booking_form_file')->store('booking_forms', 'public')
                : null;

            $bookingId = DB::table('mandate_bookings')->insertGetId([
                'booking_date' => $request->booking_date,
                'project_id'   => $request->project_id,
                'tower'        => $request->tower,
                'wing'         => $request->wing,
                'unit_no'      => $request->unit_no,
                'floor_no'     => $request->floor_no,
                'configuration'=> $request->configuration,
                'rera_carpet_area' => $request->rera_carpet_area,
                'parking_count'=> $request->parking_count,
                'parking_type' => $request->parking_type,
                'property_type'=> $request->property_type,
                'booking_source' => $request->booking_source,
                'channel_partner_id' => $request->channel_partner_id,
                'reference_name' => $request->reference_name,
                'reference_contact' => $request->reference_contact,
                'source_remark' => $request->source_remark,
                'booking_form_file' => $bookingFormPath,
                'booking_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /* =========================
            * STEP 3: APPLICANTS
            * ========================= */
            foreach ($request->applicants as $applicant) {

                if (empty($applicant['first_name'])) continue;

                $panPath = isset($applicant['pan_file']) && $applicant['pan_file'] instanceof \Illuminate\Http\UploadedFile
                    ? $applicant['pan_file']->store('kyc', 'public')
                    : null;

                $aadhaarPath = isset($applicant['aadhar_file']) && $applicant['aadhar_file'] instanceof \Illuminate\Http\UploadedFile
                    ? $applicant['aadhar_file']->store('kyc', 'public')
                    : null;

                $applicantId = DB::table('mandate_booking_applicants')->insertGetId([
                    'booking_id' => $bookingId,
                    'type' => $applicant['type'],
                    'first_name' => $applicant['first_name'],
                    'middle_name'=> $applicant['middle_name'] ?? null,
                    'last_name'  => $applicant['last_name'] ?? null,
                    'mobile'     => $applicant['mobile'],
                    'alternate_mobile' => $applicant['alternate_mobile'] ?? null,
                    'email' => $applicant['email'] ?? null,
                    'pan_number' => $applicant['pan_number'] ?? null,
                    'aadhar_number' => $applicant['aadhar_number'] ?? null,
                    'pan_file' => $panPath,
                    'aadhar_file' => $aadhaarPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($applicant['addresses'] ?? [] as $type => $address) {
                    DB::table('mandate_booking_addresses')->insert([
                        'applicant_id' => $applicantId,
                        'address_type' => $type,
                        'address' => $address['address'] ?? null,
                        'city' => $address['city'] ?? null,
                        'state' => $address['state'] ?? null,
                        'pincode' => $address['pincode'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            /* =========================
            * STEP 4: FINANCE
            * ========================= */
            $finance = $request->finance;

            $agreementValue =
                ($finance['unit_value'] ?? 0) +
                ($finance['other_charges'] ?? 0) +
                ($finance['car_park_charges'] ?? 0);

            DB::table('mandate_booking_finances')->insert([
                'booking_id' => $bookingId,
                'unit_value' => $finance['unit_value'],
                'other_charges' => $finance['other_charges'] ?? 0,
                'car_park_charges' => $finance['car_park_charges'] ?? 0,
                'agreement_value' => $agreementValue,
                'current_due_percent' => $finance['current_due_percent'] ?? 0,
                'is_registered' => $request->boolean('is_registered'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /* =========================
            * STEP 5: PAYMENTS
            * ========================= */
            foreach ($request->payments as $payment) {

                $proofPath = isset($payment['proof']) && $payment['proof'] instanceof \Illuminate\Http\UploadedFile
                    ? $payment['proof']->store('payment_proofs', 'public')
                    : null;

                DB::table('mandate_booking_payments')->insert([
                    'booking_id' => $bookingId,
                    'amount' => $payment['amount'],
                    'mode' => $payment['mode'],
                    'date' => $payment['date'] ?? null,
                    'bank_name' => $payment['bank_name'] ?? null,
                    'transaction_id' => $payment['transaction_id'] ?? null,
                    'cheque_number' => $payment['cheque_number'] ?? null,
                    'proof' => $proofPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('mandate_bookings.index')
                ->with('success', 'Booking created successfully');

        }
        catch (\Illuminate\Validation\ValidationException $e) {
            // 🔥 DO NOT swallow validation errors
            throw $e;
        }
        catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mandate_booking = MandateBooking::with([
            'applicants',
            'payments', 'signature'
        ])->findOrFail($id);
        // echo "<pre>";
        // print_r($mandate_booking); exit;
        return view('mandate_bookings.show', compact('mandate_booking'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $booking = MandateBooking::with([
            'project',
            'applicants.addresses',
            'finance',
            'payments',
            'brokerage',
            'channel_partner',
            'signature',
        ])->findOrFail($id);

        return view('mandate_bookings.edit', [
            'booking' => $booking,
            'projects' => MandateProject::all(),
            'channelPartners' => ChannelPartner::all(),
        ]);
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
        // echo "<pre>"; 
        // print_r($request->all()); exit;
        DB::beginTransaction();

        try {
            /**
             * STEP 1: VALIDATION
             */
            $request->validate([
                'booking_date' => 'required|date',
                'project_id'   => 'required|exists:mandate_projects,id',
                'applicants' => 'required|array|min:1',
                'applicants.*.type' => 'required|in:primary,co',
                'applicants.*.first_name' => 'required|string|max:100',
                'applicants.*.mobile' => 'required|digits:10',
                'finance' => 'required|array',
                'finance.unit_value' => 'required|numeric|min:1',
                'payments' => 'required|array|min:1',
                'payments.*.amount' => 'required|numeric|min:1',
                'payments.*.mode'   => 'required|in:UPI,Card,NetBanking,Cheque,Cash',
                'payments.*.proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($request->booking_source === 'Channel Partner') {
                $request->validate(['channel_partner_id' => 'required|exists:channel_partners,id']);
            }

            if ($request->booking_source === 'Reference') {
                $request->validate([
                    'reference_name' => 'required|string|max:100',
                    'reference_contact' => 'required|string|max:20',
                ]);
            }

            foreach ($request->payments as $i => $payment) {
                if (in_array($payment['mode'], ['UPI', 'Card', 'NetBanking'])) {
                    $request->validate(["payments.$i.transaction_id" => 'required|string']);
                }
                if ($payment['mode'] === 'Cheque') {
                    $request->validate(["payments.$i.cheque_number" => 'required|string']);
                }
            }

            /**
             * STEP 2: BOOKING UPDATE
             */
            $booking = DB::table('mandate_bookings')->where('id', $id)->first();

            $bookingFormPath = $request->hasFile('booking_form_file')
                ? $request->file('booking_form_file')->store('booking_forms', 'public')
                : $booking->booking_form_file;

            DB::table('mandate_bookings')->where('id', $id)->update([
                'booking_date' => $request->booking_date,
                'project_id'   => $request->project_id,
                'tower'        => $request->tower,
                'wing'         => $request->wing,
                'unit_no'      => $request->unit_no,
                'floor_no'     => $request->floor_no,
                'configuration'=> $request->configuration,
                'rera_carpet_area' => $request->rera_carpet_area,
                'parking_count'=> $request->parking_count,
                'parking_type' => $request->parking_type,
                'property_type'=> $request->property_type,
                'booking_source' => $request->booking_source,
                'channel_partner_id' => $request->channel_partner_id,
                'reference_name' => $request->reference_name,
                'reference_contact' => $request->reference_contact,
                'source_remark' => $request->source_remark,
                'booking_form_file' => $bookingFormPath,
                'updated_at' => now(),
            ]);

            /**
             * STEP 3: APPLICANTS + ADDRESSES
             */
            $existingApplicants = DB::table('mandate_booking_applicants')->where('booking_id', $id)->pluck('id');
            DB::table('mandate_booking_addresses')->whereIn('applicant_id', $existingApplicants)->delete();
            DB::table('mandate_booking_applicants')->where('booking_id', $id)->delete();

            foreach ($request->applicants as $applicant) {
                $panPath = isset($applicant['pan_file']) && $applicant['pan_file'] instanceof \Illuminate\Http\UploadedFile
                    ? $applicant['pan_file']->store('kyc', 'public')
                    : null;
                $aadhaarPath = isset($applicant['aadhar_file']) && $applicant['aadhar_file'] instanceof \Illuminate\Http\UploadedFile
                    ? $applicant['aadhar_file']->store('kyc', 'public')
                    : null;

                $applicantId = DB::table('mandate_booking_applicants')->insertGetId([
                    'booking_id' => $id,
                    'type' => $applicant['type'],
                    'first_name' => $applicant['first_name'],
                    'middle_name'=> $applicant['middle_name'] ?? null,
                    'last_name'  => $applicant['last_name'] ?? null,
                    'mobile'     => $applicant['mobile'],
                    'alternate_mobile' => $applicant['alternate_mobile'] ?? null,
                    'email' => $applicant['email'] ?? null,
                    'pan_number' => $applicant['pan_number'] ?? null,
                    'aadhar_number' => $applicant['aadhar_number'] ?? null,
                    'pan_file' => $panPath,
                    'aadhar_file' => $aadhaarPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($applicant['addresses'] ?? [] as $type => $address) {
                    DB::table('mandate_booking_addresses')->insert([
                        'applicant_id' => $applicantId,
                        'address_type' => $type,
                        'address' => $address['address'] ?? null,
                        'city' => $address['city'] ?? null,
                        'state' => $address['state'] ?? null,
                        'pincode' => $address['pincode'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            /**
             * STEP 4: FINANCE
             */
            DB::table('mandate_booking_finances')->where('booking_id', $id)->delete();
            $finance = $request->finance;
            $agreementValue = ($finance['unit_value'] ?? 0) + ($finance['other_charges'] ?? 0) + ($finance['car_park_charges'] ?? 0);

            DB::table('mandate_booking_finances')->insert([
                'booking_id' => $id,
                'unit_value' => $finance['unit_value'],
                'other_charges' => $finance['other_charges'] ?? 0,
                'car_park_charges' => $finance['car_park_charges'] ?? 0,
                'agreement_value' => $agreementValue,
                'current_due_percent' => $finance['current_due_percent'] ?? 0,
                'is_registered' => $request->boolean('is_registered'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /**
             * STEP 5: PAYMENTS
             */
            /**
             * STEP 5: PAYMENTS (SAFE UPDATE)
             */
            $existingPaymentIds = DB::table('mandate_booking_payments')
                ->where('booking_id', $id)
                ->pluck('id')
                ->toArray();

            $requestPaymentIds = [];

            foreach ($request->payments as $i => $payment) {

                $proofPath = null;

                // ✅ HANDLE FILE UPLOAD
                if ($request->hasFile("payments.$i.proof")) {
                    $proofPath = $request->file("payments.$i.proof")
                        ->store('payment_proofs', 'public');
                }

                // UPDATE existing payment
                if (!empty($payment['id'])) {

                    $updateData = [
                        'amount' => $payment['amount'],
                        'mode' => $payment['mode'],
                        'date' => $payment['date'] ?? null,
                        'bank_name' => $payment['bank_name'] ?? null,
                        'transaction_id' => $payment['transaction_id'] ?? null,
                        'cheque_number' => $payment['cheque_number'] ?? null,
                        'updated_at' => now(),
                    ];

                    // ✅ update proof ONLY if new file uploaded
                    if ($proofPath) {
                        $updateData['proof'] = $proofPath;
                    }

                    DB::table('mandate_booking_payments')
                        ->where('id', $payment['id'])
                        ->update($updateData);

                    $requestPaymentIds[] = $payment['id'];

                } else {
                    // INSERT new payment
                    $newId = DB::table('mandate_booking_payments')->insertGetId([
                        'booking_id' => $id,
                        'amount' => $payment['amount'],
                        'mode' => $payment['mode'],
                        'date' => $payment['date'] ?? null,
                        'bank_name' => $payment['bank_name'] ?? null,
                        'transaction_id' => $payment['transaction_id'] ?? null,
                        'cheque_number' => $payment['cheque_number'] ?? null,
                        'proof' => $proofPath, // ✅ saved here
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $requestPaymentIds[] = $newId;
                }
            }

            // DELETE payments removed from UI
            $paymentsToDelete = array_diff($existingPaymentIds, $requestPaymentIds);

            if (!empty($paymentsToDelete)) {
                DB::table('mandate_booking_payments')
                    ->whereIn('id', $paymentsToDelete)
                    ->delete();
            }


            /**
             * STEP 6: CONSENTS
             */
            DB::table('mandate_booking_signatures')->where('booking_id', $id)->delete();
            DB::table('mandate_booking_signatures')->insert([
                'booking_id' => $id,
                'developer_consent_file' => $request->file('developer_consent_file')?->store('signature', 'public'),
                'mandate_consent_file'   => $request->file('mandate_consent_file')?->store('signature', 'public'),
                'cp_consent_file'        => $request->file('cp_consent_file')?->store('signature', 'public'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /**
             * STEP 7: BROKERAGE CALCULATION + BILL & ACCEPTANCE FILES
             */
            $project = DB::table('mandate_projects')->where('id', $request->project_id)->first();
            $totalPaid = DB::table('mandate_booking_payments')->where('booking_id', $id)->sum('amount');
            $paymentPercent = $agreementValue > 0 ? round(($totalPaid / $agreementValue) * 100, 2) : 0;

            $isEligible = false;
            $scenario = null;
            $reason = 'Not eligible yet';

            if ($paymentPercent >= $project->threshold_percentage && $request->boolean('is_registered')) {
                $isEligible = true;
                $scenario = 'SCENARIO_1';
                $reason = 'Threshold payment completed and registration done.';
            } elseif ($paymentPercent >= ($finance['current_due_percent'] ?? 0)) {
                $isEligible = true;
                $scenario = 'SCENARIO_2';
                $reason = 'Payment completed up to current due percentage.';
            }

            // Handle bill & acceptance files
            $brokerage = DB::table('mandate_booking_brokerages')->where('booking_id', $id)->first();
            $billCopyPath = $request->file('bill_copy') 
                ? $request->file('bill_copy')->store('bill_copies', 'public') 
                : ($brokerage->bill_copy ?? null);
            $acceptanceCopyPath = $request->file('acceptance_copy') 
                ? $request->file('acceptance_copy')->store('acceptance_copies', 'public') 
                : ($brokerage->acceptance_copy ?? null);

            if ($brokerage) {
                DB::table('mandate_booking_brokerages')->where('booking_id', $id)->update([
                    'agreement_value' => $agreementValue,
                    'total_paid' => $totalPaid,
                    'payment_percent' => $paymentPercent,
                    'threshold_percentage' => $project->threshold_percentage,
                    'current_due_percentage' => $finance['current_due_percent'] ?? 0,
                    'is_registered' => $request->boolean('is_registered'),
                    'is_eligible' => $isEligible,
                    'eligibility_scenario' => $scenario,
                    'eligibility_reason' => $reason,
                    'bill_copy' => $billCopyPath,
                    'acceptance_copy' => $acceptanceCopyPath,
                    'status' => ($billCopyPath && $acceptanceCopyPath && $isEligible) ? 'approved' : ($brokerage->status ?? 'pending'),
                    'eligible_at' => ($billCopyPath && $acceptanceCopyPath && $isEligible && !$brokerage->eligible_at) ? now() : $brokerage->eligible_at,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('mandate_booking_brokerages')->insert([
                    'booking_id' => $id,
                    'agreement_value' => $agreementValue,
                    'total_paid' => $totalPaid,
                    'payment_percent' => $paymentPercent,
                    'threshold_percentage' => $project->threshold_percentage,
                    'current_due_percentage' => $finance['current_due_percent'] ?? 0,
                    'is_registered' => $request->boolean('is_registered'),
                    'is_eligible' => $isEligible,
                    'eligibility_scenario' => $scenario,
                    'eligibility_reason' => $reason,
                    'bill_copy' => $billCopyPath,
                    'acceptance_copy' => $acceptanceCopyPath,
                    'status' => ($billCopyPath && $acceptanceCopyPath && $isEligible) ? 'approved' : 'pending',
                    'eligible_at' => ($billCopyPath && $acceptanceCopyPath && $isEligible) ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('mandate_bookings.index')->with('success', 'Booking updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
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
