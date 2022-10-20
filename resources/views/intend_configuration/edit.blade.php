@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('indent_configuration.index') }}" class="text-muted fw-light">Indent Configuration/</a> Add Indent Configuration</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Indent Configuration</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('indent_configuration.index') }}"> Back</a></small>
        </div>
        <form action="{{ route('indent_configuration.update', $id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id"  class="form-select" id="user_id" aria-label="User" required>
                          <option value="" >Select User</option>
                          @if(!empty($users))
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ (((old('user_id') != "") ? old('user_id') : $intendConfiguration->user_id) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('user_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="expense_id" class="form-label">Expense</label>
                        <select name="expense_id"  class="form-select" id="expense_id" aria-label="Expense" required>
                            <option value="" >Select Expense</option>
                            @if(!empty($expenses))
                                @foreach ($expenses as $expense)
                                    <option value="{{ $expense->id }}" {{ (((old('expense_id') != "") ? old('expense_id') : $intendConfiguration->expense_id) == $expense->id) ? 'selected' : '' }}>{{ $expense->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('expense_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="monthly_limit" class="form-label">Monthly Limit</label>
                        <input type="number" name="monthly_limit" class="form-control" min="0" id="monthly_limit" value="{{ ((old('monthly_limit') != "") ? old('monthly_limit') : $intendConfiguration->monthly_limit) }}" />
                        @error('monthly_limit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="indent_limit" class="form-label">Indent Limit</label>
                        <input type="number" name="indent_limit" class="form-control" min="0" id="indent_limit" value="{{ ((old('indent_limit') != "") ? old('indent_limit') : $intendConfiguration->indent_limit) }}" />
                        @error('indent_limit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <input class="form-check-input" type="checkbox" value="1" name="monthly_limit_approval_required" id="monthly_limit_approval_required" {{ (((old('monthly_limit_approval_required') != "") ? old('monthly_limit_approval_required') : $intendConfiguration->monthly_limit_approval_required) == 1) ? 'checked' : '' }} />
                        <label class="form-check-label form-label" for="monthly_limit_approval_required"> Monthly limit approval required </label>
                        @error('monthly_limit_approval_required')
                            <br>
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <input class="form-check-input" type="checkbox" value="1" name="indent_limit_approval_required" id="indent_limit_approval_required" {{ (((old('indent_limit_approval_required') != "") ? old('indent_limit_approval_required') : $intendConfiguration->indent_limit_approval_required) == 1) ? 'checked' : '' }} />
                        <label class="form-check-label form-label" for="indent_limit_approval_required"> Indent limit approval required </label>
                        @error('indent_limit_approval_required')
                            <br>
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="d-flex flex-wrap approver-flex" >
                        <div class="mb-3 approver px-2">
                            <label for="approver1" class="form-label">Approver 1</label>
                            <select name="approver1"  class="form-select" id="approver1" aria-label="Approver 1">
                            <option value="" >Select Approver</option>
                            @if(!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (((old('approver1') != "") ? old('approver1') : $intendConfiguration->approver1) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            @error('approver1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 approver px-2">
                            <label for="approver2" class="form-label">Approver 2</label>
                            <select name="approver2"  class="form-select" id="approver2" aria-label="Approver 2">
                            <option value="" >Select Approver</option>
                            @if(!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (((old('approver2') != "") ? old('approver2') : $intendConfiguration->approver2) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            @error('approver2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 approver px-2">
                            <label for="approver3" class="form-label">Approver 3</label>
                            <select name="approver3"  class="form-select" id="approver3" aria-label="User">
                            <option value="" >Select Approver</option>
                            @if(!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (((old('approver3') != "") ? old('approver3') : $intendConfiguration->approver3) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            @error('approver3')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 approver px-2">
                            <label for="approver4" class="form-label">Approver 4</label>
                            <select name="approver4"  class="form-select" id="approver4" aria-label="User">
                            <option value="" >Select Approver</option>
                            @if(!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (((old('approver4') != "") ? old('approver4') : $intendConfiguration->approver4) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            @error('approver4')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 approver px-2">
                            <label for="approver5" class="form-label">Approver 5</label>
                            <select name="approver5"  class="form-select" id="approver5" aria-label="Approver">
                            <option value="" >Select Approver</option>
                            @if(!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (((old('approver5') != "") ? old('approver5') : $intendConfiguration->approver5) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                            </select>
                            @error('approver5')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- / Content -->
@endsection
