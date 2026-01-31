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
use App\Services\BrokerageLedgerService;
use App\Traits\UserHierarchyTrait;


class MandateBookingController extends Controller
{
    use UserHierarchyTrait;
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
                ->leftJoin('channel_partners as cp', 'cp.id', '=', 'b.channel_partner_id')
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
                    // ✅ CP NAME (only meaningful for CP source)
                    DB::raw("
                        CASE 
                            WHEN b.booking_source = 'Channel Partner' 
                            THEN COALESCE(cp.firm_name, '—')
                            ELSE '—'
                        END as cp_name
                    "),
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
            // 🔍 Channel Partner filter
            if ($request->filled('channel_partner_id')) {
                $query->where('b.channel_partner_id', $request->channel_partner_id);
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
                    if (auth()->user()->can('mandate_bookings-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('mandate_bookings.ledgers', $row->id).'" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-edit-alt me-1"></i> Ledger
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
        $channelPartners = DB::table('channel_partners')
            ->where('status', 1)
            ->orderBy('firm_name')
            ->get(['id', 'firm_name']);

        return view('mandate_bookings.index', compact('projects','sources','channelPartners'));
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
            'channelPartners' => ChannelPartner::orderBy('firm_name')->get(),
            'managers' => $this->getAccessibleUsersByBusinessUnit(auth()->user(), 'AI'),
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
                // ✅ MANAGERS
                'closing_manager_id'  => $request->closing_manager_id,
                'presales_id'         => $request->presales_id,
                'sourcing_manager_id' => $request->sourcing_manager_id,

                // ✅ CREATED BY (logged-in user)
                'created_by' => auth()->id(),

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
            'brokerageLedgers',
        ])->findOrFail($id);
        // SAFE: brokerage may or may not exist
        $brokerage = $booking->brokerage;

        return view('mandate_bookings.edit', [
            'booking' => $booking,
            'brokerage' => $brokerage,
            'projects' => MandateProject::all(),
            'channelPartners' => ChannelPartner::all(),
            'managers' => $this->getAccessibleUsersByBusinessUnit(auth()->user(), 'AI'),
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
        // print_r($_REQUEST); exit;
        DB::beginTransaction();

        try {

            /**
             * STEP 1: VALIDATION
             */
            $request->validate([
                'booking_date' => 'required|date',
                'project_id'   => 'required|exists:mandate_projects,id',
                'applicants'   => 'required|array|min:1',
                'applicants.*.type' => 'required|in:primary,co',
                'applicants.*.first_name' => 'required|string|max:100',
                'applicants.*.mobile' => 'required|digits:10',
                'finance.unit_value' => 'required|numeric|min:1',
                'payments' => 'required|array|min:1',
                'payments.*.amount' => 'required|numeric|min:1',
                'payments.*.mode'   => 'required|in:UPI,Card,NetBanking,Cheque,Cash',
            ]);

            if ($request->booking_source === 'Channel Partner') {
                $request->validate([
                    'channel_partner_id' => 'required|exists:channel_partners,id'
                ]);
            }

            /**
             * STEP 2: UPDATE BOOKING MASTER
             */
            $booking = DB::table('mandate_bookings')->where('id', $id)->first();
            $closingManagerId  = $request->filled('closing_manager_id')
                ? (int) $request->closing_manager_id
                : null;

            $presalesId = $request->filled('presales_id')
                ? (int) $request->presales_id
                : null;

            $sourcingManagerId = $request->filled('sourcing_manager_id')
                ? (int) $request->sourcing_manager_id
                : null;
            DB::table('mandate_bookings')->where('id', $id)->update([
                'booking_date' => $request->booking_date,
                'project_id'   => $request->project_id,
                'booking_source' => $request->booking_source,
                'channel_partner_id' => $request->channel_partner_id,
                'closing_manager_id'  => $closingManagerId,
                'presales_id'         => $presalesId,
                'sourcing_manager_id' => $sourcingManagerId,

                // ✅ CREATED BY (logged-in user)
                'created_by' => auth()->id(),
                'updated_at' => now(),
            ]);
 
            /**
             * STEP 3: FINANCE
             */
            DB::table('mandate_booking_finances')->where('booking_id', $id)->delete();

            $finance = $request->finance;

            $agreementValue =
                ($finance['unit_value'] ?? 0) +
                ($finance['other_charges'] ?? 0) +
                ($finance['car_park_charges'] ?? 0);

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
             * STEP 4: PAYMENTS
             */
            DB::table('mandate_booking_payments')->where('booking_id', $id)->delete();

            foreach ($request->payments as $payment) {
                DB::table('mandate_booking_payments')->insert([
                    'booking_id' => $id,
                    'amount' => $payment['amount'],
                    'mode' => $payment['mode'],
                    'date' => $payment['date'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /**
             * STEP 5: CALCULATE PAYMENT %
             */
            $totalPaid = DB::table('mandate_booking_payments')
                ->where('booking_id', $id)
                ->sum('amount');

            $paymentPercent = $agreementValue > 0
                ? round(($totalPaid / $agreementValue) * 100, 2)
                : 0;

            /**
             * STEP 6: ELIGIBILITY CHECK
             */
            $project = MandateProject::with('ladders')->findOrFail($request->project_id);

            $isEligible = false;
            $scenario = null;
            $reason = 'Not eligible yet';

            if (
                $paymentPercent >= (float) $project->threshold_percentage &&
                $request->boolean('is_registered')
            ) {
                $isEligible = true;
                $scenario = 'SCENARIO_1';
                $reason = 'Threshold payment completed & registration done';
            } elseif (
                $paymentPercent >= (float) ($finance['current_due_percent'] ?? 0)
            ) {
                $isEligible = true;
                $scenario = 'SCENARIO_2';
                $reason = 'Current due payment completed';
            }

            /**
             * STEP 7: TOTAL BROKERAGE (PROJECT LEVEL)
             */
            $projectTotalPercent = (float) $project->brokerage;

            /**
             * STEP 8: CP BROKERAGE FROM LADDER
             */
            $cpPercent = 0;
            $bookingDate = \Carbon\Carbon::parse($request->booking_date);

            foreach ($project->ladders as $ladder) {
                if (
                    $bookingDate->between(
                        \Carbon\Carbon::parse($ladder->timeline_from),
                        \Carbon\Carbon::parse($ladder->timeline_to)
                    )
                ) {
                    $cpPercent = (float) $ladder->payout_percentage;
                    break;
                }
            }

            $cpPercent = min($cpPercent, $projectTotalPercent);

            /**
             * STEP 9: BROKERAGE BASE AMOUNT
             */
            $brokerageBaseAmount = match ($project->brokerage_criteria) {
                'AV' => $agreementValue,
                'UCV_OCC' => $finance['unit_value'],
                'UCV_CPC' => $finance['unit_value'] + ($finance['car_park_charges'] ?? 0),
                default => $agreementValue,
            };

            /**
             * STEP 10: TOTAL BROKERAGE AMOUNT
             */
            $totalBrokerageAmount = round(
                ($brokerageBaseAmount * $projectTotalPercent) / 100,
                2
            );
            
            /**
             * STEP 11: SAVE BOOKING BROKERAGE (TOTAL)
             */
            MandateBookingBrokerage::updateOrCreate(
                ['booking_id' => $id],
                [
                    'agreement_value' => $agreementValue,
                    'total_paid' => $totalPaid,
                    'payment_percent' => $paymentPercent,

                    // TOTAL brokerage only
                    'brokerage_percent' => $projectTotalPercent,
                    'brokerage_amount'  => $totalBrokerageAmount,

                    'threshold_percentage' => $project->threshold_percentage,
                    'current_due_percentage' => $finance['current_due_percent'] ?? 0,
                    'is_registered' => $request->boolean('is_registered'),
                    'is_eligible' => $isEligible,
                    'eligibility_scenario' => $scenario,
                    'eligibility_reason' => $reason,
                    'status' => $isEligible ? 'approved' : 'pending',
                    'eligible_at' => $isEligible ? now() : null,
                ]
            );

            /**
             * STEP 12: CREATE LEDGER (ONLY ONCE)
             */
            // if ($isEligible) {
            //     app(\App\Services\BrokerageLedgerService::class)
            //         ->createInitialLedger($id);
            // }
            if ($isEligible) {
                 
                $booking = MandateBooking::with('brokerage')->findOrFail($id);

                app(\App\Services\BrokerageLedgerService::class)
                    ->handleEligibilityAndLadder($booking);
            }
            
            DB::commit();

            return redirect()
                ->route('mandate_bookings.index')
                ->with('success', 'Booking updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
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
