<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        $request->session()->put(['Homepage message'=>'Welcome to Homepage']);
//        $request->session()->forget('jacky');
//        $request->session()->flush();
//        return  $request->session()->all();


//        $request->session()->flash('message', 'login successful');
//        return session()->get('message');

//        return session()->all();
        return view('dashboard.index');
    }
}
