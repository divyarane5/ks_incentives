@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4"><a href="{{ route('indent.index') }}" class="text-muted fw-light">Indent</a>/ View</h4>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-header">Indent - {{ $indent->indent_code }}</h5>
            <div class="mb-4">
                <div class="row m-0">
                    <div class="col-md-6 border-right">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th  width="41%">Title: </th>
                                <td>{{ $indent->title }}</td>
                            </tr>
                            <tr>
                                <th>Bill Mode: </th>
                                <td>{{ config('constants.BILL_MODES')[$indent->bill_mode] }}</td>
                            </tr>
                            <tr>
                                <th>Location: </th>
                                <td>{{ $indent->location->name }}</td>
                            </tr>

                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Business Unit: </th>
                                <td>{{ $indent->businessUnit->name }}</td>
                            </tr>
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
                                <th width="20%">Description: </th>
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
                            <th>Expense</th>
                            <th>Vendor</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Sub Total</th>
                            <th>GST</th>
                            <th>TDS</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (!empty($indent->indentItems))
                            @foreach ($indent->indentItems as $i => $item)
                                <tr>
                                    <th>{{ $item->expense->name }}</th>
                                    <th>
                                        {{ $item->vendor->name }}
                                        @if($item->vendor->status == 0)
                                            <span class="red vendor_error">Please contact to finance for this vendor</span>
                                        @endif
                                    </th>
                                    <th>{{ $item->quantity }}</th>
                                    <th>{{ $item->unit_price }}</th>
                                    <th>{{ $item->unit_price*$item->quantity }}</th>
                                    <th>{{ $item->gst }}</th>
                                    <th>{{ $item->tds }}</th>
                                    <th>{{ $item->total }}</th>
                                    <th class="center-align">
                                        {{ config('constants.INDENT_ITEM_STATUS')[$item->status] }}<br>
                                        @if (((in_array(auth()->user()->id, explode(",", $item->next_approver_id))) || auth()->user()->hasRole('Superadmin')) &&  $item->status != "approved" && $item->status != "rejected")
                                        <button type="button" class="btn rounded-pill btn-outline-primary" onclick="changeIndentItemStatus({{ $item->id }}, 'approved');">Approve</button>
                                        <button type="button" class="btn rounded-pill btn-outline-danger" onclick="changeIndentItemStatus({{ $item->id }}, 'rejected');">Reject</button>
                                        @endif
                                    </th>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="right-align">Total</td>
                            <td colspan="2">{{ $indent->total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <h5 class="card-header">Indent Payment</h5>
            <div class="mb-4 m-0">
                <form action="{{ route('indent.payment_update', $indent->id) }}">
                    <table class="table table-striped" id="indent_payment_table">
                        <thead>
                            <tr>
                                <th>Payment method<span class="start-mark">*</span></th>
                                <th>Description</th>
                                <th>Amount<span class="start-mark">*</span></th>
                                @can('indent-payment-conclude')
                                    <th></th>
                                @endcan
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
                                        @can('indent-payment-conclude')
                                            <td></td>
                                        @endcan
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
                                <td>
                                    <b class="payment-final-total">
                                        {{ $total }}
                                    </b>
                                </td>
                                @can('indent-payment-conclude')
                                    <td><button type="button" class="btn btn-icon btn-outline-primary float-end" onclick="addPaymentItem()"><i class="tf-icons bx bx-plus"></i></button></td>
                                @endcan
                            </tr>
                        </tfoot>
                    </table>
                    @can('indent-payment-conclude')
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary my-4" >Update Payment</button>
                    </div>
                    @endcan
                </form>
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
                                            <span class="comment_date"><b>{{ $comment->user->name }}</b> {{ date('d M Y H:i:s', strtotime($comment->created_at)) }}</span>
                                            <p>{{ $comment->comment }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-top-history" role="tabpanel">
                                @if (!empty($indent->indentApproveLogs))
                                    @foreach ($indent->indentApproveLogs->sortByDesc('id') as $log)
                                    <div class="{{ ($log->status == 'rejected') ? 'red' : 'green' }}">
                                        <span class="comment_date ">{{ date('d M Y H:i:s', strtotime($log->created_at)) }}</span>
                                        <p>{{ $log->description }}</p>
                                    </div>
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

<!-- payment line item copy -->

<table id="payment_item" style="display: none">
    <tr>
        <td>
            <input type="hidden" name="indent_payment_id[]" value="">
            <select name="payment_method_id[]" class=" payment_method_id raw-select form-select" aria-label="Payment method" required>
                <option value="">Select Payment method</option>
                @if (!empty($paymentMethods))
                    @foreach ($paymentMethods as $methods)
                        <option value="{{ $methods->id }}">{{ $methods->name }}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <textarea name="payment_description[]" class="form-control payment_description" aria-label="Description" rows="2"></textarea>
        </td>
        <td>
            <input type="number" name="amount[]" class="form-control amount" min="1" required />
        </td>
        <td>
            <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removePaymentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
        </td>
    </tr>
</table>


<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route("update_indent_item_status") }}" id="indent_reject"  method="POST">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title">Indent Rejection</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <input type="hidden" name="indent_item_id" id="indent_item_id" value="">
                            <label class="form-label" for="reject_remark">Remark</label>
                            <textarea name="comment" class="form-control" id="reject_remark" required ></textarea>
                            <span class="reject_remark_error invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" id="rejectRemarkSubmit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('script')
<script src="{{ asset("assets/js/indent.js") }}"></script>
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

        $("#indent_reject").on("submit", function(e) {
            e.preventDefault();
            var indentItemId = $("#indent_reject #indent_item_id").val();
            var comment = $("#indent_reject #reject_remark").val();
            updateIndentStatus(indentItemId, "rejected", comment);
        });
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

    function changeIndentItemStatus(indentItemId, status, comment)
    {
        if (status == "rejected") {
            $("#indent_reject #indent_item_id").val(indentItemId)
            $("#rejectModal").modal('show');
            // $.confirm({
            //     title: 'Reject Indent Item Request.',
            //     content: 'Are you sure you want to reject indent item request?',
            //     type: 'red',
            //     typeAnimated: true,
            //     buttons: {
            //         tryAgain: {
            //             text: 'Yes',
            //             btnClass: 'btn-red',
            //             action: function(){
            //                 event.preventDefault();
            //                 updateIndentStatus(indentItemId, status);
            //             }
            //         },
            //         close: function () {
            //         }
            //     }
            // });
        } else {
            updateIndentStatus(indentItemId, status);
        }
    }

    function updateIndentStatus(indentItemId, status, comment = "") {
        $.ajax({
            type: 'POST',
            url: "{{ route('update_indent_item_status') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {status: status, indent_item_id: indentItemId, comment: comment},
            success: function (res) {
                $(".preloader").css('display', 'none');
                window.location.reload();
            },
            beforeSend: function (request) {
                $(".preloader").css('display', 'flex');
            }
        });
    }

    function calculatePaymentFinalTotal()
    {
        //overall total
        var finalTotal = 0;
        $("#indent_payment_table tr td:nth-child(3)").each(function (i) {
            var subTotal = $(this).find('.amount').val();
            if (subTotal != "" && !isNaN(subTotal)) {
                finalTotal += Number(subTotal);
            }
        })
        finalTotal = finalTotal + {{ $total }};
        $(".payment-final-total").html(finalTotal);
    }
</script>
@endsection
