@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('indent.index') }}" class="text-muted fw-light">Indent/</a> Edit Indent</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">

        <form action="{{ route('indent.update', $indent->id) }}" method="POST" enctype="multipart/form-data">
            @if($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach.
            </ul>
            @endif
            @csrf
            @method('put')
            @form_hidden('id', $indent->id)
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-12 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Basic Details</h5>
                        <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('indent.index') }}"> Back</a></small>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="title" class="form-label">Title<span class="start-mark">*</span></label>
                        <input type="text" name="title" class="form-control" min="0" id="title" value="{{ old('title') ? old('title') : $indent->title }}" @error('title') autofocus @enderror placeholder="Title" required />
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="bill_mode" class="form-label">Bill Mode<span class="start-mark">*</span></label>
                        <select name="bill_mode" class="form-select" id="bill_mode" aria-label="Bill Mode" @error('bill_mode') autofocus @enderror required>
                            <option value="">Select Bill Mode</option>
                            @php
                                $billModes = config('constants.BILL_MODES');
                            @endphp
                            @foreach ($billModes as $key => $mode)
                                <option value="{{ $key }}" {{ ((old('bill_mode') ? old('bill_mode') : $indent->bill_mode) == $key) ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                        @error('bill_mode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="location_id " class="form-label">Location<span class="start-mark">*</span></label>
                        <select name="location_id" class="form-select" id="location_id " aria-label="Location" @error('location_id') autofocus @enderror required>
                            <option value="">Select Location</option>
                            @if (!empty($locations))
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ ((old('location_id') ? old('location_id') : $indent->location_id) == $location->id) ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('location_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="business_unit_id" class="form-label">Business Unit<span class="start-mark">*</span></label>
                        <select name="business_unit_id" class="form-select" id="business_unit_id" aria-label="Business Unit" @error('business_unit_id') autofocus @enderror required>
                            <option value="">Select Business Unit</option>
                            @if (!empty($businessUnits))
                                @foreach ($businessUnits as $unit)
                                    <option value="{{ $unit->id }}" {{ ((old('business_unit_id') ? old('business_unit_id') : $indent->business_unit_id) == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('business_unit_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="softcopy_bill_submission_date" class="form-label">Bill Submission Date - Softcopy</label>
                        <input class="form-control" name="softcopy_bill_submission_date" type="date" value="{{ old('softcopy_bill_submission_date') ? old('softcopy_bill_submission_date') : $indent->softcopy_bill_submission_date }}" id="softcopy_bill_submission_date" @error('softcopy_bill_submission_date') autofocus @enderror />
                        @error('softcopy_bill_submission_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="hardcopy_bill_submission_date" class="form-label">Bill Submission Date - Hardcopy</label>
                        <input class="form-control" name="hardcopy_bill_submission_date" type="date" value="{{ old('hardcopy_bill_submission_date') ? old('hardcopy_bill_submission_date') : $indent->hardcopy_bill_submission_date }}" id="hardcopy_bill_submission_date"  @error('hardcopy_bill_submission_date') autofocus @enderror />
                        @error('hardcopy_bill_submission_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') ? old('description') : $indent->description }}</textarea>
                    </div>
                    <hr class="my-4">
                    @include('indent.edit_indent_items')
                    @include('indent.edit_indent_payments')

                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Attachments</h5>
                    </div>
                    <div class="mb-3 col-md-12">
                        <div class="mb-3">
                            <label class="btn btn-sm">
                                <i class="mdi mdi-upload"></i>
                                <span class="position-relative btn btn-outline-primary" >Upload Bills</span>
                                <input type="file" name="files[]" style="display: none;" id="file-input" multiple>
                            </label>
                            <div id="previewold" class="my-3 row">
                                @if (!empty($indent->indentAttachments))
                                    @foreach ($indent->indentAttachments as $attachment)
                                        <div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">
                                            @if(getimagesize(storage_path('app/'.$attachment->file_path)))
                                                <img style="width: 120px; height: 100px;" src="{{ url('storage/app/'.$attachment->file_path) }}" class="prescriptions">
                                            @else
                                                <img style="width: 120px; height: 100px;" src="{{ asset("assets/img/icons/unicons/file.png") }}" class="prescriptions">
                                            @endif
                                            <p style="word-break: break-all;">{{ $attachment->file_name }}</p>
                                            <p class="cross-image remove"><i class="tf-icons bx bx-trash"></i></p>
                                            <input type="hidden" name="attachmentId[]" value="{{ $attachment->id }}">
                                            <input type="hidden" name="attachmentName[]" value="{{ $attachment->file_name }}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div id="preview" class="my-3 row">
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- payment line item copy -->
<table id="payment_item" style="display: none">
    <tr>
        <td>
            <input type="hidden" name="indent_payment_id[]" value="">
            <select name="payment_method_id[]" class="form-select payment_method_id " aria-label="Payment method" required>
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

<!-- / Content -->
@include('indent.expense_modal')
@include('indent.vendor_modal')
@endsection

@section('script')
<script src="{{ asset("assets/js/indent.js") }}"></script>
@endsection
