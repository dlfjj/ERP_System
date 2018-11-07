<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use Redirect;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
        has_role('users',1);
    }

    public $layout = 'layouts.default';

    public function index()
    {
        $users = User::all();
//        $this->layout->module_title = "";
//        $this->layout->module_sub_title = "";
//        $this->layout->content = View::make('users.index')
        return view('users.index',compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = New User;
        $user->username = uniqid();
        $user->email= uniqid() .'@'.'somedomain.com';
        $user->created_by = Auth::user()->id;
        $user->updated_by = Auth::user()->id;
        $user->save();

        $id = $user->id;

        if (request()->ajax()){
            $data['ret_type'] = 'success';
            $data['ret_msg'] = "Success";
            $data['ret_url'] = "/users/$id";
            return json_encode($data);
        } else {
            return redirect('usersList/'.$id)
                ->with('flash_success','Operation success');
        }
    }

    public function postLoginAs($id){
        if(!has_role('admin')){
            die('Access error 1234.1');
        }
        $user = User::find($id);
        Auth::login($user);
        return redirect('/dashboard')
            ->with('flash_success','Operation success');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy("name")->get();
        $role_user = User::find($id)->roles;
        $select_companies = Company::pluck('name','id');



        return view('users.show',compact('user','roles','role_user','select_companies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
//        return $request;
        $rules = array(
            'first_name' => 'Required|Between:1,50',
            'last_name' => 'Required|Between:1,50',
            'email' => 'Required|Email|Between:1,50',
            'username' => 'Required|Between:3,50',
            'signature' => 'Between:1,500',
            'purchase_limit_amount' => 'Numeric',
            'password' => 'Min:10|Max:200|nullable',
            'password_conf' => 'Required_with:password|Same:password',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect('usersList/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $user = User::findOrFail($id);
            unset($input['password']);
            unset($input['DataTables_Table_0_length']);

            $user->fill($input);

            if($request->can_login){
                $user->can_login = 1;
            } else {
                $user->can_login = 0;
            }
            if($request->has('password') && $request->has('password_conf')){
                if($request->password != ""){
                    $user->password = Hash::make($request->password);
                }
            }
            if($request->hasFile('picture')){
                $picture = $request->file('picture');
                $public_folder = config('app.public_folder') . "users/";
                $picture_extension = $picture->getClientOriginalExtension();
                $picture->move($public_folder, md5($id) .".". $picture_extension);
                $user->picture = md5($id) .".". $picture_extension;
            }
            if($user->id == 1){
                $user->can_login = 1;
            }
            $user->save();

            $roles = $request->roles;
            if(is_array($roles) && count($roles)>0){
                //
            } else {
                $roles = array();
            }
            if($user->id == 1){
                $roles[] = 1;
            }
            $user->roles()->sync($roles);

            return redirect('usersList/'.$id)
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id == Auth::user()->id){
            return Redirect::to('usersList/'.$id)->with('flash_error','I will not let you destroy yourself');
        }
        if($id == 1){
            return Redirect::to('usersList/'.$id)->with('flash_error','He can neither be created nor destroyed');
        }


        $user = User::find($id);

        if($user_avatar_path = file_exists(public_path() ."/users/".$user->picture && $user->picture != "")){
            unlink(public_path() ."/users/".$user->picture);
        }

        User::find($id)->roles()->detach();
        $user->delete();

        $data['ret_type'] = 'success';
        $data['ret_msg'] = "Success";
        $data['ret_url'] = "/users";
        if (request()->ajax()){
            return json_encode($data);
        } else {
            return Redirect::to('usersList/')->with('flash_success','Operation success');
        }
    }
}
