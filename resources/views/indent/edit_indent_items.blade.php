<div class="mb-3 col-md-12">
    <h5 class="mb-0">Indent Items</h5>
</div>
<div class="mb-3 col-md-12" style="overflow: auto">
    <table class="table table-striped" id="indent_item_table">
        <thead>
            <tr>
                <th style="width: 272px;">Expense<span class="start-mark">*</span></th>
                <th style="width: 272px;">Vendor<span class="start-mark">*</span></th>
                <th>Quantity<span class="start-mark">*</span></th>
                <th>Unit Price<span class="start-mark">*</span></th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @php
                $total = 0;
            @endphp
            @if (!empty(old('expense_id')))
                @for ($i = 0; $i < sizeof(old('expense_id')); $i++)
                    <tr>
                        <td>
                            <input type="hidden" name="indent_item_id[]" value="{{ old('indent_item_id')[$i] }}">
                            <select name="expense_id[]" class="form-select expense_id" aria-label="Expense" required>
                                <option value="">Select Expense</option>
                                @if (!empty($expenses))
                                    @foreach ($expenses as $expense)
                                        <option value="{{ $expense->id }}" {{ (old('expense_id')[$i] == $expense->id) ? 'selected' : '' }}>{{ $expense->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <!--<span class="btn expense_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>-->
                            @error('expense_id.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <select name="vendor_id[]" class="form-select vendor_id action-divider-left" aria-label="Expense" required>
                                <option value="">Select Vendor</option>
                                @php
                                    $vendors = getVendors(old('expense_id')[$i]);
                                @endphp
                                @if (!empty($vendors))
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ (old('vendor_id')[$i] == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="btn vendor_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>
                            @error('vendor_id.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control quantity" min="1" value="{{ old('quantity')[$i] }}" required />
                            @error('quantity.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="unit_price[]" class="form-control unit_price" min="1" value="{{ old('unit_price')[$i] }}" required />
                            @error('unit_price.'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                        <td class="total">
                            @php
                                $quantity = !empty(old('quantity')[$i]) ? old('quantity')[$i] : 0;
                                $unit_price = !empty(old('unit_price')[$i]) ? old('unit_price')[$i] : 0;
                                $subTotal = $quantity*$unit_price;
                                $total += $subTotal
                            @endphp
                            {{ $subTotal }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removeIndentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endfor
            @elseif (!empty($indent->indentItems))
                @foreach ($indent->indentItems as $i => $item)
                    <tr class="{{ (($item->status != 'pending') ? 'readonly' : '') }}">
                        <td>
                            <input type="hidden" name="indent_item_id[]" value="{{ $item->id }}">
                            <select name="expense_id[]" class="form-select expense_id " aria-label="Expense" required>
                                <option value="">Select Expense</option>
                                @if (!empty($expenses))
                                    @foreach ($expenses as $expense)
                                        <option value="{{ $expense->id }}" {{ ($item->expense_id == $expense->id) ? 'selected' : '' }}>{{ $expense->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <!--<span class="btn expense_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>-->
                        </td>
                        <td>
                            <select name="vendor_id[]" class="form-select vendor_id action-divider-left" aria-label="Expense" required>
                                <option value="">Select Vendor</option>
                                @php
                                    $vendors = getVendors($item->expense_id);
                                @endphp
                                @if (!empty($vendors))
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ ($item->vendor_id == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="btn vendor_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control quantity" min="1" value="{{ $item->quantity }}" required />
                        </td>
                        <td>
                            <input type="number" name="unit_price[]" class="form-control unit_price" min="1" value="{{ $item->unit_price }}" required />
                        </td>
                        <td class="total">
                            @php
                                $total += $item->total;
                            @endphp
                            {{ $item->total }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removeIndentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <input type="hidden" name="indent_item_id[]" value="">
                        <select name="expense_id[]" class="form-select expense_id " aria-label="Expense" required>
                            <option value="">Select Expense</option>
                            @if (!empty($expenses))
                                @foreach ($expenses as $expense)
                                    <option value="{{ $expense->id }}">{{ $expense->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <!--<span class="btn expense_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>-->
                    </td>
                    <td>
                        <select name="vendor_id[]" class="form-select vendor_id action-divider-left" aria-label="Expense" required>
                            <option value="">Select Vendor</option>
                        </select>
                        <span class="btn vendor_btn action-divider-right"><i class="tf-icons bx bx-plus"></i></span>
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control quantity" min="1" required />
                    </td>
                    <td>
                        <input type="number" name="unit_price[]" class="form-control unit_price" min="1" required />
                    </td>
                    <td class="total">
                        -
                    </td>
                    <td>
                        <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removeIndentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                    </td>
                </tr>
            @endif

        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right-align">
                    Total
                </td>
                <td>
                    <b class="final-total">
                        @if (!empty(old('expense_id')) || !empty($indent->indentItems))
                            {{ $total }}
                        @else
                        -
                        @endif
                    </b>
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-outline-primary float-end" onclick="addIndentItem()"><i class="tf-icons bx bx-plus"></i></button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
