<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });

        // Save the TL / Sr. TL / CH as they were on the booking date, unless someone set them by hand.
        static::saving(function ($booking) {
            if (! $booking->sales_user_id) {
                return;
            }
            $teamTouched = $booking->isDirty(['tl_user_id', 'sr_tl_user_id', 'ch_user_id']);
            if ($teamTouched && $booking->team_source === 'manual') {
                return;
            }
            $needsTeam = ! $booking->exists
                || $booking->isDirty(['sales_user_id', 'booking_date'])
                || $booking->team_source === null;
            if ($needsTeam && $booking->team_source !== 'manual') {
                $team = app(\App\Services\BookingTeamResolver::class)
                    ->teamFor((int) $booking->sales_user_id, $booking->booking_date);
                $booking->fill($team);
                $booking->team_source = 'auto';
            }
        });

        // Brokerage: re-run the project's site ladder and the developer's AOP across all its projects.
        static::created(function ($booking) {
            app(\App\Services\BrokerageCalculationService::class)->recalculateForBooking($booking);
        });

        static::updated(function ($booking) {
            if ($booking->wasChanged([
                'agreement_value',
                'developer_id',
                'booking_date',
                'project_id',
                'booking_confirm',
            ])) {
                app(\App\Services\BrokerageCalculationService::class)->recalculateForBooking($booking);
            }
        });

        static::deleted(function ($booking) {
            app(\App\Services\BrokerageCalculationService::class)->recalculateForBooking($booking);
        });
    }

    protected $fillable = [
        'project_id',
        'developer_id',
        'client_name',
        'booking_date',
        'client_contact',
        'lead_source',
        'configuration',
        'flat_no',
        'wing',
        'tower',
        'booking_amount',
        'agreement_value',
        'passback',
        'additional_kicker',
        'registration_date',
        'sales_user_id',
        'tl_user_id',
        'sr_tl_user_id',
        'ch_user_id',
        'team_source',
        'remark',
        'base_brokerage_percent',
        'site_ladder_percent',
        'aop_ladder_percent',
        'total_brokerage_percent',
        'current_effective_amount',
        'final_revenue',
        'amount_receivable',
        'tds_amount',
        'invoice_raised',
        'total_invoice_percent',
        'total_invoice_amount',
        'total_received_amount',
        'pending_brokerage_percent',
        'pending_brokerage_amount',
        'payment_status'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }
    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'tl_user_id');
    }

    public function seniorTeamLeader()
    {
        return $this->belongsTo(User::class, 'sr_tl_user_id');
    }

    public function clusterHead()
    {
        return $this->belongsTo(User::class, 'ch_user_id');
    }

    public function brokeragePayments()
    {
        return $this->hasMany(BookingBrokeragePayment::class);
    }
}
