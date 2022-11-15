@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-12"><span class="text-muted fw-light">Indent /</span> Approvals</h4>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="row">
            <h5 class="card-header col-md-6">Indents</h5>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary pull-right my-3 mx-md-2" onclick="approveIndent();">Approve</button>
            </div>

        </div>

        <div class="table-responsive text-nowrap">
            <form id="indent_approve_form">
                @csrf
            <table id="indent-approval" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Indent Code</th>
                    <th>Expense</th>
                    <th>Vendor</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Business Unit</th>
                    <th>Bill Mode</th>
                    <th>total</th>
                    <th>Status</th>
                    <th>Created On</th>
                    <th>Raised By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
            </table>
            </form>
        </div>
        </div>
        <!--/ Striped Rows -->
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#indent-approval').DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('indent.approval') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    {data: 'approval', sortable: false},
                    {data: 'id', name: 'id'},
                    {data: 'expense', name: 'expenses.name'},
                    {data: 'vendor', name: 'vendors.name'},
                    {data: 'title', name: 'title'},
                    {data: 'location', name: 'locations.name'},
                    {data: 'business_unit', name: 'business_units.name'},
                    {data: 'bill_mode', name: 'bill_mode'},
                    {data: 'total', name: 'indent_items.total'},
                    {data: 'status', name: 'indent_items.status'},
                    {data: 'created_at', name: 'indent_items.created_at', sortable : true},
                    {data: 'raised_by', name: 'users.name'},
                    {data: 'action', 'sortable': false},
                ],
                order: [[1, 'desc']],
        });
    });

    function approveIndent() {
        var formData = $("#indent_approve_form").serializeArray();
        $.ajax({
            type: 'POST',
            url: "{{ route('bulk_indent_item_approve') }}",
            data: formData,
            success: function (res) {
                $(".preloader").css('display', 'none');
                window.location.reload();
            },
            beforeSend: function (request) {
                $(".preloader").css('display', 'flex');
            }
        });
    }

</script>
@endsection
