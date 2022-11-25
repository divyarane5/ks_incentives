<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class JoiningFormMail extends Mailable
{
    use Queueable, SerializesModels;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Onboarding Schedule with Joining details - '.$this->data['candidate']->entity.'. '.date('d M Y', strtotime($this->data['candidate']->joining_date)))->with($this->data)->view('email-templates.joining_form');
        $files = Storage::disk('local')->allFiles('joining_attachments');
        if (!empty($files)) {
            foreach ($files as $file) {
                $this->attach(storage_path('app/'.$file));
            }
        }
        return $this;
    }

    /**
     * Get the attachments for the message.
     *
     * @return \Illuminate\Mail\Mailables\Attachment[]
     */
    public function attachments()
    {
        return [
            Attachment::fromPath(storage_path('app/joining_attachments/Name-Documents (Specimen for document attachment) (1) (1) (1) (1) (1) (1) (2) (2) (1) (2).rar')),
            Attachment::fromPath(storage_path('app/joining_attachments/Specimen of joining formalities (1) (1) (1) (2) (1) (2) (1) (2).pdf')),
        ];
    }
}
