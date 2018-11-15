<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;


class OrderEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $signature;
    public $mail_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
//        $this->order = Order::all()->first();
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

        return $this->view('emails.orders')
            ->subject($this->mail_data['subject'])
            ->from($address, $name)
            ;
    }
}
