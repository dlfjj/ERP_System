<?php

namespace App\Http\Controllers;

use App\Models\ShippingTerm;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ShippingTermController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        has_role('admin',1);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping_terms = ShippingTerm::orderBy('sort_no')->get();
        return view('settings.shipping_terms.index',compact('shipping_terms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shipping_term = new ShippingTerm();
        $shipping_term->created_by = Auth::user()->id;
        $shipping_term->updated_by = Auth::user()->id;
        $shipping_term->save();

        $new_id = $shipping_term->id;

        return redirect('/settings/shipping_terms/'.$new_id)
            ->with('flash_success','Taxcode created');
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
        $shipping_term = ShippingTerm::findOrFail($id);
        return view('settings.shipping_terms.show',compact('shipping_term'));

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
            'name' => 'required|between:1,100',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect('/settings/shipping_terms/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $shipping_term = ShippingTerm::findOrFail($id);
            $shipping_term->fill($input);
            $shipping_term->save();

            return redirect('/settings/shipping_terms/')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shipping_term_id)
    {
        $shipping_term = ShippingTerm::findOrFail($shipping_term_id);

        if($shipping_term->orders->count() > 0){
            return redirect('/settings/shipping_terms/')
                ->with('flash_error','Cannot delete assigned ShippingTerm');
        }

        $shipping_term->delete();
        return redirect('/settings/shipping_terms/')
            ->with('flash_success','ShippingTerm removed');
    }

}
