@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Vendor /</span> List</h4>
        <div class="col-md-6">
            @can('vendor-create')
            <a href="{{ route('vendor.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Vendor</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Vendor</h5>
        <div class="table-responsive text-nowrap">
            <table id="vendor-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>TDS</th>
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
      var table = $('#vendor-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('vendor.index') }}",
            columns: [
                {data: 'name', name: 'vendors.name'},
                {data: 'tds', name: 'vendors.tds_percentage'},
                {data: 'status', name: 'vendors.status'},
                {data: 'created_at', name: 'vendors.created_at'},
                {data: 'updated_at', name: 'vendors.updated_at'},
                {data: 'action', 'sortable': false},
            ],
            order: [[2, 'desc']],
      });

    });

    function deleteVendor(id)
    {
        $.confirm({
            title: 'Delete Vendor',
            content: 'Are you sure you want to delete vendor?',
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

    function updateVendorStatus(element, vendorId)
    {
        var status = $(element).is(':checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "{{ route('vendor.update_status') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {status: status, vendor_id: vendorId},
            success: function (res) {
                $.alert({
                    title: 'Success!',
                    content: 'Vendor status updated successfully',
                    type: 'green',
                    typeAnimated: true,
                });
            }
        });
    }
</script>
@endsection
