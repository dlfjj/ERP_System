<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use View;

class PrintoutController extends Controller
{
    public function __construct(){
//        if($_SERVER['REMOTE_ADDR'] != "69.164.218.62"){
            $this->middleware('auth');
//        }
    }
    public function getOrdersQuotation($id){
        $order = Order::findOrFail($id);

        $this->layout = View::make('layouts.print');
        $document_name = "quotation_" . $order->id;
        $this->layout->document_name = $document_name;

        if($order->customer_id == "" || $order->customer_id == 0){
            return redirect('orders/'.$id)
                ->with('flash_error','Operation failed (No Customer chosen)');
        }

        $order_total_cbm      = 1; //OrderItem::where("order_id","=",$id)->sum('calc_cbm');
        $order_total_quantity = 1; //OrderItem::where("order_id","=",$id)->sum('quantity');
        $customer_name = $order->customer->customer_name;
//        return $order->customer->customer_name;
//        return view('printouts.orders.quotations',compact('order','document_name','order_total_cbm',
//            'order_total_quantity','customer_name'));
        $this->layout->module_title = "Order details for #$order->id";
        $this->layout->content = View::make('printouts.orders.quotations')
            ->with('order', $order)
            ->with('order_total_cbm',$order_total_cbm)
            ->with('order_total_quantity',$order_total_quantity)
            ->with('customer_name',$customer_name);
    }
}
