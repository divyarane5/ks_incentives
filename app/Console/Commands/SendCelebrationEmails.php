<?php

namespace App\Console\Commands;

use App\Mail\CelebrationMail;
use App\Models\CelebrationPost;
use App\Services\CelebrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * Emails birthday and work-anniversary wishes to each employee on the day (runs every morning).
 * Uses HR's message from the post when one was made, else the standard wish. Each wish goes once.
 */
class SendCelebrationEmails extends Command
{
    protected $signature = 'celebrations:email {--dry-run : Only list who would get an email}';

    protected $description = 'Email today\'s birthday and work-anniversary wishes to the employees';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $items = CelebrationService::upcoming(0)->filter(fn ($c) => $c['days'] === 0);
        if ($items->isEmpty()) {
            $this->info('No birthdays or work anniversaries today.');

            return self::SUCCESS;
        }

        foreach ($items as $it) {
            $u = $it['user'];
            $to = $u->official_email ?: ($u->personal_email ?: $u->email);
            $label = $u->name.' ('.$it['type'].')';
            if (! $to) {
                $this->warn("No email for {$label}, skipped.");
                continue;
            }
            $key = 'celebration-mail:'.$u->id.':'.$it['type'].':'.$today->toDateString();
            if (Cache::has($key)) {
                $this->line("Already sent: {$label}");
                continue;
            }
            $post = Schema::hasTable('celebration_posts')
                ? CelebrationPost::where('user_id', $u->id)->where('type', $it['type'])->whereDate('post_date', $today)->latest()->first()
                : null;
            $message = $post?->message ?: CelebrationService::defaultMessage($it['type'], $u, $it['years']);
            $company = (string) ($u->entity ?: ($u->businessUnit?->name ?? config('app.name')));

            if ($this->option('dry-run')) {
                $this->line("Would email {$to}: {$label}");
                continue;
            }
            try {
                Mail::to($to)->send(new CelebrationMail($u, $it['type'], $it['years'], $message, $company));
                Cache::put($key, true, now()->addDays(3));
                $this->info("Sent to {$to}: {$label}");
            } catch (\Throwable $e) {
                $this->error("Failed for {$label}: ".$e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
