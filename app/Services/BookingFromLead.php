<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Project;

/**
 * When a lead is marked "Booked" in KeyLeads, a booking is opened in the back office with
 * approval "pending". The FOS is the lead owner and the TL / Sr. TL / CH are filled from the
 * reporting chain on that day. CRM / Accounts then add flat, agreement value etc. and approve.
 */
class BookingFromLead
{
    public const STAGE_SLUG = 'booked';

    public function create(Lead $lead, ?int $projectId = null): ?Booking
    {
        $existing = Booking::where('lead_id', $lead->id)->first();
        if ($existing) {
            return $existing;
        }

        $lead->loadMissing(['projects:id', 'source:id,name']);
        $project = Project::find($projectId ?: $lead->projects->first()?->id);

        $booking = new Booking([
            'project_id' => $project?->id,
            'developer_id' => $project?->developer_id,
            'client_name' => (string) $lead->name,
            'client_contact' => (string) $lead->phone,
            'lead_source' => (string) ($lead->source?->name ?? ''),
            'tower' => '',
            'wing' => '',
            'flat_no' => '',
            'configuration' => '',
            'booking_date' => now()->toDateString(),
            'sales_user_id' => $lead->owner_id,
            'remark' => 'Created from KeyLeads lead #'.$lead->id,
        ]);
        $booking->lead_id = $lead->id;
        $booking->booking_confirm = 'pending';
        $booking->save();

        return $booking;
    }
}
