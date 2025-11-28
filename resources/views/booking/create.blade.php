@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('booking.index') }}" class="text-muted fw-light">Booking / </a>
        Add Booking
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Booking</h5>
            <a class="btn btn-primary" href="{{ route('booking.index') }}">Back</a>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-body">
                <div class="row">

                    {{-- Booking Date --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Booking Date<span class="start-mark">*</span></label>
                        <input class="form-control" name="booking_date" type="date" 
                            value="{{ old('booking_date') }}" required>
                        @error('booking_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Client Name --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Client Name<span class="start-mark">*</span></label>
                        <input name="client_name" class="form-control"
                            value="{{ old('client_name') }}" required>
                        @error('client_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Client Contact --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Client Contact<span class="start-mark">*</span></label>
                        <input name="client_contact" class="form-control"
                            value="{{ old('client_contact') }}" required>
                        @error('client_contact')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Lead Source --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Lead Source<span class="start-mark">*</span></label>
                        <input name="lead_source" class="form-control"
                            value="{{ old('lead_source') }}" required>
                        @error('lead_source')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Developer Name --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Developer Name</label>
                        <select name="developer_id" class="form-control" required>
                            <option value="">Select Developer</option>
                            @foreach ($developer_name as $developer)
                                <option value="{{ $developer->id }}"
                                    {{ old('developer_id') == $developer->id ? 'selected' : '' }}>
                                    {{ $developer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('developer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Project Name --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Project Name</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            @foreach ($project_name as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Sourcing Manager --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Sourcing Manager<span class="start-mark">*</span></label>
                        <input name="sourcing_manager" class="form-control"
                            value="{{ old('sourcing_manager') }}" required>
                        @error('sourcing_manager')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Sourcing Contact --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Sourcing Contact<span class="start-mark">*</span></label>
                        <input name="sourcing_contact" class="form-control"
                            value="{{ old('sourcing_contact') }}" required>
                        @error('sourcing_contact')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Booking Amount --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Booking Amount</label>
                        <input name="booking_amount" class="form-control"
                            value="{{ old('booking_amount') }}" required>
                        @error('booking_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Configuration --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Configuration<span class="start-mark">*</span></label>
                        <input name="configuration" class="form-control"
                            value="{{ old('configuration') }}" required>
                        @error('configuration')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Tower --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Tower<span class="start-mark">*</span></label>
                        <input name="tower" class="form-control"
                            value="{{ old('tower') }}" required>
                        @error('tower')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Flat No --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Flat No / Unit No.<span class="start-mark">*</span></label>
                        <input name="flat_no" class="form-control"
                            value="{{ old('flat_no') }}" required>
                        @error('flat_no')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Wing --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Wing<span class="start-mark">*</span></label>
                        <input name="wing" class="form-control"
                            value="{{ old('wing') }}" required>
                        @error('wing')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Agreement Value --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Agreement Value</label>
                        <input name="agreement_value" class="form-control"
                            value="{{ old('agreement_value') }}" required>
                        @error('agreement_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Passback Amount --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Passback Amount</label>
                        <input name="passback" class="form-control"
                            value="{{ old('passback') }}" required>
                        @error('passback')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Additional Kicker --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Additional Kicker</label>
                        <input name="additional_kicker" class="form-control"
                            value="{{ old('additional_kicker') }}" required>
                        @error('additional_kicker')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Payment Done --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Payment Done (%)</label>
                        <input name="payment_done" class="form-control"
                            value="{{ old('payment_done') }}" required>
                        @error('payment_done')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Registration Date --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Registration Date</label>
                        <input class="form-control" name="registration_date" type="date"
                            value="{{ old('registration_date') }}" required>
                        @error('registration_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" class="form-control">{{ old('remark') }}</textarea>
                        @error('remark')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Sales Person --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Sales Person</label>
                        <select name="sales_person" class="form-control" required>
                            <option value="">Select Sales Person</option>
                            @foreach ($user_name as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('sales_person') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sales_person')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Base Brokerage --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Base Brokerage</label>
                        <input name="brokerage" class="form-control"
                            value="{{ old('brokerage') }}" required>
                        @error('brokerage')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </div>
            </div>
        </form>
    </div>

</div>
@endsection
