<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LeadAlert;
use App\Services\FollowUpSummary;
use Illuminate\Console\Command;

/** Morning alert: each person's overdue and due-today calls, and each manager's team overdue. */
class SendDailySummary extends Command
{
    protected $signature = 'keyleads:daily-summary';

    protected $description = 'Send everyone their morning follow-up summary (in the app bell)';

    public function handle(FollowUpSummary $summary): int
    {
        $sent = 0;
        foreach (User::active()->crmUsers()->cursor() as $user) {
            $mine = $summary->forUser($user);
            $team = $summary->teamOverdue($user);
            $teamTotal = array_sum(array_column($team, 'overdue'));

            if ($mine['overdue'] + $mine['today'] + $mine['untouched'] + $teamTotal === 0) {
                continue;
            }

            $parts = [];
            if ($mine['overdue']) {
                $parts[] = $mine['overdue'].' overdue';
            }
            if ($mine['today']) {
                $parts[] = $mine['today'].' due today';
            }
            if ($mine['untouched']) {
                $parts[] = $mine['untouched'].' not yet called';
            }
            if ($teamTotal) {
                $worst = collect($team)->take(3)->map(fn ($r) => strtok($r['user']->name, ' ').' '.$r['overdue'])->join(', ');
                $parts[] = "team overdue $teamTotal ($worst)";
            }

            $user->notify(new LeadAlert(
                'Good morning. Your calls for today',
                ucfirst(implode(' · ', $parts)),
                route('crm.leads.index', ['tab' => $mine['overdue'] ? 'overdue' : 'today']),
                'summary',
            ));
            $sent++;
        }

        $this->info("Sent $sent summaries.");

        return self::SUCCESS;
    }
}
