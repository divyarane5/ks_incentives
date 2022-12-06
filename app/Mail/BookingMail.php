<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingMail extends Mailable
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
      //  print_r($this->data); echo $this->data['booking']->u_email;  exit;
        return $this->from($this->data['booking']->u_email, $this->data['booking']->u_name)->subject('Congratulations ! Homebazaar.com needs confirmation over booking of Client Name: '.$this->data['booking']->client_name.' in Project : '.$this->data['booking']->project_name.'')->with($this->data)->view('booking.show');
    }
}
