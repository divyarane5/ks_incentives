@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Project Ladder /</span> List</h4>
        <div class="col-md-6">
            @can('project_ladder-create')
            <a href="{{ route('project_ladder.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Project Ladder</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Project Ladders</h5>
        <div class="table-responsive text-nowrap">
            <table id="project_ladder-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>AOP </th>
                    <th>No. of Bookings</th>
                    <th>Ladder</th>
                    <th>Project Start Date</th>
                    <th>Project End Date</th>
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
      var table = $('#project_ladder-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('project_ladder.index') }}",
            columns: [
                {data: 'project_id', name: 'project_ladders.project_id'},
                {data: 'aop_id', name: 'project_ladders.aop_id'},
                {data: 'booking', name: 'project_ladders.booking'},
                {data: 'ladder', name: 'project_ladders.ladder'},
                {data: 'project_s_date', name: 'project_ladders.project_s_date'},
                {data: 'project_e_date', name: 'project_ladders.project_e_date'},
                {data: 'created_at', name: 'project_ladders.created_at'},
                {data: 'updated_at', name: 'project_ladders.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteDeveloperLadder(id)
    {
        $.confirm({
            title: 'Delete Project Ladder',
            content: 'Are you sure you want to delete project_ladder?',
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
