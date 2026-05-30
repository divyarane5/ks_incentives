@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

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

        success:function(response){

            $('#invoiceTable').DataTable().ajax.reload();

            alert('Invoice deleted successfully');
        },

        error:function(xhr){

            console.log(xhr);

            alert('Delete failed');
        }

    });

});
$('#invoiceTable').DataTable({

processing:true,
serverSide:true,

ajax:"{{ route('brokerage-payments.datatable') }}",

columns:[

{data:'id'},
{data:'booking_id'},
{data:'client_name'},
{data:'project_name'},
{data:'invoice_percent'},
{data:'invoice_amount_format'},
{data:'invoice_file'},
{data:'received_amount_format'},
{data:'status_badge'},
{data:'action'}

]

});

</script>

@endsection