<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Services\FollowUpSummary;
use Illuminate\Http\Request;

/** The bell: live overdue counts plus recent alerts. Opening it marks alerts as read. */
class AlertController extends Controller
{
    public function index(Request $request, FollowUpSummary $summary)
    {
        $user = $request->user();
        $alerts = $user->notifications()->latest()->paginate(30);
        $user->unreadNotifications()->update(['read_at' => now()]);

        return view('crm.alerts.index', [
            'mine' => $summary->forUser($user),
            'team' => $summary->teamOverdue($user),
            'alerts' => $alerts,
        ]);
    }
}
