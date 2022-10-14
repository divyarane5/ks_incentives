<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // $data['users'] = User::orderBy('id','desc')->paginate(5);
        // return view('users.index', $data);
        if ($request->ajax()) {
            $data = User::latest();
            return Datatables::of($data)
                ->addColumn('employee_code', function ($row) {
                    return $row->employee_code;
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('designation', function ($row) {
                    return $row->designation;
                })
                ->addColumn('department', function ($row) {
                    return $row->department;
                })
                ->addColumn('location', function ($row) {
                    return $row->locaion;
                })
                ->addColumn('entity', function ($row) {
                    return $row->entity;
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('role-edit')) {
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a
                                    >';
                    }

                    if (auth()->user()->can('role-edit')) {
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);"
                                        ><i class="bx bx-trash me-1"></i> Delete</a
                                    >';
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
        return view('users.index');
    }
    public function create()
    {
        return view('users.create');
    }
    public function account()
    {
        return view('users.account');
    }

    public function updateProfile(UserProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->dob = $request->input('dob');
        $user->gender = $request->input('gender');
        $user->save();

        //profile photo
        if ($request->has('photo')) {
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('account')->with('success', 'Profile Updated Successfully');
    }

}
