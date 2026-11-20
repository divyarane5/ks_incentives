<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Models\Lead;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** The "Today" screen: what each person needs to do now. */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $mine = fn () => Lead::query()->active()->where('owner_id', $user->id);

        $counts = [
            'overdue' => $mine()->where('next_follow_up_at', '<', now()->startOfDay())->count(),
            'today' => $mine()->whereBetween('next_follow_up_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'new' => $mine()->whereNull('picked_at')->count(),
        ];

        $todayLeads = $mine()
            ->with(['stage', 'subStage'])
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now()->endOfDay())
            ->orderBy('next_follow_up_at')
            ->limit(30)
            ->get();

        $newLeads = $mine()->with(['stage', 'source'])->whereNull('picked_at')->latest()->limit(10)->get();

        $appointments = Appointment::with('lead')
            ->where('assigned_to_id', $user->id)
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_at', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
            ->orderBy('scheduled_at')
            ->get();

        // Managers also see who on their team has overdue follow-ups.
        $teamOverdue = collect();
        if ($user->role !== \App\Models\User::ROLE_SALES) {
            $teamIds = array_values(array_diff($user->isAdmin() ? \App\Models\User::pluck('id')->all() : $user->teamUserIds(), [$user->id]));
            $teamOverdue = Lead::query()->active()
                ->whereIn('owner_id', $teamIds)
                ->where('next_follow_up_at', '<', now()->startOfDay())
                ->selectRaw('owner_id, count(*) as total')
                ->groupBy('owner_id')
                ->orderByDesc('total')
                ->limit(10)
                ->with('owner:id,name')
                ->get();
        }

        return view('crm.dashboard', compact('counts', 'todayLeads', 'newLeads', 'appointments', 'teamOverdue'));
    }
}
