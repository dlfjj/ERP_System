<?php

namespace App\Http\Controllers;

use App\Models\ValueList;
use Illuminate\Http\Request;
use Auth;
use Validator;
use View;

class CurrencyCalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');

        $currency_from = "CNY";
        $currency_to = "HKD";
        $date = date("Y-m-d");
        $amount = 1;
        $result = convert_currency($currency_from,$currency_to,$amount,$date);

        $cost_currency = "CNY";
        $sale_currency = "HKD";
        $cost = 1;
        $sale = 2;
        $margin = $sale - $cost;
        $margin_percent = 0;
        $user_id = Auth::user()->id;

        $sale_converted = convert_currency($sale_currency,$cost_currency,$sale,$date);

        $margin = $sale_converted - $cost;
        if($sale_converted != 0){
            $margin_percent = round($margin / $cost * 100,3);
        } else {
            $margin_percent = 0;
        }

        return view('currency_calculator.index',compact('currency_from','currency_to','date',
            'amount','result','cost_currency','sale_currency','cost','sale','margin','margin_percent','select_currency_codes','user_id'));
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

        $select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');

        $rules = array(
            'currency_from' => 'required|min:3|max:3',
            'currency_to' => 'required|min:3|max:3',
            'date' => 'required',
            'amount' => 'required|numeric',
            'cost' => 'required|numeric',
            'sale' => 'required|numeric'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect("/currency_calculator")
                ->with('flash_error','Operation failed')
                ->witherrors($validation->messages())
                ->withinput();
        } else {
            if($request->has('flip')){
                $currency_to = $request->currency_from;
                $currency_from = $request->currency_to;
            } else {
                $currency_from = $request->currency_from;
                $currency_to = $request->currency_to;
            }
            $date = $request->date;
            $amount = $request->amount;
            $result = convert_currency($currency_from,$currency_to,$amount,$date);

            $cost = $request->cost;
            $sale = $request->sale;
            $cost_currency = $request->cost_currency;
            $sale_currency = $request->sale_currency;

            $sale_converted = convert_currency($sale_currency,$cost_currency,$sale,$date);
            $margin = $sale_converted - $cost;

            if($margin != 0){
                if($cost == 0){
                    $margin_percent = 0;
                } else {
                    $margin_percent = round($margin / $cost * 100,3);
                }
            } else {
                $margin_percent = 0;
            }

            $user_id = Auth::user()->id;


            return view('currency_calculator.index',compact('currency_from','currency_to','date',
                'amount','result','cost_currency','sale_currency','cost','sale','margin','margin_percent','select_currency_codes','user_id'));

//            return redirect('currency_calculator')->with('flash_success','Update success');
//            return view('currency_calculator.index',compact('currency_from','currency_to','date',
//                'amount','result','cost_currency','sale_currency','cost','sale','margin','margin_percent','select_currency_codes'));
//            $this->layout->module_title = "";
//            $this->layout->module_sub_title = "";
//            $this->layout->content = View::make('currency_calculator.index')
//                ->with('currency_from',$currency_from)
//                ->with('currency_to',$currency_to)
//                ->with('date',$date)
//                ->with('amount',$amount)
//                ->with('result',$result)
//                ->with('cost_currency',$cost_currency)
//                ->with('sale_currency',$sale_currency)
//                ->with('cost',$cost)
//                ->with('sale',$sale)
//                ->with('margin',$margin)
//                ->with('margin_percent',$margin_percent)
//                ->with('select_currency_codes',$select_currency_codes);
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
