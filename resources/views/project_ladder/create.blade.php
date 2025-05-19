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
                    <?php //print_r($developer_ladders); ?>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="aop_id">AOP<span class="start-mark">*</span></label>
                        <select id="aop_id" name="aop_id" class="" @error('aop_id') autofocus @enderror>
                            <option value="">Select AOP</option>
                            @if (!empty($developer_ladders))
                                @foreach ($developer_ladders as $key => $developer_ladder)
                                    <option value="{{ $developer_ladder->id }}" {{ ($developer_ladder->id == old('aop_id') ? 'selected' : '') }}>{{ $developer_ladder->name }} {{$developer_ladder->ladder}}% ({{date('F', strtotime($developer_ladder->aop_s_date))}} {{date('Y', strtotime($developer_ladder->aop_s_date))}} - {{date('F', strtotime($developer_ladder->aop_e_date))}} {{date('Y', strtotime($developer_ladder->aop_e_date))}})</option>
                                @endforeach
                            @endif
                        </select>
                        @error('aop_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">No. of Booking<span class="start-mark">*</span></label>
                        <input name="booking" class="form-control" id="booking" value="{{ old('booking') }}" required/>
          
                        @error('booking')
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
