<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Template;
use App\Models\User;
use App\Http\Requests\ClientRequest;
use DataTables;
use Illuminate\Http\Request;
use App\Models\ClientReference;
use App\Models\ReferralClient;

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
                // ->addColumn('template_name', function ($row) {
                //     return $row->template_name;
                // })
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
                        $actions .= '<a class="dropdown-item" target=”_blank”  href="'.route('client.show', $row->id).'"
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
        $template = Template::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('client.edit', compact('id', 'client','template'));
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

    public function reference($id)
    {
       
        $client=$this->_Client->getAllData($id);
        
        // $user = User::find($client->u_id);
        // print_r($user); exit;
        return view('client.reference', compact('id', 'client'));
        //return view('client.show', ['client' => $client]);

    }
    public function rthankyou(ClientRequest $request)
    {
       //echo $client_name1; 
      
        $rclient = new ReferralClient();
        $rclient->name = $request->input('name_inquiry');
        $rclient->email = $request->input('email_inquiry');
        $rclient->mobile = $request->input('mobileno');
        $rclient->userid = $request->input('userid');
        $rclient->form_type = $request->input('referrals');
        $rclient->save();
        
        if(!empty($request->input('client_name1'))){
            $crclient = new ClientReference();
            $crclient->referral_client_id = $rclient->id;
            $crclient->client_name = $request->input('client_name1');
            $crclient->client_mobile = $request->input('client_mobile1');
            $crclient->client_email = $request->input('client_email1');
            $crclient->save();
        }
        if(!empty($request->input('client_name2'))){
            $crclient = new ClientReference();
            $crclient->referral_client_id = $rclient->id;
            $crclient->client_name = $request->input('client_name2');
            $crclient->client_mobile = $request->input('client_mobile2');
            $crclient->client_email = $request->input('client_email2');
            $crclient->save();
        }
        if(!empty($request->input('client_name3'))){
            $crclient = new ClientReference();
            $crclient->referral_client_id = $rclient->id;
            $crclient->client_name = $request->input('client_name3');
            $crclient->client_mobile = $request->input('client_mobile3');
            $crclient->client_email = $request->input('client_email3');
            $crclient->save();
        }
        if(!empty($request->input('client_name4'))){
            $crclient = new ClientReference();
            $crclient->referral_client_id = $rclient->id;
            $crclient->client_name = $request->input('client_name4');
            $crclient->client_mobile = $request->input('client_mobile4');
            $crclient->client_email = $request->input('client_email4');
            $crclient->save();
        }
        if(!empty($request->input('client_name5'))){
            $crclient = new ClientReference();
            $crclient->referral_client_id = $rclient->id;
            $crclient->client_name = $request->input('client_name5');
            $crclient->client_mobile = $request->input('client_mobile5');
            $crclient->client_email = $request->input('client_email5');
            $crclient->save();
        }
        $name = $request->input('name_inquiry');
      return view('client.rthankyou',compact('name'));
     //  return redirect()->route('client.rthankyou')->with('success', 'abcd');

    }
    
    public function service($id,$sname)
    {
      // echo $id; echo $sname; exit;
        $client=$this->_Client->getAllData($id);
        
        // $user = User::find($client->u_id);
        // print_r($user); exit;
        return view('client.service', compact('id', 'client','sname'));
        //return view('client.show', ['client' => $client]);

    }
    
    public function sthankyou(ClientRequest $request)
    {
       //echo $client_name1; 
      
       $rclient = new ReferralClient();
       $rclient->name = $request->input('name_inquiry');
       $rclient->email = $request->input('email_inquiry');
       $rclient->mobile = $request->input('mobileno');
       $rclient->userid = $request->input('userid');
       $rclient->form_type = $request->input('form_type');
       $rclient->loanamount = $request->input('loanamount');
       $rclient->preferredbank = $request->input('preferredbank');
       $rclient->remarks = $request->input('remarks');
       $rclient->address = $request->input('address');
       $rclient->assistance = $request->input('assistance');
       $rclient->save();
       
        $name = $request->input('name_inquiry');
      return view('client.thankyou',compact('name'));
     //  return redirect()->route('client.rthankyou')->with('success', 'abcd');

    }

        
        
}
