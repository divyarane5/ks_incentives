@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('developer_ladder.index') }}" class="text-muted fw-light">AOP Ladder </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit AOP Ladder</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('developer_ladder.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('developer_ladder.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    
                     <div class="mb-3 col-md-6">
                        <label class="form-label" for="developer_id">Department<span class="start-mark">*</span></label>
                        <select id="developer_id" name="developer_id" class="" @error('developer_id') autofocus @enderror required>
                            <option value="">Select Department</option>
                            @if (!empty($developers))
                                @foreach ($developers as $key => $developers)
                                    <option value="{{ $developers->id }}" {{ ($developers->id == ((old('developer_id') != "") ? old('developer_id') : $developer_ladder->developer_id) ? 'selected' : '') }}>{{ $developers->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('developer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">AOP<span class="start-mark">*</span></label>
                        <input name="aop" class="form-control" id="aop" value="{{ (old('aop') != "") ? old('aop') : $developer_ladder->aop }}" required />
                        @error('aop')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Ladder (in %)<span class="start-mark">*</span></label>
                        <input name="ladder" class="form-control" id="ladder" value="{{ (old('ladder') != "") ? old('ladder') : $developer_ladder->ladder }}" required />
                        @error('ladder')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">AOP Start Date<span class="start-mark">*</span></label> 
                        <input type="date" name="aop_s_date" class="form-control" id="aop_s_date" value="{{ (old('aop_s_date') != "") ? old('aop_s_date') : $developer_ladder->aop_s_date }}" required />
                        @error('aop_s_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">AOP End Date<span class="start-mark">*</span></label> 
                        <input type="date" name="aop_e_date" class="form-control" id="aop_e_date" value="{{ (old('aop_e_date') != "") ? old('aop_e_date') : $developer_ladder->aop_e_date }}" required />
                        @error('aop_e_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($developer_ladder->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
