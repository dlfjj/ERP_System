<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 4:19 PM
 */

namespace App\Components\Report\Services;


use App\Models\Customer;
use App\Models\Order;
use App\User;
use Auth;

class topCustomer
{
    public function topCustomerByCompany(){
        $select_currency_codes 	= Order::groupBy('currency_code')->pluck('currency_code','currency_code');
        // $date_start = Input::get('date_start',date("Y-01-01"));
        // $date_end = Input::get('date_end',date("Y-m-d"));
        $date_start = '2018-02-01';
        $date_end = '2018-06-30';
        $results = array();
        $customer_id = array();
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');
        // $orders  	 = Order::where('company_id',return_company_id())
        // 		->where('order_date','>=',$date_start)
        // 		->where('order_date','<=',$date_end)
        // 		->get();
        // print_r($orders);die;
        $customers = Customer::select('customers.id','order_items.quantity','order_items.amount_gross','orders.currency_code','customers.customer_name')
            ->leftJoin('orders','orders.customer_id','=','customers.id')
            ->leftjoin('order_items','order_items.order_id','orders.id')
            ->whereBetween('order_date',[$date_start,$date_end])
            ->groupBy('customers.id')
            ->orderBy('order_items.amount_gross','DESC')
            ->offset(1)
            ->limit(50)
            ->get()->toArray();
        for($i=0;$i<count($customers);$i++){
            if(!in_array($customers[$i]['id'], $customer_id)){
                $customer_id[] = $customers[$i]['id'];
                $results[$customers[$i]['id']] = 0;
                $quantitys[$customers[$i]['id']] = 0;
            }
            $results[$customers[$i]['id']] = convert_currency($customers[$i]['currency_code'], $currency_code[0], $customers[$i]['amount_gross']);
            $quantitys[$customers[$i]['id']]+= $customers[$i]['quantity'];
            $customers[$i]['calculated_amount'] = $results[$customers[$i]['id']] ;
        }
        $res_array = [];
        foreach($customers as $key=>$value){
            $res_array[$key] = $value['calculated_amount'];
        }
        array_multisort($res_array,SORT_DESC,$customers);
        $top = 0;

        $top_customer_list = compact('date_start','date_end','product_code','currency_code','select_currency_codes','customers','results','top');
        return $top_customer_list;
    }

    public function export_top_customers($date_start,$date_end){
        $select_currency_codes 	= Order::groupBy('currency_code')->pluck('currency_code','currency_code');
        // $date_start = Input::get('date_start',date("Y-01-01"));
        // $date_end = Input::get('date_end',date("Y-m-d"));
//        $date_start = $date_start;
//        $date_end = $date_end;
        $results = array();
        $customer_id = array();
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');
        // $orders  	 = Order::where('company_id',return_company_id())
        // 		->where('order_date','>=',$date_start)
        // 		->where('order_date','<=',$date_end)
        // 		->get();
        // print_r($orders);die;
        $customers = Customer::select('customers.id','order_items.quantity','order_items.amount_gross','orders.currency_code','customers.customer_name')
            ->leftJoin('orders','orders.customer_id','=','customers.id')
            ->leftjoin('order_items','order_items.order_id','orders.id')
            ->whereBetween('order_date',[$date_start,$date_end])
            ->groupBy('customers.id')
            ->orderBy('order_items.amount_gross','DESC')
            ->offset(1)
            ->limit(50)
            ->get()->toArray();
        for($i=0;$i<count($customers);$i++){
            if(!in_array($customers[$i]['id'], $customer_id)){
                $customer_id[] = $customers[$i]['id'];
                $results[$customers[$i]['id']] = 0;
                $quantitys[$customers[$i]['id']] = 0;
            }
            $results[$customers[$i]['id']] = convert_currency($customers[$i]['currency_code'], $currency_code[0], $customers[$i]['amount_gross']);
            $quantitys[$customers[$i]['id']]+= $customers[$i]['quantity'];
            $customers[$i]['calculated_amount'] = $results[$customers[$i]['id']] ;
        }
        $res_array = [];
        $data = [];
        foreach($customers as $key=>$value){
            $res_array[$key] = $value['calculated_amount'];
            $data[$key] = [$value['id'],$value['customer_name'],$value['calculated_amount']];
        }
        $column_title = array('Customer ID', 'CUSTOMER NAME', 'AMOUNT GROSS (USD)');
        array_unshift($data, $column_title);
        array_multisort($res_array,SORT_DESC,$customers);
        // print_r($data);die;
        $name = time().'_'.'top_customers.csv';
        $file_path = storage_path('app/reports_downloads/'.$name);
        $file = fopen($file_path, 'w') or die("Can't create file");
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return [$file_path,$headers];

//        return response()->download($file_path, 'top_customers.csv', $headers);
    }

}