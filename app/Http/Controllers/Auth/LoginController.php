<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except('logout')->except('login');
        $this->middleware('guest', ['except' => ['logout']]);

    }

    public function login(Request $request)
    {
        //dd($request->all());
        if ($this->attemptLogin($request)) {
            //return $this->sendLoginResponse($request);
            $user = User::findOrFail($this->guard()->user()->id);
            // print_r($user);die;
            $user->last_login = date("Y-m-d H:i:s");
            $user->save();
            setupCompany($user->company_id);

            if (session()->has('link')){
                $previous_url  = session()->get('link');
                session()->forget('link');
                return \redirect($previous_url)->with('message', 'Welcome Back '.Auth::user()->first_name);
            }
            else{
                return Redirect::to('home')->with('message', 'Login Successful');
            }
//            session()->flash('message', 'You did it too');
//            return Redirect::to('dashboard');
        }

        return redirect('login');
    }

    public function showLoginForm(){
//        store the previous url in the session
        session(['link'=> url()->previous()]);
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect(session('link'));
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    protected function credentials(Request $request)
    {
        return $request->only('username', 'company_id', 'password');
    }


}
