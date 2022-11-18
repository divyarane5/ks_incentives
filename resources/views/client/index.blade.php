@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light"> Clients /</span> List</h4>
        <div class="col-md-6">
            @can('referral-client-create')
            <a href="{{ route('client.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Client</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header"> Clients</h5>
        <div class="table-responsive text-nowrap">
            <table id="client-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Sales Person</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Subject Name</th>
                    <th>Template Name</th>
                    <th>Status</th>
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
      var table = $('#client-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('client.index') }}",
            columns: [
                {data: 'sales_person', name: 'clients.sales_person'},
                {data: 'client_name', name: 'clients.client_name'},
                {data: 'client_email', name: 'clients.client_email'},
                {data: 'subject_name', name: 'clients.subject_name'},
                {data: 'template_name', name: 'templates.name'},
                {data: 'status', name: 'click'},
                {data: 'created_at', name: 'clients.created_at'},
                {data: 'updated_at', name: 'clients.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteLocation(id)
    {
        $.confirm({
            title: 'Delete Location',
            content: 'Are you sure you want to delete location?',
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
