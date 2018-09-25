<?php

namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
        has_role('orders',1);
    }

    public $layout = 'layouts.default';


    public function index()
    {
        $outstanding_balance_currency_code = "USD";
        $outstanding_balance_amount = 0; //$this->_getOutstandingBalance($outstanding_balance_currency_code);

        return view('orders.index', compact('outstanding_balance_currency_code','outstanding_balance_amount'));
//        $this->layout->content = View::make('orders.index')
//            ->with('outstanding_balance_currency_code', $outstanding_balance_currency_code)
//            ->with('outstanding_balance_amount', $outstanding_balance_amount);
    }

    public function getOrderData(){
        $orders = Order::Leftjoin('customers','customers.id','=','orders.customer_id')
            ->Leftjoin('order_status','orders.status_id','=','order_status.id')
            ->select(
                array(
                    'orders.id',
                    'orders.order_no',
                    'order_status.name',
                    'orders.customer_order_number',
                    'orders.order_date',
                    'customers.customer_name',
                    'orders.estimated_finish_date',
                    'orders.total_gross',
                ))
            ->where('orders.company_id',return_company_id());

        return Datatables::of($orders)->make(true);
//            ->add_column('operations','<ul class="table-controls"><li><a href="/orders/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
//            ->remove_column('id')
//            ->make();
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
