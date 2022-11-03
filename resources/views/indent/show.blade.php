@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Indent /</span> View</h4>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-header">Indent - {{ $indent->title }}</h5>
            <div class="mb-4">
                <div class="row m-0">
                    <div class="col-md-6 border-right">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Bill Mode: </th>
                                <td>{{ config('constants.BILL_MODES')[$indent->bill_mode] }}</td>
                            </tr>
                            <tr>
                                <th>Location: </th>
                                <td>{{ $indent->location->name }}</td>
                            </tr>
                            <tr>
                                <th>Business Unit: </th>
                                <td>{{ $indent->businessUnit->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Bill Submission Date - Softcopy: </th>
                                <td>{{ (!empty($indent->softcopy_bill_submission_date)) ? date('d-m-Y', strtotime($indent->softcopy_bill_submission_date)) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Bill Submission Date - Hardcopy: </th>
                                <td>{{ (!empty($indent->hardcopy_bill_submission_date)) ? date('d-m-Y', strtotime($indent->hardcopy_bill_submission_date)) : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tr>
                                <th>Description: </th>
                                <td>{{ $indent->description }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class="card-header">Indent Items</h5>
            <div class="mb-4 m-0">
                <table class="table table-striped" id="indent_item_table">
                    <thead>
                        <tr>
                            <th style="width: 272px;">Expense</th>
                            <th style="width: 272px;">Vendor</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (!empty($indent->indentItems))
                            @foreach ($indent->indentItems as $i => $item)
                                <tr>
                                    <th>{{ $item->expense->name }}</th>
                                    <th>{{ $item->vendor->name }}</th>
                                    <th>{{ $item->quantity }}</th>
                                    <th>{{ $item->unit_price }}</th>
                                    <th>{{ $item->total }}</th>
                                    <th>
                                        @if ($item->next_approver_id == auth()->user()->id)
                                        <button type="button" class="btn rounded-pill btn-outline-primary" onclick="changeIndentItemStatus({{ $item->id }}, 'approved');">Approve</button>
                                        <button type="button" class="btn rounded-pill btn-outline-danger" onclick="changeIndentItemStatus({{ $item->id }}, 'rejected');">Reject</button>
                                        @else
                                        {{ config('constants.INDENT_ITEM_STATUS')[$item->status] }}
                                        @endif
                                    </th>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="right-align">Total</td>
                            <td colspan="2">{{ $indent->total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <h5 class="card-header">Indent Payment</h5>
            <div class="mb-4 m-0">
                <table class="table table-striped" id="indent_payment_table">
                    <thead>
                        <tr>
                            <th>Payment method<span class="start-mark">*</span></th>
                            <th>Description</th>
                            <th>Amount<span class="start-mark">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @if (!empty($indent->indentPayments))
                            @foreach ($indent->indentPayments as $i => $payment)
                                <tr>
                                    <td>{{ $payment->paymentMethod->name }}</td>
                                    <td>{{ $payment->description }}</td>
                                    <td>{{ $payment->amount }}</td>
                                </tr>
                                @php
                                    $total += $payment->amount;
                                @endphp
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="right-align">Total</td>
                            <td>{{ $total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <h5 class="card-header">Indent Attachments</h5>
            <div class="mb-4 m-0 row">
                @if (!empty($indent->indentAttachments))
                    @foreach ($indent->indentAttachments as $attachment)
                        <div class="col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">
                            @if(getimagesize(storage_path('app/'.$attachment->file_path)))
                                <a class="filePopup" data-type="image" target="_blank" href="{{ url('storage/app/'.$attachment->file_path) }}">
                                <img style="width: 120px; height: 100px;" src="{{ url('storage/app/'.$attachment->file_path) }}" class="prescriptions">
                            @else
                                <a class="filePopup" data-type="file" target="_blank" href="{{ url('storage/app/'.$attachment->file_path) }}">
                                <img style="width: 120px; height: 100px;" src="{{ asset("assets/img/icons/unicons/file.png") }}" class="prescriptions">
                            @endif
                            </a>
                            <p style="word-break: break-all;">{{ $attachment->file_name }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="">
                    <div class="nav-align-top mb-4">
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-comment" aria-controls="navs-top-home" aria-selected="true" >
                                    Comments
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-history" aria-controls="navs-top-profile" aria-selected="false" >
                                    History
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-top-comment" role="tabpanel">
                                <div class="form-group">
                                    <textarea class="form-control" name="comment" id="comment" placeholder="Add Comment"></textarea>
                                    <span class="invalid-feedback invalid-feedback-comment" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary my-4" onclick="addComment()">Submit</button>
                                    <button type="button" class="btn btn-default my-4" onclick="document.getElementById('comment').value = ''">Cancel</button>
                                </div>
                                <div>
                                    @if ($indent->indentComments)
                                        @foreach ($indent->indentComments->sortByDesc('id') as $comment)
                                            <span class="comment_date">{{ date('d M Y H:i:s', strtotime($comment->created_at)) }}</span>
                                            <p>{{ $comment->comment }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-top-history" role="tabpanel">
                                @if (!empty($indent->indentApproveLogs))
                                    @foreach ($indent->indentApproveLogs->sortByDesc('id') as $log)
                                        <span class="comment_date">{{ date('d M Y H:i:s', strtotime($log->created_at)) }}</span>
                                        <p>{{ $log->description }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        })
    });

    function addComment()
    {
        var comment = $("#comment").val();
        if (comment == "") {
            $(".invalid-feedback-comment strong").html("The comment field is required");
            return false;
        }
        $(".invalid-feedback-comment").html("");
        $.ajax({
            type: 'POST',
            url: "{{ route('indent.comment') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {comment: comment, indent_id: {{ $indent->id }}},
            success: function (res) {
                window.location.reload();
            }
        });
    }

    function changeIndentItemStatus(indentItemId, status)
    {
        if (status == "rejected") {
            $.confirm({
                title: 'Reject Indent Item Request.',
                content: 'Are you sure you want to reject indent item request?',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Yes',
                        btnClass: 'btn-red',
                        action: function(){
                            event.preventDefault();
                            updateIndentStatus(indentItemId, status);
                        }
                    },
                    close: function () {
                    }
                }
            });
        } else {
            updateIndentStatus(indentItemId, status);
        }
    }

    function updateIndentStatus(indentItemId, status) {
        $.ajax({
            type: 'POST',
            url: "{{ route('update_indent_item_status') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {status: status, indent_item_id: indentItemId},
            success: function (res) {
                window.location.reload();
            }
        });
    }
</script>
@endsection
