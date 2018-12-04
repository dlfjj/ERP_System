<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;


class PurchaseEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $signature;
    public $mail_data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->signature =Auth::user()->signature;
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = Auth::user()->first_name . " " . Auth::user()->last_name;
        $address = Auth::user()->email;
        return $this->view('emails.purchases')
            ->subject($this->mail_data['subject'])
            ->from($address, $name);
    }
}
