@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Referral Client References /</span> View</h4>
        <div class="col-md-6">
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">View Referral Client References</h5>
        <div class="table-responsive text-nowrap">
        @if(!empty($creferences))
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
        @endif
        </div>
        </div>
        <!--/ Striped Rows -->
    </div>
@endsection

