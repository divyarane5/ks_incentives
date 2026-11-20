<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Models\CallLog;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Core reports. Every number comes from the same lead visibility rules and date filter as the
 * lead list, and each count links to the matching list, so totals always agree.
 */
class ReportController extends Controller
{
    public const REPORTS = [
        'users' => 'By person',
        'sources' => 'By source',
        'subsources' => 'By sub source',
        'projects' => 'By project',
        'visits' => 'Meetings & visits',
        'calls' => 'Calls',
    ];

    public const PERIODS = [
        'all' => 'All time',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7d' => 'Last 7 days',
        'this_month' => 'This month',
        'last_month' => 'Last month',
        'this_year' => 'This year',
        'custom' => 'Pick dates',
    ];

    private const CALL_OUTCOMES = [
        'answered' => 'Answered', 'not_answered' => 'Not answered', 'busy' => 'Busy',
        'not_reachable' => 'Not reachable', 'switched_off' => 'Switched off', 'wrong_number' => 'Wrong number',
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $report = array_key_exists($request->query('report'), self::REPORTS) ? $request->query('report') : 'users';
        $period = array_key_exists($request->query('period'), self::PERIODS) ? $request->query('period') : 'all';
        [$from, $to] = $this->range($period, $request->query('from'), $request->query('to'));

        $table = match ($report) {
            'users', 'sources', 'subsources', 'projects' => $this->stageReport($report, $user, $from, $to),
            'visits' => $this->visitsReport($user, $from, $to),
            'calls' => $this->callsReport($user, $from, $to),
        };

        if ($request->query('export') === 'csv') {
            return $this->csv($table, self::REPORTS[$report], $period);
        }

        return view('crm.reports.index', [
            'report' => $report,
            'reports' => self::REPORTS,
            'period' => $period,
            'periods' => self::PERIODS,
            'from' => $from,
            'to' => $to,
            'table' => $table,
        ]);
    }

    /** Leads by stage, one row per owner, source or project. */
    private function stageReport(string $report, User $user, ?Carbon $from, ?Carbon $to): array
    {
        $stages = LeadStage::whereNull('parent_id')->orderBy('sort_order')->get(['id', 'name', 'color', 'outcome']);
        $base = Lead::query()->visibleTo($user)
            ->when($from, fn ($q) => $q->where('leads.created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('leads.created_at', '<=', $to));

        [$keyColumn, $param] = match ($report) {
            'users' => ['owner_id', 'owner'],
            'sources', 'subsources' => ['source_id', 'source'],
            'projects' => ['lead_project.project_id', 'project'],
        };
        // By sub source: one row per source + sub source pair, so "Portal" under two sources stays apart.
        $sub = $report === 'subsources';
        $extra = $sub ? ', sub_source as row_sub' : '';
        $groups = $sub ? [$keyColumn, 'sub_source'] : [$keyColumn];
        $rowId = fn ($c) => ($c->row_key ?? 'none').($sub ? '|'.mb_strtolower((string) $c->row_sub) : '');
        if ($report === 'projects') {
            $base->join('lead_project', 'lead_project.lead_id', '=', 'leads.id');
        }

        $counts = (clone $base)->selectRaw("$keyColumn as row_key$extra, stage_id, count(*) as total")
            ->groupBy(...$groups, ...['stage_id'])->toBase()->get();
        $overdue = (clone $base)->where('is_archived', false)->where('next_follow_up_at', '<', now())
            ->selectRaw("$keyColumn as row_key$extra, count(*) as total")->groupBy(...$groups)->toBase()->get()
            ->mapWithKeys(fn ($c) => [$rowId($c) => $c->total]);

        $names = match ($report) {
            'users' => User::whereIn('id', $counts->pluck('row_key')->filter())->pluck('name', 'id'),
            'sources', 'subsources' => LeadSource::whereIn('id', $counts->pluck('row_key')->filter())->pluck('name', 'id'),
            'projects' => Project::withTrashed()->whereIn('id', $counts->pluck('row_key'))->pluck('name', 'id'),
        };
        $blank = ['users' => 'Unassigned', 'sources' => 'No source', 'subsources' => 'No source', 'projects' => ''][$report];

        $rows = [];
        foreach ($counts as $c) {
            $key = $c->row_key ?? 'none';
            $id = $rowId($c);
            $label = $key === 'none' ? $blank : ($names[$key] ?? '(removed)');
            $filter = [$param => $key];
            if ($sub) {
                $label .= ' · '.(filled($c->row_sub) ? $c->row_sub : 'no sub source');
                $filter['sub_source'] = filled($c->row_sub) ? $c->row_sub : 'none';
            }
            $rows[$id] ??= ['key' => $key, 'filter' => $filter, 'label' => $label, 'total' => 0, 'overdue' => (int) ($overdue[$id] ?? 0), 'stages' => []];
            $rows[$id]['stages'][$c->stage_id] = ($rows[$id]['stages'][$c->stage_id] ?? 0) + $c->total;
            $rows[$id]['total'] += $c->total;
        }
        usort($rows, fn ($a, $b) => $b['total'] <=> $a['total']);

        // Only show stages that have leads, so the table fits on a phone.
        $used = $stages->filter(fn ($s) => collect($rows)->sum(fn ($r) => $r['stages'][$s->id] ?? 0) > 0)->values();

        $columns = [['key' => 'total', 'label' => 'Total']];
        foreach ($used as $s) {
            $columns[] = ['key' => 's'.$s->id, 'label' => $s->name, 'stage' => $s->id, 'color' => $s->color];
        }
        $columns[] = ['key' => 'overdue', 'label' => 'Overdue'];

        $out = [];
        foreach ($rows as $r) {
            $cells = ['total' => $r['total'], 'overdue' => $r['overdue']];
            foreach ($used as $s) {
                $cells['s'.$s->id] = $r['stages'][$s->id] ?? 0;
            }
            $out[] = ['label' => $r['label'], 'filter' => $r['filter'], 'cells' => $cells];
        }

        // A lead can be linked to several projects, so the project report has no grand total.
        $totals = null;
        if ($report !== 'projects') {
            $totals = [];
            foreach ($columns as $col) {
                $totals[$col['key']] = array_sum(array_map(fn ($r) => $r['cells'][$col['key']], $out));
            }
        }

        return [
            'columns' => $columns,
            'rows' => $out,
            'totals' => $totals,
            'linkable' => true,
            'note' => $report === 'projects' ? 'A lead interested in two projects is counted under both.' : null,
        ];
    }

    /** Meetings and site visits planned in the period, by the person they were assigned to. */
    private function visitsReport(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $counts = Appointment::query()
            ->whereIn('lead_id', Lead::visibleTo($user)->select('id'))
            ->when($from, fn ($q) => $q->where('scheduled_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('scheduled_at', '<=', $to))
            ->selectRaw('assigned_to_id, type, status, count(*) as total')
            ->groupBy('assigned_to_id', 'type', 'status')->toBase()->get();

        $columns = [
            ['key' => 'meeting_all', 'label' => 'Meetings planned'],
            ['key' => 'meeting_done', 'label' => 'Meetings done'],
            ['key' => 'site_visit_all', 'label' => 'Site visits planned'],
            ['key' => 'site_visit_done', 'label' => 'Site visits done'],
            ['key' => 'not_done', 'label' => 'Not done'],
            ['key' => 'cancelled', 'label' => 'Cancelled'],
        ];

        return $this->peopleTable($counts, 'assigned_to_id', $columns, function (array &$cells, $c) {
            $cells[$c->type.'_all'] += $c->total;
            if ($c->status === 'done') {
                $cells[$c->type.'_done'] += $c->total;
            } elseif (in_array($c->status, ['not_done', 'cancelled'], true)) {
                $cells[$c->status] += $c->total;
            }
        });
    }

    /** Calls logged in the CRM in the period, by the person who made them. */
    private function callsReport(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $counts = CallLog::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->whereIn('user_id', $user->teamUserIds()))
            ->when($from, fn ($q) => $q->where('called_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('called_at', '<=', $to))
            ->selectRaw('user_id, outcome, count(*) as total, count(distinct lead_id) as leads')
            ->groupBy('user_id', 'outcome')->toBase()->get();

        $columns = [['key' => 'calls', 'label' => 'Calls']];
        foreach (self::CALL_OUTCOMES as $key => $label) {
            $columns[] = ['key' => $key, 'label' => $label];
        }

        $table = $this->peopleTable($counts, 'user_id', $columns, function (array &$cells, $c) {
            $cells['calls'] += $c->total;
            if (array_key_exists($c->outcome, $cells)) {
                $cells[$c->outcome] += $c->total;
            }
        });
        $table['note'] = 'Calls made with the Call button, with the outcome the caller picked.';

        return $table;
    }

    private function peopleTable($counts, string $userColumn, array $columns, callable $add): array
    {
        $names = User::whereIn('id', $counts->pluck($userColumn)->filter())->pluck('name', 'id');
        $empty = array_fill_keys(array_column($columns, 'key'), 0);

        $rows = [];
        foreach ($counts as $c) {
            $key = $c->{$userColumn} ?? 'none';
            $rows[$key] ??= ['label' => $key === 'none' ? 'Unassigned' : ($names[$key] ?? '(removed)'), 'cells' => $empty];
            $add($rows[$key]['cells'], $c);
        }
        $rows = array_values($rows);
        usort($rows, fn ($a, $b) => array_sum($b['cells']) <=> array_sum($a['cells']));

        $totals = $empty;
        foreach ($rows as $r) {
            foreach ($r['cells'] as $k => $v) {
                $totals[$k] += $v;
            }
        }

        // Hide columns that are zero everywhere, except the first.
        $columns = array_values(array_filter($columns, fn ($col, $i) => $i === 0 || $totals[$col['key']] > 0, ARRAY_FILTER_USE_BOTH));

        return ['columns' => $columns, 'rows' => $rows, 'totals' => $rows ? $totals : null, 'linkable' => false, 'note' => null];
    }

    /** @return array{0: ?Carbon, 1: ?Carbon} */
    private function range(string $period, ?string $from, ?string $to): array
    {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $this->date($from)?->startOfDay(),
                $this->date($to)?->endOfDay(),
            ],
            default => [null, null],
        };
    }

    private function date(?string $value): ?Carbon
    {
        try {
            return $value ? Carbon::createFromFormat('!Y-m-d', $value) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function csv(array $table, string $name, string $period)
    {
        return response()->streamDownload(function () use ($table) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_merge([''], array_column($table['columns'], 'label')));
            foreach ($table['rows'] as $row) {
                fputcsv($out, array_merge([$row['label']], array_map(fn ($c) => $row['cells'][$c['key']], $table['columns'])));
            }
            if ($table['totals']) {
                fputcsv($out, array_merge(['Total'], array_map(fn ($c) => $table['totals'][$c['key']], $table['columns'])));
            }
            fclose($out);
        }, 'keyleads-'.str($name)->slug().'-'.$period.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
