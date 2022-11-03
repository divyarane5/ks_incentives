<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route("expense.ajax.store") }}" id="expense-form" method="POST">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title">Expense</h5>
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
                            <label class="form-label" for="expense_name">Name</label>
                            <input name="name" class="form-control" id="expense_name" required />
                            <span class="expense_name_error invalid-feedback" role="alert">
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
