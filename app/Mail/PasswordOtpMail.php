<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** The 6-digit "forgot password" code. Sent straight away (not queued). */
class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $name, public string $code, public int $minutes)
    {
    }

    public function build()
    {
        return $this->subject("{$this->code} is your RealtySuite password reset code")
            ->view('emails.password-otp');
    }
}
