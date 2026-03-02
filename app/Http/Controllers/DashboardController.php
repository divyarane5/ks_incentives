<?php

namespace App\Http\Controllers;

use App\Models\MandateProject;
use App\Models\ChannelPartner;
use App\Models\ClientEnquiry;
use App\Models\MandateBooking;
use App\Models\MandateBookingBrokerage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role; // e.g., 'admin', 'manager', 'employee'
        $businessUnit = $user->business_unit_id ?? null;

        // Base queries
        $mandateBookingQuery   = MandateBooking::query();
        $mandateProjectQuery   = MandateProject::query();
        $channelPartnerQuery   = ChannelPartner::query();
        $clientEnquiryQuery    = ClientEnquiry::query();
        $mandateBookingBrokerageQuery = MandateBookingBrokerage::query();

        // Filter by business unit for non-admin users
        if ($role !== 'admin' && $businessUnit) {
            $mandateBookingQuery->where('business_unit_id', $businessUnit);
            $mandateProjectQuery->where('business_unit_id', $businessUnit);
            $channelPartnerQuery->where('business_unit_id', $businessUnit);
            $clientEnquiryQuery->where('business_unit_id', $businessUnit);
            $mandateBookingBrokerageQuery->whereHas('booking', function($q) use ($businessUnit) {
                $q->where('business_unit_id', $businessUnit);
            });
        }

        // Counts
        $totalRegistrations         = $mandateBookingQuery->count();
        $eligibleBrokerageBookings  = $mandateBookingBrokerageQuery->where('is_eligible', 1)
            ->distinct('booking_id')
            ->count('booking_id');

        $pendingBookings   = (clone $mandateBookingQuery)->where('booking_status', 'pending')->count();
        $completedBookings = (clone $mandateBookingQuery)->where('booking_status', 'completed')->count();
        $cancelledBookings = (clone $mandateBookingQuery)->where('booking_status', 'cancelled')->count();

        return view('dashboard', [
            'totalRegistrations'         => $totalRegistrations,
            'eligibleBrokerageBookings' => $eligibleBrokerageBookings,
            'pendingBookings'           => $pendingBookings,
            'completedBookings'         => $completedBookings,
            'cancelledBookings'         => $cancelledBookings,
            'mandateProjectsCount'      => $mandateProjectQuery->count(),
            'channelPartnersCount'      => $channelPartnerQuery->count(),
            'clientEnquiriesCount'      => $clientEnquiryQuery->count(),
            'mandateBookingsCount'      => $mandateBookingQuery->count(),
            'role'                      => $role,
            'businessUnit'              => $businessUnit,
        ]);
    }
}
