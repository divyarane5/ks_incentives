@extends('layouts.app')

@section('content')
<style>
    h3 {
        font-size: 22px;
    }
    .card-body {
        flex: 1 1 auto;
        padding: 15px 15px;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">

<h4 class="fw-bold mb-4">Brokerage CRM Dashboard</h4>

{{-- GLOBAL FILTERS --}}
<div class="card mb-4">
<div class="card-body">

<form method="POST" action="{{ url('/dashboard-two') }}">
@csrf

<div class="row g-3">

<div class="col-md-3">
<select name="cluster_head" class="form-select">
<option value="">Cluster Head</option>
@foreach($clusterHeads as $ch)
<option value="{{$ch->id}}" {{ request('cluster_head') == $ch->id ? 'selected' : '' }}>
{{$ch->name}}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<select name="sr_tl" class="form-select">
<option value="">Sr TL</option>
@foreach($srTls as $sr)
<option value="{{$sr->id}}" {{ request('sr_tl') == $sr->id ? 'selected' : '' }}>
{{$sr->name}}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<select name="tl" class="form-select">
<option value="">TL</option>
@foreach($tls as $tl)
<option value="{{$tl->id}}" {{ request('tl') == $tl->id ? 'selected' : '' }}>
{{$tl->name}}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<select name="sales_manager" class="form-select">
<option value="">Sales Manager</option>
@foreach($salesManagers as $sm)
<option value="{{$sm->id}}" {{ request('sales_manager') == $sm->id ? 'selected' : '' }}>
{{$sm->name}}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<select name="developer" class="form-select">
<option value="">Developer</option>
@foreach($developers as $dev)
<option value="{{$dev->id}}" {{ request('developer') == $dev->id ? 'selected' : '' }}>
{{$dev->name}}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
</div>

<div class="col-md-3">
<input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Apply Filters</button>
</div>

<div class="col-md-2">
<a href="{{ url('/dashboard-two') }}" class="btn btn-secondary w-100">Reset</a>
</div>

</div>

</form>

</div>
</div>


{{-- KPI CARDS --}}
<div class="row mb-4">

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Total Bookings</span>
<h3>{{$totalBookings}}</h3>
</div>
</div>
</div>

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Agreement Value</span>
<h3>₹{{number_format($totalAgreementValue)}}</h3>
</div>
</div>
</div>

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Total Brokerage</span>
<h3>₹{{number_format($totalBrokerage)}}</h3>
</div>
</div>
</div>

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Invoice Raised</span>
<h3>₹{{number_format($totalInvoice)}}</h3>
</div>
</div>
</div>

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Received</span>
<h3 class="text-success">₹{{number_format($totalReceived)}}</h3>
</div>
</div>
</div>

<div class="col-lg-2 col-md-4">
<div class="card text-center">
<div class="card-body">
<span>Pending Brokerage</span>
<h3 class="text-danger">₹{{number_format($pendingBrokerage)}}</h3>
</div>
</div>
</div>

</div>


{{-- CHART ROW --}}
<div class="row">

<div class="col-lg-4">
<div class="card">
<div class="card-header">Payment Status</div>
<div class="card-body">
<div id="paymentChart"></div>
</div>
</div>
</div>

<div class="col-lg-4">
<div class="card">
<div class="card-header">Lead Source Distribution</div>
<div class="card-body">
<div id="leadChart"></div>
</div>
</div>
</div>

<div class="col-lg-4">
<div class="card">
<div class="card-header">Brokerage Recovery</div>
<div class="card-body">
<div id="recoveryChart"></div>
</div>
</div>
</div>

</div>


{{-- MONTHLY REVENUE --}}
<div class="row mt-4">

<div class="col-lg-12">
<div class="card">
<div class="card-header">Monthly Revenue</div>
<div class="card-body">
<div id="revenueChart"></div>
</div>
</div>
</div>

</div>


{{-- TABLES --}}
<div class="row mt-4">

<div class="col-lg-6">
<div class="card">
<div class="card-header">Top Projects</div>

<table class="table table-striped">
<thead>
<tr>
<th>Project</th>
<th>Brokerage</th>
</tr>
</thead>

<tbody>

@foreach($topProjects as $project)

<tr>
<td>{{$project->name}}</td>
<td>₹{{number_format($project->brokerage)}}</td>
</tr>

@endforeach

</tbody>

</table>

</div>
</div>


<div class="col-lg-6">

<div class="card">
<div class="card-header">Top Sales Managers</div>

<table class="table table-striped">

<thead>

<tr>
<th>Sales Manager</th>
<th>Revenue</th>
</tr>

</thead>

<tbody>

@foreach($topSales as $sales)

<tr>
<td>{{$sales->name}}</td>
<td>₹{{number_format($sales->revenue)}}</td>
</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>


{{-- PENDING BROKERAGE --}}
<div class="row mt-4">

<div class="col-lg-12">

<div class="card">

<div class="card-header">
Pending Brokerage Recovery
</div>

<table class="table table-striped">

<thead>

<tr>
<th>Client</th>
<th>Project</th>
<th>Brokerage</th>
<th>Received</th>
<th>Pending</th>
</tr>

</thead>

<tbody>

@foreach($pendingRecovery as $row)

<tr>

<td>{{$row->client_name}}</td>

<td>{{$row->project->name ?? ''}}</td>

<td>₹{{number_format($row->current_effective_amount)}}</td>

<td>₹{{number_format($row->total_received_amount)}}</td>

<td class="text-danger">
₹{{number_format($row->pending_brokerage_amount)}}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>


</div>

@endsection
@section('script')

<script>

var paymentChart = new ApexCharts(document.querySelector("#paymentChart"), {

chart:{type:'pie'},

series:[
{{$pendingPayments}},
{{$partialPayments}},
{{$completedPayments}}
],

labels:['Pending','Partial','Completed']

});

paymentChart.render();



var leadChart = new ApexCharts(document.querySelector("#leadChart"), {

chart:{type:'pie'},

series:{!! json_encode($leadSources->values()) !!},

labels:{!! json_encode($leadSources->keys()) !!}

});

leadChart.render();



var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {

chart:{type:'bar'},

series:[{
name:'Revenue',
data:{!! json_encode($monthlyRevenue->values()) !!}
}],

xaxis:{
categories:{!! json_encode($monthlyRevenue->keys()) !!}
}

});

revenueChart.render();



var recoveryChart = new ApexCharts(document.querySelector("#recoveryChart"), {

chart:{type:'donut'},

series:[
{{$totalReceived}},
{{$pendingBrokerage}}
],

labels:['Recovered','Pending']

});

recoveryChart.render();

</script>

@endsection