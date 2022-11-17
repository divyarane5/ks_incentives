@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('role.index') }}" class="text-muted fw-light">Roles </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Role</h5>
            <small class="text-muted float-end"><a class="btn-sm btn-primary" href="{{ route('role.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('role.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name<span class="start-mark">*</span></label>
                        <input name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $role->name }}" required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    @if(!empty($permissions))
                        <div class="nav-align-top mb-4 permission_tabs">
                            <ul class="nav nav-pills" role="tablist">
                                @php $navFlag = 0; @endphp
                                @foreach ($permissions as $module => $permissionList)
                                    <li class="nav-item">
                                        <button
                                        type="button"
                                        class="nav-link {{ ($navFlag == 0) ? 'active' : '' }}"
                                        role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#{{ $module }}"
                                        aria-controls="{{ $module }}"
                                        aria-selected="{{ ($navFlag == 0) ? 'true' : 'false' }}"
                                        >
                                        {{ ucfirst(str_replace("_", " ", $module)) }}
                                        </button>
                                        @php $navFlag = 1; @endphp
                                    </li>
                                @endforeach

                            </ul>
                            <div class="tab-content">
                                @php $navContentFlag = 0;  @endphp
                                @foreach ($permissions as $module => $permissionList)
                                    <div class="tab-pane fade {{ ($navContentFlag == 0) ? ' show active' : '' }}" id="{{ $module }}" role="tabpanel">
                                       <div class="row" >
                                            @foreach ($permissionList as $permission)
                                                <div class="form-check mt-3 col-md-3">
                                                    <input class="form-check-input" name="permissions[]" type="checkbox" value="{{ $permission->name }}" id="{{ $permission->name }}" {{ (!empty(old('permissions')) && in_array($permission->name, old('permissions'))) ? 'checked' : ($role->hasPermissionTo($permission->name) ? 'checked' : '')  }} />
                                                    <label class="form-check-label" for="{{ $permission->name }}"> {{ ucfirst(str_replace("-", " ", $permission->action)) }} </label>
                                                </div>
                                            @endforeach
                                       </div>
                                    </div>
                                    @php $navContentFlag = 1; @endphp
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @error('permission')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($role->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
