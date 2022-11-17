@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Business Unit /</span> List</h4>
        <div class="col-md-6">
            @can('business_unit-create')
            <a href="{{ route('business_unit.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Business Unit</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Business Unit</h5>
        <div class="table-responsive text-nowrap">
            <table id="business_unit-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
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
      var table = $('#business_unit-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business_unit.index') }}",
            columns: [
                {data: 'name', name: 'business_units.name'},
                {data: 'created_at', name: 'business_units.created_at'},
                {data: 'updated_at', name: 'business_units.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteBusinessUnit(id)
    {
        $.confirm({
            title: 'Delete Business Unit',
            content: 'Are you sure you want to delete business unit?',
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
