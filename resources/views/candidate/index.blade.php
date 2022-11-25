@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Candidate /</span> List</h4>
        <div class="col-md-6">
            @can('candidate-create')
            <a href="{{ route('candidate.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Candidate</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Candidate</h5>
        <div class="table-responsive text-nowrap">
            <table id="candidate-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Created By</th>
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
      var table = $('#candidate-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('candidate.index') }}",
            columns: [
                {data: 'name', name: 'candidates.name'},
                {data: 'email', name: 'candidates.email'},
                {data: 'phone', name: 'candidates.phone'},
                {data: 'status', name: 'candidates.status'},
                {data: 'created_at', name: 'candidates.created_at'},
                {data: 'created_by', name: 'users.name'},
                {data: 'action', 'sortable': false},
            ],
            order: [4, 'desc']
      });

    });

    function deleteCandidate(id)
    {
        $.confirm({
            title: 'Delete Candidate',
            content: 'Are you sure you want to delete candidate?',
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
