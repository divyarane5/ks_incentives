<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Mail\JoiningFormMail;
use App\Models\Candidate;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Exception;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Candidate::select(['candidates.*', 'users.name as created_by'])->join('users', 'candidates.created_by', '=', 'users.id');
            return DataTables::of($data)
                ->addColumn('created_by', function ($row) {
                    return $row->created_by;
                })
                ->addColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s', strtotime($row->created_at));
                })
                ->addColumn('status', function ($row) {
                    return config('constants.CANDIDATE_STATUS.'.$row->status);
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if ($row->status != "ready" && $row->status != "sent") {
                        $actions .= '<a target="_blank" class="dropdown-item" href="'.route('joining_form.show', $row->id).'"
                                        ><i class="bx bx-show me-1"></i> View</a>';
                        $actions .= '<a target="_blank" class="dropdown-item" href="'.route('joining_form.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit Joining Form</a>';
                    }

                    if ($row->status == "ready" && $row->status == "sent") {
                        $actions .= '<a class="dropdown-item" href="'.route('candidate.send_form', $row->id).'"
                                    ><i class="bx bx-send  me-1"></i> Send Joining Form</a>';
                    }

                    if ($row->status == "submitted") {
                        $actions .= '<a class="dropdown-item" href="'.route('candidate.change_status', [$row->id, 'approved']).'"
                                    ><i class="bx bxs-user-check  me-1"></i>Mark as Approved</a>';
                        $actions .= '<a class="dropdown-item" href="'.route('candidate.change_status', [$row->id, 'rejected']).'"
                                    ><i class="bx bxs-user-x  me-1"></i>Mark as Rejected</a>';
                    }

                    if ($row->status == "approved") {
                        $actions .= '<a class="dropdown-item" href="'.route('candidate.change_status', [$row->id, 'closed']).'"
                                    ><i class="bx bxs-user-check  me-1"></i>Mark as Closed</a>';
                    }

                    if (auth()->user()->can('candidate-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('candidate.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('candidate-delete')) {
                        $actions .= '<button type="button" class="dropdown-item" onclick="deleteCandidate('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('candidate.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
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
        return view('candidate.index');
    }

    public function create()
    {
        return view('candidate.create');
    }

    public function store(CandidateRequest $request)
    {
        $candidate = new Candidate();
        $candidate->name = $request->input('name');
        $candidate->email = $request->input('email');
        $candidate->phone = $request->input('phone');
        $candidate->designation = $request->input('designation');
        $candidate->joining_date = $request->input('joining_date');
        $candidate->entity = $request->input('entity');
        $candidate->save();
        return redirect()->route('candidate.index')->with('success', 'Candidate Added Successfully');
    }

    public function edit($id)
    {
        $candidate = Candidate::find($id);
        return view('candidate.edit', compact('id', 'candidate'));
    }

    public function update(CandidateRequest $request, $id)
    {
        $candidate = Candidate::find($id);
        $candidate->name = $request->input('name');
        $candidate->email = $request->input('email');
        $candidate->phone = $request->input('phone');
        $candidate->designation = $request->input('designation');
        $candidate->joining_date = $request->input('joining_date');
        $candidate->entity = $request->input('entity');
        $candidate->save();
        return redirect()->route('candidate.index')->with('success', 'Candidate Updated Successfully');
    }

    public function destroy($id)
    {
        Candidate::where('id', $id)->delete();
        return redirect()->route('candidate.index')->with('success', 'Candidate Deleted Successfully');
    }

    public function sendJoiningForm($id)
    {
        try {
            $candidate = Candidate::find($id);
            $encodedId = base64_encode($id);
            $url = route('joining_form.create', $encodedId);
            $candidate->url = $url;
            Mail::to('vrushali.bangar@homebazaar.com')->send(new JoiningFormMail(['candidate' => $candidate]));
            unset($candidate->url);
            if ($candidate->status == "ready") {
                $candidate->status = 'sent';
                $candidate->save();
            }
        } catch (Exception $e) {
            \Log::emergency($e);
            return redirect()->route('candidate.index')->with('error', 'Failed to send email');
        }
        return redirect()->route('candidate.index')->with('success', 'Email Sent Successfully');
    }

    public function changeStatus($id, $status)
    {
        $candidate = Candidate::find($id);
        $candidate->status = $status;
        $candidate->save();
        return redirect()->back()->with('success', 'Status Changed Successfully');
    }
}
