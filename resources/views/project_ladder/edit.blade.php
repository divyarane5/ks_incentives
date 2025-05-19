@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('project_ladder.index') }}" class="text-muted fw-light">Project Ladder </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Project Ladder</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('project_ladder.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('project_ladder.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    
                     <div class="mb-3 col-md-6">
                        <label class="form-label" for="project_id">Project<span class="start-mark">*</span></label>
                        <select id="project_id" name="project_id" class="" @error('project_id') autofocus @enderror required>
                            <option value="">Select Project</option>
                            @if (!empty($projects))
                                @foreach ($projects as $key => $project)
                                    <option value="{{ $project->id }}" {{ ($project->id == ((old('project_id') != "") ? old('project_id') : $project_ladder->project_id) ? 'selected' : '') }}>{{ $project->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('project_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="aop_id">Project<span class="start-mark">*</span></label>
                        <select id="aop_id" name="aop_id" class="" @error('aop_id') autofocus @enderror required>
                            <option value="">Select Project</option>
                            @if (!empty($developer_ladders))
                                @foreach ($developer_ladders as $key => $developer_ladder)
                                    <option value="{{ $developer_ladder->id }}" {{ ($developer_ladder->id == ((old('aop_id') != "") ? old('aop_id') : $project_ladder->aop_id) ? 'selected' : '') }}>{{ $developer_ladder->name }} {{$developer_ladder->ladder}}% ({{date('F', strtotime($developer_ladder->aop_s_date))}} {{date('Y', strtotime($developer_ladder->aop_s_date))}} - {{date('F', strtotime($developer_ladder->aop_e_date))}} {{date('Y', strtotime($developer_ladder->aop_e_date))}})</option>
                                @endforeach
                            @endif
                        </select>
                        @error('project_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">No. Of Bookings<span class="start-mark">*</span></label>
                        <input name="booking" class="form-control" id="booking" value="{{ (old('booking') != "") ? old('booking') : $project_ladder->booking }}" required />
                        @error('booking')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Ladder (in %)<span class="start-mark">*</span></label>
                        <input name="ladder" class="form-control" id="ladder" value="{{ (old('ladder') != "") ? old('ladder') : $project_ladder->ladder }}" required />
                        @error('ladder')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Project Start Date<span class="start-mark">*</span></label> 
                        <input type="date" name="project_s_date" class="form-control" id="project_s_date" value="{{ (old('project_s_date') != "") ? old('project_s_date') : $project_ladder->project_s_date }}" required />
                        @error('project_s_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Project End Date<span class="start-mark">*</span></label> 
                        <input type="date" name="project_e_date" class="form-control" id="project_e_date" value="{{ (old('project_e_date') != "") ? old('project_e_date') : $project_ladder->project_e_date }}" required />
                        @error('project_e_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($project_ladder->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
