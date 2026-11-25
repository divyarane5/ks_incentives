<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Birthday or work-anniversary wishes, emailed to the employee on the day. */
class CelebrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $person, public string $type, public ?int $years, public string $message, public string $company)
    {
    }

    public function build()
    {
        $first = explode(' ', trim((string) ($this->person->first_name ?: $this->person->name)))[0];
        $subject = $this->type === 'birthday'
            ? "Happy birthday, {$first}!"
            : "Happy {$this->years}".\App\Services\CelebrationService::suffix($this->years)." work anniversary, {$first}!";

        return $this->subject($subject)->view('emails.celebration', ['first' => $first]);
    }
}
