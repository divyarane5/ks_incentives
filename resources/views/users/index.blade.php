@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <a class="btn btn-primary btn-rt" href="{{ route('users.create') }}"> Add User</a>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Users /</span> View</h4>
   
  
    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Users</h5>
        
        <div class="table-responsive text-nowrap">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            
            <table class="table table-striped">
            <thead>
                <tr>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Designation</th>
                <th>Department</th>
                <th>DOB</th>
                <th>Location</th>
                <th>Gender</th>
                <th>Company Name</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            @foreach ($users as $user)
                <tr>
                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $user->employee_code }}</strong></td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->designation_id }}</td>
                <td>{{ $user->department_id }}</td>
                <td>{{ $user->dob }}</td>
                <td>{{ $user->entity }}</td>
                <td>{{ $user->gender }}</td>
                <td><span class="badge bg-label-primary me-1">Active</span></td>
                <td>
                    <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('users.edit',$user->id) }}"
                        ><i class="bx bx-edit-alt me-1"></i> Edit</a
                        >
                        
                        <form  action="{{ route('users.destroy',$user->id) }}" method="Post">
                            @csrf
                            @method('DELETE')
                            <!-- <button type="submit" class="btn btn-danger"><i class="bx bx-trash me-1"></i> Delete</button> -->
                            <a type="submit" class="dropdown-item" href="javascript:void(0);"
                            ><i class="bx bx-trash me-1"></i> Delete</a
                            >
                        </form>
                    </div>
                    </div>
                </td>
                </tr>
            @endforeach
            </tbody>
            </table>
            {!! $users->links() !!}
        </div>
    </div>
    <!--/ Striped Rows -->

   
<!-- / Content -->
@endsection
