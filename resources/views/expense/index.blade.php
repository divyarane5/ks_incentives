@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Expense /</span> List</h4>
        <div class="col-md-6">
            @can('expense-create')
            <a href="{{ route('expense.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Expense</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Expenses</h5>
        <div class="table-responsive text-nowrap">
            <table id="expense-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Vendors</th>
                    <th>Created On</th>
                    <th>Modified On</th>
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
      var table = $('#expense-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('expense.index') }}",
            columns: [
                {data: 'name', name: 'expenses.name'},
                {data: 'vendors', name: 'expenses.vendors', sortable: false},
                {data: 'created_at', name: 'expenses.created_at'},
                {data: 'updated_at', name: 'expenses.updated_at'},
                {data: 'action', 'sortable': false, sortable: false},
            ]
      });

    });

    function deleteExpense(id)
    {
        $.confirm({
            title: 'Delete Expense',
            content: 'Are you sure you want to delete expense?',
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
