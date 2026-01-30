<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MandateBooking;
use App\Models\MandateBookingBrokerage;
use App\Models\MandateBookingBrokerageLedger;
use App\Models\ChannelPartner;

class BookingLedgerController extends Controller
{
    /**
     * Show ledger page for a booking
     */
    public function index($bookingId)
    {
        $booking = MandateBooking::with([
            'project',
            'channel_partner',
            'brokerageLedgers'
        ])->findOrFail($bookingId);

        // Total brokerage (from mandate)
        $brokerage = $booking->brokerage;

        // All ledger entries
        $ledgers = $booking->brokerageLedgers()
            ->orderBy('effective_from')
            ->orderBy('id')
            ->get();

        /* ===============================
        STEP 2: CP & OUR TOTALS
        =============================== */

        $totalBrokeragePercent = $brokerage->brokerage_percent;
        $totalBrokerageAmount  = $brokerage->brokerage_amount;

        $cpPercent = $ledgers
            ->where('party_type', 'CP')
            ->sum('brokerage_percent');

        $cpAmount = $ledgers
            ->where('party_type', 'CP')
            ->sum('brokerage_amount');

        // OUR is ALWAYS derived (never entered manually)
        $ourPercent = round($totalBrokeragePercent - $cpPercent, 2);
        $ourAmount  = round($totalBrokerageAmount - $cpAmount, 2);

        return view('mandate_bookings.ledgers.index', compact(
            'booking',
            'brokerage',
            'ledgers',
            'cpPercent',
            'cpAmount',
            'ourPercent',
            'ourAmount'
        ));
    }


    /**
     * Store CP Adjustment Ledger (OUR auto-calculated)
     */
    public function store(Request $request, $bookingId)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'brokerage_percent' => 'required|numeric',
                'effective_from' => 'required|date',
                'remark' => 'nullable|string|max:255',
                'channel_partner_id' => 'required|exists:channel_partners,id',
            ]);

            $booking = MandateBooking::with(['brokerage', 'finance'])->findOrFail($bookingId);

            if (!$booking->brokerage) {
                throw new \Exception('Brokerage not generated for this booking.');
            }

            if (!$booking->finance) {
                throw new \Exception('Finance data not found.');
            }

            $brokerage = $booking->brokerage;

            /* ---------------- BASE AMOUNT ---------------- */
            $finance = $booking->finance;

            $agreementValue =
                $finance->unit_value +
                ($finance->other_charges ?? 0) +
                ($finance->car_park_charges ?? 0);

            $baseAmount = match ($brokerage->brokerage_criteria) {
                'AV' => $agreementValue,
                'UCV_OCC' => $finance->unit_value,
                'UCV_CPC' => $finance->unit_value + ($finance->car_park_charges ?? 0),
                default => $agreementValue,
            };

            /* ---------------- CURRENT TOTALS ---------------- */
            $currentCpPercent = MandateBookingBrokerageLedger::where('booking_id', $bookingId)
                ->where('party_type', 'CP')
                ->sum('brokerage_percent');

            $adjustmentPercent = round($request->brokerage_percent, 2);
            $newCpPercent = round($currentCpPercent + $adjustmentPercent, 2);

            if ($newCpPercent < 0) {
                throw new \Exception('CP brokerage cannot be negative.');
            }

            $totalPercent = $brokerage->brokerage_percent;
            $newOurPercent = round($totalPercent - $newCpPercent, 2);

            if ($newOurPercent < 0) {
                throw new \Exception('OUR brokerage cannot be negative.');
            }

            /* ---------------- AMOUNTS ---------------- */
            $cpAmount = round(($baseAmount * $adjustmentPercent) / 100, 2);
            $ourAmount = round(($baseAmount * (-$adjustmentPercent)) / 100, 2);

            /* ---------------- CREATE CP LEDGER ---------------- */
            MandateBookingBrokerageLedger::create([
                'booking_id' => $bookingId,
                'brokerage_id' => $brokerage->id,
                'channel_partner_id' => $request->channel_partner_id,
                'party_type' => 'CP',
                'brokerage_percent' => $adjustmentPercent,
                'brokerage_amount' => $cpAmount,
                'entry_type' => 'adjustment',
                'calculation_type' => 'ladder_upgrade',
                'status' => 'pending',
                'is_locked' => 0,
                'remark' => $request->remark,
                'effective_from' => $request->effective_from,
                'created_by' => auth()->id(),
            ]);

            /* ---------------- CREATE OUR LEDGER ---------------- */
            MandateBookingBrokerageLedger::create([
                'booking_id' => $bookingId,
                'brokerage_id' => $brokerage->id,
                'channel_partner_id' => null,
                'party_type' => 'OUR',
                'brokerage_percent' => -$adjustmentPercent,
                'brokerage_amount' => $ourAmount,
                'entry_type' => 'adjustment',
                'calculation_type' => 'ladder_upgrade',
                'status' => 'pending',
                'is_locked' => 0,
                'remark' => $request->remark,
                'effective_from' => $request->effective_from,
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            
            return redirect()
                ->route('mandate_bookings.ledgers', $bookingId)
                ->with('success', 'Ledger adjustment added successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }




    /**
     * Delete ledger entry (only if unlocked)
     */
    public function destroy($ledgerId)
    {
        $ledger = MandateBookingBrokerageLedger::findOrFail($ledgerId);

        if ($ledger->is_locked) {
            abort(403, 'Locked ledger cannot be deleted.');
        }

        $bookingId = $ledger->booking_id;
        $ledger->delete();

        return redirect()
            ->route('mandate_bookings.ledgers', $bookingId)
            ->with('success', 'Ledger entry deleted.');
    }

    public function cpIndex(Request $request)
    {
        $ledgers = MandateBookingBrokerageLedger::with([
                'booking.project',
                'channelPartner'
            ])
            ->whereIn('party_type', ['CP', 'OUR'])
         //   ->where('status', 'pending')
            ->where('is_locked', 1)
            ->when($request->channel_partner_id, fn($q) =>
                $q->where('channel_partner_id', $request->channel_partner_id)
            )
            ->when($request->from_date, fn($q) =>
                $q->whereDate('effective_from', '>=', $request->from_date)
            )
            ->when($request->to_date, fn($q) =>
                $q->whereDate('effective_from', '<=', $request->to_date)
            )
            ->orderBy('booking_id','desc')
            ->get();

        $channelPartners = ChannelPartner::orderBy('firm_name')->get();

        return view('brokerage_ledgers.index', compact('ledgers','channelPartners'));
    }
    public function markPaid(Request $request, $ledgerId)
    {
       
        $ledger = MandateBookingBrokerageLedger::findOrFail($ledgerId);

        if ($ledger->status === 'paid') {
            return back()->withErrors('Ledger already paid');
        }
      //  echo $ledger->brokerage_amount; exit;
        //echo $ledger->status; exit;
        $request->validate([
            'payment_mode' => 'required|in:NEFT,RTGS,UPI,CHEQUE,INTERNAL',
            'payment_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $ledger->update([
            'payment_mode' => $request->payment_mode,
            'payment_date' => $request->payment_date,
            'reference_no' => $request->reference_no,
            'status' => 'paid',
        ]);

        // ✅ AUTO UPDATE BROKERAGE STATUS
        $this->syncBrokerageStatus($ledger->brokerage_id);

        return back()->with('success','Ledger marked as paid');
    }
    protected function syncBrokerageStatus($brokerageId)
    {
        $pending = MandateBookingBrokerageLedger::where('brokerage_id', $brokerageId)
           // ->where('party_type', 'CP')
            ->where('status', '!=', 'paid')
            ->exists();

        MandateBookingBrokerage::where('id', $brokerageId)->update([
            'status' => $pending ? 'pending' : 'paid'
        ]);
    }

}
