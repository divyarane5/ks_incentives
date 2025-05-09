<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Template;
use App\Http\Requests\ClientRequest;
use App\Interfaces\ClientRepositoryInterface;
use DataTables;
use Illuminate\Http\Request;
use App\Models\ClientReference;
use App\Models\ReferralClient;
use App\Mail\ReferralMail;
use Illuminate\Support\Facades\Mail;
use Auth;
use Exception;
class ClientController extends Controller
{
    private $clientRepository;
    function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->middleware('permission:referral-client-view', ['only' => ['index']]);
        $this->middleware('permission:referral-client-create', ['only' => ['create','store']]);
        $this->middleware('permission:referral-client-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:referral-client-delete', ['only' => ['destroy']]);
        $this->clientRepository = $clientRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::select(['clients.*', 'templates.name as template_name','users.reporting_user_id'])->join('templates', 'clients.template_id', '=', 'templates.id')->join('users', 'clients.created_by', '=', 'users.id')->where('clients.created_by','=',Auth::id())->orWhere('users.reporting_user_id','=',Auth::id())->orderBy('clients.id','desc');
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
                ->addColumn('status', function ($row) {
                    if($row->click == 1){
                        return '<span class="badge bg-label-success me-1">Read</span>';
                    }else{
                        return '<span class="badge bg-label-warning me-1">Unread</span>';
                    }
                    //return $click;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    $actions .= '<a class="dropdown-item" target=”_blank”  href="'.route('client.show', $row->id).'"
                                        ><i class="bx bx-show me-1"></i> View</a>';

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
                    if($row->click == 0){
                    if (auth()->user()->can('referral-client-send-email')) {
                        $actions .= '<a class="dropdown-item" target=”_blank”  href="'.url('send_referral_mail/'.$row->id).'"
                                        ><i class="bx bx-send me-1"></i> Send Email</a>';
                    }
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
                ->rawColumns(['action','status'])
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
    $api_key = '653d394114dbd2a366a4a3ccfee7d2b5c46384ed819787f150385ff32025';
    $emailToValidate = $request->input('client_email');
    $IPToValidate = '99.123.12.122';
    // use curl to make the request
    //$url = 'https://api.zerobounce.net/v2/validate?api_key='.$api_key.'&email='.urlencode($emailToValidate).'&ip_address='.urlencode($IPToValidate);
    $url = 'https://api.quickemailverification.com/v1/verify?email='.urlencode($emailToValidate).'&apikey='.urlencode($api_key);
   // echo $url; exit;
    $ch = curl_init($url);
    //PHP 5.5.19 and higher has support for TLS 1.2
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 150); 
    $response = curl_exec($ch);
    curl_close($ch);
    //echo '<pre>' ;
    $json = json_decode($response, true);
  //print_r($json); 
  //die;
   //     echo $json['status']; exit;
    if($json['result'] == 'valid'){
        //create client
        $client = new Client();
        $client->template_id = $request->input('template_id');
        $client->sales_person = $request->input('sales_person');
        $client->client_name = $request->input('client_name');
        $client->client_email = $request->input('client_email');
        $client->subject_name = $request->input('subject_name');
        $client->save();

        return redirect()->route('client.index')->with('success', 'Client Added Successfully');
    }else{
        //echo "ssss"; exit;
     //   echo "<p class='aaa'>Email address Invalid.</p>";
        echo "<p class='bbb'>Email address Invalid.</p>";
    }   
    }

    public function edit($id)
    {
        $client = Client::find($id);
        $template = Template::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('client.edit', compact('id', 'client','template'));
    }

    public function update(ClientRequest $request, $id)
    {
        $api_key = '653d394114dbd2a366a4a3ccfee7d2b5c46384ed819787f150385ff32025';
        $emailToValidate = $request->input('client_email');
        $IPToValidate = '99.123.12.122';
        // use curl to make the request
        //$url = 'https://api.zerobounce.net/v2/validate?api_key='.$api_key.'&email='.urlencode($emailToValidate).'&ip_address='.urlencode($IPToValidate);
        $url = 'https://api.quickemailverification.com/v1/verify?email='.urlencode($emailToValidate).'&apikey='.urlencode($api_key);
       // echo $url; exit;
        $ch = curl_init($url);
        //PHP 5.5.19 and higher has support for TLS 1.2
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 150); 
        $response = curl_exec($ch);
        curl_close($ch);
        //echo '<pre>' ;
        $json = json_decode($response, true);
      //print_r($json); 
      //die;
       //     echo $json['status']; exit;
        if($json['result'] == 'valid'){
        $client = Client::find($id);
        $client->template_id = $request->input('template_id');
        $client->sales_person = $request->input('sales_person');
        $client->client_name = $request->input('client_name');
        $client->client_email = $request->input('client_email');
        $client->subject_name = $request->input('subject_name');
        $client->save();

        return redirect()->route('client.index')->with('success', 'Client Updated Successfully');
        }else{
            //echo "ssss"; exit;
        //   echo "<p class='aaa'>Email address Invalid.</p>";
            echo "<p class='bbb'>Email address Invalid.</p>";
        }
    }

    public function destroy($id)
    {
        Client::where('id', $id)->delete();
        return redirect()->route('client.index')->with('success', 'Client Deleted Successfully');
    }

    public function show($id)
    {
        $client = $this->clientRepository->getClientDetails($id);
        return view('client.show', compact('id', 'client'));
    }

    public function click($id)
    {
        Client::where('id', $id)->update(['click' => 1]);
    }

    public function sendReferralMail($id)
    {
        try {
           // echo "ff"; exit;
            $client = $this->clientRepository->getClientDetails($id);
            $arr = [
                'id' => $id,
                'client' => $client
            ];
            Mail::to($client->client_email)->send(new ReferralMail($arr));// $client->client_email
            return redirect()->route('client.index')->with('success', 'Mail Sent Successfully');
        } catch (Exception $e) {
          //  echo "ss"; exit;
            \Log::emergency($e);
            return redirect()->route('client.index')->with('error', 'Failed to send email');
        }

    }

    public function reference($id)
    {
        $client = $this->clientRepository->getClientDetails($id);
        return view('client.reference', compact('id', 'client'));
    }
    public function rthankyou(ClientRequest $request)
    {
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

    }

    public function service($id,$sname)
    {
        $client = $this->clientRepository->getClientDetails($id);
        return view('client.service', compact('id', 'client','sname'));
    }

    public function sthankyou(ClientRequest $request)
    {
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
        return view('client.sthankyou',compact('name'));

    }
}
