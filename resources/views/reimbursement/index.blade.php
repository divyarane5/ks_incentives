@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Reimbursements /</span> List</h4>
        <div class="col-md-6">
            @can('reimbursement-create')
            <a href="{{ route('reimbursement.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Reimbursement</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="row">
            <h5 class="card-header col-md-6">Reimbursements</h5>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary pull-right my-3 mx-2 status_btn" onclick="changeReimbursementsStatus('approved');">Approve</button>
                <button type="button" class="btn btn-danger pull-right my-3 mx-2 status_btn" onclick="changeReimbursementsStatus('rejected');">Reject</button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <form id="reimbursement_approve_form">
                @csrf
                <table id="reimbursement-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Client Name</th>
                        <th>Project Name</th>
                        <th>Who's Visit Attended</th>
                        <th>From Location</th>
                        <th>To Destination</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                </tbody>
                </table>
            </form>
        </div>
        </div>
        <!--/ Striped Rows -->
    </div>
@endsection

@section('script')
@include('reimbursement.reimbursement_script')

<script type="text/javascript">

    $(document).ready(function () {
      var table = $('#reimbursement-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reimbursement.index') }}",
            columns: [
                {data: 'approval', sortable:false},
                {data: 'id', name: 'reimbursements.id'},
                {data: 'client_name', name: 'client_name'},
                {data: 'project_name', name: 'project_name'},
                {data: 'visit_attended_of_user', name: 'attended_of.name'},
                {data: 'source', name: 'source'},
                {data: 'destination', name: 'destination'},
                {data: 'amount', name: 'amount'},
                {data: 'status', sortable: false},
                {data: 'action', sortable: false},
            ],
            drawCallback: function( settings ) {
                if ($(".reimbursement_approval").length == 0) {
                    $(".status_btn").css('display', 'none');
                }
            },
            order: [1, 'desc']
      });



    });

    function deleteReimbursement(id)
    {
        $.confirm({
            title: 'Delete Reimbursement',
            content: 'Are you sure you want to delete reimbursement?',
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

    function changeReimbursementsStatus(status)
    {
        var formData = $("#reimbursement_approve_form").serializeArray();
        formData.push({"name":"status", "value": status });
        $.ajax({
            type: 'POST',
            url: "{{ route('update_bulk_reimbursement_status') }}",
            data: formData,
            success: function (res) {
                $(".preloader").css('display', 'none');
                $('#reimbursement-datatable').DataTable().ajax.reload();
            },
            beforeSend: function (request) {
                $(".preloader").css('display', 'flex');
            }
        });
    }


</script>
@endsection
