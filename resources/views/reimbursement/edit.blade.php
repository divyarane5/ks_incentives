@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('reimbursement.index') }}" class="text-muted fw-light">Reimbursement</a>/ Edit Reimbursement</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Reimbursement</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('reimbursement.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('reimbursement.update', $reimbursement->id) }}" method="POST"  enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $reimbursement->id }}" >
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="client_name">Client Name<span class="start-mark">*</span></label>
                        <input type="text"  name="client_name" class="form-control" id="client_name" value="{{ !empty(old('client_name')) ? old('client_name') : $reimbursement->client_name }}" required  @error('client_name') autofocus @enderror />
                        @error('client_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="project_name">Project Name<span class="start-mark">*</span></label>
                        <input type="text"  name="project_name" class="form-control" id="project_name" value="{{ !empty(old('project_name')) ? old('project_name') : $reimbursement->project_name }}" required @error('project_name') autofocus @enderror />
                        @error('project_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="source">From Location</label>
                        <input type="text"  name="source" class="form-control" id="source" value="{{ !empty(old('source')) ? old('source') : $reimbursement->source }}" @error('source') autofocus @enderror  />
                        @error('source')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="destination">To Destination</label>
                        <input type="text"  name="destination" class="form-control" id="destination" value="{{ !empty(old('destination')) ? old('destination') : $reimbursement->destination }}" @error('destination') autofocus @enderror  />
                        @error('destination')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="reimbursement_date">Reimbursement Date</label>
                        <input type="date"  name="reimbursement_date" class="form-control" id="reimbursement_date" value="{{ !empty(old('reimbursement_date')) ? old('destination') : $reimbursement->reimbursement_date }}" @error('reimbursement_date') autofocus @enderror  />
                        @error('reimbursement_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="transport_mode">Transport Mode</label>
                        <select name="transport_mode" class="" id="transport_mode" aria-label="Transport Mode" @error('transport_mode') autofocus @enderror required>
                            <option value="">Select Transport Mode</option>
                            @php
                                $transportModes = config('constants.TRANSPORT_MODE')
                            @endphp
                            @if (!empty($transportModes))
                                @foreach ($transportModes as $key => $transportMode)
                                    <option value="{{ $key }}" {{ ((!empty(old('transport_mode')) ? old('transport_mode') : $reimbursement->transport_mode) == $key) ? 'selected' : '' }}>{{ $transportMode }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('transport_mode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="visit_attended_of_id">Who's Visit Attended<span class="start-mark">*</span></label>
                        <select name="visit_attended_of_id" class="" id="visit_attended_of_id" aria-label="Who's Visit Attended" @error('visit_attended_of_id') autofocus @enderror required>
                            <option value="">Select User</option>
                            @if (!empty($users))
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ ((old('visit_attended_of_id') != "" ? old('visit_attended_of_id') : $reimbursement->visit_attended_of_id) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('visit_attended_of_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="amount">Amount<span class="start-mark">*</span></label>
                        <input type="number"  name="amount" class="form-control" id="amount" value="{{ !empty(old('amount')) ? old('amount') : $reimbursement->amount }}" @error('amount') autofocus @enderror  required/>
                        @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="comment">Comment</label>
                        <textarea  name="comment" class="form-control" id="comment" @error('comment') autofocus @enderror >{{ !empty(old('comment')) ? old('comment') : $reimbursement->comment }}</textarea>
                        @error('comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <div class="mb-3">
                            <label class="btn btn-sm">
                                <i class="mdi mdi-upload"></i>
                                <span class="position-relative btn btn-outline-primary" >Upload Bill</span>
                                <input type="file" name="bill" style="display: none;" id="file-input">
                            </label>
                            <div id="preview" class="my-3 row">
                                @if ($reimbursement->file_name != "")
                                <div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">
                                    @if(getimagesize(storage_path('app/'.$reimbursement->file_path)))
                                        <img style="width: 120px; height: 100px;" src="{{ url('storage/app/'.$reimbursement->file_path) }}" class="prescriptions">
                                    @else
                                        <img style="width: 120px; height: 100px;" src="{{ asset("assets/img/icons/unicons/file.png") }}" class="prescriptions">
                                    @endif
                                    <p style="word-break: break-all;">{{ $reimbursement->file_name }}</p>
                                    <input type="hidden" name="attachmentName" value="{{ $reimbursement->file_name }}">
                                </div>
                                @endif
                            </div>
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

@section('script')
<script src="{{ asset("assets/js/reimbursement.js") }}"></script>
@endsection
