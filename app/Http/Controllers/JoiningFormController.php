<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoiningFormRequest;
use App\Mail\IndentApprovalEmail;
use App\Models\Candidate;
use App\Models\JoiningForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class JoiningFormController extends Controller
{
    public function create($id)
    {
        $id = base64_decode($id);
        $joiningForm = JoiningForm::where('candidate_id', $id)->first();
        if (!empty($joiningForm)) {
            return redirect(route('joining_form.already_responded'));
        }
        //Mail::to(['mita.chavan@homebazaar.com', 'vrushali.bangar@homebazaar.com', 'sanjay.more@homebazaar.com'])->send(new IndentApprovalEmail([]));
        return view('joining_form.create', compact('id'));
    }

    public function store(JoiningFormRequest $request)
    {
        $data = $request->only([
            'candidate_id',
            'joining_date',
            'designation',
            'first_name',
            'last_name',
            'middle_name',
            'present_address',
            'permanent_address',
            'mobile',
            'email',
            'dob',
            'marital_status',
            'pan_number',
            'blood_group',
            'aadhar_number',
            'gender',
            'emergency_contact_name',
            'emergency_contact_relation',
            'emergency_contact_number',
            'bank_name',
            'branch_name',
            'account_number',
            'ifsc',
            'educational_details',
            'organizational_details',
            'family_details',
            'professional_details',
            'suffered_from_disease',
            'practitioner_details',
            'convicted_in_law'
        ]);
        $data['educational_details'] = json_encode($data['educational_details']);
        $data['organizational_details'] = json_encode($data['organizational_details']);
        $data['family_details'] = json_encode($data['family_details']);
        $data['professional_details'] = json_encode($data['professional_details']);

        $joiningForm = JoiningForm::create($data);

        //profile photo
        if ($request->has('photo')) {
            $joiningForm->photo = uploadFile($request->file('photo'), config('uploadfilepath.CANDIDATE_PROFILE_PHOTO'));
            $joiningForm->save();
        }

        $candidate = Candidate::find($request->input('candidate_id'));
        $candidate->status = 'submitted';
        $candidate->save();

        return redirect(route('joining_form.thank_you'));
    }

    public function show($id)
    {
        $joiningForm = JoiningForm::where('candidate_id', $id)->latest()->first();
        return view('joining_form.show', compact('joiningForm'));
    }

    public function thankYou()
    {
        return view('joining_form.thank_you');
    }

    public function alreadyResponded()
    {
        return view('joining_form.already_responded');
    }

    public function edit($candidateId)
    {
        $joiningForm = JoiningForm::where('candidate_id', $candidateId)->latest()->first();
        return view('joining_form.edit', compact('joiningForm'));
    }

    public function update(JoiningFormRequest $request, $id)
    {
        $data = $request->only([
            'candidate_id',
            'joining_date',
            'designation',
            'first_name',
            'last_name',
            'middle_name',
            'present_address',
            'permanent_address',
            'mobile',
            'email',
            'dob',
            'marital_status',
            'pan_number',
            'blood_group',
            'aadhar_number',
            'gender',
            'emergency_contact_name',
            'emergency_contact_relation',
            'emergency_contact_number',
            'bank_name',
            'branch_name',
            'account_number',
            'ifsc',
            'educational_details',
            'organizational_details',
            'family_details',
            'professional_details',
            'suffered_from_disease',
            'practitioner_details',
            'convicted_in_law'
        ]);
        $data['educational_details'] = json_encode($data['educational_details']);
        $data['organizational_details'] = json_encode($data['organizational_details']);
        $data['family_details'] = json_encode($data['family_details']);
        $data['professional_details'] = json_encode($data['professional_details']);

        JoiningForm::where('id', $id)->update($data);

        $joiningForm = JoiningForm::find($id);

        //profile photo
        if ($request->has('photo')) {
            if ($joiningForm->photo != "") {
                unlink(storage_path('app/'.$joiningForm->photo));
            }
            $joiningForm->photo = uploadFile($request->file('photo'), config('uploadfilepath.CANDIDATE_PROFILE_PHOTO'));
            $joiningForm->save();
        }
        return redirect(route('candidate.index'))->with("success", "Candidate Joining Form Updated Successfully");
    }

    public function downloadPdf($id)
    {
        $pdf = true;
        $joiningForm = JoiningForm::where('candidate_id', $id)->latest()->first();
        $pdf = PDF::loadView('joining_form.show', compact('joiningForm', 'pdf'));
        $pdf->setOptions(['isHTML5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $fileName = 'joining_form_'.str_replace(" ", "-", $joiningForm->first_name.'-'.$joiningForm->last_name).'.pdf';
        $pdf->save(storage_path("app/temp/".$fileName));
        $pdfMerger = PDFMerger::init();
        $pdfMerger->addPDF(storage_path("app/temp/".$fileName));
        $pdfMerger->addPDF(storage_path('app/3.PF-Form2-Revised.pdf'));
        $pdfMerger->addPDF(storage_path('app/4PFForm.pdf'));
        $pdfMerger->addPDF(storage_path('app/5.PolicyManual.pdf'));
        $pdfMerger->addPDF(storage_path('app/5.PolicyManual.pdf'));
        $pdfMerger->addPDF(storage_path('app/6.pdf'));
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/temp/".$fileName));
        return response()->download(storage_path("app/temp/".$fileName))->deleteFileAfterSend(true);
    }
}
