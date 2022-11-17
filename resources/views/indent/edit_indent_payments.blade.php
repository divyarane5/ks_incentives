<div class="mb-3 col-md-12 my-4">
    <h5 class="mb-0">Payment Details</h5>
</div>
<div class="mb-3 col-md-12" style="overflow: auto">
    <table class="table table-striped" id="indent_payment_table">
        <thead>
            <tr>
                <th>Payment method<span class="start-mark">*</span></th>
                <th>Description</th>
                <th>Amount<span class="start-mark">*</span></th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @php
                $total = 0;
            @endphp
            @if (!empty(old('payment_method_id')))
                @for ($i = 0; $i < sizeof(old('payment_method_id')); $i++)
                    <tr>
                        <td>
                            <input type="hidden" name="indent_payment_id[]" value="{{ old('indent_payment_id')[$i] }}">
                            <select name="payment_method_id[]" class=" payment_method_id raw-select form-select" aria-label="Payment method" >
                                <option value="">Select Payment method</option>
                                @if (!empty($paymentMethods))
                                    @foreach ($paymentMethods as $methods)
                                        <option value="{{ $methods->id }}" {{ (old('payment_method_id')[$i] == $methods->id) ? 'selected' : '' }}>{{ $methods->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('payment_method_id.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <textarea name="payment_description[]" class="form-control payment_description" aria-label="Description" rows="2">{{ old('payment_description')[$i] }}</textarea>
                            @error('payment_description.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="amount[]" class="form-control amount" min="1" value="{{ old('amount')[$i] }}" />
                            @error('amount.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removePaymentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @php
                        $amount = !empty(old('amount')[$i]) ? old('amount')[$i] : 0;
                        $total += $amount;
                    @endphp
                @endfor
            @elseif (!empty($indent->indentPayments))
                @foreach ($indent->indentPayments as $i => $payment)
                    <tr>
                        <td>
                            <input type="hidden" name="indent_payment_id[]" value="{{ $payment->id }}">
                            <select name="payment_method_id[]" class=" payment_method_id raw-select form-select" aria-label="Payment method" >
                                <option value="">Select Payment method</option>
                                @if (!empty($paymentMethods))
                                    @foreach ($paymentMethods as $methods)
                                        <option value="{{ $methods->id }}" {{ ($payment->payment_method_id == $methods->id) ? 'selected' : '' }}>{{ $methods->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            <textarea name="payment_description[]" class="form-control payment_description" aria-label="Description" rows="2">{{ $payment->description }}</textarea>
                        </td>
                        <td>
                            <input type="number" name="amount[]" class="form-control amount" min="1" value="{{ $payment->amount }}" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removePaymentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @php
                        $total += $payment->amount;
                    @endphp
                @endforeach
            @else
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
                        <input type="number" name="amount[]" class="form-control amount" min="1"  required/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removePaymentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                    </td>
                </tr>
            @endif

        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="right-align">
                    Total
                </td>
                <td>
                    <b class="payment-final-total">
                        @if (!empty(old('payment_method_id')) || !empty($indent->indentPayments))
                            {{ $total }}
                        @else
                        -
                        @endif
                    </b>
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-outline-primary float-end" onclick="addPaymentItem()"><i class="tf-icons bx bx-plus"></i></button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
