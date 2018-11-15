<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;

class TestEmail extends Mailable
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
    public function __construct()
    {
        $this->order = Order::all()->first();
        $this->signature = Auth::user()->signature;
        $this->mail_data = array(
//            'from_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//            'from_email' => Auth::user()->email,
//            'reply_to_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//            'reply_to_email' => Auth::user()->email,
//            'to_email' => $customer_contact->username,
//            'subject' => "Test Mail",
            'mail_body' => 'We would love to work with you next time',
//            'file_path' => $filepath,
//            'signature' => Auth::user()->signature,
//            'mail_to'  => $mail_to,
//            'order' => $order
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'dlfjj123@gmail.com';
        $name = Auth::user()->first_name . " " . Auth::user()->last_name;
        $path = storage_path('app/public/DSC02819.JPG');




        return $this->view('emails.test-mail')
            ->from($address, $name)
//            ->cc($address, $name)
//            ->bcc($address, $name)
//            ->replyTo($address, $name)
            ->subject('Test Mail')
            ->attach($path);

//            ->with([
//                'OrderPrice' => $mail_body,
//            ]);

//        return $this->view('emails.test-mail')->with(['name' => $this->name]);
    }
}
