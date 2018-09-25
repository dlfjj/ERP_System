<?php

namespace App\Http\Controllers;
use App\Models\Container;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use App\Models\ShippingTerm;
use App\Models\Taxcode;
use App\Models\User;
use App\Models\ValueList;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Auth;
use Validator;

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

        return Datatables::of($orders)
            ->addColumn('action', function ($order) {
                return '<a href="/orders/'.$order->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
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
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);

        if(Auth::user()->company_id != $order->company_id){
            die("Access violation E01");
        }

        // Profit Calc
        $total_sales = 0;
        $total_cost  = 0;
        if($order->commission == ""){
            $commission  = $customer->salesman_commission;
        } else {
            $commission = $order->commission;
        }
        $discount    = $order->discount;

        if(count($order->items)>0){
            foreach($order->items as $okey=>$orderitem){
                $total_cost += $orderitem->quantity * $orderitem->base_price;
                $total_sales += $orderitem->quantity * $orderitem->unit_price_net;
            }
        }

        $over_limit = false;
        if($order->customer->credit > 0){
            if($order->customer->getOutstandingMoney($order->customer->currency_code) > $order->customer->credit){
                $over_limit = true;
            }
        }


        if($discount > 0){
            $total_sales -= $total_sales / 100 * $discount;
        }
        $total_cost += $order->shipping_cost_actual;
        $total_sales += $order->shipping_cost;

        if($total_sales > 0 && $total_cost > 0){
            $balance_before_commissions = $total_sales;
            $commission_percent = $commission;
            $commission = $balance_before_commissions / 100 * $commission_percent;
            $profit = $balance_before_commissions - $commission - $total_cost;
            $profit_percent = ($profit + $commission) / $total_cost * 100;
        } else {
            $balance_before_commissions = 0;
            $commission_percent = 0;
            $commission = 0;
            $profit = 0;
            $profit_percent = 0;
        }
        //$total_cost += $order->shipping_cost;
        // Profit Calc End


        $select_users          = User::pluck('username','id');
        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = PaymentTerm::pluck('name','id');

        $select_shipping_terms = ShippingTerm::pluck('name','id');

        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_customer_contacts = $customer->contacts->pluck('contact_name','id');
        $select_taxcodes  	   = Taxcode::orderBy('sort_no', 'asc')->pluck('name','id');
        $select_containers     = Container::orderBy('name', 'asc')->pluck('name','id');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );


        return view('orders.show',compact('select_status','select_customer_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','order','select_taxcodes','select_containers','customer','total_cost','total_sales',
            'commission_percent','commission','profit','profit_percent'));
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
        $rules = array(
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $order = Order::findOrFail($id);

        $uid = Auth::user()->id;
        $sup = Auth::user()->superior_id;

        if($order->created_by != $uid){
            if($order->customer->salesman_id != $uid){
                if(!has_role('company_admin')){
                    return redirect('orders/show/'.$id)
                        ->with('flash_error','Permission Denied')
                        ->withErrors($validation->Messages())
                        ->withInput();
                }
            }
        }



        if($validation->fails()){
            return redirect('orders/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $action = $request->input('action','update');

            if($action == 'update'){
                $order = Order::findOrFail($id);
                $order->fill($input);
                $order->updated_by = Auth::user()->id;
                $order->save();
            }

            if($action == 'update_ois'){
                $ois = $request->input('oi');
                if(!is_array($ois)){
                    return redirect('orders/'.$id)
                        ->with('flash_error','Operation failed');
                }

                foreach($ois as $order_id => $data){
                    $order_item = OrderItem::find($order_id);
                    $order_item->remark = $data['remark'];
                    $order_item->commodity_code = $data['commodity_code'];
                    $order_item->quantity = $data['quantity'];
                    $order_item->unit_price_net = $data['price'];
                    $order_item->save();
                }
            }

            updateOrderStatus($id);

            return redirect('orders/'.$id)
                ->with('flash_success','Operation success');
        }
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
