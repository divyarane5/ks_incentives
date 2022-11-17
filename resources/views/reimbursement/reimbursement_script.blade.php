
<div class="modal fade" id="settlementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route("update_reimbursement_status") }}" id="reimbursement_settle"  method="POST">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title">Reimbursement Settlement</h5>
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
                            <input type="hidden" name="reimbursement_id" id="reimbursement_id" value="">
                            <input type="hidden" name="status" id="reimbursement_status" value="">
                            <label class="form-label" for="settlement_amount">Settlement Amount</label>
                            <input type="number" name="settlement_amount" class="form-control" id="settlement_amount" required >
                            <span class="settlement_amount_error invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="settlement_comment">Remark</label>
                            <textarea name="settlement_comment" class="form-control" id="settlement_comment" required ></textarea>
                            <span class="settlement_comment_error invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" id="settlementSubmit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $("#reimbursement_settle").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        $.ajax({
            type: 'POST',
            url: "{{ route('update_reimbursement_status') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: data,
            success: function (res) {
                $(".preloader").css('display', 'none');
                $("#settlementModal").modal("hide");
                if ($('#reimbursement-datatable').length > 0) {
                    $('#reimbursement-datatable').DataTable().ajax.reload();
                } else {
                    window.location.reload();
                }
            },
            beforeSend: function (request) {
                $(".preloader").css('display', 'flex');
            }
        });
      });
    });


    function updateStatus(reimbursementId, status) {
        if (status == "settled") {
            $("#reimbursement_id").val(reimbursementId);
            $("#reimbursement_status").val(status);
            $("#settlementModal").modal("show");
        } else {
            $.ajax({
                type: 'POST',
                url: "{{ route('update_reimbursement_status') }}",
                headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                },
                data: {reimbursement_id: reimbursementId, status: status},
                success: function (res) {
                    $(".preloader").css('display', 'none');
                    if ($('#reimbursement-datatable').length > 0) {
                        $('#reimbursement-datatable').DataTable().ajax.reload();
                    } else {
                        window.location.reload();
                    }
                },
                beforeSend: function (request) {
                    $(".preloader").css('display', 'flex');
                }
            });
        }
    }
</script>
