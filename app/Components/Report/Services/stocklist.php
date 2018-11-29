<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 5:51 PM
 */

namespace App\Components\Report\Services;


use App\Models\Order;
use App\Models\Product;
use App\User;
use Auth;

class stocklist
{
    public function getStocklist(){

        //        $date_start = date("2016-01-01");
        $select_currency_codes 	= Order::groupBy('currency_code')->pluck('currency_code','currency_code');
//        $date_start 			= Input::get('date_start',date("Y-01-01"));
//        $date_start 			= Input::get('date_start',date("2017-01-01"));
        //get current day

//        doesnt really matter what date it end and start
        $date_end 				= date("Y-m-d", time());
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code')[0];
        $products 	= Product::where('company_id',return_company_id())
            ->where('stock','>',0)
            ->get();

        return compact('date_start','date_end','product_code','products','currency_code','select_currency_codes');
    }
}