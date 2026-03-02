@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Developer /</span> List</h4>
        <div class="col-md-6">
            @can('developer-create')
            <a href="{{ route('developer.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Developer</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Developers</h5>
        <div class="table-responsive text-nowrap">
            <table id="developer-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Ladders</th>
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
      var table = $('#developer-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('developer.index') }}",
            columns: [
                {data: 'name', name: 'developers.name'},
                {data: 'view_ladders', orderable: false, searchable: false},
                {data: 'created_at', name: 'developers.created_at'},
                {data: 'updated_at', name: 'developers.updated_at'},
                {data: 'action', orderable: false, searchable: false},
            ]
      });

    });

    function deleteDeveloper(id)
    {
        $.confirm({
            title: 'Delete Developer',
            content: 'Are you sure you want to delete developer?',
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
