@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row indent_counts">
        <div class="col-lg-12 col-md-12 order-1">
            <div class="row">
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/total.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Indent</span>
                        <h3 class="card-title mb-2">{{ !empty($indents) ? array_sum($indents) : 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/pending.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">{{ config('constants.INDENT_STATUS')['pending'] }} Indent</span>
                        <h3 class="card-title mb-2">{{ (isset($indents['pending']) ? $indents['pending'] : 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/approved.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">{{ config('constants.INDENT_STATUS')['approved'] }} Indent</span>
                        <h3 class="card-title mb-2">{{ (isset($indents['approved']) ? $indents['approved'] : 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/partial-approved.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">{{ config('constants.INDENT_STATUS')['half-approved'] }} Indent</span>
                        <h3 class="card-title mb-2">{{ (isset($indents['half-approved']) ? $indents['half-approved'] : 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/rejected.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">{{ config('constants.INDENT_STATUS')['rejected'] }} Indent</span>
                        <h3 class="card-title mb-2">{{ (isset($indents['rejected']) ? $indents['rejected'] : 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset("assets/img/dashboard_icons/closed.png") }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">{{ config('constants.INDENT_STATUS')['closed'] }} Indent</span>
                        <h3 class="card-title mb-2">{{ (isset($indents['closed']) ? $indents['closed'] : 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Revenue -->
        <div class="col-12 col-lg-8 order-2  order-lg-2 mb-4">
        <div class="card">
            <div class="row row-bordered g-0">
            <div class="col-md-12">
                <input type="hidden" id="weekly_indent_expense" value="{{ json_encode($indentExpenseArray) }}">
                <input type="hidden" id="weekly_reimbursement_expense" value="{{ json_encode($reimbursementExpenseArray) }}">
                <h5 class="card-header m-0 me-2 pb-3">Total Expense</h5>
                <div id="totalRevenueChart" class="px-2"></div>
            </div>
            </div>
        </div>
        </div>
        <!--/ Total Revenue -->
        <div class="col-12 col-md-12 col-8 col-lg-4 order-3 order-md-2">
        <div class="row">
            <div class="col-lg-6 col-md-4 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                    <img src="{{ asset("assets/img/dashboard_icons/payment.png") }}" alt="Credit Card" class="rounded" />
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Indent Payments</span>
                <h3 class="card-title text-nowrap mb-2">₹{{ !empty($totalIndentExpense) ? $totalIndentExpense : 0 }}</h3>
                </div>
            </div>
            </div>
            <div class="col-lg-6  col-md-4 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                    <img src="{{ asset("assets/img/dashboard_icons/payment.png") }}" alt="Credit Card" class="rounded" />
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Reimbursement</span>
                <h3 class="card-title mb-2">₹{{ !empty($totalReimbursementExpense) ? $totalReimbursementExpense : 0 }}</h3>
                </div>
            </div>
            </div>
            <div class="col-lg-12  col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body" style="padding-bottom: 1.1rem;">
                <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                    <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                    <div class="card-title">
                        <h5 class="text-nowrap mb-2">Overall Expense</h5>
                        <span class="badge bg-label-warning rounded-pill">{{ date("d-m-Y", strtotime('-6  days'))." - ".date("d-m-Y") }}</span>
                    </div>
                    <div class="mt-sm-auto">
                        <h3 class="mb-0">₹{{ !empty($totalWeeklyExpenseArray) ? array_sum(array_column($totalWeeklyExpenseArray, 'y')) : 0 }}</h3>
                        <span class="fw-semibold-13 d-block mb-1">Indents - ₹{{ array_sum($indentExpenseArray) }}</span>
                        <span class="fw-semibold-13 d-block mb-1">Reimbursements - ₹{{ array_sum($reimbursementExpenseArray) }}</span>
                    </div>
                    </div>
                    <input type="hidden" id="total_weekly_expense" value="{{ json_encode($totalWeeklyExpenseArray) }}">

                    <div id="profileReportChart"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    <div class="row">
        @can('indent-approval')
        <!-- Indent Approvals -->
        <div class="col-md-6 col-lg-6 col-xl-6 order-0 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Indent Approval</h5>
            </div>
            </div>
            <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex flex-column align-items-center gap-1">
                </div>
            </div>
            <ul class="p-0 m-0">
                @if (!$indentApproval->isEmpty())
                    @foreach ($indentApproval as $indentItem)
                        <li class="d-flex mb-4 pb-1">
                            <div class="flex-shrink-0 me-3 auto_margin">
                                <a target="_blank" href="{{ route('indent.show', $indentItem->id) }}" class="avatar-initial rounded bg-label-info px-2 py-2 ">
                                    {{ $indentItem->indent_code }}
                                </a>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                <h6 class="mb-0">{{ $indentItem->expense }} ({{ $indentItem->title }})</h6>
                                <small class="text-muted">{{ $indentItem->vendor }}</small>
                                </div>
                                <div class="user-progress">
                                <small class="fw-semibold">₹ {{ $indentItem->total }}</small>
                                </div>
                            </div>
                        </li>
                    @endforeach
                @else
                <li class="d-flex mb-4 pb-1">
                    No data available
                </li>
                @endif
            </ul>
            </div>
        </div>
        </div>
        <!--/ Indent Approvals -->
        @endcan

        @can('reimbursement-approval')
        <!-- Expense Approvals -->
        <div class="col-md-6 col-lg-6 col-xl-6 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Reimbursement Approval</h5>
                </div>
                </div>
                <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column align-items-center gap-1">
                    </div>
                </div>
                <ul class="p-0 m-0">
                    @if (!$reimbursementApproval->isEmpty())
                        @foreach ($reimbursementApproval as $reimbursement)
                            <li class="d-flex mb-4 pb-1">
                                <div class="flex-shrink-0 me-3 auto_margin">
                                    <a target="_blank" href="{{ route('reimbursement.show', $reimbursement->id) }}" class="avatar-initial rounded bg-label-primary px-2 py-2 ">
                                        {{ $reimbursement->reimbursement_code }}
                                    </a>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                    <h6 class="mb-0">{{ $reimbursement->project_name }} ({{ $reimbursement->visit_created_by }})</h6>
                                    <small class="text-muted">{{ $reimbursement->source }} To {{ $reimbursement->destination }}</small>
                                    </div>
                                    <div class="user-progress">
                                    <small class="fw-semibold">₹ {{ $reimbursement->amount }}</small>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                    <li class="d-flex mb-4 pb-1">
                        No data available
                    </li>
                    @endif
                </ul>
                </div>
            </div>
        </div>
        <!--/ Expense Approvals -->
        @endcan
    </div>
</div>
<!-- / Content -->
@endsection

@section('script')
<!-- Page JS -->
<script src="{{ asset("assets/js/dashboards-analytics.js") }}"></script>

@endsection
