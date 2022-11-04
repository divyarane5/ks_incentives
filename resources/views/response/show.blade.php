@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light"> Client Details /</span> View</h4>
        <div class="col-md-6">
        </div>
    </div>
    <?php //print_r($rclient); ?>
    <div class="card">
        <h5 class="card-header">View Client Details</h5>
        <div class="table-responsive text-nowrap">
             <table class="table table-borderless">
                <tbody>
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Client Name</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->name}}</p>
                    </td>
                    </tr>
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Client Email</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->email}}</p>
                    </td>
                    </tr>
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Client Mobile</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->mobile}}</p>
                    </td>
                    </tr>
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Status</small></td>
                    <td class="py-3">
                        <?php
                            if($rclient->status == 0){
                                $r = "Pending";
                            }elseif($rclient->status == 1){
                                $r = "In Progress";
                            }elseif($rclient->status == 2){
                                $r = "Completed";
                            }elseif($rclient->status == 3){
                                $r = "Deleted (Invalid)";
                            }
                           // return $r;
                        ?>
                        <p class="mb-0">{{$r}}</p>
                    </td>
                    </tr>
                    @if($rclient->form_type == 'homeloan')
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Loan Amount</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->loanamount}}</p>
                    </td>
                    </tr>
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Preferred Bank</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->preferredbank}}</p>
                    </td>
                    </tr>
                    @endif
                    @if($rclient->form_type == 'property')
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Assistance</small></td>
                    <td class="py-3">
                        <?php if($rclient->assistance == 0){ $a = 'Rent'; }else{ $a = 'Resale;'; } ?>
                        <p class="mb-0">{{$a}}</p>
                    </td>
                    </tr>
                    @endif
                    @if(($rclient->form_type == 'property') || ($rclient->form_type == 'collection') || ($rclient->form_type == 'document'))
                    <tr>
                    <td class="align-middle"><small class="text-light fw-semibold">Remark</small></td>
                    <td class="py-3">
                        <p class="mb-0">{{$rclient->remarks}}</p>
                    </td>
                    </tr>
                    @endif


                </tbody>
            </table>

        </div>
    </div>


    <!-- Striped Rows -->
        @if(count($creferences) > 0)
        <div class="card">
            <h5 class="card-header">View Referral Client References</h5>
            <div class="table-responsive text-nowrap">

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Client Name</th>
                        <th scope="col">Client Mobile</th>
                        <th scope="col">Client Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($creferences as $cref)
                    <tr>
                        <th scope="row">1</th>
                        <td>{{$cref->client_name}}</td>
                        <td>{{$cref->client_mobile}}</td>
                        <td>{{$cref->client_email}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
        <!--/ Striped Rows -->
        @endif

        <form action="{{ route('client_response.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            @form_hidden('form_type', $rclient->form_type)
            <div class="card-body">
                <div class="row">

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="status">Status<span class="start-mark">*</span></label>
                        <select id="status" name="status" class="form-select" @error('status') autofocus @enderror required>
                            <option>Select Status</option>


                            <option value="0" {{ (0 == ((old('status') != "") ? old('status') : $rclient->status) ? 'selected' : '') }}>Pending</option>
                            <option value="1" {{ (1 == ((old('status') != "") ? old('status') : $rclient->status) ? 'selected' : '') }}>In Progress</option>
                            <option value="2" {{ (2 == ((old('status') != "") ? old('status') : $rclient->status) ? 'selected' : '') }}>Completed</option>
                            <option value="3" {{ (3 == ((old('status') != "") ? old('status') : $rclient->status) ? 'selected' : '') }}>Deleted</option>

                        </select>
                        @error('location_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($rclient->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
</div>
@endsection

