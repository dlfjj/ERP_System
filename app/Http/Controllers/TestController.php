<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\TestEmail;

class TestController extends Controller
{
    public function testmail()
    {
        // Send an email to codebriefly@yopmail.com
        Mail::to('dlfjj123@gmail.com')->send(new TestEmail);
        return "You did it";
    }
}
