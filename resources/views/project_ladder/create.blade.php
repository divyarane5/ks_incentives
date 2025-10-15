@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('project_ladder.index') }}" class="text-muted fw-light">Project Ladder</a>/ Add Project Ladder</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Project Ladder</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('project_ladder.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('project_ladder.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="project_id">project<span class="start-mark">*</span></label>
                        <select id="project_id" name="project_id" class="" @error('project_id') autofocus @enderror required>
                            <option value="">Select project</option>
                            @if (!empty($projects))
                                @foreach ($projects as $key => $project)
                                    <option value="{{ $project->id }}" {{ ($project->id == old('project_id') ? 'selected' : '') }}>{{ $project->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('project_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <?php //print_r($developers); ?>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="developer_id">Developer<span class="start-mark">*</span></label>
                        <select id="developer_id" name="developer_id" class="" @error('developer_id') autofocus @enderror>
                            <option value="">Select Developer</option>
                            @if (!empty($developers))
                                @foreach ($developers as $key => $developer)
                                    <option value="{{ $developer->id }}" {{ ($developer->id == old('developer_id') ? 'selected' : '') }}>{{ $developer->name }}</option>
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
                        <label class="form-label" for="name">No. of Booking (From)<span class="start-mark">*</span></label>
                        <input name="s_booking" class="form-control" id="s_booking" value="{{ old('s_booking') }}" required/>
          
                        @error('s_booking')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">No. of Booking (To)<span class="start-mark">*</span></label>
                        <input name="e_booking" class="form-control" id="e_booking" value="{{ old('e_booking') }}" required/>
          
                        @error('e_booking')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="ladder"> Ladder  (in %)<span class="start-mark">*</span></label>
                        <input name="ladder" class="form-control" id="ladder" value="{{ old('ladder') }}" required/>
                        @error('ladder')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="project_s_date" class="form-label">Start Date<span class="start-mark">*</span></label>
                        <input class="form-control" name="project_s_date" type="date" value="{{ old('project_s_date') }}" id="project_s_date" name="project_s_date" @error('project_s_date') autofocus @enderror required />
                        @error('project_s_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="project_e_date" class="form-label">End Date<span class="start-mark">*</span></label>
                        <input class="form-control" name="project_e_date" type="date" value="{{ old('project_e_date') }}" id="project_e_date" name="project_e_date" @error('project_e_date') autofocus @enderror required />
                        @error('project_e_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
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
