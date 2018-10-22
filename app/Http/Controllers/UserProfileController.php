<?php

namespace App\Http\Controllers;

use App\Photo;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Redirect;


class UserProfileController extends Controller
{
    public function __construct(){
         $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);

        return view('user_profile.show',compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
//        $id = Auth::user()->id;
//        return $request;
        $input = $request->all();
        if ($request->action === 'password_change'){
            $rules = array(
                'username' => 'Required|alpha_num|Between:1,50',
                'password' => 'Min:10|Max:200',
                'password_conf' => 'Required_with:password|Same:password',
            );

            $validation = Validator::make($input, $rules);

            if($validation->fails()){
                return Redirect::to('userprofiles')
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            } else {
                $user = User::findOrFail($id);

                unset($input['password']);

                $user->fill($input);

                if($request->password && $request->password_conf){
                    if($request->password != ""){
                        $user->password = Hash::make($request->password);
                    }
                }
                $user->save();
                return Redirect::to('userprofiles')
                    ->with('flash_success','Operation success');
            }
        }elseif($request->action === 'change_avatar'){

            $rules = array(
                'picture'=>'required|mimes:jpeg,bmp,png,jpg|max:5000',
            );
            $validation = Validator::make($input, $rules);

            if($validation->fails()){
                return redirect()->back()
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            } else {
                $user = User::findOrFail($id);

                if ($file = $request->file('picture')) {
                    $picture = $file;
                    $public_folder = config('app.public_folder') . "users/";
                    $name = md5($id).time().$file->getClientOriginalName();
//                    $picture_extension = $picture->getClientOriginalExtension();
//                    $picture->move($public_folder, md5($id) . "." . $picture_extension);
                    $photo = Photo::create(['file'=>$name]);
                    $input['picture'] = $photo->id;
                    $user->picture = $name;
                }
                unset($input['action']);
                $user->update($input);

//                $user->save();

                return Redirect::to('userprofiles')
                    ->with('flash_success', 'Operation success');
            }
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
        //
    }
}
