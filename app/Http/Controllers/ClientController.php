<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Template;
use App\Http\Requests\ClientRequest;
use DataTables;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:referral-client-view', ['only' => ['index']]);
        $this->middleware('permission:referral-client-create', ['only' => ['create','store']]);
        $this->middleware('permission:referral-client-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:referral-client-delete', ['only' => ['destroy']]);
        $this->_Client=new Client();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::all();
            return DataTables::of($data)
                ->addColumn('template_name', function ($row) {
                    return $row->template_name;
                })
                ->addColumn('sales_person', function ($row) {
                    return $row->sales_person;
                })
                ->addColumn('client_name', function ($row) {
                    return $row->client_name;
                })
                ->addColumn('client_email', function ($row) {
                    return $row->client_email;
                })
                ->addColumn('subject_name', function ($row) {
                    return $row->subject_name;
                })
                ->addColumn('email_sent', function ($row) {
                    return $row->email_sent;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('referral-client-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('client.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('referral-client-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteClient('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('client.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
                    }
                    if (auth()->user()->can('referral-client-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('client.show', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> View</a>';
                    }
                    if (!empty($actions)) {
                        return '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                        '.$actions.'
                                        </div>
                                    </div>';
                    }
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('client.index');
    }

    public function create()
    {
        $template = Template::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('client.create', compact('template'));
    }

    public function store(ClientRequest $request)
    {
        //create location
        $client = new Client();
        $client->template_id = $request->input('template_id');
        $client->sales_person = $request->input('sales_person');
        $client->client_name = $request->input('client_name');
        $client->client_email = $request->input('client_email');
        $client->subject_name = $request->input('subject_name');
        $client->save();

        return redirect()->route('client.index')->with('success', 'Client Added Successfully');
    }

    public function edit($id)
    {
        $client = Client::find($id);
        return view('client.edit', compact('id', 'client'));
    }

    public function update(ClientRequest $request, $id)
    {
        $client = Client::find($id);
        $client->template_id = $request->input('template_id');
        $client->sales_person = $request->input('sales_person');
        $client->client_name = $request->input('client_name');
        $client->client_email = $request->input('client_email');
        $client->subject_name = $request->input('subject_name');
        $client->save();

        return redirect()->route('client.index')->with('success', 'Client Updated Successfully');
    }

    public function destroy($id)
    {
        Location::where('id', $id)->delete();
        return redirect()->route('client.index')->with('success', 'Client Deleted Successfully');
    }

    public function show($id)
    {
        $client=$this->_Client->getAllData($id);
       // $client = Client::find($id);
        return view('client.show', compact('id', 'client'));
        //return view('client.show', ['client' => $client]);

    }
    
}
