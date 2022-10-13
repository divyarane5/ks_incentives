@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Users /</span> View</h4>

  
    <!-- Striped Rows -->
    <div class="card">
    <h5 class="card-header">Striped rows</h5>
    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
        <thead>
            <tr>
            <th>Project</th>
            <th>Client</th>
            <th>Users</th>
            <th>Status</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            <tr>
            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Angular Project</strong></td>
            <td>Albert Cook</td>
            <td>
                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Lilian Fuller"
                >
                    <img src="../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Sophia Wilkerson"
                >
                    <img src="../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Christina Parker"
                >
                    <img src="../assets/img/avatars/7.png" alt="Avatar" class="rounded-circle" />
                </li>
                </ul>
            </td>
            <td><span class="badge bg-label-primary me-1">Active</span></td>
            <td>
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1"></i> Edit</a
                    >
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1"></i> Delete</a
                    >
                </div>
                </div>
            </td>
            </tr>
            <tr>
            <td><i class="fab fa-react fa-lg text-info me-3"></i> <strong>React Project</strong></td>
            <td>Barry Hunter</td>
            <td>
                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Lilian Fuller"
                >
                    <img src="../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Sophia Wilkerson"
                >
                    <img src="../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Christina Parker"
                >
                    <img src="../assets/img/avatars/7.png" alt="Avatar" class="rounded-circle" />
                </li>
                </ul>
            </td>
            <td><span class="badge bg-label-success me-1">Completed</span></td>
            <td>
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1"></i> Edit</a
                    >
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1"></i> Delete</a
                    >
                </div>
                </div>
            </td>
            </tr>
            <tr>
            <td><i class="fab fa-vuejs fa-lg text-success me-3"></i> <strong>VueJs Project</strong></td>
            <td>Trevor Baker</td>
            <td>
                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Lilian Fuller"
                >
                    <img src="../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Sophia Wilkerson"
                >
                    <img src="../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Christina Parker"
                >
                    <img src="../assets/img/avatars/7.png" alt="Avatar" class="rounded-circle" />
                </li>
                </ul>
            </td>
            <td><span class="badge bg-label-info me-1">Scheduled</span></td>
            <td>
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1"></i> Edit</a
                    >
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1"></i> Delete</a
                    >
                </div>
                </div>
            </td>
            </tr>
            <tr>
            <td>
                <i class="fab fa-bootstrap fa-lg text-primary me-3"></i> <strong>Bootstrap Project</strong>
            </td>
            <td>Jerry Milton</td>
            <td>
                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Lilian Fuller"
                >
                    <img src="../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Sophia Wilkerson"
                >
                    <img src="../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                </li>
                <li
                    data-bs-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-bs-placement="top"
                    class="avatar avatar-xs pull-up"
                    title="Christina Parker"
                >
                    <img src="../assets/img/avatars/7.png" alt="Avatar" class="rounded-circle" />
                </li>
                </ul>
            </td>
            <td><span class="badge bg-label-warning me-1">Pending</span></td>
            <td>
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-edit-alt me-1"></i> Edit</a
                    >
                    <a class="dropdown-item" href="javascript:void(0);"
                    ><i class="bx bx-trash me-1"></i> Delete</a
                    >
                </div>
                </div>
            </td>
            </tr>
        </tbody>
        </table>
    </div>
    </div>
    <!--/ Striped Rows -->

    //sample
    <div class="container mt-2">
<div class="row">
<div class="col-lg-12 margin-tb">
<div class="pull-left">
<h2>Laravel 8 CRUD Example Tutorial</h2>
</div>
<div class="pull-right mb-2">
<a class="btn btn-success" href="{{ route('companies.create') }}"> Create Company</a>
</div>
</div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
<p>{{ $message }}</p>
</div>
@endif
<table class="table table-bordered">
<tr>
<th>S.No</th>
<th>Company Name</th>
<th>Company Email</th>
<th>Company Address</th>
<th width="280px">Action</th>
</tr>
@foreach ($users as $user)
<tr>
<td>{{ $user->id }}</td>
<td>{{ $user->name }}</td>
<td>{{ $user->email }}</td>
<td>{{ $user->address }}</td>
<td>
<form action="{{ route('users.destroy',$user->id) }}" method="Post">
<a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Edit</a>
@csrf
@method('DELETE')
<button type="submit" class="btn btn-danger">Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
{!! $users->links() !!}
   
</div>
<!-- / Content -->
@endsection
