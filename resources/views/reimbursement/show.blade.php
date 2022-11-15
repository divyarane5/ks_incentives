@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4"><a href="{{ route('reimbursement.index') }}" class="text-muted fw-light">Reimbursements </a>/ View</h4>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="card-body">
            <div class="row">
            <h5 class="card-header col-md-6">Reimbursements - {{ $reimbursement->reimbursement_code }}</h5>
            <div class="col-md-6">
                @if (auth()->user()->can('reimbursement-settlement') && $reimbursement->status == "approved")
                    <button class="btn btn-primary pull-right" onclick="updateStatus({{ $reimbursement->id }}, 'settled')">Settle</button>
                @elseif (($reimbursement->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin')) && $reimbursement->status == "pending" )
                    <button class="btn btn-primary pull-right" onclick="updateStatus({{ $reimbursement->id }}, 'approved')">Approve</button>
                    <button class="btn btn-danger pull-right mr-12" onclick="updateStatus({{ $reimbursement->id }}, 'rejected')">Reject</button>
                @endif
            </div>
            </div>
            <div class="mb-4">
                <div class="row m-0">
                    <div class="col-md-6 border-right">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th  width="41%">Client Name: </th>
                                <td>{{ $reimbursement->client_name }}</td>
                            </tr>
                            <tr>
                                <th>From Location: </th>
                                <td>{{ $reimbursement->source }}</td>
                            </tr>
                            <tr>
                                <th>To Destination: </th>
                                <td>{{ $reimbursement->destination }}</td>
                            </tr>
                            <tr>
                                <th>Amount: </th>
                                <td>{{ $reimbursement->amount }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Project Name: </th>
                                <td>{{ $reimbursement->project_name }}</td>
                            </tr>
                            <tr>
                                <th>Transport Mode: </th>
                                <td>{{ config('constants.TRANSPORT_MODE')[$reimbursement->transport_mode] }}</td>
                            </tr>
                            <tr>
                                <th>Who's Visit Attended: </th>
                                <td>{{ $reimbursement->visitAttendedOf->name }}</td>
                            </tr>
                            <tr>
                                <th>Comment: </th>
                                <td>{{ $reimbursement->comment }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if (!empty($reimbursement->file_path))
                <h5 class="card-header">Indent Attachments</h5>
                <div class="mb-4 m-0 row">
                    <div class="col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">
                        @if(getimagesize(storage_path('app/'.$reimbursement->file_path)))
                            <a class="filePopup" data-type="image" target="_blank" href="{{ url('storage/app/'.$reimbursement->file_path) }}">
                            <img style="width: 120px; height: 100px;" src="{{ url('storage/app/'.$reimbursement->file_path) }}" class="prescriptions">
                        @else
                            <a class="filePopup" data-type="file" target="_blank" href="{{ url('storage/app/'.$reimbursement->file_path) }}">
                            <img style="width: 120px; height: 100px;" src="{{ asset("assets/img/icons/unicons/file.png") }}" class="prescriptions">
                        @endif
                        </a>
                        <p style="word-break: break-all;">{{ $reimbursement->file_name }}</p>
                    </div>
                </div>
                @endif
            </div>


            @if (!$reimbursementLog->isEmpty())
            <hr>
                @foreach ($reimbursementLog as $log)
                <div class="{{ ($log->status == 'rejected') ? 'red' : 'green' }}">
                    <span class="comment_date ">{{ date('d M Y H:i:s', strtotime($log->created_at)) }}</span>
                    <p>{{ $log->description }}</p>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="file-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style=" margin: -0.125rem -0.75rem -3.125rem auto; z-index: 9;">
        </button>
        </div>
        <div class="modal-content">
        </div>
    </div>
</div>
@endsection


@section('script')
@include('reimbursement.reimbursement_script')
<script>
    $(document).ready(function () {
        $(".filePopup").on('click', function (e) {
            e.preventDefault();
            if ($(this).data('type') == "image") {
                $("#file-modal .modal-content").html('<img src="'+$(this).attr("href")+'" frameborder="0" height="450px" style="width: fit-content;margin: auto;">');
            } else {
                $("#file-modal .modal-content").html('<embed src="'+$(this).attr("href")+'" frameborder="0" height="450px">');
            }
            $("#file-modal").modal('show');
        });
    });
</script>
@endsection
