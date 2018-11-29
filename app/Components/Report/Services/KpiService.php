<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 2:50 PM
 */

namespace App\Components\Report\Services;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ValueList;
use App\User;
use Auth;
use DB;


class KpiService
{

    public function getKpiByCompany(){
        $company_id 	= return_company_id();
        $time_start 	 = time();
        $date_start 	 = date("Y-01-01");
        $date_end 		 = date("Y-m-d");
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code')[0];
        $year_0 	= date("Y");
        $year_1 	=  date("Y",strtotime("-1 year"));
        $year_2 	=  date("Y",strtotime("-2 year"));

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');

        $customer_count_active 		= Customer::where('company_id', return_company_id())->where('status','Active')->count();
        $customer_count_inactive 	= Customer::where('company_id', return_company_id())->where('status','Inactive')->count();
        $product_count_active 		= Product::where('company_id', return_company_id())->where('status','Active')->count();
        $product_count_inactive 	= Product::where('company_id', return_company_id())->where('status','Inactive')->count();
        $orders_count_2 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2015')->count();
        $orders_count_1 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2016')->count();
        $orders_count_0 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2017')->count();

        $turnover_0 = $this->get_turnover_0($currency_code,$year_0);
        // print_r($turnover_0);die;
        // $turnover_0 = 0;
        // $turnover_1 = 0;
        $turnover_1 = $this->get_turnover_1($currency_code,$year_1);
        $turnover_2 = $this->get_turnover_2($currency_code,$year_2);

        $order_quantities_0 = OrderItem::
        rightjoin('orders','order_items.order_id','=','orders.id')
            ->where('orders.company_id',$company_id)->where('orders.status_id',7)->whereRaw("YEAR(orders.order_date) = {$year_0}")->sum('order_items.quantity');
        // $order_quantities_1 = 0;
        $order_quantities_1 = OrderItem::
        rightjoin('orders','order_items.order_id','=','orders.id')
            ->where('orders.company_id',$company_id)->where('orders.status_id',7)->whereRaw("YEAR(orders.order_date) = {$year_1}")->sum('order_items.quantity');
        // print_R($order_quantities_1);die;
        // $order_quantities_2 = 0;
        $order_quantities_2 = OrderItem::
        rightjoin('orders','order_items.order_id','=','orders.id')
            ->where('orders.company_id',$company_id)->where('orders.status_id',7)->whereRaw("YEAR(orders.order_date) = {$year_2}")->sum('order_items.quantity');
        // remove raw queries and use laravel
        // $unpaid_invoices_0 =0;
        $unpaid_invoices_0 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.company_id",return_company_id())
            ->orderBy('id','DESC')->whereRaw("YEAR(orders.order_date) = {$year_0}")
            ->sum('orders.open_amount');
        // $unpaid_invoices_1 = 0;
        $unpaid_invoices_1 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.company_id",return_company_id())
            ->orderBy('id','DESC')->whereRaw("YEAR(orders.order_date) = {$year_1}")
            ->sum('orders.open_amount');
        // $unpaid_invoices_2 = 0;
        $unpaid_invoices_2 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.company_id",return_company_id())
            ->orderBy('id','DESC')->whereRaw("YEAR(orders.order_date) = {$year_2}")
            ->sum('orders.open_amount');
        // $overdue_invoices_0 = 0;
        $overdue_invoices_0 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.estimated_finish_date',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
            ->orderBy("orders.estimated_finish_date","DESC")
            ->whereRaw("YEAR(orders.order_date) = {$year_0}")
            ->sum('orders.open_amount');
        // $overdue_invoices_1= 0;
        $overdue_invoices_1 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.estimated_finish_date',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
            ->orderBy("orders.estimated_finish_date","DESC")
            ->whereRaw("YEAR(orders.order_date) = {$year_1}")
            ->sum('orders.open_amount');
        $overdue_invoices_2 = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.estimated_finish_date',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
            ->orderBy("orders.estimated_finish_date","DESC")
            ->whereRaw("YEAR(orders.order_date) = {$year_2}")
            ->sum('orders.open_amount');

        $seconds_used = time() - $time_start;
        $company_currency_code = User::with('company')->where('users.id',Auth::user()->id)->get()->toArray();

        $kpi_data = compact('select_currency_codes','currency_code','date_start','date_end','seconds_used','po_payments_total','po_placed_total','expenses_total','expenses_total','invoices_total','invoices_written','invoices_shipped','invoice_payments_received','gross_profit','turnover','turnover_quantities','customer_count_active','customer_count_inactive','product_count_active','product_count_inactive','orders_count_0','orders_count_1','orders_count_2','order_quantities_0','order_quantities_1','order_quantities_2','company_currency_code','turnover_0','turnover_1','turnover_2','unpaid_invoices_0','unpaid_invoices_1','unpaid_invoices_2','overdue_invoices_0','overdue_invoices_1','overdue_invoices_2');

        return $kpi_data;
    }
    public function get_turnover_2($currency_code,$year_2){
        $company_id = Auth::user()->company_id;
        $status_id = 7;
        $date_code = null;
        $turnover_2_rows = Order::where("status_id",7)
            ->where('company_id',return_company_id())
            ->whereRaw("YEAR(order_date) = '{$year_2}'")
            ->get();
        // $turnover_2_rows = DB::select("CALL GetYearTurnover('$status_id','$company_id','$year_2')");
        $turnover_2_rows1 = json_decode(json_encode($turnover_2_rows), True);
        $turnover_2 = 0;
        $turnover_2 = convert_turnover_currency($turnover_2_rows1,$date_code,$currency_code);
        return $turnover_2;
    }

    public function get_turnover_1($currency_code,$year_1){
        $company_id = Auth::user()->company_id;
        $status_id = 7;
        $date_code = null;
        $turnover_1_rows = Order::where("status_id",7)
            ->where('company_id',return_company_id())
            ->whereRaw("YEAR(order_date) = '{$year_1}'")
            ->get();
        // $turnover_1_rows = DB::select("CALL GetYearTurnover('$status_id','$company_id','$year_1')");
        $turnover_1_rows1 = json_decode(json_encode($turnover_1_rows), True);
        $turnover_1 = 0;
        $turnover_1 = convert_turnover_currency($turnover_1_rows1,$date_code,$currency_code);
        return $turnover_1;
    }
    public function get_turnover_0($currency_code,$year_0){
        $company_id = Auth::user()->company_id;
        $status_id = 7;
        $date_code = null;
        $turnover_0_rows = Order::where("status_id",7)
            ->where('company_id',return_company_id())
            ->whereRaw("YEAR(order_date) = '{$year_0}'")
            ->get()->toArray();
        // print_r($turnover_0_rows);die;
        // $turnover_0_rows = DB::select("CALL GetYearTurnover('$status_id','$company_id','$year_0')");
        $turnover_0_rows1 = json_decode(json_encode($turnover_0_rows), True);
        $turnover_0 = 0;
        $turnover_0 = convert_turnover_currency($turnover_0_rows1,$date_code,$currency_code);
        return $turnover_0;
    }


    //    public function export_kpis()
//    {
////        return dd(session()->has('kpi'));
////        return session()->get('kpi');
////        $company_currency_code = session()->get('kpi')['company_currency_code'];
//
//        $kpi_data = session()->get('kpi');
//
////        return $kpi_data;
//        $result_data = [
//            'turnover'=>[$kpi_data['turnover_0'],$kpi_data['turnover_1'],$kpi_data['turnover_2']],
//            'quantites'=>[$kpi_data['order_quantities_0'],$kpi_data['order_quantities_1'],$kpi_data['order_quantities_2']],
//            'order_count'=>[$kpi_data['orders_count_0'],$kpi_data['orders_count_1'],$kpi_data['orders_count_2']],
//            'unpain_invoices'=>[$kpi_data['unpaid_invoices_0'],$kpi_data['unpaid_invoices_1'],$kpi_data['unpaid_invoices_2']],
//            'overdue_invoices'=>[$kpi_data['overdue_invoices_0'],$kpi_data['overdue_invoices_1'],$kpi_data['overdue_invoices_2']],
//            'products'=>[$kpi_data['product_count_active'],$kpi_data['product_count_inactive']],
//            'customers' =>[$kpi_data['customer_count_active'],$kpi_data['customer_count_active']]
//        ];
//
//        $name = time().'_'.'kpis.csv';
//        $file_path = storage_path('app/reports_downloads/'.$name);
//        $file = fopen($file_path, 'w') or die("Can't create file");
//
//        foreach ($result_data as $row) {
//            fputcsv($file, $row);
//        }
//        fclose($file);
//        $headers = array(
//            'Content-Type' => 'text/csv',
//        );
//        session()->flush();
//        return response()->download($file_path, 'kpis.csv', $headers);
//    }
//
}