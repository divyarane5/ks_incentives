<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncentiveSlab;

class IncentiveSlabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(auth()->user()->can('incentive-slab-view'), 403);
        $slabs = IncentiveSlab::orderBy('financial_year', 'desc')
            ->orderBy('role')
            ->orderBy('from_times')
            ->get();

        return view('incentive_slabs.index', compact('slabs'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('incentive-slab-create'), 403);
        return view('incentive_slabs.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('incentive-slab-create'), 403);
        $request->validate([
            'financial_year' => 'required',
            'role' => 'required',
            'from_times' => 'required|numeric',
            'to_times' => 'required|numeric',
            'incentive_percent' => 'required|numeric',
            'justification_multiplier' => 'required|numeric',
        ]);

        IncentiveSlab::create($request->all());

        return redirect()
            ->route('incentive-slabs.index')
            ->with('success', 'Incentive slab created successfully.');
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->can('incentive-slab-edit'), 403);
        $slab = IncentiveSlab::findOrFail($id);

        return view('incentive_slabs.edit', compact('slab'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->can('incentive-slab-edit'), 403);
        $request->validate([
            'financial_year' => 'required',
            'role' => 'required',
            'from_times' => 'required|numeric',
            'to_times' => 'required|numeric',
            'incentive_percent' => 'required|numeric',
            'justification_multiplier' => 'required|numeric',
        ]);

        $slab = IncentiveSlab::findOrFail($id);

        $slab->update($request->all());

        return redirect()
            ->route('incentive-slabs.index')
            ->with('success', 'Incentive slab updated successfully.');
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->can('incentive-slab-delete'), 403);
        $slab = IncentiveSlab::findOrFail($id);

        $slab->delete();

        return redirect()
            ->route('incentive-slabs.index')
            ->with('success', 'Incentive slab deleted successfully.');
    }
}
