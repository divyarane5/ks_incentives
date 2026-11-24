@include('users.partials.people-styles')
<style>
    .hr-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 12px; }
    .hr-head h4 { margin: 0; }
    .hr-head .sub { font-size: .82rem; color: var(--kp-muted); }
    .hr-head select, .hr-head .btn { min-height: 40px; border-radius: 10px; }
    .hr-bal { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 14px; }
    .hr-bal .kp-fact { border-left: 4px solid var(--c, var(--kp-gold)); }
    .hr-bal .v { font-size: 1.4rem !important; }
    .hr-bal .v small { font-size: .75rem; color: var(--kp-muted); font-weight: 600; }
    .hr-status { font-size: .72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; white-space: nowrap; }
    .hr-status.pending { background: #fff4e0; color: #9a5b00; }
    .hr-status.approved { background: #e7f6ec; color: #1a7f3c; }
    .hr-status.rejected { background: #fde8e8; color: #b42318; }
    .hr-status.cancelled { background: #eef1f6; color: #6b778c; }
    .hr-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; margin-right: 5px; vertical-align: middle; }
    .hr-form .form-control, .hr-form .form-select { min-height: 44px; border-radius: 10px; font-size: 16px; }
    .hr-form .form-label { font-size: .78rem; font-weight: 600; color: var(--kp-muted); margin-bottom: 3px; }
    .hr-seg { display: flex; background: #f4f6fa; border-radius: 10px; padding: 3px; }
    .hr-seg label { flex: 1; text-align: center; padding: 8px 4px; border-radius: 8px; font-size: .82rem; font-weight: 600; color: var(--kp-muted); cursor: pointer; margin: 0; }
    .hr-seg input { display: none; }
    .hr-seg input:checked + span { color: var(--kp-navy); }
    .hr-seg label:has(input:checked) { background: #fff; box-shadow: 0 1px 3px rgba(16, 24, 40, .1); color: var(--kp-navy); }
    .hr-count { font-weight: 700; color: var(--kp-navy); font-size: .9rem; }
    .hr-actions { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
    .hr-actions .btn { min-height: 38px; border-radius: 9px; font-weight: 600; }
    .hr-pills { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; margin-bottom: 12px; }
    .hr-pills::-webkit-scrollbar { display: none; }
    .hr-pills a { white-space: nowrap; padding: 7px 12px; border-radius: 999px; background: #fff; border: 1px solid var(--kp-line); font-size: .82rem; font-weight: 600; color: var(--kp-muted); text-decoration: none; }
    .hr-pills a.on { background: var(--kp-navy); color: #fff; border-color: var(--kp-navy); }
    .hr-table { width: 100%; font-size: .88rem; }
    .hr-table th { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: var(--kp-muted); font-weight: 700; padding: 8px 10px; border-bottom: 1px solid var(--kp-line); white-space: nowrap; }
    .hr-table td { padding: 9px 10px; border-bottom: 1px solid var(--kp-line); vertical-align: middle; }
    .hr-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .hr-table tfoot td { font-weight: 700; background: #f6f8fb; color: var(--kp-navy); }
    @media (min-width: 768px) { .hr-bal { grid-template-columns: repeat(4, 1fr); } }
</style>
