<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use Yajra\DataTables\DataTables;

class HistoryController extends Controller
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

    public function anyDtOrders($customer_id){

        $orders = Order::Leftjoin('customers','orders.customer_id','=','customers.id')
            ->Leftjoin('order_status','orders.status_id','=','order_status.id')
            ->select(
                array(
                    'orders.id',
                    'orders.order_no',
                    'order_status.name',
                    'orders.order_date',
                    'orders.customer_order_number',
                    'orders.currency_code',
                    'orders.total_gross',
                    'orders.total_paid'
                ))
            ->where("orders.customer_id",$customer_id)
            ->where('orders.company_id',return_company_id());

        return Datatables::of($orders)
            ->addColumn('action',function ($order) {
                return '<ul class="table-controls"><li><a href="/orders/'.$order->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>';})
            ->editColumn('total_paid', function($row){
                return round($row->total_gross - $row->total_paid,2);
            })
            ->make(true);
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
        $customer = Customer::findorfail($id);
//        $orders_history = Order::Leftjoin('customers','orders.customer_id','=','customers.id')
//            ->Leftjoin('order_status','orders.status_id','=','order_status.id')
//            ->select(
//                array(
//                    'orders.id',
//                    'orders.order_no',
//                    'order_status.name',
//                    'orders.order_date',
//                    'orders.customer_order_number',
//                    'orders.currency_code',
//                    'orders.total_gross',
//                    'orders.total_paid'
//                ))
//            ->where("orders.customer_id",$id)
//            ->where('orders.company_id')->get();

        return view('customers.history.show',compact('customer'));

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
