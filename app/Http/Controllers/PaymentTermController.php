<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Validator;
use Auth;

class PaymentTermController extends Controller
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
        $payment_terms = PaymentTerm::orderBy('sort_no')->get();
        return view('settings.payment_terms.index',compact('payment_terms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $payment_term = new PaymentTerm();
        $payment_term->created_by = Auth::user()->id;
        $payment_term->updated_by = Auth::user()->id;
        $payment_term->save();

        $new_id = $payment_term->id;

        return redirect('/settings/payment_terms/'.$new_id)
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
        $payment_term = PaymentTerm::findOrFail($id);
        return view('settings.payment_terms.show',compact('payment_term'));
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
            'credit' => 'required|numeric|digits_between:1,100|min:0'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect()->back()
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $payment_term = PaymentTerm::findOrFail($id);
            $payment_term->fill($input);
            $payment_term->save();

            return redirect('/settings/payment_terms/')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($payment_term_id)
    {
        $payment_term = PaymentTerm::findOrFail($payment_term_id);

        if($payment_term->orders->count() > 0){
            return redirect('/settings/payment_terms/')
                ->with('flash_error','Cannot delete assigned PaymentTerm');
        }

        $payment_term->delete();
        return redirect('/settings/payment_terms/')
            ->with('flash_success','PaymentTerm removed');
    }
}
