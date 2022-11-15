@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-12"><span class="text-muted fw-light">Indent /</span> Closures</h4>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Indents</h5>
        <div class="table-responsive text-nowrap">
            <table id="indent-approval" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Business Unit</th>
                    <th>Bill Mode</th>
                    <th>total</th>
                    <th>Status</th>
                    <th>Close</th>
                    <th>Created On</th>
                    <th>Raised By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
            </table>
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
                    url: "{{ route('indent.closure') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'location', name: 'locations.name'},
                    {data: 'business_unit', name: 'business_units.name'},
                    {data: 'bill_mode', name: 'bill_mode'},
                    {data: 'total', name: 'total'},
                    {data: 'status', name: 'status'},
                    {data: 'close', sortable: false},
                    {data: 'created_at', name: 'indents.created_at', sortable : true},
                    {data: 'raised_by', name: 'users.name'},
                    {data: 'action', 'sortable': false},
                ],
                order: [[6, 'desc']],
        });
    });

    function closeIndent(indentId)
    {
        $.ajax({
            type: 'GET',
            url: "{{ route('indent.close', '') }}/"+indentId,
            dataType: "html",
            success: function (res) {
                $('#indent-approval').DataTable().ajax.reload();
                $.alert({
                    title: 'Success!',
                    content: 'Indent status updated successfully',
                    type: 'green',
                    typeAnimated: true,
                });
            }
        });
    }
</script>
@endsection
