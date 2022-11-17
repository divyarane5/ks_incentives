<div class="mb-3 col-md-12">
    <h5 class="mb-0">Indent Items</h5>
</div>
<div class="mb-3 col-md-12" style="overflow: auto">
    <table class="table table-striped" id="indent_item_table">
        <thead>
            <tr>
                <th style="min-width: 200px;">Expense<span class="start-mark">*</span></th>
                <th style="min-width: 200px;">Vendor<span class="start-mark">*</span></th>
                <th>Quantity<span class="start-mark">*</span></th>
                <th>Unit Price<span class="start-mark">*</span></th>
                <th>Sub Total</th>
                <th>GST</th>
                <th>TDS</th>
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
                            <select name="expense_id[]" class=" expense_id raw-select form-select" aria-label="Expense" required>
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
                            <div class="input-group">
                                <select name="vendor_id[]" class=" vendor_id action-divider-left raw-select form-select" aria-label="Expense" required>
                                    <option value="">Select Vendor</option>
                                    @php
                                        $vendors = getVendors(old('expense_id')[$i]);
                                    @endphp
                                    @if (!empty($vendors))
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"  data-tds-percentage="{{ $vendor->tds_percentage }}" {{ (old('vendor_id')[$i] == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <button class="btn btn-outline-primary vendor_btn" type="button" id="button-addon2"><i class="tf-icons bx bx-plus"></i></button>
                            </div>
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
                            @endphp
                            {{ $subTotal }}
                        </td>
                        <td>
                            <input type="number" name="gst[]" class="form-control gst" min="1" value="{{ old('gst')[$i] }}" required />
                        </td>
                        <td>
                            <input type="hidden" class="tds" name="tds[]"  value="{{ old('tds')[$i] }}">
                            <span class="tds_column">{{ old('tds')[$i] }}</span>
                        </td>
                        <td class="final_total_column">
                            @php
                                $gst = !empty(old('gst')[$i]) ? old('gst')[$i] : 0;
                                $tds = !empty(old('tds')[$i]) ? old('tds')[$i] : 0;
                                $lineItemTotal = ($subTotal + $gst) - $tds;
                                $total += $lineItemTotal;
                            @endphp
                            {{ $lineItemTotal }}
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
                            <select name="expense_id[]" class=" expense_id raw-select form-select" aria-label="Expense" required>
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
                            <div class="input-group">
                                <select name="vendor_id[]" class=" vendor_id action-divider-left raw-select form-select" aria-label="Expense" required>
                                    <option value="">Select Vendor</option>
                                    @php
                                        $vendors = getVendors($item->expense_id);
                                    @endphp
                                    @if (!empty($vendors))
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" data-tds-percentage="{{ $vendor->tds_percentage }}" {{ ($item->vendor_id == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <button class="btn btn-outline-primary vendor_btn" type="button" id="button-addon2"><i class="tf-icons bx bx-plus"></i></button>
                            </div>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control quantity" min="1" value="{{ $item->quantity }}" required />
                        </td>
                        <td>
                            <input type="number" name="unit_price[]" class="form-control unit_price" min="1" value="{{ $item->unit_price }}" required />
                        </td>
                        <td class="total">
                            {{ $item->quantity*$item->unit_price }}
                        </td>
                        <td>
                            <input type="number" name="gst[]" class="form-control gst" value="{{ $item->gst }}" min="1" required />
                        </td>
                        <td>
                            <input type="hidden" class="tds" name="tds[]" value="{{ $item->tds }}" >
                            <span class="tds_column">{{ $item->tds }}</span>
                        </td>
                        <td class="final_total_column">
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
                        <select name="expense_id[]" class=" expense_id raw-select form-select" aria-label="Expense" required>
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
                        <div class="input-group">
                            <select name="vendor_id[]" class=" vendor_id action-divider-left raw-select form-select" aria-label="Expense" required>
                                <option value="">Select Vendor</option>
                            </select>
                            <button class="btn btn-outline-primary vendor_btn" type="button" id="button-addon2"><i class="tf-icons bx bx-plus"></i></button>
                        </div>
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
                        <input type="number" name="gst[]" class="form-control gst" min="1" required />
                    </td>
                    <td>
                        <input type="hidden" class="tds" name="tds[]" value="">
                        <span class="tds_column">-</span>
                    </td>
                    <td class="final_total_column"></td>
                    <td>
                        <button type="button" class="btn btn-icon btn-outline-danger float-end" onclick="removeIndentItem(this)"><i class="tf-icons bx bx-trash"></i></button>
                    </td>
                </tr>
            @endif

        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="right-align">
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
