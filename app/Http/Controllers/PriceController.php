<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use App\Models\ProductCustomerSpecific;
use App\Models\ProductPriceOverride;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ValueList;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\ProductPrice;
use Validator;


class PriceController extends Controller
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
        return $request;
        $rules = array(
            'select_groups' => 'required|integer|digits_between:1,6',
            'surcharge_20' => 'numeric|required|digits_between:1,5 0',
            'surcharge_40' => 'numeric|required|digits_between:1,50',
        );
        $validation = Validator::make($request->all(), $rules);

        if($validation->fails()){
            return redirect('/products/prices/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $group_price = ProductPrice::where('product_id', $product->id)
                ->where('customer_group_id', $request->get('select_groups'))
                ->first();
            if (!$group_price) {
                $group_price = new ProductPrice();
            }
            $group_price->product_id = $product->id;
            $group_price->customer_group_id = $request->get('select_groups');
            $group_price->surcharge_20 = $request->get('surcharge_20', 1);
            $group_price->surcharge_40 = $request->get('surcharge_40', 1);
            $group_price->company_id = return_company_id();
            $group_price->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$product_customer_id=null)
    {


        $product = Product::findOrFail($id);

        if($product_customer_id != null){
            $product_customer = ProductCustomer::findOrFail($product_customer_id);
        } else {
            $product_customer = null;
        }

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_customers = Customer::where('company_id',return_company_id())
            ->where('status','=','ACTIVE')->orderBy('customer_name', 'asc')->pluck('customer_name','id');

        $select_groups   = CustomerGroup::where('company_id',return_company_id())
            ->pluck('group','id')
        ;
        $group_prices = ProductPrice::where('product_id',$product->id)->where('company_id',return_company_id())->orderBy('customer_group_id','DESC')->get();

        return view('products.prices.show',compact('product','product_customer','select_groups','select_customers','select_currency_codes','group_prices'));
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
//        updating the base price
        $product = Product::findOrFail($id);
        $action = $request->action;

//        $rules = array(
//            'base_price_20' => 'required|numeric',
//                'base_price_20' => 'numeric|required|regex:/^\d*(\.\d{1,2})?$/',
//            'base_price_40' => 'required|numeric',
//        );
//        return gettype($request->base_price_20);
//        return $request;

        if($action == 'group_add'){
            $rules = array(
                'select_groups' => 'required|integer|digits_between:1,6',
                'surcharge_20' => 'numeric|required',
                'surcharge_40' => 'numeric|required',
            );
        } else if($action == 'update_base_price'){
            $rules = array(
                'base_price_20' => 'required|numeric',
                'base_price_40' => 'required|numeric',
            );
        } else if($action == 'customer_specific_add'){
            $rules = array(
                'customer_id' => 'numeric|required|digits_between:1,50',
            );
        } else {
            $rules = array(
                'customer_id' => 'required|integer|digits_between:1,6',
                'base_price_20' => 'numeric|required|digits_between:1,50',
                'base_price_40' => 'numeric|required|digits_between:1,50',
            );
        }


        $validation = Validator::make($request->all(), $rules);

//        return $rules;

        if($validation->fails()){
            return redirect('/products/prices/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = $request->all();

            if($action == 'group_add'){
                $group_price = ProductPrice::where('product_id',$product->id)
                    ->where('customer_group_id', $request->get('select_groups'))
                    ->first();
                if(!$group_price){
                    $group_price = new ProductPrice();
                }
                $group_price->product_id = $product->id;
                $group_price->customer_group_id = $request->get('select_groups');
                $group_price->surcharge_20 = $request->get('surcharge_20',1);
                $group_price->surcharge_40 = $request->get('surcharge_40',1);
                $group_price->company_id = return_company_id();
                $group_price->save();
            } else if($action == 'update_base_price') {
                $history = PriceHistory::where('product_id',$product->id)
                    ->where('created',date("Y-m-d"))
                    ->first();
                if(!$history){
                    $history = new PriceHistory();
                }
                $history->base_price_20 	= $product->base_price_20;
                $history->base_price_40 	= $product->base_price_40;
                $history->created 			= date("Y-m-d");
                $history->product_id 		= $product->id;
                $history->save();

                $product->base_price_20 = $request->get('base_price_20');
                $product->base_price_40 = $request->get('base_price_40');
                $product->landed_20 = $request->get('landed_20');
                $product->landed_40 = $request->get('landed_40');
                $product->sales_base_20 = $request->get('sales_base_20');
                $product->sales_base_40 = $request->get('sales_base_40');
                $product->save();

            } else if($action == 'customer_specific_add'){
                $spec = new ProductCustomerSpecific();
                $spec->product_id = $product->id;
                $spec->customer_id = $request->get('customer_id');
                $spec->save();
            } else {
                $customer_price = ProductPriceOverride::where('product_id',$product->id)
                    ->where('customer_id',$request->get('customer_id'))
                    ->first();
                if(!$customer_price){
                    $customer_price = new ProductPriceOverride();
                }
                $customer_price->product_id = $product->id;
                $customer_price->base_price_20 = $request->get('base_price_20',1);
                $customer_price->base_price_40 = $request->get('base_price_40',1);
                $customer_price->customer_id = $request->get('customer_id');
                $customer_price->company_id = return_company_id();
                $customer_price->save();
            }




            return redirect('/products/prices/'.$id)
                ->with('flash_success','Update success');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $action = $request->action;
        $product_id = $request->get_current_product_id;
//        price group delete
        if($action == 'delete_price_group') {
            $group_price = ProductPrice::findOrFail($id);
            $product_id = $group_price->product_id;
            $group_price->delete();
        }else if($action == 'delete_product_customer_specific_record') {
//        product customer specific delete
            $spec = ProductCustomerSpecific::findOrFail($id);
            $product = $spec->product;
            if ($product->company_id != return_company_id()) {
                return redirect('/products/prices/'.$product_id)
                    ->with('flash_error', 'Access violation');
            } else {
                $spec->delete();
            }
        }

        return redirect('/products/prices/'.$product_id)
            ->with('flash_success','Operation success');
    }
}
