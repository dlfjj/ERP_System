<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ValueList;
use App\Models\WarehouseTransaction;
use Illuminate\Http\Request;
use Validator;

class StockController extends Controller
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
        //
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
        $product = Product::findOrFail($id);
        $transactions = WarehouseTransaction::where('product_id',$product->id)
            ->where('company_id',return_company_id())
            ->orderBy('id', 'desc')->limit(100)->get();

        $select_inventory_adjustment = ValueList::where('uid','=','inventory_adjustment')->orderBy('name', 'asc')->pluck('name','name');

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
        $select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        return view("products.stock.show ",compact('transactions','product','select_uom','select_package','select_currency_codes','select_inventory_adjustment'));
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
            'id' => 'required|integer|digits_between:1,6',
            'quantity' => 'required|integer|digits_between:1,6',
            'remark' => 'required'
        );


        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect()->back()
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $product = Product::findOrFail($id);
//            $input = Input::get();
            $product->fill($input);
            if($request->get('stock_min') == ""){
                $product->stock_min = null;
            }

            $quantity = $request->get('quantity');
            if(!is_numeric($quantity)){
                return redirect('/products/stock/'.$id)
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }

            if($quantity == 0){
                return redirect('/products/stock/'.$id)
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }

            $current_stock = $product->getStockOnHand();
            if($current_stock + $quantity < 0){
                return redirect()->back()
                    ->with('flash_error','Cannot allow negative Stock')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }

            warehouse_transaction($product->id,$request->get('quantity',0), $request->get('remark'));


            return redirect('/products/stock/'.$id)
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
