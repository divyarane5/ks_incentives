@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold mb-4">Mandate Dashboard</h4>

    <div class="row">

        {{-- Mandate Projects --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="{{ url('/mandate_projects') }}" class="text-decoration-none text-dark">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Mandate Projects</span>
                        <h2 class="fw-bold">{{ $mandateProjectsCount }}</h2>
                    </div>
                </div>
            </a>
        </div>

        {{-- Channel Partners --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="{{ url('/channel_partners') }}" class="text-decoration-none text-dark">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Channel Partners</span>
                        <h2 class="fw-bold">{{ $channelPartnersCount }}</h2>
                    </div>
                </div>
            </a>
        </div>

        {{-- Client Enquiries --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="{{ url('/client-enquiries') }}" class="text-decoration-none text-dark">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Client Enquiries</span>
                        <h2 class="fw-bold">{{ $clientEnquiriesCount }}</h2>
                    </div>
                </div>
            </a>
        </div>

       

    </div>
    
     {{-- REGISTRATION & BROKERAGE --}}
    <div class="row mb-4">
         {{-- Mandate Bookings --}}
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="{{ url('/mandate_bookings') }}" class="text-decoration-none text-dark">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Total Bookings</span>
                        <h2 class="fw-bold">{{ $mandateBookingsCount }}</h2>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <span class="fw-semibold">Total Registrations</span>
                    <h2 class="fw-bold">{{ $totalRegistrations }}</h2>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <span class="fw-semibold">Eligible for Brokerage</span>
                    <h2 class="fw-bold text-success">{{ $eligibleBrokerageBookings }}</h2>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <span class="fw-semibold">Pending Bookings</span>
                    <h2 class="fw-bold text-warning">{{ $pendingBookings }}</h2>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <span class="fw-semibold">Completed Bookings</span>
                    <h2 class="fw-bold text-success">{{ $completedBookings }}</h2>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <span class="fw-semibold">Cancelled Bookings</span>
                    <h2 class="fw-bold text-danger">{{ $cancelledBookings }}</h2>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/total.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/pending.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Pending Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/approved.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Approved Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/partial-approved.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Partial Approved Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/rejected.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Rejected Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="http://localhost/ks_incentives/public/assets/img/dashboard_icons/closed.png" alt="chart success" class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Closed Indent</span>
                        <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
            </div>
</div>
@endsection
