<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Imports\ImportUser;
use App\Imports\UpdateUserReporting;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    private $userRepository;

    function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('permission:user-view', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);

        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userRequest = $request->only(['entity', 'location_id', 'department_id', 'designation_id', 'role_id']);
            $users = $this->userRepository->getUsers($userRequest);
            return DataTables::of($users)
                ->addColumn('role', function ($row) {
                    return $row->getRoleNames()[0];
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    $actions .= '<a class="dropdown-item" href="'.route('users.show', $row->id).'"><i class="bx bx-show  me-1"></i> View</a>';
                    if (auth()->user()->can('user-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('users.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('user-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteUser('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('users.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
                    }
                    if (auth()->user()->can('configuration-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('indent_configuration.index').'?user_id='.$row->id.'"><i class="bx bx-list-ul me-1"></i> Indent Configuration</a>';
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
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('users.index', compact('locations', 'departments', 'designations', 'roles'));
    }

    public function create()
    {
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $reportingUsers = User::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('users.create', compact('locations', 'departments', 'designations', 'reportingUsers', 'roles'));
    }

    public function store(UserRequest $request)
    {
        //store user
        $user = new User();
        $this->userRepository->updateUser($user, $request);

        //profile photo
        if ($request->has('photo')) {
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User Added Successfully');
    }

    public function edit($id)
    {
        $user = User::find($id);
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $reportingUsers = User::select(['id', 'name'])->where('id', '!=', $id)->orderBy('name', 'asc')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('users.edit', compact('user', 'locations', 'departments', 'designations', 'reportingUsers', 'roles'));
    }

    public function update(UserRequest $request, $id)
    {
        //store user
        $user = User::find($id);
        $this->userRepository->updateUser($user, $request);

        //profile photo
        if ($request->has('photo')) {
            if ($user->photo != "") {
                unlink(storage_path('app/'.$user->photo));
            }
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User Updated Successfully');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User Deleted Successfully');
    }

    //profile section
    public function account()
    {
        return view('users.account');
    }

    public function updateProfile(UserProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->input('name');
        $user->dob = $request->input('dob');
        $user->gender = $request->input('gender');
        $user->save();

        //profile photo
        if ($request->has('photo')) {
            if (auth()->user()->photo != "") {
                unlink(storage_path('app/'.auth()->user()->photo));
            }
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('account')->with('success', 'Profile Updated Successfully');
    }

    public function importUser()
    {
        Excel::import(new ImportUser, storage_path('app/employees.xlsx'));
        Excel::import(new UpdateUserReporting, storage_path('app/employees.xlsx'));
        //return redirect()->back();
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.view', compact('user'));
    }

}
