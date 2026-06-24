@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6">
            <span class="text-muted fw-light">Company Bank /</span> List
        </h4>

        <div class="col-md-6">
            @can('company-bank-create')
                <a href="{{ route('company-bank.create') }}"
                   class="btn btn-primary pull-right my-3 mb-4">
                    Add Bank Account
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Company Bank Accounts</h5>

        <div class="table-responsive text-nowrap">
            <table id="bank-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Bank</th>
                        <th>Account No</th>
                        <th>IFSC</th>
                        <th>GSTIN</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function () {

    $('#bank-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('company-bank.index') }}",
        columns: [
            {data: 'account_name', name: 'account_name'},
            {data: 'bank_name', name: 'bank_name'},
            {data: 'account_number', name: 'account_number'},
            {data: 'ifsc', name: 'ifsc'},
            {data: 'gstin', name: 'gstin'},
            {data: 'status', name: 'status'},
            {data: 'action', orderable: false, searchable: false},
        ]
    });

});

// delete
function deleteRow(id)
{
    if(confirm('Are you sure?')) {
        document.getElementById(id).submit();
    }
}
</script>
@endsection