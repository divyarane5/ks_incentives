@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Indent /</span> List</h4>
        <div class="col-md-6">
            @can('indent-create')
            <a href="{{ route('indent.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Indent</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Indents</h5>
        <div class="table-responsive text-nowrap">
            <table id="indent-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
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
        </div>
        </div>
        <!--/ Striped Rows -->
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#indent-datatable').DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('indent.index') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'location', name: 'locations.name'},
                    {data: 'business_unit', name: 'business_units.name'},
                    {data: 'bill_mode', name: 'bill_mode'},
                    {data: 'total', name: 'total'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'indents.created_at', sortable : true},
                    {data: 'raised_by', name: 'users.name'},
                    {data: 'action', 'sortable': false},
                ],
                order: [[6, 'desc']],
        });
    });

    function deleteIndent(id)
    {
        $.confirm({
            title: 'Delete Indent',
            content: 'Are you sure you want to delete indent?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Yes',
                    btnClass: 'btn-red',
                    action: function(){
                        event.preventDefault();
                        document.getElementById(id).submit()
                    }
                },
                close: function () {
                }
            }
        });
    }
</script>
@endsection
