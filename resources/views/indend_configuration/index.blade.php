@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Indent Configuration /</span> List</h4>
        <div class="col-md-6">
            @can('configuration-create')
            <a href="{{ route('indent_configuration.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Indent Configuration</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Indent Configurations</h5>
        <div class="table-responsive text-nowrap">
            <table id="indent-configuration-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Expense</th>
                    <th>Monthly Limit</th>
                    <th>Indent Limit</th>
                    <th>Created On</th>
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
      var table = $('#indent-configuration-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('indent_configuration.index') }}",
            columns: [
                {data: 'user', name: 'users.name'},
                {data: 'expense', name: 'expenses.name'},
                {data: 'monthly_limit', name: 'monthly_limit'},
                {data: 'indent_limit', name: 'indent_limit'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteIndentConfiguration(id)
    {
        $.confirm({
            title: 'Delete Indent Configuration',
            content: 'Are you sure you want to delete indent configuration?',
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
