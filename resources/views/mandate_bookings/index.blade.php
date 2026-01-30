@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Mandate /</span> Bookings</h4>
        <div class="col-md-6 text-end">
            @can('mandate-booking-create')
            <a href="{{ route('mandate_bookings.create') }}" class="btn btn-primary mb-3">Add Booking</a>
            @endcan
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card my-4" id="BookingFilter">
        <div class="card-body row">
            <div class="mb-3 col-md-3">
                <label class="form-label">Project</label>
                <select id="project_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Booking Status</label>
                <select id="status" class="form-select">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Registered</label>
                <select id="is_registered" class="form-select">
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Brokerage Eligible</label>
                <select id="is_eligible" class="form-select">
                    <option value="">All</option>
                    <option value="1">Eligible</option>
                    <option value="0">Not Eligible</option>
                    <option value="null">Not Evaluated</option>
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Brokerage Status</label>
                <select id="brokerage_status" class="form-select">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Booking Source</label>
                <select id="booking_source" class="form-select">
                    <option value="">All</option>
                    @foreach ($sources as $source)
                        <option value="{{ $source }}">{{ $source }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Booking Date From</label>
                <input type="date" id="booking_date_from" class="form-control">
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">Booking Date To</label>
                <input type="date" id="booking_date_to" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Channel Partner</label>
                <select class="form-select" id="channel_partner_id">
                    <option value="">All</option>
                    @foreach ($channelPartners as $cp)
                        <option value="{{ $cp->id }}">{{ $cp->firm_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label">&nbsp;</label><br>
                <button id="filter" class="btn btn-primary me-2">Filter</button>
                <button id="clear" class="btn btn-secondary">Clear</button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <h5 class="card-header">Mandate Bookings</h5>
        <div class="table-responsive text-nowrap">
            <table id="mandate-bookings-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking Date</th>
                        <th>Project</th>
                        <th>Agreement Value</th>
                        <th>Total Paid</th>
                        <th>Payment %</th>
                        <th>Registered</th>
                        <th>Brokerage Eligible</th>
                        <th>Brokerage Status</th>
                        <th>Booking Status</th>
                        <th>Booking Source</th>
                        <th>Channel Partner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {
    var table = $('#mandate-bookings-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('mandate_bookings.index') }}",
            data: function(d) {
                d.project_id = $('#project_id').val();
                d.status = $('#status').val();
                d.is_registered = $('#is_registered').val();
                d.is_eligible = $('#is_eligible').val();
                d.brokerage_status = $('#brokerage_status').val();
                d.booking_source = $('#booking_source').val();
                d.booking_date_from = $('#booking_date_from').val();
                d.booking_date_to = $('#booking_date_to').val();
                d.channel_partner_id = $('#channel_partner_id').val();

            }   
        },
        columns: [
            { data: 'id', name: 'b.id' },
            { data: 'booking_date', name: 'b.booking_date' },
            { data: 'project_name', name: 'p.project_name' },
            { data: 'agreement_value', name: 'f.agreement_value', render: function(data){ return data ? '₹ ' + parseFloat(data).toLocaleString() : '—'; }},
            { data: 'total_paid', name: 'br.total_paid', render: function(data){ return data ? '₹ ' + parseFloat(data).toLocaleString() : '—'; }},
            { data: 'payment_percent', name: 'br.payment_percent', render: function(data){ return data ? data + ' %' : '—'; }},
            { data: 'is_registered', name: 'f.is_registered', render: function(data){ return data==1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>'; }},
            { data: 'is_eligible', name: 'br.is_eligible', render: function(data){ 
                if (data === null) return '<span class="badge bg-secondary">Not Evaluated</span>';
                return data==1 ? '<span class="badge bg-success">Eligible</span>' : '<span class="badge bg-danger">Not Eligible</span>';
            }},
            {
                data: 'brokerage_status',
                name: 'br.status',
                render: function (data, type, row) {

                    // ✅ If already PAID → show text, no dropdown
                    if (data === 'paid') {
                        return `<span class="badge bg-success">Paid</span>`;
                    }

                    // ✅ Only allowed options when NOT paid
                    let options = ['pending', 'approved'];

                    let html = `<select class="form-select form-select-sm brokerage-status" data-id="${row.id}">`;
                    html += `<option value="">—</option>`;

                    options.forEach(opt => {
                        html += `<option value="${opt}" ${data === opt ? 'selected' : ''}>
                                    ${opt.charAt(0).toUpperCase() + opt.slice(1)}
                                </option>`;
                    });

                    html += `</select>`;
                    return html;
                }
            },
            { 
                data: 'booking_status', 
                name: 'b.booking_status', 
                render: function(data, type, row){
                    let options = ['pending','completed','cancelled'];
                    let html = `<select class="form-select form-select-sm booking-status" data-id="${row.id}">`;
                    html += `<option value="">—</option>`;
                    options.forEach(opt => {
                        html += `<option value="${opt}" ${data === opt ? 'selected' : ''}>${opt.charAt(0).toUpperCase() + opt.slice(1)}</option>`;
                    });
                    html += `</select>`;
                    return html;
                } 
            },
            { data: 'booking_source', name: 'b.booking_source', render: function(data){ return data ?? '—'; }},
            {  data: 'cp_name', name: 'cp.name',render: function(data){ return data ?? '—';} },
            { data: 'action', orderable:false, searchable:false }
        ]
    });

    $('#filter').click(function(){ table.ajax.reload(); });
    $('#clear').click(function(){
        $('#BookingFilter select').val('');
        table.ajax.reload();
    });
});

// Booking Status update
$('#mandate-bookings-datatable').on('change', '.booking-status', function () {
    let id = $(this).data('id');
    let value = $(this).val();
    $.ajax({
        url: "{{ route('mandate_bookings.updateStatus') }}",
        type: "POST",
        data: {_token: "{{ csrf_token() }}", id: id, booking_status: value},
        success: function (res) {
            if (res.success) {
                toastr.success('Booking status updated');
                $('#mandate-bookings-datatable').DataTable().ajax.reload(null, false);
            } else {
                toastr.error(res.message || 'Update failed');
            }
        }
    });
});

// Brokerage Status update
$('#mandate-bookings-datatable').on('change', '.brokerage-status', function(){
    let id = $(this).data('id');
    let value = $(this).val();
    $.ajax({
        url: "{{ route('mandate_bookings.updateStatus') }}",
        type: 'POST',
        data: {_token: "{{ csrf_token() }}", id: id, brokerage_status: value},
        success: function(res){
            if(res.success){
                toastr.success('Brokerage status updated');
            } else {
                toastr.error(res.message || 'Update failed');
            }
            $('#mandate-bookings-datatable').DataTable().ajax.reload(null, false);
        }
    });
});
</script>
@endsection
