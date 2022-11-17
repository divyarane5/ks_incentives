<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReimbursementRequest;
use App\Interfaces\ReimbursementRepositoryInterface;
use App\Models\Reimbursement;
use App\Models\ReimbursementLog;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class ReimbursementController extends Controller
{
    private $reimbursementRepository;

    function __construct(ReimbursementRepositoryInterface $reimbursementRepository)
    {
        $this->middleware('permission:reimbursement-view-all|reimbursement-view-own|reimbursement-approval', ['only' => ['index']]);
        $this->middleware('permission:reimbursement-create', ['only' => ['create','store']]);
        $this->middleware('permission:reimbursement-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:reimbursement-delete', ['only' => ['destroy']]);
        $this->reimbursementRepository = $reimbursementRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reimbursements = Reimbursement::select(['reimbursements.*', 'attended_of.name as visit_attended_of_user', 'created_by_user.name as visit_attended_by', 'created_by_user.reporting_user_id'])
                                ->join('users as attended_of', 'reimbursements.visit_attended_of_id', '=', 'attended_of.id')
                                ->join('users as created_by_user', 'reimbursements.created_by', '=', 'created_by_user.id');
            if (!auth()->user()->can('reimbursement-view-all') && auth()->user()->can('reimbursement-view-own')) {
                $reimbursements = $reimbursements->where(function($query) {
                    $query = $query->where('reimbursements.created_by', auth()->user()->id)
                        ->orWhere('created_by_user.reporting_user_id', auth()->user()->id);
                    if (auth()->user()->can('reimbursement-settlement')) {
                        $query = $query->orWhere('reimbursements.status', 'approved');
                    }
                });
            }

            return DataTables::of($reimbursements)
                ->addColumn("approval", function ($row) {
                    if (($row->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin')) && $row->status == "pending") {
                        return '<div class="form-check mt-3">
                                                    <input class="form-check-input reimbursement_approval" name="reimbursement_approval[]" type="checkbox" value="'.$row->id.'">
                                                </div>';
                    }
                    return '';
                })
                ->addColumn("id", function ($row) {
                    return $row->reimbursement_code;
                })
                ->addColumn('client_name', function ($row) {
                    return $row->client_name;
                })
                ->addColumn('project_name', function ($row) {
                    return $row->project_name;
                })
                ->addColumn('visit_attended_of_user', function ($row) {
                    return $row->visit_attended_of_user;
                })
                ->addColumn('source', function ($row) {
                    return $row->source;
                })
                ->addColumn('destination', function ($row) {
                    return $row->destination;
                })
                ->addColumn('amount', function ($row) {
                    return $row->amount;
                })
                ->addColumn('status', function ($row) {
                    $str = "";
                    if (auth()->user()->can('reimbursement-settlement') && $row->status == "approved") {
                        $str .= '<div class="btn-group">
                                    <button type="button" class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Approved
                                    </button>
                                    <ul class="dropdown-menu p-0" style="transform: translate3d(0px, 40.8px, 0px);">
                                        <li><a class="bg-label-success dropdown-item" href="javascript:void(0);" onclick="updateStatus('.$row->id.', \'settled\')">Settled</a></li>
                                    </ul>
                                </div>';
                    } else if (($row->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin')) && $row->status == "pending" ) {
                        $str .= '<div class="btn-group">
                                    <button type="button" class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Pending
                                    </button>
                                    <ul class="dropdown-menu p-0" style="transform: translate3d(0px, 40.8px, 0px);">
                                        <li><a class="bg-label-info dropdown-item" href="javascript:void(0);" onclick="updateStatus('.$row->id.', \'approved\')">Approve</a></li>
                                        <li><a class=" bg-label-danger dropdown-item" href="javascript:void(0);" onclick="updateStatus('.$row->id.', \'rejected\')">Reject</a></li>
                                    </ul>
                                </div>';
                    } else {
                        $statusClass = "bg-label-primary";
                        if ($row->status == "rejected") {
                            $statusClass = "bg-label-danger";
                        } else if ($row->status == "pending") {
                            $statusClass = "bg-label-warning";
                        } else if ($row->status == "settled") {
                            $statusClass = "bg-label-success";
                        } else if ($row->status == "approved") {
                            $statusClass = "bg-label-info";
                        }
                        $str .= '<span class="badge '.$statusClass.' me-1">'.config('constants.REIMBURSEMENT_STATUS')[$row->status].'</span>';
                    }
                    return $str;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<a class="dropdown-item" href="'.route('reimbursement.show', $row->id).'"
                    ><i class="bx bx-show me-1"></i> View</a>';

                    if (auth()->user()->can('reimbursement-edit') && !in_array($row->status, ['approved', 'rejected', 'settled'])) {
                        $actions .= '<a class="dropdown-item" href="'.route('reimbursement.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('reimbursement-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteReimbursement('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('reimbursement.destroy', $row->id).'" method="POST" class="d-none">
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
                ->rawColumns(['action', 'status', 'approval'])
                ->make(true);
        }
        return view('reimbursement.index');
    }

    public function create()
    {
        $users = User::select(['id', 'name'])->get();
        return view('reimbursement.create', compact('users'));
    }

    public function store(ReimbursementRequest $request)
    {
        $reimbursement = new Reimbursement();
        $reimbursement->client_name = $request->input('client_name');
        $reimbursement->project_name = $request->input('project_name');
        $reimbursement->visit_attended_of_id = $request->input('visit_attended_of_id');
        $reimbursement->source = $request->input('source');
        $reimbursement->destination = $request->input('destination');
        $reimbursement->transport_mode = $request->input('transport_mode');
        $reimbursement->amount = $request->input('amount');
        $reimbursement->comment = $request->input('comment');
        $reimbursement->save();

        //attachments
        if ($request->has('attachmentName') && $request->has('bill')) {
            $reimbursement->file_name = $request->input('attachmentName');
            $file = $request->file('bill');
            $reimbursement->file_path = uploadFile($file, config('uploadfilepath.REIMBURSEMENT_FILES'));
            $reimbursement->save();
        }
        return redirect()->route('reimbursement.index')->with('success', 'Reimbursement Added Successfully');
    }

    public function edit($id)
    {
        $users = User::select(['id', 'name'])->get();
        $reimbursement = Reimbursement::find($id);
        return view('reimbursement.edit', compact('users', 'reimbursement'));
    }

    public function update(ReimbursementRequest $request, $id)
    {
        $reimbursement = Reimbursement::find($id);
        $reimbursement->client_name = $request->input('client_name');
        $reimbursement->project_name = $request->input('project_name');
        $reimbursement->visit_attended_of_id = $request->input('visit_attended_of_id');
        $reimbursement->source = $request->input('source');
        $reimbursement->destination = $request->input('destination');
        $reimbursement->transport_mode = $request->input('transport_mode');
        $reimbursement->amount = $request->input('amount');
        $reimbursement->comment = $request->input('comment');
        $reimbursement->save();

        //attachments
        if ($request->has('attachmentName') && $request->has('bill')) {
            if ($reimbursement->file_path != "") {
                unlink(storage_path('app/'.$reimbursement->file_path));
            }
            $reimbursement->file_name = $request->input('attachmentName');
            $file = $request->file('bill');
            $reimbursement->file_path = uploadFile($file, config('uploadfilepath.REIMBURSEMENT_FILES'));
            $reimbursement->save();
        }
        return redirect()->route('reimbursement.index')->with('success', 'Reimbursement Updated Successfully');
    }

    public function show($id)
    {
        $reimbursement = Reimbursement::select(["reimbursements.*", "created_by_user.reporting_user_id"])
                            ->join('users as created_by_user', 'reimbursements.created_by', '=', 'created_by_user.id')
                            ->where('reimbursements.id', $id)
                            ->first();
        $reimbursementLog = ReimbursementLog::where('reimbursement_id', $id)->orderBy('id', 'desc')->get();
        return view('reimbursement.show', compact('reimbursement', 'reimbursementLog'));
    }

    public function destroy($id)
    {
        Reimbursement::where('id', $id)->delete();
        return redirect()->route('reimbursement.index')->with('success', 'Reimbursement Deleted Successfully');
    }

    public function updateReimbursementStatus(Request $request)
    {
        $reimbursementId = $request->input('reimbursement_id');
        $status = $request->input('status');
        $reimbursement = Reimbursement::find($reimbursementId);
        $this->reimbursementRepository->updateReimbursementStatus($reimbursement, $status, $request);
    }

    public function updateBulkReimbursementStatus(Request $request)
    {
        $status = $request->input('status');
        $ids = $request->input('reimbursement_approval');
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $reimbursement = Reimbursement::find($id);
                $this->reimbursementRepository->updateReimbursementStatus($reimbursement, $status, $request);
            }
        }
    }

}
