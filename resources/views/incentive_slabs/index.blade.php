@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Incentive Slabs</h4>

        <a href="{{ route('incentive-slabs.create') }}"
           class="btn btn-primary">
            Add Slab
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>FY</th>
                        <th>Role</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Incentive %</th>
                        <th>Justification</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($slabs as $slab)

                    <tr>
                        <td>{{ $slab->id }}</td>
                        <td>{{ $slab->financial_year }}</td>
                        <td>{{ $slab->role }}</td>
                        <td>{{ $slab->from_times }}</td>
                        <td>{{ $slab->to_times }}</td>
                        <td>{{ $slab->incentive_percent }}%</td>
                        <td>{{ $slab->justification_multiplier }}x</td>

                        <td>

                            <a href="{{ route('incentive-slabs.edit', $slab->id) }}"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <form action="{{ route('incentive-slabs.destroy', $slab->id) }}"
                                  method="POST"
                                  class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        onclick="return confirm('Delete this slab?')"
                                        class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>

                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No slabs found.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection