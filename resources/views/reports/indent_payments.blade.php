@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold ">Indent Payments</h4>

    <!-- Striped Rows -->
    <div class="card my-4" id="IndentFilter">
        <div class="card-body row">
            <div class="mb-3 col-md-3">
                <label for="bill_mode" class="form-label">Bill Mode</label>
                <select name="bill_mode" class="" id="bill_mode" aria-label="Bill Mode">
                    <option value="">Select Bill Mode</option>
                    @php
                        $billModes = config('constants.BILL_MODES');
                    @endphp
                    @foreach ($billModes as $key => $mode)
                        <option value="{{ $key }}" >{{ $mode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label for="location_id " class="form-label">Location</label>
                <select name="location_id" class="" id="location_id" aria-label="Location" >
                    <option value="">Select Location</option>
                    @if (!empty($locations))
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label for="business_unit_id" class="form-label">Business Unit</label>
                <select name="business_unit_id" class="" id="business_unit_id" aria-label="Business Unit" >
                    <option value="">Select Business Unit</option>
                    @if (!empty($businessUnits))
                        @foreach ($businessUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="" id="status" aria-label="Business Unit" >
                    <option value="">Select Status</option>
                    @php
                        $statuses = config('constants.INDENT_STATUS');
                    @endphp
                    @if (!empty($statuses))
                        @foreach ($statuses as $key => $status)
                            <option value="{{ $key }}" >{{ $status }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-3 col-md-12">
                <button type="button" id="filter" class="btn btn-primary me-sm-2">Filter</button>
                <button type="button" id="clear" class="btn btn-secondary" >Clear</button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="row">
        <div class="datatableButtons mr-2 pull-right my-3 mb-4 right-align col-md-12"></div>
        </div>
        <div class="table-responsive text-nowrap">
            <table id="indent-payment-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment By</th>
                    <th>Created On</th>
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
<script src="{{ asset('assets/vendor/dataTable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/buttons.html5.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#indent-payment-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.indent_payments') }}",
                    data: function (d) {
                        d.location_id = $("#location_id").val();
                        d.bill_mode = $("#bill_mode").val();
                        d.business_unit_id = $("#business_unit_id").val();
                        d.status = $("#status").val();
                    }
                },
                columns: [
                    {data: 'code', name: 'indents.id'},
                    {data: 'title', name: 'indents.title'},
                    {data: 'amount', name: 'indent_payments.amount'},
                    {data: 'payment_method', name: 'payment_methods.name'},
                    {data: 'payment_by', name: 'users.name'},
                    {data: 'created_at', name: 'indent_payments.created_at'}
                ]
        });

        $("#filter").on('click', function () {
            console.log($("#location_id").val());
            $('#indent-payment-datatable').DataTable().ajax.reload();
        });


        $("#clear").on('click', function () {
            $("#IndentFilter select").val('');
            $("#IndentFilter select").selectpicker('refresh');
            $('#indent-payment-datatable').DataTable().ajax.reload();
        });

        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'indent_payment_export',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5]
                    }
                }
            ]
        }).container().appendTo($('.datatableButtons'));
        $('.buttons-excel').removeClass('dt-button buttons-excel buttons-html5').addClass('btn btn-success btn-plus mx-3').attr('id', 'excelHtml5').html('<i class="fa fa-file-excel-o mr-2"></i> Export to Excel');
    });


</script>
@endsection
