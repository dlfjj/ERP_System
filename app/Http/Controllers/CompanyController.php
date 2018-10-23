<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

use Validator;
use Redirect;

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
        //
    }
}
