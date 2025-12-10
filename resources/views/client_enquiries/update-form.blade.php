@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Client Enquiry /</span> Update
    </h4>

    <!-- =========================
        ENQUIRY DETAILS HEADER
    ========================== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Update Client Enquiry (ID: {{ $enquiry->id }})</strong>
        </div>
    </div>

    <!-- =========================
        SHOW UPDATE HISTORY
    ========================== -->
    @if($enquiry->updates && $enquiry->updates->count() > 0)

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <strong>Previous Updates</strong>
        </div>

        <div class="card-body">

            @foreach($enquiry->updates as $u)
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_',' ', $u->status)) }}</p>
                    </div>

                    <div class="col-md-8">
                        <p><strong>Feedback:</strong> {{ $u->feedback }}</p>
                    </div>
                </div>

                <div class="row">
                    @if($u->revisit_scheduled)
                    <div class="col-md-4">
                        <p><strong>Revisit Scheduled:</strong> {{ $u->revisit_scheduled }}</p>
                    </div>
                    @endif

                    @if($u->revisit_done)
                    <div class="col-md-4">
                        <p><strong>Revisit Done:</strong> {{ $u->revisit_done }}</p>
                    </div>
                    @endif

                    @if($u->followup_date)
                    <div class="col-md-4">
                        <p><strong>Followup Date:</strong> {{ $u->followup_date }}</p>
                    </div>
                    @endif
                </div>

                <small class="text-muted">
                    Updated on: {{ $u->created_at->format('d M Y, h:i A') }}
                </small>
            </div>
            @endforeach

        </div>
    </div>
    @endif

    <!-- =========================
        NEW UPDATE FORM
    ========================== -->
    <form action="{{ route('client-enquiries.updates.store', $enquiry->id) }}" method="POST">
        @csrf
        <input type="hidden" name="client_enquiry_id" value="{{ $enquiry->id }}">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Add New Update</strong>
            </div>

            <div class="card-body">

                <!-- Feedback -->
                <div class="mb-3">
                    <label class="form-label">Feedback / Notes</label>
                    <textarea name="feedback" class="form-control" rows="2" placeholder="Enter feedback or notes"></textarea>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Select Status</option>
                        <option value="new">New</option>
                        <option value="followup">Follow Up</option>
                        <option value="revisit">Revisit</option>
                        <option value="booked">Booked</option>
                        <option value="not_interested">Not Interested</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <!-- Date Fields -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Revisit Scheduled Date</label>
                        <input type="date" name="revisit_scheduled" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Revisit Done Date</label>
                        <input type="date" name="revisit_done" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Followup Date</label>
                        <input type="date" name="followup_date" class="form-control">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Submit Update</button>
                    <a href="{{ route('client-enquiries.index') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection
