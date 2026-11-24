<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadSubSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Admin screen for the lists of sources and the sub sources under each one. */
class SourceController extends Controller
{
    public function index()
    {
        $counts = Lead::query()->whereNotNull('sub_source')
            ->selectRaw('source_id, sub_source, count(*) as total')->groupBy('source_id', 'sub_source')->toBase()->get()
            ->mapWithKeys(fn ($r) => [$r->source_id.'|'.mb_strtolower($r->sub_source) => $r->total]);

        return view('crm.sources.index', [
            'sources' => LeadSource::with('subSources')->orderBy('sort_order')->orderBy('name')->get(),
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:lead_sources,name']]);
        LeadSource::create([
            'name' => Str::squish($data['name']),
            'slug' => Str::slug($data['name']) ?: Str::random(8),
            'sort_order' => (int) LeadSource::max('sort_order') + 1,
        ]);

        return back()->with('status', 'Source added.');
    }

    public function update(Request $request, LeadSource $source)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('lead_sources', 'name')->ignore($source->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $source->update(['name' => Str::squish($data['name']), 'is_active' => (bool) ($data['is_active'] ?? false)]);

        return back()->with('status', 'Source saved.')->with('open', $source->id);
    }

    public function storeSub(Request $request, LeadSource $source)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        $name = Str::squish($data['name']);
        $exists = $source->subSources()->get()->contains(fn ($s) => mb_strtolower($s->name) === mb_strtolower($name));
        if ($exists) {
            return back()->withErrors(['name' => "$source->name already has $name."]);
        }
        $source->subSources()->create(['name' => $name]);

        return back()->with('status', "Added $name under $source->name.")->with('open', $source->id);
    }

    /** Renaming a sub source also renames it on every lead that has it, so filters and reports stay whole. */
    public function updateSub(Request $request, LeadSubSource $subSource)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $name = Str::squish($data['name']);
        $clash = LeadSubSource::where('source_id', $subSource->source_id)->where('id', '!=', $subSource->id)->get()
            ->contains(fn ($s) => mb_strtolower($s->name) === mb_strtolower($name));
        if ($clash) {
            return back()->withErrors(['name' => "There is already a sub source called $name."]);
        }

        DB::transaction(function () use ($subSource, $name, $data) {
            if ($name !== $subSource->name) {
                Lead::withTrashed()->where('source_id', $subSource->source_id)->where('sub_source', $subSource->name)
                    ->toBase()->update(['sub_source' => $name]);
            }
            $subSource->update(['name' => $name, 'is_active' => (bool) ($data['is_active'] ?? false)]);
        });

        return back()->with('status', 'Sub source saved.')->with('open', $subSource->source_id);
    }
}
