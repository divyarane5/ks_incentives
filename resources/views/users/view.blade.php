@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> /</span> View</h4>

    <div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header center-align">User Details - {{ $user->employee_code }}</h5>
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                <img
                    src="{{ ($user->photo != "") ? url('storage/app/'.$user->photo) : asset("assets/img/avatars/profile.png") }}"
                    alt="user-avatar"
                    class="d-block rounded auto_margin"
                    height="100"
                    width="100"
                    id="uploadedAvatar"
                />
                </div>
                <hr class="" />
                <div class=" mb-4">
                    <h5 class="card-header">Basic Information</h5>
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <table class="table table-borderless col-md-6">
                                <tr>
                                    <th width="40%">Name: </th>
                                    <td>{{ (!empty($user->name) ? $user->name : '') }}</td>
                                </tr>
                                <tr>
                                    <th>E-mail: </th>
                                    <td>{{ (isset($user->email) ? $user->email : '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Gender: </th>
                                    <td>{{ (isset($user->gender) ? $user->gender : '-') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless col-md-6">
                                <tr>
                                    <th width="40%">Date Of Birth: </th>
                                    <td>{{ (isset($user->dob) ? $user->dob : '-') }}</td>
                                </tr>
                                <tr>
                                    <th >Date of Joining: </th>
                                    <td>{{ (isset($user->joining_date) ? $user->joining_date : '-') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr class="" />
                <div class=" mb-4">
                    <h5 class="card-header">Work Information</h5>
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <table class="table table-borderless col-md-6">
                                <tr>
                                    <th width="40%">Company: </th>
                                    <td>{{ (!empty($user->entity) ? $user->entity : '') }}</td>
                                </tr>
                                <tr>
                                    <th>Reporting To: </th>
                                    <td>{{ (isset($user->reportingTo->name) ? $user->reportingTo->name : '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Role: </th>
                                    <td>{{ $user->getRoleNames()[0] }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table   class="table table-borderless col-md-6">
                                <tr>
                                    <th width="40%">Location: </th>
                                    <td>{{ (isset($user->location->name) ? $user->location->name : '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Department: </th>
                                    <td>{{ (isset($user->department->name) ? $user->department->name : '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Designation: </th>
                                    <td>{{ (isset($user->designation->name) ? $user->designation->name : '-') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- / Content -->
@endsection

