<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\MandateBooking;

class BrokerageLedgerService
{
    public function handleEligibilityAndLadder(MandateBooking $booking): void
    {
        // 🔒 Only proceed if brokerage exists & is eligible
        if (
            !$booking->relationLoaded('brokerage')
            || !$booking->brokerage
            || !$booking->brokerage->is_eligible
        ) {
            return;
        }

        DB::transaction(function () use ($booking) {

            /**
             * 1️⃣ Create INITIAL ledger for THIS booking (ONLY ONCE)
             */
            $this->createInitialLedger($booking->id);

            /**
             * 2️⃣ Apply LADDER ADJUSTMENTS to OLD bookings
             *    (based on this booking becoming eligible)
             */
            if ($booking->booking_source === 'Channel Partner') {
                $this->applyLadderAdjustments($booking);
            }
        });
    }


    private function getLadderPercent($projectId, $bookingCount)
    {
        return ProjectBrokerageLadder::where('project_id', $projectId)
            ->where('min_bookings', '<=', $bookingCount)
            ->orderByDesc('min_bookings')
            ->value('cp_percent'); // e.g. 2.5
    }

    public function createInitialLedger(int $bookingId): void
    {
        // 🔒 Prevent duplicate initial ledgers
        $exists = DB::table('mandate_booking_brokerage_ledgers')
            ->where('booking_id', $bookingId)
            ->where('calculation_type', 'initial')
            ->exists();

        if ($exists) {
            return;
        }

        $brokerage = DB::table('mandate_booking_brokerages')
            ->where('booking_id', $bookingId)
            ->first();

        if (!$brokerage || !$brokerage->is_eligible) {
            return;
        }

        $booking = DB::table('mandate_bookings')->where('id', $bookingId)->first();

        $totalPercent = (float) $brokerage->brokerage_percent;
        $totalAmount  = (float) $brokerage->brokerage_amount;

        $cpPercent = 0;

        if ($booking->booking_source === 'Channel Partner') {

            $bookingDate = Carbon::parse($booking->booking_date);

            $ladders = DB::table('mandate_project_ladders')
                ->where('mandate_project_id', $booking->project_id)
                ->where('status', 1)
                ->orderBy('no_of_units', 'asc')
                ->get();

            foreach ($ladders as $ladder) {

                // ✅ SAFE DATE CHECK (NO FLOAT CONVERSION)
                if ($bookingDate->between(
                    Carbon::parse($ladder->timeline_from),
                    Carbon::parse($ladder->timeline_to)
                )) {

                    $cpBookingCount = DB::table('mandate_bookings')
                        ->where('channel_partner_id', $booking->channel_partner_id)
                        ->where('project_id', $booking->project_id)
                        ->whereDate('booking_date', '<=', $bookingDate)
                        ->count();

                    if ($cpBookingCount >= $ladder->no_of_units) {
                        $cpPercent = (float) $ladder->payout_percentage;
                    }
                }
            }

            $cpPercent = min($cpPercent, $totalPercent);

            $cpAmount = $totalPercent > 0
                ? round(($totalAmount * $cpPercent) / $totalPercent, 2)
                : 0;

            // CP ledger
            DB::table('mandate_booking_brokerage_ledgers')->insert([
                'booking_id'         => $bookingId,
                'brokerage_id'       => $brokerage->id,
                'party_type'         => 'CP',
                'channel_partner_id' => $booking->channel_partner_id,
                'brokerage_percent'  => $cpPercent,
                'brokerage_amount'   => $cpAmount,
                'calculation_type'   => 'initial',
                'is_locked'          => true,
                'effective_from'     => now(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // OUR ledger
            DB::table('mandate_booking_brokerage_ledgers')->insert([
                'booking_id'         => $bookingId,
                'brokerage_id'       => $brokerage->id,
                'party_type'         => 'OUR',
                'brokerage_percent'  => $totalPercent - $cpPercent,
                'brokerage_amount'   => $totalAmount - $cpAmount,
                'calculation_type'   => 'initial',
                'is_locked'          => true,
                'effective_from'     => now(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

        } else {

            DB::table('mandate_booking_brokerage_ledgers')->insert([
                'booking_id'         => $bookingId,
                'brokerage_id'       => $brokerage->id,
                'party_type'         => 'OUR',
                'brokerage_percent'  => $totalPercent,
                'brokerage_amount'   => $totalAmount,
                'calculation_type'   => 'initial',
                'is_locked'          => true,
                'effective_from'     => now(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    private function applyLadderAdjustments(\App\Models\MandateBooking $currentBooking): void
    {
        // Only for Channel Partner bookings
        if ($currentBooking->booking_source !== 'Channel Partner') {
            return;
        }

        $cpId      = $currentBooking->channel_partner_id;
        $projectId = $currentBooking->project_id;
       // echo $cpId; echo $projectId; exit;
        /**
         * 1️⃣ Get all ELIGIBLE bookings for this CP + Project
         *    that were eligible BEFORE the current booking
         */
        $oldBookings = \App\Models\MandateBooking::where('channel_partner_id', $cpId)
            ->where('project_id', $projectId)
            ->where('id', '!=', $currentBooking->id)
            ->whereHas('brokerage', function ($q) {
                $q->where('is_eligible', 1);
            })
           // ->orderBy('eligible_at', 'asc')
            ->get();
            // echo "<pre>"; 
             echo count($oldBookings); exit;
        if ($oldBookings->isEmpty()) {
            return;
        }

        /**
         * 2️⃣ Booking count INCLUDING current booking
         */
        $bookingCount = $oldBookings->count() + 1;
        //echo $bookingCount; exit;
        /**
         * 3️⃣ Fetch NEW ladder CP %
         */
        $newCpPercent = DB::table('mandate_project_ladders')
            ->where('mandate_project_id', $projectId)
            ->where('status', 1)
            ->where('no_of_units', '<=', $bookingCount)
            ->orderByDesc('no_of_units')
            ->value('payout_percentage');

        if (!$newCpPercent) {
            return;
        }
        //echo $newCpPercent; exit;
        /**
         * 4️⃣ Apply delta to EACH OLD booking
         */
        foreach ($oldBookings as $oldBooking) {

            // 🔒 Sum of CP % already granted (initial + adjustments)
            $grantedCpPercent = DB::table('mandate_booking_brokerage_ledgers')
                ->where('booking_id', $oldBooking->id)
                ->where('party_type', 'CP')
                ->whereIn('calculation_type', ['initial', 'manual_adjustment'])
                ->sum('brokerage_percent');
            //echo $grantedCpPercent; echo $newCpPercent; exit;
            // If already at or above ladder — skip
            if ($grantedCpPercent >= $newCpPercent) {
                continue;
            }

            $deltaPercent = round($newCpPercent - $grantedCpPercent, 2);

            if ($deltaPercent <= 0) {
                continue;
            }

            // Get brokerage base
            $brokerage = DB::table('mandate_booking_brokerages')
                ->where('booking_id', $oldBooking->id)
                ->first();

            if (!$brokerage || $brokerage->brokerage_percent <= 0) {
                continue;
            }

            $deltaAmount = round(
                ($brokerage->brokerage_amount * $deltaPercent) / $brokerage->brokerage_percent,
                2
            );
            //echo $deltaPercent;
           // echo $deltaAmount; 
           // exit;
            /**
             * 5️⃣ INSERT ADJUSTMENT LEDGERS
             */

            // ➕ CP Adjustment
            DB::table('mandate_booking_brokerage_ledgers')->insert([
                'booking_id'         => $oldBooking->id,
                'brokerage_id'       => $brokerage->id,
                'party_type'         => 'CP',
                'channel_partner_id' => $oldBooking->channel_partner_id,
                'brokerage_percent'  => $deltaPercent,
                'brokerage_amount'   => $deltaAmount,
                'calculation_type'   => 'manual_adjustment',
                'is_locked'          => true,
                'effective_from'     => now(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // ➖ OUR Adjustment
            DB::table('mandate_booking_brokerage_ledgers')->insert([
                'booking_id'         => $oldBooking->id,
                'brokerage_id'       => $brokerage->id,
                'party_type'         => 'OUR',
                'brokerage_percent'  => -$deltaPercent,
                'brokerage_amount'   => -$deltaAmount,
                'calculation_type'   => 'manual_adjustment',
                'is_locked'          => true,
                'effective_from'     => now(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }



    
}
