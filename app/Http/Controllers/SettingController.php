<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Auth;

class SettingController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        has_role('company_admin',1);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_settings = Setting::all();
        $user = Auth::user();
        $settings = array();
        foreach ($all_settings as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        $required_settings = array(
            "customer_name",
            "company_bill_to",
            "company_deliver_to",
            "auto_part_numbers"
        );

        foreach($required_settings as $required){
            if(!isset($settings[$required])){
                $settings[$required] = "";
            }
        }

        return view('settings.index',compact('settings','user'));

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
    public function update(Request $request,$id)
    {
        $input = $request->all();
        foreach($input as $key=>$value){
            $setting = Setting::where('name','=',$key)->first();
            if($setting){
                $setting->value = $value;
                $setting->save();
            } else {
                $new = new Setting;
                $new->name  = $key;
                $new->value = $value;
                $new->save();
            }
        }
        return redirect('setting/')
            ->with('flash_success','Operation success');
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
