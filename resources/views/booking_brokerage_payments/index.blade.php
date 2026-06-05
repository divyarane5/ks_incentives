@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <small class="text-muted">
                    Total Invoiced
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="Total brokerage invoices raised">
                    </i>
                </small>
                <h4 id="totalInvoiced">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <small class="text-muted">
                    Total Received
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="Net amount received from developers">
                    </i>
                </small>
                <h4 id="totalReceived">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-secondary shadow-sm">
            <div class="card-body">
                <small class="text-muted">
                    Total TDS
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="TDS deducted by developers">
                    </i>
                </small>
                <h4 id="totalTds">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <small class="text-muted">
                    Pending Collection
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="Invoice Amount - Received - TDS">
                    </i>
                </small>
                <h4 id="totalPending">₹ 0</h4>
            </div>
        </div>
    </div>

</div>
<div class="row mb-4">

    <div class="col-md-6">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">

                <small class="text-muted">
                    Collection Efficiency
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="(Received + TDS) ÷ Invoice Amount × 100">
                    </i>
                </small>

                <h2 id="collectionEfficiency" class="text-success">
                    0%
                </h2>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">

                <small class="text-muted">
                    Excess Collection
                    <i class="bx bx-info-circle"
                       data-bs-toggle="tooltip"
                       title="Amount received above invoice value">
                    </i>
                </small>

                <h2 id="excessCollection" class="text-danger">
                    ₹ 0
                </h2>

            </div>
        </div>
    </div>

</div>
<h5 class="mb-3">
    Outstanding Aging Analysis
</h5>
<div class="row mb-3">

    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body">
                <h6>0-30 Days</h6>
                <h4 id="aging30">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <h6>31-60 Days</h6>
                <h4 id="aging60">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6>61-90 Days</h6>
                <h4 id="aging90">₹ 0</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-dark">
            <div class="card-body">
                <h6>90+ Days</h6>
                <h4 id="aging90plus">₹ 0</h4>
            </div>
        </div>
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
            <div class="col-md-2">
                <label>&nbsp;</label>

                <button class="btn btn-success w-100"
                        id="exportExcel">
                    Export Excel
                </button>
            </div>

        </div>

    </div>
</div>
<div class="card">

<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered nowrap w-100"
       id="invoiceTable">

<thead>
<tr>
<th>ID</th>
<th>Booking</th>
<th>Client</th>
<th>Project</th>
<th>Developer</th>
<th>Invoice Date</th>
<th>Invoice %</th>
<th>Invoice Amount</th>
<th>Invoice File</th>
<th>Received</th>
<th>TDS</th>
<th>Outstanding</th>
<th>Remarks</th>
<th>Status</th>
<th>Invoice Age</th>
<th>Action</th>
</tr>
</thead>

</table>
</div>
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
    scrollX:true,
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
        {data:'developer_name'},
        {data:'invoice_date_format'},
        {data:'invoice_percent'},
        {data:'invoice_amount_format'},
        {data:'invoice_file_html'},
        {data:'received_amount_format'},
        {data:'tds_amount_format'},
        {data:'outstanding_amount_format'},
        {data:'remarks_text'},
        {data:'status_badge'},
        {data:'invoice_age'},
        {data:'action'}

    ]

});



$('#applyFilter').click(function(){

    table.ajax.reload();

    loadSummary();
    loadAging();
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
            $('#collectionEfficiency').html(
                res.collection_efficiency + '%'
            );
            $('#excessCollection').html(
                '₹ ' + Number(res.excess_collection).toLocaleString()
            );

            $('#outstandingAmount').html(
                '₹ ' + Number(res.outstanding).toLocaleString()
            );
        }
    );
}



loadSummary();

function loadAging()
{
    $.get(
        "{{ route('brokerage-payments.aging') }}",
        function(res){

            $('#aging30').html(
                '₹ ' + Number(res.aging30).toLocaleString()
            );

            $('#aging60').html(
                '₹ ' + Number(res.aging60).toLocaleString()
            );

            $('#aging90').html(
                '₹ ' + Number(res.aging90).toLocaleString()
            );

            $('#aging90plus').html(
                '₹ ' + Number(res.aging90plus).toLocaleString()
            );
        }
    );
}

loadAging();


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
$(document).on('click','.editInvoiceBtn',function(){

    let id = $(this).data('id');

    window.location.href =
        "{{ url('brokerage-payments') }}/" + id + "/edit";

});
$('#exportExcel').click(function(){

    let params = $.param({

        project_id: $('#projectFilter').val(),
        developer_id: $('#developerFilter').val(),
        status: $('#statusFilter').val(),
        date_from: $('#dateFrom').val(),
        date_to: $('#dateTo').val()

    });

    window.location =
        "{{ route('brokerage-payments.export') }}?" + params;

});
</script>

@endsection