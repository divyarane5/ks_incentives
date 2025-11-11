<?php

namespace App\Http\Controllers;

use App\Models\ClientEnquiry;
use App\Models\ChannelPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreClientEnquiryRequest;

class ClientEnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:client-enquiry-view', ['only' => ['index']]);
        $this->middleware('permission:client-enquiry-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:client-enquiry-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:client-enquiry-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientEnquiry::with(['channelPartner', 'closingManager']);

            return DataTables::of($data)
                ->addColumn('id', fn($row) => $row->id)
                ->addColumn('customer_name', fn($row) => $row->customer_name)
                ->addColumn('contact_no', fn($row) => $row->contact_no)
                ->addColumn('property_type', fn($row) => $row->property_type)
                ->addColumn('purchase_purpose', fn($row) => $row->purchase_purpose)
                ->addColumn('funding_source', fn($row) => $row->funding_source)
                ->addColumn('source_of_visit', fn($row) => is_array($row->source_of_visit) ? implode(', ', $row->source_of_visit) : $row->source_of_visit)
                ->addColumn('channel_partner', fn($row) => $row->channelPartner?->firm_name ?? '-')
                ->addColumn('closing_manager', fn($row) => $row->closingManager?->name ?? '-')
                ->addColumn('created_at', fn($row) => date("d-m-Y", strtotime($row->created_at)))
                ->addColumn('action', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('client-enquiries-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('client-enquiries.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>';
                    }

                    if (auth()->user()->can('client-enquiries-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteEnquiry('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                    <form id="delete-form-'.$row->id.'" action="'.route('client-enquiries.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().method_field('DELETE').'
                                    </form>';
                    }

                    if($actions) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">'.$actions.'</div>
                                </div>';
                    }

                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('client_enquiries.index');
    }



    public function create()
    {
        $channelPartners = ChannelPartner::all(['id', 'firm_name']);
        $managers = User::all(['id', 'name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding','Mailers/SMS',
            'Online Ad','Call Center','Walk in','Exhibition','Insert','Existing Client','Property Portal'
        ];

        return view('client_enquiries.create', compact('channelPartners', 'managers', 'sources'));
    }

    public function store(StoreClientEnquiryRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['team_call_received'] = $request->boolean('team_call_received');
        $data['source_of_visit'] = $request->source_of_visit ? json_encode($request->source_of_visit) : null;

        ClientEnquiry::create($data);

        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry added successfully.');
    }

    public function edit($id)
    {
        $clientEnquiry = ClientEnquiry::findOrFail($id);
        $channelPartners = ChannelPartner::all(['id', 'firm_name']);
        $managers = User::all(['id', 'name']);
        $sources = [
            'Reference','Channel Partner','Website','News','Paper Ad','Hoarding','Mailers/SMS',
            'Online Ad','Call Center','Walk in','Exhibition','Insert','Existing Client','Property Portal'
        ];

        $clientEnquiry->source_of_visit = json_decode($clientEnquiry->source_of_visit, true);

        return view('client_enquiries.edit', compact('clientEnquiry', 'channelPartners', 'managers', 'sources'));
    }

    public function update(StoreClientEnquiryRequest $request, $id)
    {
        $clientEnquiry = ClientEnquiry::findOrFail($id);
        $data = $request->validated();
        $data['team_call_received'] = $request->boolean('team_call_received');
        $data['source_of_visit'] = $request->source_of_visit ? json_encode($request->source_of_visit) : null;

        $clientEnquiry->update($data);

        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry updated successfully.');
    }

    public function destroy($id)
    {
        ClientEnquiry::where('id', $id)->delete();
        return redirect()->route('client-enquiries.index')
            ->with('success', 'Client Enquiry deleted successfully.');
    }
}
