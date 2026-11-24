<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/** Checks the email settings in .env by sending one test email: php artisan mail:test you@example.com */
class MailTest extends Command
{
    protected $signature = 'mail:test {to : Email address to send the test to}';

    protected $description = 'Send a test email to check the MAIL_ settings';

    public function handle(): int
    {
        $this->line('Mailer: '.config('mail.default').'  Host: '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port')
            .'  From: '.config('mail.from.address'));

        try {
            Mail::raw('This is a test email from RealtySuite. Email is working.', function ($m) {
                $m->to($this->argument('to'))->subject('RealtySuite test email');
            });
        } catch (\Throwable $e) {
            $this->error('Failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Sent. Check the inbox (and spam) of '.$this->argument('to'));

        return self::SUCCESS;
    }
}
