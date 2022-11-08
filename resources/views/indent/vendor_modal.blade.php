<div class="modal fade" id="vendorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route("vendor.ajax.store") }}" id="vendor-form" method="POST">
                @csrf
                <input type="hidden" name="expense_id" id="vendor_expense_id">
                <div class="modal-header">
                    <h5 class="modal-title">Vendor</h5>
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
                            <label class="form-label" for="name">Name</label>
                            <input name="name" class="form-control" id="vendor_name" />
                            <span class="invalid-feedback vendor_name_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" id="expenseSubmit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
