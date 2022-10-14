<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Spatie\Permission\Models\Role;

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
            $users = $this->userRepository->getUsers();
            return DataTables::of($users)
                ->addColumn('action', function ($row) {
                    $actions = '';
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
        $user->email = $request->input('email');
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

}
