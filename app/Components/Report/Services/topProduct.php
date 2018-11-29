<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 4:40 PM
 */

namespace App\Components\Report\Services;


use App\Models\Order;
use App\Models\Product;
use App\User;
use Auth;

class topProduct
{
    public function topProductByCompany(){
        // $purchase_items = Product::leftJoin('purchase_items','products.id','=','purchase_items.product_id')->orderBy('gross_total','DESC')->offset(1)
        // ->limit(50)->get();
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');

        $select_currency_codes = Order::groupBy('currency_code')->pluck('currency_code','currency_code');
        // $date_start=Input::get('date_start',date("Y-01-01"));
        // $date_end=Input::get('date_end',date("Y-m-d"));
        $date_start = '2018-02-01';
        $date_end = '2018-06-30';
        $report_type = 'value';
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');
        $results = array();
        $product_id = array();
        $quantities = array();
        $products = Product::select('products.id','order_items.amount_gross','orders.currency_code','products.product_name','order_items.quantity')
            ->leftJoin('order_items','order_items.product_id','=','products.id')
            ->leftjoin('orders','order_items.order_id','orders.id')
            ->whereBetween('orders.order_date',[$date_start,$date_end])
            ->groupBy('products.id')
            ->orderBy('order_items.amount_gross','DESC')
            ->offset(1)
            ->limit(50)
            ->get()->toArray();
        for($i=0;$i<count($products);$i++){
            if(!in_array($products[$i]['id'], $product_id)){
                $product_id[] = $products[$i]['id'];
                $results[$products[$i]['id']] = 0;
                $quantities[$products[$i]['id']] = 0;
            }
            $results[$products[$i]['id']] = convert_currency($products[$i]['currency_code'], $currency_code[0], $products[$i]['amount_gross']);
            $quantities[$products[$i]['id']] += $products[$i]['quantity'];
            $products[$i]['calculated_amount'] = $results[$products[$i]['id']];
            $products[$i]['calculated_quantity'] = $quantities[$products[$i]['id']];
        }
        $res_array = [];
        $res_array2 = [];
        foreach($products as $key=>$value){
            $res_array[$key] = $value['calculated_amount'];
            // $res_array2[$key] = $value['calculated_quantity'];
        }
        array_multisort($res_array,SORT_DESC,$products);
        $quantity_array = [];
        foreach($products as $key=>$value){
            $quantity_array[$key] = $value['calculated_quantity'];
        }
        rsort($quantity_array );
        $top = 0;

        $top_product_list = compact('date_start','date_end','product_code','results','quantities','currency_code','select_currency_codes','report_type','currency_code','products','quantity_array','top');

        return $top_product_list;
    }
}