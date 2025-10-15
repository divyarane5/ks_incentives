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
                        <label class="form-label" for="developer_id">Developer<span class="start-mark">*</span></label>
                        <select id="developer_id" name="developer_id" class="" @error('developer_id') autofocus @enderror required>
                            <option value="">Select Project</option>
                            @if (!empty($developers))
                                @foreach ($developers as $key => $developer)
                                    <option value="{{ $developer->id }}" {{ ($developer->id == ((old('developer_id') != "") ? old('developer_id') : $project_ladder->developer_id) ? 'selected' : '') }}>{{ $developer->name }}</option>
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
                        <label class="form-label" for="name">No. Of Bookings (From)<span class="start-mark">*</span></label>
                        <input name="s_booking" class="form-control" id="s_booking" value="{{ (old('s_booking') != "") ? old('s_booking') : $project_ladder->s_booking }}" required />
                        @error('s_booking')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">No. Of Bookings (To)<span class="start-mark">*</span></label>
                        <input name="e_booking" class="form-control" id="e_booking" value="{{ (old('e_booking') != "") ? old('e_booking') : $project_ladder->e_booking }}" required />
                        @error('e_booking')
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
