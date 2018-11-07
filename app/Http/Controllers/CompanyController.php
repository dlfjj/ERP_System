<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

use Validator;
use Redirect;
use Auth;
use App\Models\CustomerGroup;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('auth');
        has_role('admin',1);
    }

    public function index()
    {
        $companies = Company::all();
        return view('companies.index',compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = New Company();
        $company->created_by = Auth::user()->id;
        $company->updated_by = Auth::user()->id;
        $company->currency_code = "USD";
        $company->save();

        $id = $company->id;

        setupCompany($id);


        $new = new CustomerGroup();
        $new->company_id = $id;
        $new->group      = "Price Group 1";
        $new->surcharge_20 = 0.95;
        $new->surcharge_40 = 0.95;
        $new->save();

        $new = new CustomerGroup();
        $new->company_id = $id;
        $new->group      = "Price Group 2";
        $new->surcharge_20 = 0.95;
        $new->surcharge_40 = 0.95;
        $new->save();

        $new = new CustomerGroup();
        $new->company_id = $id;
        $new->group      = "Price Group 3";
        $new->surcharge_20 = 0.95;
        $new->surcharge_40 = 0.95;
        $new->save();

        if (request()->ajax()){
            $data['ret_type'] = 'success';
            $data['ret_msg'] = "Success";
            $data['ret_url'] = "/users/$id";
            return json_encode($data);
        } else {
            return redirect('companies/'.$id)
                ->with('flash_success','Operation success');
        }
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
        $company = Company::findOrFail($id);

        return view('companies.show',compact('company'));
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
        $rules = array(
            'name' => 'Required|Between:1,50',
            'currency_code' => 'Required|Between:1,50',
            'letter' => 'Required|Between:3,3',
            'customer_id' => 'Required|numeric|exists:customers,id'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('companies/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $company = Company::findOrFail($id);
            $company->fill($input);

            if($request->has('can_login')){
                $company->can_login = 1;
            } else {
                $company->can_login = 0;
            }

            if($file = $request->file('company_logo')){
                $picture = $file;
                $public_folder = config('app.public_folder') . "global/companies/";
                $picture_extension = $picture->getClientOriginalExtension();
                $picture->move($public_folder, "{$id}.{$picture_extension}");
                $company->company_logo = "{$id}.{$picture_extension}";
            }

            $company->save();

            return Redirect::to('companies/'.$id)
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
        if($id == Auth::user()->company_id){
            return Redirect::to('users/'.$id)->with('flash_error','I will not let you destroy yourself');
        }
        if($id == 1){
            return Redirect::to('users/'.$id)->with('flash_error','He can neither be created nor destroyed');
        }
        $company = Company::find($id);
        $company->delete();

        $data['ret_type'] = 'success';
        $data['ret_msg'] = "Success";
        $data['ret_url'] = "/users";
        if (request()->ajax()){
            return json_encode($data);
        } else {
            return Redirect::to('companies/')->with('flash_success','Operation success');
        }
    }
}
