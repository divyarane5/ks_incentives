<?php

namespace App\Mail;

use App\Models\IssuedLetter;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** An HR letter (appointment, confirmation …) sent to the employee with the PDF attached. */
class LetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public IssuedLetter $letter, public string $pdf, public string $fileName, public string $company, public ?User $sender = null)
    {
    }

    public function build()
    {
        $title = $this->letter->subject ?: ucfirst($this->letter->type).' letter';
        $mail = $this->subject($title.' – '.$this->company)
            ->view('emails.letter', ['title' => $title, 'first' => explode(' ', trim((string) ($this->letter->user->first_name ?: $this->letter->user->name)))[0]])
            ->attachData($this->pdf, $this->fileName, ['mime' => 'application/pdf']);
        if ($this->sender?->email) {
            $mail->replyTo($this->sender->email, $this->sender->name);
        }

        return $mail;
    }
}
