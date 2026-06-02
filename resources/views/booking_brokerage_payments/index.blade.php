@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Total Invoiced</h6>
                <h4 id="totalInvoiced">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Total Received</h6>
                <h4 id="totalReceived">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Total TDS</h6>
                <h4 id="totalTds">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Pending Collection</h6>
                <h4 id="totalPending">₹ 0</h4>
            </div>
        </div>
    </div>

</div>
<div class="row mb-3">

    <div class="col-md-6">
        <h4 class="fw-bold py-3 mb-0">
            Brokerage Invoice Management
        </h4>
    </div>

    <div class="col-md-6 text-end">

        @can('payment-create')
        <a href="{{ route('brokerage-payments.create') }}"
           class="btn btn-primary">
            Add Invoice
        </a>
        @endcan

    </div>

</div>
<div class="card mb-3">
    <div class="card-body">

        <div class="row">

            <div class="col-md-2">
                <label>Project</label>
                <select id="projectFilter" class="form-select">
                   
                </select>
            </div>

            <div class="col-md-2">
                <label>Developer</label>
                <select id="developerFilter" class="form-select">
                   
                </select>
            </div>

            <div class="col-md-2">
                <label>Status</label>
                <select id="statusFilter" class="form-control">
                    <option value="">All</option>
                    <option value="received">Received</option>
                    <option value="invoice_raised">Invoice Raised</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>Invoice From</label>
                <input type="date"
                       id="dateFrom"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <label>Invoice To</label>
                <input type="date"
                       id="dateTo"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <label>&nbsp;</label>

                <button class="btn btn-primary w-100"
                        id="applyFilter">
                    Apply
                </button>
            </div>

        </div>

    </div>
</div>
<div class="card">

<div class="card-body">

<table class="table table-bordered" id="invoiceTable">

<thead>
<tr>
<th>ID</th>
<th>Booking</th>
<th>Client</th>
<th>Project</th>
<th>Invoice %</th>
<th>Invoice Amount</th>
<th>Invoice File</th>
<th>Received</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

</table>

</div>
</div>
</div>

@endsection

@section('script')

<script>
// ------------------------------------
// Load Project Dropdown
// ------------------------------------

$.get("{{ route('brokerage-payments.projects') }}", function(data){

    $('#projectFilter').empty()
        .append('<option value="">All</option>');

    $.each(data, function(index,item){

        $('#projectFilter').append(
            `<option value="${item.id}">${item.name}</option>`
        );

    });

    $('#projectFilter').selectpicker('refresh');

});
// ------------------------------------
// Load Developer Dropdown
// ------------------------------------

$.get("{{ route('brokerage-payments.developers') }}", function(data){

    $('#developerFilter').empty()
        .append('<option value="">All</option>');

    $.each(data, function(index,item){

        $('#developerFilter').append(
            `<option value="${item.id}">${item.name}</option>`
        );

    });

    $('#developerFilter').selectpicker('refresh');

});


let table = $('#invoiceTable').DataTable({

    processing:true,
    serverSide:true,

    ajax:{
        url:"{{ route('brokerage-payments.datatable') }}",

        data:function(d){

            d.project_id = $('#projectFilter').val();
            d.developer_id = $('#developerFilter').val();
            d.status = $('#statusFilter').val();
            d.date_from = $('#dateFrom').val();
            d.date_to = $('#dateTo').val();

        }
    },

    columns:[

        {data:'id'},
        {data:'booking_id'},
        {data:'client_name'},
        {data:'project_name'},
        {data:'invoice_percent'},
        {data:'invoice_amount_format'},
        {data:'invoice_file_html'},
        {data:'received_amount_format'},
        {data:'status_badge'},
        {data:'action'}

    ]

});



$('#applyFilter').click(function(){

    table.ajax.reload();

    loadSummary();

});



function loadSummary()
{
    $.get(
        "{{ route('brokerage-payments.summary') }}",
        {
            project_id: $('#projectFilter').val(),
            developer_id: $('#developerFilter').val(),
            status: $('#statusFilter').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val()
        },
        function(res){

            $('#totalInvoiced').html(
                '₹ ' + Number(res.invoice).toLocaleString()
            );

            $('#totalReceived').html(
                '₹ ' + Number(res.received).toLocaleString()
            );

            $('#totalTds').html(
                '₹ ' + Number(res.tds).toLocaleString()
            );

            $('#totalPending').html(
                '₹ ' + Number(res.pending).toLocaleString()
            );

        }
    );
}



loadSummary();



$(document).on('click','.delete-payment',function(){

    let id = $(this).data('id');

    if(!confirm('Are you sure you want to delete this invoice?')){
        return;
    }

    $.ajax({

        url: "{{ url('brokerage-payments') }}/" + id,

        type: 'DELETE',

        data:{
            _token:'{{ csrf_token() }}'
        },

        success:function(){

            table.ajax.reload();

            loadSummary();

            alert('Invoice deleted successfully');
        },

        error:function(){

            alert('Delete failed');
        }

    });

});

</script>

@endsection