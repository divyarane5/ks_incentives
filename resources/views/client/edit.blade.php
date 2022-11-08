@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('client.index') }}" class="text-muted fw-light">Client /</a> Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Client</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('client.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('client.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="template_id">Template<span class="start-mark">*</span></label>
                        <select id="template_id" name="template_id" class="form-select" @error('template_id') autofocus @enderror required>
                            <option>Select Template</option>
                            @if (!empty($template))
                                @foreach ($template as $key => $temp)
                                    <option value="{{ $temp->id }}" {{ ($temp->id == ((old('template_id') != "") ? old('template_id') : $client->template_id) ? 'selected' : '') }}>{{ $temp->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('template_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="sales_person">Sales Person<span class="start-mark">*</span></label>
                        <input name="sales_person" class="form-control" id="sales_person" value="{{ (old('sales_person') != "") ? old('name') : $client->sales_person }}" required />
                        @error('sales_person')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="client_name">Client Name<span class="start-mark">*</span></label>
                        <input name="client_name" class="form-control" id="client_name" value="{{ (old('client_name') != "") ? old('name') : $client->client_name }}" required />
                        @error('client_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="client_email">Client Email<span class="start-mark">*</span></label>
                        <input name="client_email" class="form-control" id="client_email" value="{{ (old('client_email') != "") ? old('name') : $client->client_email }}" required />
                        @error('client_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="subject_name">Subject Name<span class="start-mark">*</span></label>
                        <input name="subject_name" class="form-control" id="subject_name" value="{{ (old('subject_name') != "") ? old('name') : $client->subject_name }}" required />
                        @error('subject_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>


                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($client->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
