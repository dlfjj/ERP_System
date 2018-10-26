<?php

namespace App\Http\Controllers;

use App\Models\Taxcode;
use Illuminate\Http\Request;
use Validator;
use Redirect;

class TaxcodeController extends Controller
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
        $taxcodes = Taxcode::orderBy('sort_no')->get();

        return view('settings.taxcodes.index',compact('taxcodes'));

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
        $taxcode = Taxcode::findOrFail($id);

        return view('settings.taxcodes.show',compact('taxcode'));

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
            'id' => 'required|integer|digits_between:1,6',
            'name' => 'required|between:1,35',
            'percent' => 'required|numeric|between:0,100'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/settings/taxcodes/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $taxcode = Taxcode::findOrFail($id);
            $taxcode->fill($input);
            $taxcode->save();

            return Redirect::to('/settings/taxcodes')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($taxcode_id)
    {
        $taxcode = Taxcode::findOrFail($taxcode_id);

        if($taxcode->vendors->count() > 0){
            return Redirect::to('/settings/taxcodes/')
                ->with('flash_error','Cannot delete assigned Taxcode');
        }

        $taxcode->delete();
        return Redirect::to('/settings/taxcodes/')
            ->with('flash_success','Taxcode removed');
    }
}
