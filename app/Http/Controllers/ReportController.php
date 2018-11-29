<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Components\Report\Services\topProduct;
use App\Expense;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReportSubscription;
use App\Models\ValueList;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Auth;
use App\Components\Report\Services\KpiService;
use App\Components\Report\Services\topCustomer;


class ReportController extends Controller
{
    private $kpiService;
    private $topCustomer;
    private $topProduct;

    public function __construct(KpiService $KpiService, topCustomer $TopCustomer, topProduct $TopProduct){
        $this->middleware('auth');
        has_role('reports',1);

        $this->kpiService = $KpiService;
        $this->topCustomer = $TopCustomer;
        $this->topProduct = $TopProduct;

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

//        session()->put('kpi', $this->kpiService->getKpiByCompany());

        return view('reports.kpis.index',$this->kpiService->getKpiByCompany());
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


    public function getTopCustomer(){

        return view('reports.top_customer',$this->topCustomer->topCustomerByCompany());
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

        return view('reports.top_products', $this->topProduct->topProductByCompany());//change view calling syntax
    }


//    stocklist rendering
    public function getStocklist(){

//        $date_start = date("2016-01-01");
        $select_currency_codes 	= Order::groupBy('currency_code')->pluck('currency_code','currency_code');
//        $date_start 			= Input::get('date_start',date("Y-01-01"));
//        $date_start 			= Input::get('date_start',date("2017-01-01"));
        //get current day
        $date_end 				= Input::get('date_end',date("Y-m-d", time()));
        $currency_code = User::leftjoin('companies','companies.id','=','users.company_id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code')[0];
        $products 	= Product::where('company_id',return_company_id())
            ->where('stock','>',0)
            ->get();

        return view('reports.stocklist',compact('date_start','date_end','product_code','products','currency_code','select_currency_codes'));
    }


//    rendering expenses table
    public function getExpensesByCategory(){
        $date_start 	 = Input::get('date_start',date("Y-01-01"));
        $date_end 		 = Input::get('date_end',date("Y-m-t"));
        $company_id 	 = return_company_id();

        $categories 	 = ChartOfAccount::where('company_id', $company_id)
            ->where('type', 'Expense')
            ->orderBy('name')
            ->get();
        $expenses   = Expense::where('company_id',$company_id)

            ->where('amount_conv',0)
            ->get();

        $category_ids = ChartOfAccount::where('company_id', return_company_id())
            ->where('type','Expense')
            ->pluck('id');
        $expense_total = Expense::where('company_id', return_company_id())
            ->where('date_created','>=',$date_start)
            ->where('date_created','<=',$date_end)
            ->whereIn('account_id', $category_ids)
            ->sum('amount_conv');
        foreach($categories as $category){
            $expenses_category = Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->get();
            $category_total = Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->sum('amount_conv');
        }
        return view('reports.expenses.category',compact('categories','date_start','date_end','company_id','expenses','category_ids','expense_total','category_total','expenses_category'));
    }

//    history reports files
    public function getDownloads(){
        // $file_path  = app_path().'/'. return_company_id() . "/reports/";
        $file_path = storage_path().'/app/reports_downloads/';
        // echo $file_path;die;
        if(file_exists($file_path)){
            $files      = scandir($file_path);
            array_shift($files);
            array_shift($files);
        } else {
            $files = array();
        }
        foreach($files as $filename){
            $subscription = ReportSubscription::where('user_id',Auth::user()->id)->where('file_name',$filename)->first();
        }
        return view('reports.downloads',compact('files','file_path','subscription'));

    }


    public function export_top_customers($date_start,$date_end){

        $downloadable_file =  $this->topCustomer->export_top_customers($date_start,$date_end);

        return response()->download($downloadable_file[0], 'top_customers.csv', $downloadable_file[1]);

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
