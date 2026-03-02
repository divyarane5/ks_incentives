@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('booking.index') }}" class="text-muted fw-light">Booking / </a>
        Edit Booking
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Booking</h5>
            <a class="btn btn-primary" href="{{ route('booking.index') }}">Back</a>
        </div>

        <form action="{{ route('booking.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">

                    {{-- Booking Date --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Booking Date</label>
                        <input class="form-control" name="booking_date" type="date" 
                            value="{{ old('booking_date', $booking->booking_date) }}">
                    </div>

                    {{-- Client Name --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Client Name *</label>
                        <input name="client_name" class="form-control"
                            value="{{ old('client_name', $booking->client_name) }}" required>
                    </div>

                    {{-- Client Contact --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Client Contact</label>
                        <input name="client_contact" class="form-control"
                            value="{{ old('client_contact', $booking->client_contact) }}">
                    </div>

                    {{-- Lead Source --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Lead Source</label>
                        <input name="lead_source" class="form-control"
                            value="{{ old('lead_source', $booking->lead_source) }}">
                    </div>

                    {{-- Developer --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Developer *</label>
                        <select name="developer_id" class="form-control" required>
                            <option value="">Select Developer</option>
                            @foreach ($developer_name as $developer)
                                <option value="{{ $developer->id }}"
                                    {{ old('developer_id', $booking->developer_id) == $developer->id ? 'selected' : '' }}>
                                    {{ $developer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Project --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Project *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            @foreach ($project_name as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project_id', $booking->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tower --}}
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Tower</label>
                        <input name="tower" class="form-control"
                            value="{{ old('tower', $booking->tower) }}">
                    </div>

                    {{-- Wing --}}
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Wing</label>
                        <input name="wing" class="form-control"
                            value="{{ old('wing', $booking->wing) }}">
                    </div>

                    {{-- Flat --}}
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Flat No</label>
                        <input name="flat_no" class="form-control"
                            value="{{ old('flat_no', $booking->flat_no) }}">
                    </div>

                    {{-- Configuration --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Configuration</label>
                        <input name="configuration" class="form-control"
                            value="{{ old('configuration', $booking->configuration) }}">
                    </div>

                    {{-- Booking Amount --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Booking Amount</label>
                        <input name="booking_amount" type="number" step="0.01" class="form-control"
                            value="{{ old('booking_amount', $booking->booking_amount) }}">
                    </div>

                    {{-- Agreement Value --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Agreement Value *</label>
                        <input name="agreement_value" type="number" step="0.01" class="form-control"
                            value="{{ old('agreement_value', $booking->agreement_value) }}" required>
                    </div>

                    {{-- Additional Kicker --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Additional Kicker</label>
                        <input name="additional_kicker" type="number" step="0.01" class="form-control"
                            value="{{ old('additional_kicker', $booking->additional_kicker) }}">
                    </div>

                    {{-- Passback --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Passback</label>
                        <input name="passback" type="number" step="0.01" class="form-control"
                            value="{{ old('passback', $booking->passback) }}">
                    </div>

                    {{-- Registration Date --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Registration Date</label>
                        <input class="form-control" name="registration_date" type="date"
                            value="{{ old('registration_date', $booking->registration_date) }}">
                    </div>

                    {{-- Sales Manager --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Sales Manager *</label>
                        <select name="sales_user_id" class="form-control" required>
                            <option value="">Select Sales Manager</option>
                            @foreach ($salesManagers as $manager)
                                <option value="{{ $manager->id }}"
                                    {{ old('sales_user_id', $booking->sales_user_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" class="form-control">{{ old('remark', $booking->remark) }}</textarea>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection