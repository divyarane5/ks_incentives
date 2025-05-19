@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">AOP Ladder /</span> List</h4>
        <div class="col-md-6">
            @can('developer_ladder-create')
            <a href="{{ route('developer_ladder.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add AOP Ladder</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">AOP Ladders</h5>
        <div class="table-responsive text-nowrap">
            <table id="developer_ladder-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Developer Name</th>
                    <th>AOP Amount</th>
                    <th>Ladder</th>
                    <th>AOP Start Date</th>
                    <th>AOP End Date</th>
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
      var table = $('#developer_ladder-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('developer_ladder.index') }}",
            columns: [
                {data: 'developer_id', name: 'developer_ladders.developer_id'},
                {data: 'aop', name: 'developer_ladders.aop'},
                {data: 'ladder', name: 'developer_ladders.ladder'},
                {data: 'aop_s_date', name: 'developer_ladders.aop_s_date'},
                {data: 'aop_e_date', name: 'developer_ladders.aop_e_date'},
                {data: 'created_at', name: 'developer_ladders.created_at'},
                {data: 'updated_at', name: 'developer_ladders.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteDeveloperLadder(id)
    {
        $.confirm({
            title: 'Delete AOP Ladder',
            content: 'Are you sure you want to delete developer_ladder?',
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
