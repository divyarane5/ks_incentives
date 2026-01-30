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
        // 🔹 TOTAL REGISTRATIONS
        $totalRegistrations = MandateBooking::count();

        // 🔹 ELIGIBLE FOR BROKERAGE
        $eligibleBrokerageBookings = MandateBookingBrokerage::where('is_eligible', 1)
            ->distinct('booking_id')
            ->count('booking_id');

        // 🔹 BOOKING STATUS COUNTS
        $pendingBookings   = MandateBooking::where('booking_status', 'pending')->count();
        $completedBookings = MandateBooking::where('booking_status', 'completed')->count();
        $cancelledBookings = MandateBooking::where('booking_status', 'cancelled')->count();

        return view('dashboard', [
            'totalRegistrations'         => $totalRegistrations,
            'eligibleBrokerageBookings' => $eligibleBrokerageBookings,
            'pendingBookings'           => $pendingBookings,
            'completedBookings'         => $completedBookings,
            'cancelledBookings'         => $cancelledBookings,
            'mandateProjectsCount'      => MandateProject::count(),
            'channelPartnersCount'      => ChannelPartner::count(),
            'clientEnquiriesCount'      => ClientEnquiry::count(),
            'mandateBookingsCount'      => MandateBooking::count(),
        ]);
    }
}
