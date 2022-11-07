@extends('layouts.app')

@section('content')
<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.homebazaar.com/api/v2/searchfilter',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"city_name":"Mumbai","city_id":1,"module_type":"buy","limit":10}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);


curl_close($curl);

$json = json_decode($response, TRUE);
//print_r($json); exit;
// foreach ($json['data'] as $index => $v) {
//   echo $v['projectName'].'<br>';
// }  exit;
?>
<?php

$curl1 = curl_init();

curl_setopt_array($curl1, array(
  CURLOPT_URL => 'https://www.homebazaar.com/api/v2/developers?skip=0&limit=10',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: AWSALBTG=uGODxQPrbEwmiONbQm7WMr7P3VsN74W8sg0Vk4rmMJTjlAEtwxAbATR2TqBUFEf9fdQBYGDILS4KtdqgSp9lnJdJuP6/betV+JRaddSmvzWiqkCiDi1APjMPOdTool23W4CoIj/BeS50o9oI/hKBUcEhFdEF9DH/crPcOjP1Hzy+Wa+QicM=; AWSALBTGCORS=uGODxQPrbEwmiONbQm7WMr7P3VsN74W8sg0Vk4rmMJTjlAEtwxAbATR2TqBUFEf9fdQBYGDILS4KtdqgSp9lnJdJuP6/betV+JRaddSmvzWiqkCiDi1APjMPOdTool23W4CoIj/BeS50o9oI/hKBUcEhFdEF9DH/crPcOjP1Hzy+Wa+QicM='
  ),
));

$response1 = curl_exec($curl1);

curl_close($curl1);
$json1 = json_decode($response1, TRUE);
//print_r($json1); exit;
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('booking.index') }}" class="text-muted fw-light">Booking /</a> Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Booking</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('booking.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('booking.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            @form_hidden('id', $id)
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="project_name">Project Name<span class="start-mark">*</span></label>
                            <select id="project_name" name="project_name" class="form-select" @error('project_name') autofocus @enderror required>
                                <option>Select Project Name</option>
                                @if (!empty($json))
                                    @foreach ($json['data'] as $index => $temp)
                                        <option value="{{ $temp['projectName'] }}" {{ ($temp['projectName'] == ((old('project_name') != "") ? old('project_name') : $booking->project_name) ? 'selected' : '') }}>{{ $temp['projectName'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @error('template_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="developer_name">Developer Name<span class="start-mark">*</span></label>
                            <select id="developer_name" name="developer_name" class="form-select" @error('developer_name') autofocus @enderror required>
                                <option>Select Project Name</option>
                                @if (!empty($json1))
                                    @foreach ($json1['data'] as $index => $temp1)
                                        <option value="{{ $temp1['developer_name'] }}" {{ ($temp1['developer_name'] == ((old('developer_name') != "") ? old('developer_name') : $booking->developer_name) ? 'selected' : '') }}>{{ $temp1['developer_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @error('developer_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="developer_email">Developer Email<span class="start-mark">*</span></label>
                        <input name="developer_email" class="form-control" id="developer_email" value="{{ (old('developer_email') != "") ? old('developer_email') : $booking->developer_email }}" required />
                        @error('developer_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="client_name">Client Name<span class="start-mark">*</span></label>
                        <input name="client_name" class="form-control" id="client_name" value="{{ (old('client_name') != "") ? old('client_name') : $booking->client_name }}" required />
                        @error('client_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="configuration">Configuration<span class="start-mark">*</span></label>
                        <input name="configuration" class="form-control" id="configuration" value="{{ (old('configuration') != "") ? old('configuration') : $booking->configuration }}" required />
                        @error('configuration')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="tower">Tower<span class="start-mark">*</span></label>
                        <input name="tower" class="form-control" id="tower" value="{{ (old('tower') != "") ? old('tower') : $booking->tower }}" required />
                        @error('tower')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="flat_no">Flat Number<span class="start-mark">*</span></label>
                        <input name="flat_no" class="form-control" id="flat_no" value="{{ (old('flat_no') != "") ? old('flat_no') : $booking->flat_no }}" required />
                        @error('flat_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="wing">Wing<span class="start-mark">*</span></label>
                        <input name="wing" class="form-control" id="wing" value="{{ (old('wing') != "") ? old('wing') : $booking->wing }}" required />
                        @error('wing')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="sales_person">Sales Person<span class="start-mark">*</span></label>
                        <input name="sales_person" class="form-control" id="sales_person" value="{{ (old('sales_person') != "") ? old('sales_person') : $booking->sales_person }}" required />
                        @error('sales_person')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="sourcing_manager">Sourcing Manager<span class="start-mark">*</span></label>
                        <input name="sourcing_manager" class="form-control" id="sourcing_manager" value="{{ (old('sourcing_manager') != "") ? old('sourcing_manager') : $booking->sourcing_manager }}" required />
                        @error('sourcing_manager')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($booking->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
