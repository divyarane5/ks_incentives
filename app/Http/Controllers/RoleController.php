<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DataTables;

class RoleController extends Controller
{
    private $roleRepository;

    function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->middleware('permission:role-view', ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the role.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::all();
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('role-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('role.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('role-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteRole('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('role.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('roles.index');
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = $this->roleRepository->getPermissions();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in database.
     */
    public function store(RoleRequest $request)
    {
        //create role
        $role = new Role();
        $role->name = $request->input('name');
        $role->save();

        //permissions
        $permissions = $request->input('permissions');
        $role->syncPermissions($permissions);

        return redirect()->route('role.index')->with('success', 'Role Created Successfully');
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = $this->roleRepository->getPermissions();
        return view('roles.edit', compact('permissions', 'id', 'role'));
    }

    /**
     * Update the specified role in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        //permissions
        $permissions = $request->input('permissions');
        $role->syncPermissions($permissions);

        return redirect()->route('role.index')->with('success', 'Role Updated Successfully');
    }

    /**
     * Remove the specified role from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::where('id', $id)->delete();
        return redirect()->route('role.index')->with('success', 'Role Deleted Successfully');
    }
}
