<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadImport;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Project;
use App\Models\PropertyType;
use App\Models\User;
use App\Support\Spreadsheet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Turns rows of an Excel/CSV sheet into leads. Used for everyday uploads and for the one-time
 * Leadrat import: column names are matched automatically and can be changed before the import runs.
 *
 * - A phone number that already exists becomes a re-enquiry on that lead (no duplicates).
 * - A row whose Leadrat id / serial number was already imported is skipped, so an import can be re-run safely.
 * - Owners, stages, sources and projects are matched by name; names that don't match are listed
 *   in the import summary instead of creating messy new records.
 */
class LeadImporter
{
    /** Fields a column can be mapped to: label and header names that map to it automatically. */
    public const FIELDS = [
        'name' => ['Name', ['name', 'leadname', 'customername', 'clientname', 'fullname', 'customer']],
        'phone' => ['Phone', ['phone', 'phonenumber', 'phoneno', 'mobile', 'mobileno', 'mobilenumber', 'contactno', 'contactnumber', 'contact', 'primarycontactno', 'primarynumber']],
        'alt_phone' => ['Alternate phone', ['alternatecontactno', 'alternatenumber', 'alternatephone', 'altphone', 'alternatecontact', 'alternatemobile', 'altcontactno']],
        'email' => ['Email', ['email', 'emailid', 'emailaddress']],
        'source' => ['Source', ['source', 'leadsource']],
        'sub_source' => ['Sub-source', ['subsource', 'leadsubsource']],
        'campaign' => ['Campaign', ['campaign', 'campaignname', 'campaigns']],
        'stage' => ['Stage / status', ['status', 'stage', 'leadstatus', 'leadstage']],
        'sub_stage' => ['Sub-stage / reason', ['substatus', 'substage', 'reason', 'leadsubstatus']],
        'owner' => ['Assigned to', ['assignedto', 'assigneduser', 'owner', 'leadowner', 'primaryowner', 'salesperson', 'assigned']],
        'secondary_owner' => ['Secondary owner', ['secondaryowner', 'secondaryuser', 'secondaryassignedto']],
        'project' => ['Project', ['project', 'projects', 'projectname', 'projectnames']],
        'notes' => ['Notes', ['notes', 'note', 'remarks', 'remark', 'comments', 'comment']],
        'created_at' => ['Received date', ['createddate', 'createdon', 'receiveddate', 'receivedon', 'leaddate', 'date', 'createdat', 'enquirydate']],
        'next_follow_up_at' => ['Next follow-up', ['scheduleddate', 'scheduledon', 'followupdate', 'nextfollowup', 'nextfollowupdate', 'callbackdate', 'scheduledat']],
        'enquired_for' => ['Buy or rent', ['enquiredfor', 'requirementtype', 'lookingfor']],
        'property_type' => ['Property type', ['propertytype', 'propertysubtype', 'propertytypes']],
        'bhk' => ['BHK', ['bhk', 'bhks', 'noofbhk', 'noofbhks', 'configuration']],
        'budget_min' => ['Budget from', ['minbudget', 'lowerbudget', 'budgetfrom', 'budgetmin']],
        'budget_max' => ['Budget (up to)', ['budget', 'maxbudget', 'upperbudget', 'budgetto', 'budgetmax']],
        'location' => ['Location', ['location', 'locations', 'locality', 'area', 'enquiredlocation', 'preferredlocation', 'city']],
        'temperature' => ['Hot / warm / cold', ['tags', 'tag', 'temperature', 'leadtags', 'priority']],
        'legacy_id' => ['Leadrat id / serial no.', ['serialnumber', 'serialno', 'leadid', 'leadrefid', 'referenceid', 'id']],
    ];

    /** Other names portals and old CRMs use for our lead sources. */
    private const SOURCE_ALIASES = [
        'ninetynineacres' => '99acres', '99acre' => '99acres', 'housing' => 'housingcom', 'google' => 'googleads',
        'fb' => 'facebook', 'facebookads' => 'facebook', 'walkins' => 'walkin', 'cp' => 'channelpartner',
        'excel' => 'excelupload', 'bulkupload' => 'excelupload', 'websiteform' => 'website', 'microsite' => 'website',
        'projectmicrosite' => 'website', 'propertymicrosite' => 'website', 'magicbrick' => 'magicbricks',
    ];

    public const MAX_PROBLEMS = 2000;

    private array $stages = [];      // normalized name => LeadStage (top level)

    private array $subStages = [];   // parent id => [normalized name => LeadStage]

    private array $anySub = [];      // normalized child name => LeadStage (child)

    private ?LeadStage $defaultStage = null;

    private array $users = [];       // normalized name / email / phone => user id

    private array $sources = [];     // normalized name => id

    private array $projects = [];    // normalized name => id

    private array $propertyTypes = [];

    private array $problems;

    private array $summary;

    private array $counts = ['created' => 0, 're_enquiry' => 0, 'skipped' => 0, 'error' => 0];

    public function __construct(private LeadImport $import)
    {
        $this->problems = $import->problems ?? [];
        $this->summary = $import->summary ?? [];
        $this->loadLookups();
    }

    /** Guess which column holds which field from the header names. */
    public static function guessMapping(array $headers): array
    {
        $normalized = array_map(fn ($h) => self::norm((string) $h), $headers);
        $mapping = [];
        $used = [];

        foreach (self::FIELDS as $field => [, $aliases]) {
            foreach ($aliases as $alias) {
                foreach ($normalized as $i => $header) {
                    if ($header === $alias && ! isset($used[$i])) {
                        $mapping[$field] = $i;
                        $used[$i] = true;

                        continue 3;
                    }
                }
            }
        }

        return $mapping;
    }

    /**
     * Import data rows [$start, $start + $limit). Row 0 is the first row after the header.
     *
     * @return bool whether there are more rows after this chunk
     */
    public function run(int $start, int $limit): bool
    {
        $by = $this->import->user ?? User::active()->crmUsers([User::ROLE_ADMIN])->first();
        $index = -2; // the header row is -1, data rows count from 0
        $processed = 0;
        $more = false;

        foreach (Spreadsheet::rows(Storage::disk('local')->path($this->import->path)) as $row) {
            if (++$index < $start) {
                continue;
            }
            if ($index >= $start + $limit) {
                $more = true;
                break;
            }
            $processed++;
            if (Spreadsheet::isEmptyRow($row)) {
                continue;
            }

            try {
                $this->importRow($row, $index + 2, $by);
            } catch (\Throwable $e) {
                report($e);
                $this->problem($index + 2, $row, 'Could not save: '.str($e->getMessage())->limit(120));
            }
        }

        $this->import->forceFill([
            'processed_rows' => $this->import->processed_rows + $processed,
            'created_count' => $this->import->created_count + $this->counts['created'],
            're_enquiry_count' => $this->import->re_enquiry_count + $this->counts['re_enquiry'],
            'skipped_count' => $this->import->skipped_count + $this->counts['skipped'],
            'error_count' => $this->import->error_count + $this->counts['error'],
            'problems' => $this->problems,
            'summary' => $this->summary,
        ])->save();

        return $more;
    }

    private function importRow(array $row, int $rowNumber, User $by): void
    {
        $get = function (string $field) use ($row) {
            $col = $this->import->mapping[$field] ?? null;
            if ($col === null || $col === '' || ! array_key_exists((int) $col, $row)) {
                return null;
            }
            $value = $row[(int) $col];
            if ($value instanceof \DateTimeInterface) {
                return $value;
            }
            if (is_float($value) && floor($value) === $value) {
                $value = sprintf('%.0f', $value); // phone numbers stored as numbers in Excel
            }
            $value = trim((string) $value);

            return $value === '' ? null : $value;
        };
        $options = $this->import->options ?? [];

        // Phone: the only thing a lead can't do without. A cell may hold two numbers.
        $phones = preg_split('/[,\/;|]+/', (string) $get('phone')) ?: [];
        $phone = Lead::normalizePhone(trim($phones[0] ?? ''));
        if (! $phone || strlen($phone) < 10 || strlen($phone) > 15) {
            $this->problem($rowNumber, $row, 'Phone number missing or not valid');

            return;
        }
        $alt = $get('alt_phone') ?? (isset($phones[1]) ? trim($phones[1]) : null);
        $alt = $alt ? Lead::normalizePhone($alt) : null;

        $legacyId = $get('legacy_id');
        if ($legacyId && Lead::withTrashed()->where('legacy_id', $legacyId)->exists()) {
            $this->skip('Already imported earlier');

            return;
        }

        [$stage, $subStage] = $this->matchStage($get('stage'), $get('sub_stage'));
        $createdAt = $this->parseDate($get('created_at'));

        if (! empty($options['skip_closed']) && in_array($stage->outcome, ['lost', 'cancelled'], true)) {
            $this->skip('Closed lead (not interested, dropped or cancelled)');

            return;
        }
        if (! empty($options['skip_before']) && $createdAt && $stage->outcome !== 'won'
            && $createdAt->lt(Carbon::parse($options['skip_before'])->startOfDay())) {
            $this->skip('Received before '.Carbon::parse($options['skip_before'])->format('d M Y'));

            return;
        }

        $name = str((string) ($get('name') ?? ''))->squish()->limit(150, '')->value() ?: 'No name';
        $sourceId = $this->matchSource($get('source')) ?? ($options['default_source_id'] ?? null);
        $ownerId = $this->matchUser($get('owner'), 'owners', $options['default_owner_id'] ?? null);
        $secondaryId = $this->matchUser($get('secondary_owner'), 'owners', null);
        $projectIds = $this->matchProjects($get('project'));
        $notes = $get('notes');
        $email = $get('email');
        $email = $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? strtolower($email) : null;

        DB::transaction(function () use ($phone, $alt, $legacyId, $stage, $subStage, $createdAt, $name, $sourceId, $ownerId, $secondaryId, $projectIds, $notes, $email, $get, $by) {
            $existing = Lead::findByPhone($phone);
            if ($existing) {
                $existing->increment('re_enquiry_count');
                $existing->forceFill(['re_enquired_at' => now()])->save();
                if ($projectIds) {
                    $existing->projects()->syncWithoutDetaching($projectIds);
                }
                $existing->activities()->create([
                    'user_id' => $by->id,
                    'type' => 're_enquiry',
                    'body' => trim('From upload "'.$this->import->file_name.'". '.($notes ?? '')),
                    'meta' => ['source_id' => $sourceId, 'sub_source' => $get('sub_source'), 'import_id' => $this->import->id],
                ]);
                $this->counts['re_enquiry']++;

                return;
            }

            $when = $createdAt ?? now();
            $closed = $stage->isClosed();
            $followUp = $closed ? null : $this->parseDate($get('next_follow_up_at'));
            $isNew = $stage->is_default;

            $lead = new Lead;
            $lead->forceFill([
                'name' => $name,
                'phone' => $phone,
                'alt_phone' => $alt && $alt !== $phone ? $alt : null,
                'email' => $email,
                'source_id' => $sourceId,
                'sub_source' => str((string) $get('sub_source'))->limit(100, '')->value() ?: null,
                'campaign' => str((string) $get('campaign'))->limit(150, '')->value() ?: null,
                'stage_id' => $stage->id,
                'sub_stage_id' => $subStage?->id,
                'temperature' => $this->matchTemperature($get('temperature')),
                'owner_id' => $ownerId,
                'secondary_owner_id' => $secondaryId !== $ownerId ? $secondaryId : null,
                'assigned_by_id' => $ownerId ? $by->id : null,
                'assigned_at' => $ownerId ? $when : null,
                'picked_at' => $isNew ? null : $when,
                'next_follow_up_at' => $followUp,
                'next_follow_up_type' => $followUp ? ($stage->schedule_type ?? 'callback') : null,
                'is_archived' => in_array($stage->outcome, ['lost', 'cancelled'], true),
                'legacy_id' => $legacyId ? str($legacyId)->limit(64, '')->value() : null,
                'created_by_id' => $by->id,
                'created_at' => $when,
                'updated_at' => $when,
            ])->save();

            [$min, $max] = $this->budget($get('budget_min'), $get('budget_max'));
            $lead->enquiry()->create([
                'enquired_for' => str_contains(strtolower((string) $get('enquired_for')), 'rent') ? 'rent' : 'buy',
                'property_type_id' => $this->propertyTypes[self::norm((string) $get('property_type'))] ?? null,
                'bhk' => $this->bhk($get('bhk')),
                'budget_min' => $min,
                'budget_max' => $max,
                'locations' => str((string) $get('location'))->limit(500, '')->value() ?: null,
            ]);
            if ($projectIds) {
                $lead->projects()->sync($projectIds);
            }

            $created = new LeadActivity([
                'lead_id' => $lead->id,
                'user_id' => $by->id,
                'type' => 'created',
                'body' => 'Imported from "'.$this->import->file_name.'"',
                'meta' => ['import_id' => $this->import->id],
            ]);
            $created->created_at = $when;
            $created->save();
            if ($notes) {
                $lead->activities()->create(['user_id' => $by->id, 'type' => 'note', 'body' => str($notes)->limit(5000)->value()]);
            }

            if ($followUp && in_array($stage->schedule_type, ['meeting', 'site_visit'], true)) {
                $lead->appointments()->create([
                    'type' => $stage->schedule_type,
                    'project_id' => $projectIds[0] ?? null,
                    'scheduled_at' => $followUp,
                    'assigned_to_id' => $ownerId,
                ]);
            }

            if (! $ownerId && ! $closed) {
                app(\App\Services\AutoAssigner::class)->assign($lead, notify: false); // no alert per row of a big upload
            }

            $this->counts['created']++;
        });
    }

    // Matching

    /** @return array{0: LeadStage, 1: ?LeadStage} */
    private function matchStage(?string $stageText, ?string $subText): array
    {
        $stage = $stageText ? ($this->stages[self::norm($stageText)] ?? null) : null;
        $sub = null;

        if (! $stage && $stageText) {
            // "Callback - Busy" or just the sub-status name
            $parts = preg_split('/\s*[-–>:|]\s*/', $stageText, 2);
            if (count($parts) === 2 && isset($this->stages[self::norm($parts[0])])) {
                $stage = $this->stages[self::norm($parts[0])];
                $subText ??= $parts[1];
            } elseif ($child = $this->anySub[self::norm($stageText)] ?? null) {
                $sub = $child;
                $stage = $this->stages['#'.$child->parent_id];
            }
        }

        if (! $stage) {
            if ($stageText) {
                $this->unmatched('stages', $stageText);
            }

            return [$this->defaultStage, null];
        }

        if (! $sub && $subText) {
            $sub = $this->subStages[$stage->id][self::norm($subText)] ?? null;
        }

        return [$stage, $sub];
    }

    private function matchSource(?string $text): ?int
    {
        if (! $text) {
            return null;
        }
        $key = self::norm($text);
        $id = $this->sources[$key] ?? $this->sources[self::SOURCE_ALIASES[$key] ?? ''] ?? null;
        if (! $id) {
            $this->unmatched('sources', $text);
        }

        return $id;
    }

    private function matchUser(?string $text, string $bucket, $default): ?int
    {
        if (! $text) {
            return $default ? (int) $default : null;
        }
        $key = filter_var($text, FILTER_VALIDATE_EMAIL) ? strtolower($text) : self::norm($text);
        $id = $this->users[$key] ?? $this->users['p'.Lead::normalizePhone($text)] ?? null;
        if (! $id) {
            $this->unmatched($bucket, $text);

            return $default ? (int) $default : null;
        }

        return $id;
    }

    private function matchProjects(?string $text): array
    {
        if (! $text) {
            return [];
        }
        $ids = [];
        foreach (preg_split('/\s*[,;|]\s*/', $text) as $name) {
            if ($name === '') {
                continue;
            }
            if ($id = $this->projects[self::norm($name)] ?? null) {
                $ids[] = $id;
            } else {
                $this->unmatched('projects', $name);
            }
        }

        return array_values(array_unique($ids));
    }

    private function matchTemperature(?string $text): ?string
    {
        $text = strtolower((string) $text);
        foreach (['hot', 'warm', 'cold'] as $t) {
            if (str_contains($text, $t)) {
                return $t;
            }
        }

        return null;
    }

    // Parsing

    public function parseDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->shiftTimezone(config('app.timezone'));
        }
        if (is_numeric($value) && $value > 20000 && $value < 80000) {
            // Excel date serial number
            return Carbon::create(1899, 12, 30, 0, 0, 0, 'UTC')->addSeconds((int) round($value * 86400))
                ->shiftTimezone(config('app.timezone'));
        }
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        $formats = ['d-m-Y H:i:s', 'd-m-Y H:i', 'd-m-Y h:i A', 'd-m-Y h:i:s A', 'd-m-Y', 'd/m/Y H:i:s', 'd/m/Y H:i',
            'd/m/Y h:i A', 'd/m/Y h:i:s A', 'd/m/Y', 'd.m.Y', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d', 'd M Y', 'd M Y h:i A',
            'd M Y, h:i A', 'd-M-Y', 'd-M-Y h:i A', 'd/m/y', 'd-m-y'];
        foreach ($formats as $format) {
            $hasTime = str_contains($format, 'H') || str_contains($format, 'h');
            try {
                $date = Carbon::createFromFormat(($hasTime ? '' : '!').$format, $value);
            } catch (\Throwable) {
                continue;
            }
            if ($date && $date->format($format) === $value) {
                return $date;
            }
            if ($date && strcasecmp($date->format($format), $value) === 0) {
                return $date;
            }
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /** "50 L", "1.2 Cr", "75,00,000", or a range like "50L - 1Cr" in the budget column. */
    public static function money(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }
        $v = strtolower(str_replace([',', '₹', 'rs.', 'rs', 'inr'], '', $value));
        if (! preg_match('/(\d+(?:\.\d+)?)\s*(crores?|cr|lakhs?|lacs?|lac|l|k|thousand)?/', $v, $m)) {
            return null;
        }
        $n = (float) $m[1];
        $unit = $m[2] ?? '';
        $n *= match (true) {
            str_starts_with($unit, 'c') => 10_000_000,
            str_starts_with($unit, 'l') => 100_000,
            $unit === 'k' || $unit === 'thousand' => 1_000,
            default => 1,
        };

        return $n > 0 && $n < 1e13 ? (int) round($n) : null;
    }

    private function budget(?string $min, ?string $max): array
    {
        if (! $min && $max && preg_match('/^(.+?)\s*(?:-|–|to)\s*(.+)$/i', $max, $m)) {
            return [self::money($m[1]), self::money($m[2])];
        }

        return [self::money($min), self::money($max)];
    }

    private function bhk(?string $value): ?float
    {
        if ($value && preg_match('/(\d+(?:\.\d)?)/', $value, $m) && (float) $m[1] <= 20) {
            return (float) $m[1];
        }

        return null;
    }

    // Bookkeeping

    private function problem(int $rowNumber, array $row, string $reason): void
    {
        $this->counts['error']++;
        if (count($this->problems) < self::MAX_PROBLEMS) {
            $cell = function (string $field) use ($row): string {
                $col = $this->import->mapping[$field] ?? null;
                $value = ($col === null || $col === '') ? null : ($row[(int) $col] ?? null);

                return $value instanceof \DateTimeInterface ? $value->format('d-m-Y') : (string) $value;
            };
            $this->problems[] = [$rowNumber, $cell('name'), $cell('phone'), $reason];
        }
    }

    private function skip(string $reason): void
    {
        $this->counts['skipped']++;
        $this->summary['skipped'][$reason] = ($this->summary['skipped'][$reason] ?? 0) + 1;
    }

    private function unmatched(string $bucket, string $name): void
    {
        $name = str($name)->squish()->limit(80)->value();
        if (isset($this->summary[$bucket][$name]) || count($this->summary[$bucket] ?? []) < 300) {
            $this->summary[$bucket][$name] = ($this->summary[$bucket][$name] ?? 0) + 1;
        }
    }

    public static function norm(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower($value));
    }

    private function loadLookups(): void
    {
        foreach (LeadStage::orderBy('sort_order')->get() as $stage) {
            if ($stage->parent_id === null) {
                $this->stages[self::norm($stage->name)] = $stage;
                $this->stages[self::norm($stage->slug)] = $stage;
                $this->stages['#'.$stage->id] = $stage;
                if ($stage->is_default) {
                    $this->defaultStage = $stage;
                }
            } else {
                $this->subStages[$stage->parent_id][self::norm($stage->name)] = $stage;
                $this->anySub[self::norm($stage->name)] ??= $stage;
            }
        }
        $this->defaultStage ??= LeadStage::whereNull('parent_id')->orderBy('sort_order')->firstOrFail();

        $importer = $this->import->user;
        $users = User::query();
        if ($importer && ! $importer->isAdmin()) {
            // Managers can only hand leads to their own team.
            $users->whereIn('id', $importer->teamUserIds());
        }
        foreach ($users->get(['id', 'name', 'email', 'official_email', 'official_contact', 'personal_contact']) as $user) {
            $this->users[self::norm($user->name)] ??= $user->id;
            $this->users[strtolower((string) $user->email)] = $user->id;
            if ($user->official_email) {
                $this->users[strtolower($user->official_email)] ??= $user->id;
            }
            if ($user->phone) {
                $this->users['p'.Lead::normalizePhone($user->phone)] = $user->id;
            }
        }

        foreach (LeadSource::get(['id', 'name', 'slug']) as $source) {
            $this->sources[self::norm($source->name)] = $source->id;
            $this->sources[self::norm($source->slug)] ??= $source->id;
        }
        foreach (Project::active()->get(['id', 'name']) as $project) {
            $this->projects[self::norm($project->name)] = $project->id;
        }
        foreach (PropertyType::get(['id', 'name']) as $type) {
            $this->propertyTypes[self::norm($type->name)] ??= $type->id;
        }
    }
}
