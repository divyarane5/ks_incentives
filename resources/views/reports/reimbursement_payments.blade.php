@extends('layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6">Reimbursements Payments</h4>
    </div>
    <div class="card">
        <div class="row">
            <div class="col-md-12 datatableButtons  mr-2 pull-right my-3 mb-4 right-align "></div>
        </div>
        <div class="table-responsive text-nowrap">
            <table id="reimbursement-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Settled Amount</th>
                        <th>Settled By</th>
                        <th>Settled At</th>
                        <th>Client Name</th>
                        <th>Project Name</th>
                        <th>Who's Visit Attended</th>
                        <th>Attended By</th>
                        <th>From Location</th>
                        <th>To Destination</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{ asset('assets/vendor/dataTable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/buttons.html5.min.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function () {
      var table = $('#reimbursement-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reports.reimbursement_payments') }}",
            columns: [
                {data: 'code', name: 'reimbursements.id'},
                {data: 'settlement_amount', name: 'settlement_amount'},
                {data: 'settled_by', name: 'settled_by_user.name'},
                {data: 'settled_at', name: 'reimbursement_logs.created_at'},
                {data: 'client_name', name: 'client_name'},
                {data: 'project_name', name: 'project_name'},
                {data: 'visit_attended_of_user', name: 'attended_of.name'},
                {data: 'visit_attended_by', name: 'created_by_user.name'},
                {data: 'source', name: 'source'},
                {data: 'destination', name: 'destination'},
            ],
            order: [3, 'desc']
      });

      var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'reimbursement_payment_export',
                }
            ]
        }).container().appendTo($('.datatableButtons'));
        $('.buttons-excel').removeClass('dt-button buttons-excel buttons-html5').addClass('btn btn-success btn-plus mx-3').attr('id', 'excelHtml5').html('<i class="fa fa-file-excel-o mr-2"></i> Export to Excel');
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
