<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Project;
use App\Services\LeadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/** Fetch one Facebook Lead Ads lead and add it to KeyLeads (re-enquiry if the number exists). */
class FetchFacebookLead implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [30, 120, 600, 1800];

    public function __construct(public string $leadgenId) {}

    public function handle(LeadService $leads): void
    {
        // The same lead can be delivered twice; only add it once.
        if (Cache::has($this->seenKey()) || Lead::withTrashed()->where('legacy_id', 'fb:'.$this->leadgenId)->exists()) {
            return;
        }

        $cfg = config('services.facebook');
        $data = Http::timeout(20)->retry(2, 1000)
            ->get("https://graph.facebook.com/{$cfg['graph_version']}/{$this->leadgenId}", [
                'access_token' => $cfg['page_token'],
                'fields' => 'created_time,field_data,ad_name,campaign_name,form_id,form_name',
            ])->throw()->json();

        $fields = [];
        foreach ($data['field_data'] ?? [] as $f) {
            $fields[strtolower($f['name'] ?? '')] = trim((string) ($f['values'][0] ?? ''));
        }
        $pick = function (array $keys) use ($fields) {
            foreach ($keys as $k) {
                if (! empty($fields[$k])) {
                    return $fields[$k];
                }
            }

            return null;
        };

        $phone = Lead::normalizePhone($pick(['phone_number', 'phone', 'mobile_number', 'mobile', 'contact_number']) ?? '');
        if (strlen((string) $phone) < 10) {
            return; // nothing we can call
        }

        $name = $pick(['full_name', 'name']) ?? trim(($pick(['first_name']) ?? '').' '.($pick(['last_name']) ?? ''));
        $projectName = $pick(['project', 'project_name', 'which_project_are_you_interested_in?']);
        $projectIds = $projectName ? Project::active()->where('name', $projectName)->pluck('id')->all() : [];

        // Anything else the form asked goes into the notes.
        $skip = ['full_name', 'name', 'first_name', 'last_name', 'phone_number', 'phone', 'mobile_number', 'mobile', 'contact_number', 'email'];
        $extra = collect($fields)->except($skip)->filter()->map(fn ($v, $k) => Str::headline($k).': '.$v)->join("\n");

        [$lead, $created] = $leads->createOrReEnquire([
            'name' => Str::limit($name ?: 'Facebook lead', 150, ''),
            'phone' => $phone,
            'email' => filter_var($pick(['email']), FILTER_VALIDATE_EMAIL) ? strtolower($pick(['email'])) : null,
            'source_id' => LeadSource::where('slug', 'facebook')->value('id'),
            'sub_source' => Str::limit($data['form_name'] ?? $data['ad_name'] ?? 'Lead form', 100, ''),
            'campaign' => isset($data['campaign_name']) ? Str::limit($data['campaign_name'], 150, '') : null,
            'owner_id' => null,
            'notes' => $extra ?: null,
        ], ['enquired_for' => 'buy'], $projectIds, null);

        if ($created) {
            $lead->forceFill(['legacy_id' => 'fb:'.$this->leadgenId])->save();
        }
        Cache::put($this->seenKey(), true, now()->addDays(30));
    }

    private function seenKey(): string
    {
        return 'facebook-lead:'.$this->leadgenId;
    }
}
