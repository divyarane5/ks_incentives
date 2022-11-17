@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-12"><span class="text-muted fw-light">Indent /</span> Approvals</h4>
    </div>

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
    <!-- Striped Rows -->
    <div class="card">
        <div class="row">
            <h5 class="card-header col-md-6">Indents</h5>
            <div class="col-md-6 datatableButtons  mr-2 pull-right my-3 mb-4 right-align ">
                <button type="button" class="btn btn-primary pull-right mx-md-2" onclick="approveIndent();">Approve</button>
            </div>

        </div>

        <div class="table-responsive text-nowrap">
            <form id="indent_approve_form">
                @csrf
            <table id="indent-approval" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Indent Code</th>
                    <th>Expense</th>
                    <th>Vendor</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Business Unit</th>
                    <th>Bill Mode</th>
                    <th>total</th>
                    <th>Status</th>
                    <th>Created On</th>
                    <th>Raised By</th>
                    <th>Actions</th>
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
<script src="{{ asset('assets/vendor/dataTable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/buttons.html5.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#indent-approval').DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('indent.approval') }}",
                    data: function (d) {
                        d.location_id = $("#location_id").val();
                        d.bill_mode = $("#bill_mode").val();
                        d.business_unit_id = $("#business_unit_id").val();
                        d.status = $("#status").val();
                    }
                },
                columns: [
                    {data: 'approval', sortable: false},
                    {data: 'id', name: 'id'},
                    {data: 'expense', name: 'expenses.name'},
                    {data: 'vendor', name: 'vendors.name'},
                    {data: 'title', name: 'title'},
                    {data: 'location', name: 'locations.name'},
                    {data: 'business_unit', name: 'business_units.name'},
                    {data: 'bill_mode', name: 'bill_mode'},
                    {data: 'total', name: 'indent_items.total'},
                    {data: 'status', name: 'indent_items.status'},
                    {data: 'created_at', name: 'indent_items.created_at', sortable : true},
                    {data: 'raised_by', name: 'users.name'},
                    {data: 'action', 'sortable': false},
                ],
                order: [[1, 'desc']],
        });
        $("#filter").on('click', function () {
            console.log($("#location_id").val());
            $('#indent-approval').DataTable().ajax.reload();
        });


        $("#clear").on('click', function () {
            $("#IndentFilter select").val('');
            $("#IndentFilter select").selectpicker('refresh');
            $('#indent-approval').DataTable().ajax.reload();
        });

        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'indent_approval_export',
                    exportOptions: {
                        columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                    }
                }
            ]
        }).container().appendTo($('.datatableButtons'));
        $('.buttons-excel').removeClass('dt-button buttons-excel buttons-html5').addClass('btn btn-success btn-plus mx-3').attr('id', 'excelHtml5').html('<i class="fa fa-file-excel-o mr-2"></i> Export to Excel');

    });

    function approveIndent() {
        var formData = $("#indent_approve_form").serializeArray();
        $.ajax({
            type: 'POST',
            url: "{{ route('bulk_indent_item_approve') }}",
            data: formData,
            success: function (res) {
                $(".preloader").css('display', 'none');
                window.location.reload();
            },
            beforeSend: function (request) {
                $(".preloader").css('display', 'flex');
            }
        });
    }

</script>
@endsection
