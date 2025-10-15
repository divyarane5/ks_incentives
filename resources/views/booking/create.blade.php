@extends('layouts.app')

@section('content')
<!-- Content -->
<?php
// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://www.homebazaar.com/api/v2/searchfilter',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'{"city_name":"Mumbai","city_id":1,"module_type":"buy","limit":5000}',
//   CURLOPT_HTTPHEADER => array(
//     'Content-Type: application/json'
//   ),
// ));

// $response = curl_exec($curl);


// curl_close($curl);

// $json = json_decode($response, TRUE);
// //print_r($json); exit;
// // foreach ($json['data'] as $index => $v) {
// //   echo $v['projectName'].'<br>';
// // }  exit;
// ?>
// <?php

// $curl1 = curl_init();

// curl_setopt_array($curl1, array(
//   CURLOPT_URL => 'https://www.homebazaar.com/api/v2/developers?skip=0&limit=5000',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'GET',
//   CURLOPT_HTTPHEADER => array(
//     'Cookie: AWSALBTG=uGODxQPrbEwmiONbQm7WMr7P3VsN74W8sg0Vk4rmMJTjlAEtwxAbATR2TqBUFEf9fdQBYGDILS4KtdqgSp9lnJdJuP6/betV+JRaddSmvzWiqkCiDi1APjMPOdTool23W4CoIj/BeS50o9oI/hKBUcEhFdEF9DH/crPcOjP1Hzy+Wa+QicM=; AWSALBTGCORS=uGODxQPrbEwmiONbQm7WMr7P3VsN74W8sg0Vk4rmMJTjlAEtwxAbATR2TqBUFEf9fdQBYGDILS4KtdqgSp9lnJdJuP6/betV+JRaddSmvzWiqkCiDi1APjMPOdTool23W4CoIj/BeS50o9oI/hKBUcEhFdEF9DH/crPcOjP1Hzy+Wa+QicM='
//   ),
// ));

// $response1 = curl_exec($curl1);

// curl_close($curl1);
// $json1 = json_decode($response1, TRUE);
// //print_r($json1); exit;
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('booking.index') }}" class="text-muted fw-light">Booking/</a> Add Booking</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Booking</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('booking.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="booking_date" class="form-label">Booking Date<span class="start-mark">*</span></label>
                    <input class="form-control" name="booking_date" type="date" value="{{ old('booking_date') }}" id="booking_date" name="booking_date" @error('booking_date') autofocus @enderror required />
                    @error('booking_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
               
                
                
               
                <!-- <div class="mb-3 col-md-6">
                    <label class="form-label" for="sales_person">Sales Person<span class="start-mark">*</span></label>
                    <input name="sales_person" class="form-control" id="sales_person" value="{{ old('sales_person') }}" required/>
                    @error('sales_person')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> -->
               <div class="mb-3 col-md-6">
                    <label class="form-label" for="client_name">Client Name<span class="start-mark">*</span></label>
                    <input name="client_name" class="form-control" id="client_name" value="{{ old('client_name') }}" required/>
                    @error('client_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="client_contact">Client Contact<span class="start-mark">*</span></label>
                    <input name="client_contact" class="form-control" id="client_contact" value="{{ old('client_contact') }}" required/>
                    @error('client_contact')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="lead_source">Lead Source<span class="start-mark">*</span></label>
                    <input name="lead_source" class="form-control" id="lead_source" value="{{ old('lead_source') }}" required/>
                    @error('lead_source')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                    <div class="mb-3 col-md-6">
                        <label for="developer_id" class="form-label">Developer Name</label>
                        <select name="developer_id"  class="" id="user_id" aria-label="User" required>
                          <option value="" >Select Developer</option>
                          @if(!empty($developer_name))
                            @foreach ($developer_name as $developer_name)
                                <option value="{{ $developer_name->id }}" {{ (old('developer_id') == $developer_name->id) ? 'selected' : '' }}>{{ $developer_name->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('developer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="project_id" class="form-label">Project Name</label>
                        <select name="project_id"  class="" id="project_id" aria-label="User" required>
                          <option value="" >Select Project</option>
                          @if(!empty($project_name))
                            @foreach ($project_name as $project_name)
                                <option value="{{ $project_name->id }}" {{ (old('project_id') == $project_name->id) ? 'selected' : '' }}>{{ $project_name->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('project_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                    <label class="form-label" for="sourcing_manager">Sourcing Manager<span class="start-mark">*</span></label>
                    <input name="sourcing_manager" class="form-control" id="sourcing_manager" value="{{ old('sourcing_manager') }}" required/>
                    @error('sourcing_manager')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="sourcing_contact">Sourcing Contact<span class="start-mark">*</span></label>
                    <input name="sourcing_contact" class="form-control" id="sourcing_contact" value="{{ old('sourcing_contact') }}" required/>
                    @error('sourcing_contact')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="booking_amount">Booking Amount</label>
                    <input name="booking_amount" class="form-control" id="booking_amount" value="{{ old('booking_amount') }}" required/>
                    @error('booking_amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="configuration">Configuration<span class="start-mark">*</span></label>
                    <input name="configuration" class="form-control" id="configuration" value="{{ old('configuration') }}" required/>
                    @error('configuration')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="tower">Tower<span class="start-mark">*</span></label>
                    <input name="tower" class="form-control" id="tower" value="{{ old('tower') }}" required/>
                    @error('tower')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="flat_no">Flat No./ Unit No.<span class="start-mark">*</span></label>
                    <input name="flat_no" class="form-control" id="flat_no" value="{{ old('flat_no') }}" required/>
                    @error('flat_no')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="wing">Wing<span class="start-mark">*</span></label>
                    <input name="wing" class="form-control" id="wing" value="{{ old('wing') }}" required/>
                    @error('wing')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                
                
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="agreement_value">Agreement Value<span class="start-mark">*</span></label>
                    <input name="agreement_value" class="form-control" id="agreement_value" value="{{ old('agreement_value') }}" required/>
                    @error('agreement_value')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="agreement_value">Passback Amount</label>
                    <input name="passback" class="form-control" id="passback" value="{{ old('passback') }}" required/>
                    @error('passback')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="agreement_value">Additional Kicker</label>
                    <input name="additional_kicker" class="form-control" id="additional_kicker" value="{{ old('additional_kicker') }}" required/>
                    @error('additional_kicker')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="payment_done">Payment Done (in %)</label>
                    <input name="payment_done" class="form-control" id="payment_done" value="{{ old('payment_done') }}" required/>
                    @error('payment_done')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label for="registration_date" class="form-label">Registration Date<span class="start-mark">*</span></label>
                    <input class="form-control" name="registration_date" type="date" value="{{ old('registration_date') }}" id="registration_date" name="registration_date" @error('registration_date') autofocus @enderror required />
                    @error('registration_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 col-md-12">
                        <label class="form-label" for="remark">Remark</label>
                        <textarea  name="remark" class="form-control" id="remark" @error('remark') autofocus @enderror >{{ old('remark') }}</textarea>
                        @error('remark')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                <div class="mb-3 col-md-6">
                        <label for="project_name" class="form-label">Sales Person</label>
                        <select name="sales_person"  class="" id="sales_person" aria-label="User" required>
                          <option value="" >Select Sales Person</option>
                          @if(!empty($user_name))
                            @foreach ($user_name as $user_name)
                                <option value="{{ $user_name->id }}" {{ (old('sales_person') == $user_name->id) ? 'selected' : '' }}>{{ $user_name->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('user_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label" for="brokerage">Base Brokerage</label>
                    <input name="brokerage" class="form-control" id="brokerage" value="{{ old('brokerage') }}" required/>
                    @error('brokerage')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                    
                  
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- / Content -->
@endsection
