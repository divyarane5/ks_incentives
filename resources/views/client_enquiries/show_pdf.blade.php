<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Enquiry {{ $clientEnquiry->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h5, h6 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; width: 35%; }
        td, th { padding: 4px 0; }
        .section { margin-bottom: 20px; }
        hr { margin: 20px 0; }
        .card-header { text-align:center; font-size:16px; margin-bottom:10px; }
    </style>
</head>
<body>

<div class="card mb-4 p-3">

    <h5 class="card-header">
        Client Enquiry Details - {{ $clientEnquiry->id }}
    </h5>

    {{-- Basic Information --}}
    <div class="section">
        <h6 class="fw-bold">Basic Information</h6>
        <table>
            <tr><th>Customer Name:</th><td>{{ $clientEnquiry->customer_name ?? '-' }}</td></tr>
            <tr><th>Email:</th><td>{{ $clientEnquiry->email ?? '-' }}</td></tr>
            <tr><th>Contact No:</th><td>{{ $clientEnquiry->contact_no ?? '-' }}</td></tr>
            <tr><th>Alternate No:</th><td>{{ $clientEnquiry->alternate_no ?? '-' }}</td></tr>
            <tr><th>Address:</th><td>{{ $clientEnquiry->address ?? '-' }}</td></tr>
            <tr><th>Pin Code:</th><td>{{ $clientEnquiry->pin_code ?? '-' }}</td></tr>
            <tr><th>Profession:</th><td>{{ $clientEnquiry->profession ?? '-' }}</td></tr>
            <tr><th>Company Name:</th><td>{{ $clientEnquiry->company_name ?? '-' }}</td></tr>
            <tr><th>Residential Status:</th><td>{{ $clientEnquiry->residential_status ?? '-' }}</td></tr>
            <tr><th>NRI Country:</th><td>{{ $clientEnquiry->nri_country ?? '-' }}</td></tr>
            <tr><th>Created By:</th><td>{{ $clientEnquiry->createdBy->name ?? '-' }}</td></tr>
            <tr><th>Created Date:</th><td>{{ $clientEnquiry->created_at->format('d-m-Y') }}</td></tr>
        </table>
    </div>

    <hr>

    {{-- Property Requirement --}}
    <div class="section">
        <h6 class="fw-bold">Property Requirement</h6>
        <table>
            <tr><th>Property Type:</th><td>{{ $clientEnquiry->property_type ?? '-' }}</td></tr>
            <tr><th>Budget:</th><td>{{ $clientEnquiry->budget ?? '-' }}</td></tr>
            <tr><th>Purchase Purpose:</th><td>{{ $clientEnquiry->purchase_purpose ?? '-' }}</td></tr>
            <tr><th>Funding Source:</th><td>{{ $clientEnquiry->funding_source ?? '-' }}</td></tr>
            <tr><th>Source of Visit:</th>
                <td>
                    @php $sources = json_decode($clientEnquiry->source_of_visit); @endphp
                    {{ is_array($sources) ? implode(', ', $sources) : $clientEnquiry->source_of_visit ?? '-' }}
                </td>
            </tr>
            <tr><th>Team Call Received:</th><td>{{ $clientEnquiry->team_call_received ? 'Yes' : 'No' }}</td></tr>
        </table>
    </div>

    <hr>

    {{-- Reference & Manager Info --}}
    <div class="section">
        <h6 class="fw-bold">Reference & Manager Info</h6>
        <table>
            <tr><th>Reference Name:</th><td>{{ $clientEnquiry->reference_name ?? '-' }}</td></tr>
            <tr><th>Reference Contact:</th><td>{{ $clientEnquiry->reference_contact ?? '-' }}</td></tr>
            <tr><th>Channel Partner:</th><td>{{ $clientEnquiry->channelPartner->firm_name ?? '-' }}</td></tr>
            <tr><th>Presales Manager:</th><td>{{ $clientEnquiry->presales->name ?? '-' }}</td></tr>
            <tr><th>Sourcing Manager:</th><td>{{ $clientEnquiry->sourcingManager->name ?? '-' }}</td></tr>
            <tr><th>Closing Manager:</th><td>{{ $clientEnquiry->closingManager->name ?? '-' }}</td></tr>
            <tr><th>Remarks:</th><td>{{ $clientEnquiry->remarks ?? '-' }}</td></tr>
        </table>
    </div>

    <hr>

    {{-- Feedback --}}
    <div class="section">
        <h6 class="fw-bold">Feedback</h6>
        <table>
            <tr><th>Feedback:</th><td>{{ $clientEnquiry->feedback ?? '-' }}</td></tr>
        </table>
    </div>

</div>

</body>
</html>
