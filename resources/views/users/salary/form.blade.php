<div class="card">
    <h5 class="card-header">Add / Update Salary</h5>

    <div class="card-body">
        <form method="POST" action="{{ route('users.salary.store', $user->id) }}">
            @csrf

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Financial Year</label>
                    <input type="text" name="financial_year" class="form-control" placeholder="2025-26" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-control" required>
                        <option value="">Select Month</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="credited_amount" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Credited On</label>
                    <input type="date" name="credited_on" class="form-control" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Save Salary
            </button>
        </form>
    </div>
</div>
