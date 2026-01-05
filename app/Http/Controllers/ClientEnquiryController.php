<?php

namespace App\Http\Controllers;

use App\Models\ClientEnquiry;
use App\Models\ChannelPartner;
use App\Models\MandateProject;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreClientEnquiryRequest;
use App\Traits\UserHierarchyTrait;

class ClientEnquiryController extends Controller
{
    use UserHierarchyTrait;

    public function __construct()
    {
        $this->middleware('permission:client-enquiry-view', ['only' => ['index']]);
        $this->middleware('permission:client-enquiry-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:client-enquiry-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:client-enquiry-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // ================= AJAX (DataTable) =================
        if ($request->ajax()) {

            $user = auth()->user();

            if ($user->hasRole('Superadmin')) {
                $users = User::select('id', 'name')->get();
            } else {
                $accessibleUserIds = $this->getAccessibleUserIds($user);

                $users = User::whereIn('id', $accessibleUserIds)
                    ->select('id', 'name')
                    ->get();
            }


            // Base query
            $query = ClientEnquiry::with([
                'mandateProject', // ✅ ADD THIS
                'latestUpdate',
                'updates',
                'channelPartner',
                'sourcingManager',
                'closingManager'
            ]);

            // 🔒 Role-based visibility (only if NOT Superadmin)
            if (!$user->hasRole('Superadmin')) {

                $accessibleUserIds = $this->getAccessibleUserIds($user);

                $query->where(function ($q) use ($accessibleUserIds) {
                    $q->whereIn('sourcing_manager_id', $accessibleUserIds)
                    ->orWhereIn('closing_manager_id', $accessibleUserIds);
                });
            }

            // ================= Filters =================
            if ($request->filled('source_of_visit')) {
                $query->where('source_of_visit', $request->source_of_visit);
            }

            if ($request->filled('channel_partner_id')) {
                $query->where('channel_partner_id', $request->channel_partner_id);
            }

            if ($request->filled('sourcing_manager_id')) {
                $query->where('sourcing_manager_id', $request->sourcing_manager_id);
            }

            if ($request->filled('closing_manager_id')) {
                $query->where('closing_manager_id', $request->closing_manager_id);
            }
            if ($request->filled('mandate_project_id')) {
                $query->where('mandate_project_id', $request->mandate_project_id);
            }
            // ================= DataTable Response =================
            return DataTables::of($query)

                ->addColumn('id', fn ($row) => $row->id)
                ->addColumn('project_name', function ($row) {
                    return optional($row->mandateProject)->project_name ?? '-';
                })
                ->addColumn('customer_name', fn ($row) => $row->customer_name)
                ->addColumn('contact_no', fn ($row) => $row->contact_no)
                ->addColumn('property_type', fn ($row) => ucfirst($row->property_type))
                ->addColumn('purchase_purpose', fn ($row) => $row->purchase_purpose ?? '-')
                ->addColumn('funding_source', fn ($row) => $row->funding_source ?? '-')
                ->addColumn('source_of_visit', fn ($row) => $row->source_of_visit ?? '-')
                ->addColumn('channel_partner', fn ($row) => $row->channelPartner->firm_name ?? '-')
                ->addColumn('sourcing_manager', fn ($row) => $row->sourcingManager->name ?? '-')
                ->addColumn('closing_manager', fn ($row) => $row->closingManager->name ?? '-')
                ->addColumn('created_at', fn ($row) => $row->created_at->format('d-m-Y H:i'))

                ->addColumn('feedback', fn ($row) => $row->feedback ?? '-')

                ->addColumn('latest_update', function ($row) {
                    $latest = $row->latestUpdate;

                    if (!$latest) {
                        return '<span class="badge bg-secondary">No Updates</span>';
                    }

                    return '
                        <strong>Status:</strong> ' . ucfirst(str_replace('_', ' ', $latest->status)) . '<br>
                        <strong>Feedback:</strong> ' . $latest->feedback . '<br>
                        <strong>Total:</strong> ' . $row->updates->count() . ' updates<br>
                        <small class="text-muted">
                            Updated: ' . $latest->created_at->format('d M Y, h:i A') . '
                        </small>
                    ';
                })

                ->addColumn('history', function ($row) {
                    return '
                        <button 
                            class="btn btn-sm btn-outline-primary"
                            onclick="openHistory('.$row->id.')"
                        >
                            View ('.$row->updates->count().')
                        </button>
                    ';
                })

                ->addColumn('action', function ($row) {

                    $actions = '';
                    
                    if (auth()->user()->can('client-enquiry-edit')) {
                        $actions = '<a class="dropdown-item" href="'.route('client-enquiries.show', $row->id).'">
                            <i class="bx bx-show me-1"></i> View
                        </a>';
                    }
                    if (auth()->user()->can('client-enquiry-edit')) {
                        $actions .= '<a class="dropdown-item" href="' . route('client-enquiries.edit', $row->id) . '">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>';
                    }

                    if (auth()->user()->can('client-enquiry-update')) {
                        $actions .= '<a class="dropdown-item" href="' . route('client-enquiries.updates', $row->id) . '">
                            <i class="bx bx-refresh me-1"></i> Update History
                        </a>';
                    }

                    if (auth()->user()->can('client-enquiry-delete')) {
                        $actions .= '
                            <button class="dropdown-item" onclick="deleteClientEnquiry(' . $row->id . ')">
                                <i class="bx bx-trash me-1"></i> Delete
                            </button>
                            <form id="delete-form-' . $row->id . '" 
                                action="' . route('client-enquiries.destroy', $row->id) . '" 
                                method="POST" class="d-none">
                                ' . csrf_field() . method_field('DELETE') . '
                            </form>';
                    }

                    return '
                        <div class="dropdown">
                            <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">' . $actions . '</div>
                        </div>';
                })

                ->rawColumns(['latest_update', 'history', 'action'])
                ->make(true);
        }

        // ================= NORMAL VIEW LOAD =================
        $channelPartners = ChannelPartner::select('id', 'firm_name')->get();
        //$users = User::select('id', 'name')->get();
        $projects = MandateProject::where('status', 1)->get(['id', 'project_name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding',
            'Mailers/SMS','Online Ad','Call Center','Walk in','Exhibition',
            'Insert','Existing Client','Property Portal'
        ];

        return view('client_enquiries.index', compact(
            'channelPartners',
            'users',
            'sources','projects'
        ));
    }

    public function history($id)
    {
        $enquiry = ClientEnquiry::with('updates')->findOrFail($id);

        return view('client_enquiries.partials.history', compact('enquiry'));
    }

    /**
     * Get all accessible user IDs for the current user (hierarchy-aware)
     */
    // private function getAccessibleUserIds($user)
    // {
    //     // ✅ Superadmin sees all users
    //     if ($user->hasRole('Superadmin')) {
    //         return \App\Models\User::pluck('id')->toArray();
    //     }

    //     $ids = [$user->id];

    //     // Get all users under hierarchy
    //     $teamIds = \App\Models\User::whereNotNull('reporting_manager_id')
    //         ->get()
    //         ->filter(function ($u) use ($user) {
    //             return $this->isUnderHierarchy($u, $user->id);
    //         })
    //         ->pluck('id')
    //         ->toArray();

    //     return array_merge($ids, $teamIds);
    // }


    /**
     * Check if a user is under the hierarchy of a given manager
     */
    private function isUnderHierarchy($user, $managerId)
    {
        $current = $user;
        while ($current->reporting_manager_id) {
            if ($current->reporting_manager_id == $managerId) {
                return true;
            }
            $current = \App\Models\User::find($current->reporting_manager_id);
            if (!$current) break;
        }
        return false;
    }



    public function create()
    {
        $channelPartners = ChannelPartner::all(['id', 'firm_name']);
         $managers =  \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('code', 'AI');
        })->get(['id', 'name']);
        $projects = MandateProject::where('status', 1)->get(['id', 'project_name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding','Mailers/SMS',
            'Online Ad','Call Center','Walk in','Exhibition','Insert','Existing Client','Property Portal'
        ];

        return view('client_enquiries.create', compact('channelPartners', 'managers', 'sources','projects'));
    }

    public function store(StoreClientEnquiryRequest $request)
    {
       // dd($request->all());
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['team_call_received'] = $request->team_call_received ?? 0;
        $data['source_of_visit'] = $request->source_of_visit ?? null;

        ClientEnquiry::create($data);
        
        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry added successfully.');
    }

    public function edit($id)
    {
        $clientEnquiry = ClientEnquiry::findOrFail($id);

        $channelPartners = ChannelPartner::all(['id', 'firm_name']);
         $managers =  \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('code', 'AI');
        })->get(['id', 'name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding','Mailers/SMS',
            'Online Ad','Call Center','Walk in','Exhibition','Insert','Existing Client','Property Portal'
        ];
        $projects = MandateProject::where('status', 1)->get(['id', 'project_name']);

        return view('client_enquiries.edit', compact('clientEnquiry', 'channelPartners', 'managers', 'sources','projects'));
    }

    public function update(StoreClientEnquiryRequest $request, $id)
    {
        $clientEnquiry = ClientEnquiry::findOrFail($id);
        $data = $request->validated();

        // Boolean fields
        $data['team_call_received'] = $request->boolean('team_call_received');

        // ✅ Store source_of_visit as plain string
        $data['source_of_visit'] = $request->source_of_visit ?? null;

        $clientEnquiry->update($data);

        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry updated successfully.');
    }

    public function show($id)
    {
        $clientEnquiry = ClientEnquiry::with([
            'channelPartner',
            'closingManager',
            'sourcingManager',
            'createdBy',
            'updates' => function ($q) {
                $q->latest(); // 👈 important
            },
        ])->findOrFail($id);

        return view('client_enquiries.show', compact('clientEnquiry'));
    }
    public function destroy($id)
    {
        ClientEnquiry::where('id', $id)->delete();
        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry deleted successfully.');
    }

    public function download($id)
    {
        $clientEnquiry = ClientEnquiry::with([
            'channelPartner',
            'closingManager',
            'sourcingManager',
            'presales',
            'createdBy',
             'updates.user' // ✅ LOAD UPDATES
        ])->findOrFail($id);

        $pdf = \PDF::loadView('client_enquiries.show_pdf', [
            'clientEnquiry' => $clientEnquiry
        ])->setPaper('a4', 'portrait');


        return $pdf->download('client-enquiry-'.$clientEnquiry->id.'.pdf');
    }
    
    // Show Step 1
    public function createPublicStep1()
    {
       // $managers = User::whereHas('roles', function($q){ /* optional filter */ })->get(['id','name']); // adjust as needed
        $managers =  \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('code', 'AI');
        })->get(['id', 'name']);
        $channelPartners = ChannelPartner::select('id','firm_name')->get();
        // If user already started, prefill from session
        $step1 = session('client_enquiry.step1', []);
        $projects = MandateProject::where('status', 1)->get(['id', 'project_name']);
        return view('client_enquiries.public_step1', compact('managers','channelPartners','step1','projects'));
    }

    // Save Step 1 into session and redirect to Step 2
    public function storePublicStep1(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'mandate_project_id' => 'required|string|max:255',
            'contact_no'    => 'required|string|max:20',
            'alternate_no'  => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'profession'    => 'nullable|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:1000',
            'pin_code'      => 'required|string|max:20',
            'residential_status' => 'nullable|in:India,NRI',
            'nri_country'   => 'nullable|required_if:residential_status,NRI|string|max:255',
            'property_type' => 'nullable|string|max:255',
            'budget'        => 'nullable|string|max:255',
            'purchase_purpose' => 'nullable|string|max:255',
            'funding_source'   => 'nullable|string|max:255',
            'presales_id'   => 'nullable|exists:users,id',
            'team_call_received' => 'nullable|in:0,1',
            'closing_manager_id' => 'nullable|exists:users,id',
            'feedback'      => 'nullable|string|max:2000',
        ]);

        // Save to session
        session(['client_enquiry.step1' => $validated]);

        return redirect()->route('client-enquiry.public.source');
    }

    // Show Step 2 (Source of Visit)
    public function createPublicSource()
    {
        $step1 = session('client_enquiry.step1');
        if (!$step1) {
            return redirect()->route('client-enquiry.public.create')
                ->with('error', 'Please fill the first step before proceeding.');
        }
         $managers =  \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('code', 'AI');
        })->get(['id', 'name']);
        $channelPartners = ChannelPartner::select('id','firm_name')->get();

        // allow prefill old values from session
        $step2 = session('client_enquiry.step2', []);
        return view('client_enquiries.public_source', compact('managers','channelPartners','step1','step2'));
    }

    // Final store: validate step2, merge with step1, create DB record
    public function storePublicSource(Request $request)
    {
        $step1 = session('client_enquiry.step1');
        if (!$step1) {
            return redirect()->route('client-enquiry.public.create')
                ->with('error', 'Session expired — please complete Step 1 again.');
        }

        $validated = $request->validate([
            'source_of_visit' => 'required|string',
            'reference_name'  => 'nullable|required_if:source_of_visit,Reference|string|max:255',
            'reference_contact' => 'nullable|required_if:source_of_visit,Reference|string|max:20',
            'channel_partner_id' => 'nullable|required_if:source_of_visit,Channel Partner|exists:channel_partners,id',
            'sourcing_manager_id' => 'nullable|required_if:source_of_visit,Channel Partner|exists:users,id',
            'remarks' => 'nullable|string|max:2000'
        ]);

        // merge data
        $data = array_merge($step1, $validated);

        // create record (adjust column names & model fillable accordingly)
        ClientEnquiry::create($data);

        // clear session
        session()->forget('client_enquiry');

        return redirect()->route('client-enquiry.public.create')
            ->with('success', 'Thank you — your enquiry has been submitted.');
    }

}
