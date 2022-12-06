<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralMail extends Mailable
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
        // print_r($this->data); echo $this->data['client']->u_email;  exit;
        return $this->from($this->data['client']->u_email, $this->data['client']->u_name)->subject($this->data['client']->subject_name)->with($this->data)->view('client.show');
    }
}
