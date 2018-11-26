<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ValueList;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Auth;

class ReportController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        has_role('reports',1);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.index');
//        $this->layout->content = View::make('reports.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    //Executive dashboard rendering
    public function getDashboard()
    {
        $company_id = Auth::user()->company_id;
        $time_start = time();
        $date_start = Input::get('date_start', date("Y-m-01"));
        $date_end = Input::get('date_end', date("Y-m-t"));
        $currency_code = Input::get('currency_code', 'USD');
        // $_POST['date_start'] = $date_start;
        // print_R($date_start);die;
        // $_POST['date_end']   = $date_end;
        // $_POST['currency_code'] = $currency_code;
        $companies = Company::pluck('name', 'id');
        if (getDateDifferenceInDays($date_start, $date_end) > 365) {
            return redirect('reports/dashboard')
                ->with('flash_error', 'Timeframe max is currently 365 days');
        }
        $select_currency_codes = ValueList::where('uid', '=', 'currency_codes')->orderBy('name', 'asc')->pluck('name', 'name');
        $po_placed_total           = 0; //$this->_get_pos_placed($date_start,$date_end,$currency_code);
        $po_payments_total         = 0; //$this->_get_po_payments($date_start,$date_end,$currency_code);
        $invoices_written          = 0; //$this->_get_invoices_written($date_start,$date_end,$currency_code);
        $invoices_shipped          = 0; //$this->_get_invoices_shipped($date_start,$date_end,$currency_code);
        $invoice_payments_received = 0; //$this->_get_invoice_payments_received($date_start,$date_end,$currency_code);
        $orders_placed             = 0; //$this->_get_orders_placed($date_start,$date_end,$currency_code);
        $orders_to_be_shipped      = 0; //$this->_get_orders_to_be_shipped($date_start,$date_end,$currency_code);
        $quotations_placed         = 0; //$this->_get_quotations_placed($date_start,$date_end,$currency_code);
        $endorsed_to_wh            = 0; //$this->_get_endorsed_to_wh($date_start,$date_end,$currency_code);
        $expenses_total 		   = 0; //$this->_get_expenses_total($date_start,$date_end,$currency_code);
        $gross_profit 			   = 0;
        $invoices_total 		   = 0;
        $invoices_written 			= 0;
        $invoices_shipped 			= 0;
        $invoice_payments_received 	= 0;

        $seconds_used = time() - $time_start;

        return view('reports.dashboard.index', compact('select_currency_codes', 'currency_code', 'date_start', 'date_end', 'seconds_used', 'po_payments_total', 'po_placed_total', 'expenses_total', 'invoices_total', 'invoices_written', 'invoices_shipped', 'invoice_payments_received', 'gross_profit', 'turnover', 'turnover_quantities', 'orders_placed', 'companies'));
    }

    //KPI rendering
    public function getKpi(){
        $company_id 	= return_company_id();
        $time_start 	 = time();
        $date_start 	 = Input::get('date_start',date("Y-m-01"));
        $date_end 		 = Input::get('date_end',date("Y-m-t"));
        $currency_code 	 = Input::get('currency_code','USD');
        $year_0 	= date("Y");
        $year_1 	=  date("Y",strtotime("-1 year"));
        // print_r($year_1);die;
        $year_2 	=  date("Y",strtotime("-2 year"));
        $_POST['date_start'] = $date_start;
        $_POST['date_end']   = $date_end;
        $_POST['currency_code'] = $currency_code;

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');

        $customer_count_active 		= Customer::where('company_id', return_company_id())->where('status','Active')->count();
        $customer_count_inactive 	= Customer::where('company_id', return_company_id())->where('status','Inactive')->count();
        // $product_count_active   = 0;
        $product_count_active 		= Product::where('company_id', return_company_id())->where('status','Active')->count();
        // $product_count_inactive   = 0;
        $product_count_inactive 	= Product::where('company_id', return_company_id())->where('status','Inactive')->count();
        // print_r($product_count_active);die;
        // $orders_count_2   = 0;
        $orders_count_2 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2015')->count();
        // $orders_count_1   = 0;
        $orders_count_1 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2016')->count();
        // $orders_count_0   = 0;
        $orders_count_0 			= Order::where('company_id',return_company_id())->whereRaw('YEAR(order_date) = 2017')->count();
        // $turnover 					= $this->_get_turnover($date_start,$date_end,$currency_code,$company_id);
        // $turnover_quantities 		= $this->_get_turnover_quantities($date_start,$date_end,$currency_code,$company_id);
        // $turnover_1 = 0;
        $turnover_0 = $this->get_turnover_0($currency_code,$year_0);
        // print_r($turnover_0);die;
        // $turnover_0 = 0;
        // $turnover_1 = 0;
        $turnover_1 = $this->get_turnover_1($currency_code,$year_1);
        $turnover_2 = $this->get_turnover_2($currency_code,$year_2);
        // $turnover_2 = 0;
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

        return view('reports.kpis.index',compact('select_currency_codes','currency_code','date_start','date_end','seconds_used','po_payments_total','po_placed_total','expenses_total','expenses_total','invoices_total','invoices_written','invoices_shipped','invoice_payments_received','gross_profit','turnover','turnover_quantities','customer_count_active','customer_count_inactive','product_count_active','product_count_inactive','orders_count_0','orders_count_1','orders_count_2','order_quantities_0','order_quantities_1','order_quantities_2','company_currency_code','turnover_0','turnover_1','turnover_2','unpaid_invoices_0','unpaid_invoices_1','unpaid_invoices_2','overdue_invoices_0','overdue_invoices_1','overdue_invoices_2'));
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

    public function getTopCustomer(){
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
        return view('reports.top_customer',compact('date_start','date_end','product_code','currency_code','select_currency_codes','customers','results','top'));
    }


    public function getTopProducts($report_type="value"){
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

        return view('reports.top_products',compact('date_start','date_end','product_code','results','quantities','currency_code','select_currency_codes','report_type','currency_code','products','quantity_array','top'));//change view calling syntax
    }


    public function getStocklist(){

        $select_currency_codes 	= Order::groupBy('currency_code')->pluck('currency_code','currency_code');
//        $date_start 			= Input::get('date_start',date("Y-01-01"));
        $date_start 			= Input::get('date_start',date("2017-01-01"));
        //get current day
        $date_end 				= Input::get('date_end',date("Y-m-d", time()));
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code')[0];
        $products 	= Product::where('company_id',return_company_id())
            ->where('stock','>',0)
            ->get();

        return view('reports.stocklist',compact('date_start','date_start','date_end','product_code','products','currency_code','select_currency_codes'));
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
        //
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
