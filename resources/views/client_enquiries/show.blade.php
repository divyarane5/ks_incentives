@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">
            <a href="{{ route('client-enquiries.index') }}" class="text-muted fw-light">Client Enquiries</a> /
        </span> View
    </h4>

    {{-- Download Button: Only show on web, hide in PDF --}}
    @if(!isset($pdf))
    <div class="mb-3 text-end">
        <a href="{{ route('client-enquiries.download', $clientEnquiry->id) }}" class="btn btn-primary">
            <i class="bx bx-download"></i> Download PDF
        </a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 p-3">

                <h5 class="card-header text-center">
                    Client Enquiry Details - {{ $clientEnquiry->id }}
                </h5>

                {{-- Basic Information --}}
                <div class="mb-4 mt-3">
                    <h6 class="fw-bold">Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <table class="table table-borderless">
                                <tr><th>Customer Name:</th><td>{{ $clientEnquiry->customer_name ?? '-' }}</td></tr>
                                <tr><th>Email:</th><td>{{ $clientEnquiry->email ?? '-' }}</td></tr>
                                <tr><th>Contact No:</th><td>{{ $clientEnquiry->contact_no ?? '-' }}</td></tr>
                                <tr><th>Alternate No:</th><td>{{ $clientEnquiry->alternate_no ?? '-' }}</td></tr>
                                <tr><th>Address:</th><td>{{ $clientEnquiry->address ?? '-' }}</td></tr>
                                <tr><th>Pin Code:</th><td>{{ $clientEnquiry->pin_code ?? '-' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th>Profession:</th><td>{{ $clientEnquiry->profession ?? '-' }}</td></tr>
                                <tr><th>Company Name:</th><td>{{ $clientEnquiry->company_name ?? '-' }}</td></tr>
                                <tr><th>Residential Status:</th><td>{{ $clientEnquiry->residential_status ?? '-' }}</td></tr>
                                <tr><th>NRI Country:</th><td>{{ $clientEnquiry->nri_country ?? '-' }}</td></tr>
                                <tr><th>Created By:</th><td>{{ $clientEnquiry->createdBy->name ?? '-' }}</td></tr>
                                <tr><th>Created Date:</th><td>{{ $clientEnquiry->created_at->format('d-m-Y') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Property Requirement --}}
                <div class="mb-4">
                    <h6 class="fw-bold">Property Requirement</h6>
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <table class="table table-borderless">
                                <tr><th>Property Type:</th><td>{{ $clientEnquiry->property_type ?? '-' }}</td></tr>
                                <tr><th>Budget:</th><td>{{ $clientEnquiry->budget ?? '-' }}</td></tr>
                                <tr><th>Purchase Purpose:</th><td>{{ $clientEnquiry->purchase_purpose ?? '-' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th>Funding Source:</th><td>{{ $clientEnquiry->funding_source ?? '-' }}</td></tr>
                                <tr><th>Source of Visit:</th>
                                    <td>
                                        @php
                                            $sources = json_decode($clientEnquiry->source_of_visit);
                                        @endphp
                                        {{ is_array($sources) ? implode(', ', $sources) : $clientEnquiry->source_of_visit ?? '-' }}
                                    </td>
                                </tr>
                                <tr><th>Team Call Received:</th><td>{{ $clientEnquiry->team_call_received ? 'Yes' : 'No' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Reference Information --}}
                <div class="mb-4">
                    <h6 class="fw-bold">Reference & Manager Info</h6>
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <table class="table table-borderless">
                                <tr><th>Reference Name:</th><td>{{ $clientEnquiry->reference_name ?? '-' }}</td></tr>
                                <tr><th>Reference Contact:</th><td>{{ $clientEnquiry->reference_contact ?? '-' }}</td></tr>
                                <tr><th>Channel Partner:</th><td>{{ $clientEnquiry->channelPartner->firm_name ?? '-' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th>Presales Manager:</th><td>{{ $clientEnquiry->presales->name ?? '-' }}</td></tr>
                                <tr><th>Sourcing Manager:</th><td>{{ $clientEnquiry->sourcingManager->name ?? '-' }}</td></tr>
                                <tr><th>Closing Manager:</th><td>{{ $clientEnquiry->closingManager->name ?? '-' }}</td></tr>
                                <tr><th>Remarks:</th><td>{{ $clientEnquiry->remarks ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Feedback --}}
                <div class="mb-4">
                    <h6 class="fw-bold">Feedback</h6>
                    <table class="table table-borderless">
                        <tr><th width="20%">Inital Feedback:</th><td>{{ $clientEnquiry->feedback ?? '-' }}</td></tr>
                    </table>
                </div>

                <!-- =========================
                UPDATE HISTORY
                ========================== -->
                <div class="card mt-4">
                    <!-- <div class="card-header bg-secondary text-white">
                        <strong>Enquiry Update History</strong>
                    </div> -->

                    <div class="card-body">

                        @if($clientEnquiry->updates->count() > 0)

                            @foreach($clientEnquiry->updates as $update)
                                <div class="border rounded p-3 mb-3 bg-light">

                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <strong>Status:</strong>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_',' ', $update->status)) }}
                                            </span>
                                        </div>

                                        <div class="col-md-8 text-end">
                                            <small class="text-muted">
                                                Updated on:
                                                {{ $update->created_at->format('d M Y, h:i A') }}
                                            </small>
                                        </div>
                                    </div>

                                    <p>
                                        <strong>Feedback:</strong><br>
                                        {{ $update->feedback ?? '-' }}
                                    </p>

                                    <div class="row">
                                        @if($update->followup_date)
                                            <div class="col-md-4">
                                                <strong>Followup Date:</strong>
                                                {{ \Carbon\Carbon::parse($update->followup_date)->format('d-m-Y') }}
                                            </div>
                                        @endif

                                        @if($update->revisit_scheduled)
                                            <div class="col-md-4">
                                                <strong>Revisit Scheduled:</strong>
                                                {{ \Carbon\Carbon::parse($update->revisit_scheduled)->format('d-m-Y') }}
                                            </div>
                                        @endif

                                        @if($update->revisit_done)
                                            <div class="col-md-4">
                                                <strong>Revisit Done:</strong>
                                                {{ \Carbon\Carbon::parse($update->revisit_done)->format('d-m-Y') }}
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @endforeach

                        @else
                            <p class="text-muted mb-0">No updates added yet.</p>
                        @endif

                    </div>
                </div>

            </div> <!-- /card -->
        </div>
    </div>
</div>
@endsection
