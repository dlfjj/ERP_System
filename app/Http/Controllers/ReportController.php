<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Components\Report\Services\expensesByCategory;
use App\Components\Report\Services\stocklist;
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
    private $expensesByCategory;
    private $stockList;

    public function __construct(KpiService $KpiService, topCustomer $TopCustomer, topProduct $TopProduct, expensesByCategory $ExpensesByCategory, stocklist $StockList){
        $this->middleware('auth');
        has_role('reports',1);

        $this->kpiService = $KpiService;
        $this->topCustomer = $TopCustomer;
        $this->topProduct = $TopProduct;
        $this->expensesByCategory = $ExpensesByCategory;
        $this->stockList = $StockList;
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


    public function getTopCustomer(){

        return view('reports.top_customer',$this->topCustomer->topCustomerByCompany());
    }

    public function export_top_customers($date_start,$date_end){

        $downloadable_file =  $this->topCustomer->export_top_customers($date_start,$date_end);

        return response()->download($downloadable_file[0], 'top_customers.csv', $downloadable_file[1]);

    }


    public function getTopProducts($report_type="value"){

        return view('reports.top_products', $this->topProduct->topProductByCompany());//change view calling syntax
    }


//    stocklist rendering
    public function getStocklist(){

        return view('reports.inventory.stocklist', $this->stockList->getStocklist());

    }


//    rendering expenses table
    public function getExpensesByCategory(){

        return view('reports.expenses.category',$this->expensesByCategory->getExpensesByCategory());
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
