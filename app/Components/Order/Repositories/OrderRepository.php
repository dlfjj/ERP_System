<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/12/9
 * Time: 6:31 PM
 */

namespace App\Components\Order\Repositories;


use App\Models\Container;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\ChartOfAccount;

use App\Models\ShippingTerm;
use App\Models\Taxcode;
use App\Models\User;
use App\Models\ValueList;
use App\OrderHistory;
use Auth;


class OrderRepository
{
    public function getOrderDataForIndex(){
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

        return $orders;
    }

    public function getOrderById(int $id){

        return Order::findOrFail($id);

    }

    public function getCustomerList(){
        $customers = Customer::Select(
            array(
                'customers.id',
                'customers.customer_code',
                'customers.customer_name'
            ))
            ->where('status','ACTIVE')
            ->where('company_id',return_company_id())
        ;
        return $customers;
    }

    public function getCustomerByOrderId($orderId){

        return Customer::findOrFail($orderId);

    }

    public function getProductList(){
        $products = Product::select(
            array(
                'products.id',
                'products.product_code',
                'products.product_name',
                'products.pack_unit',
                'products.pack_unit_hq'
            ))
            ->where('products.status','Active')
            ->where('products.company_id',return_company_id())
        ;
        return $products;
    }

    public function getPaymentById($id){
        $order = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);
        if(Auth::user()->company_id != $order->company_id){
            die("Access violation E01");
        }

        $tree = ChartOfAccount::where('company_id',return_company_id())->get()->toHierarchy();
//        $select_accounts = printSelect($tree,13);
//        $tree = ChartOfAccount::where('company_id',return_company_id())->get()->toHierarchy();
        $select_accounts = printSelect($tree,176,'account_id');
        $select_bank_accounts  = ValueList::where('uid','=','BANK_ACCOUNTS')->orderBy('name', 'asc')->pluck('name','name')->toArray();

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_customer_contacts = $customer->contacts->pluck('name','name');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );
        $select_payment_methods = array(
            "BANK TRANSFER" => "BANK TRANSFER",
            "CHEQUE" => "CHEQUE",
            "CASH" => "CASH"
        );
        return compact('select_status','select_payment_methods','select_customer_contacts',
            'select_payment_terms', 'select_currency_codes', 'select_shipping_methods', 'select_shipping_terms', 'select_accounts',
            'order','customer','select_bank_accounts');
    }

    public function getEmailRecordByOrderId($id){

        $order = Order::findOrFail($id);

        if (Auth::user()->company_id != $order->company_id) {
            die("Access violation E01");
        }
        $customer = Customer::findOrFail($order->customer_id);

        $select_status = OrderStatus::pluck('name', 'id');

        $mail_to = "";
        $mail_cc = "";
        $mail_bcc = "";
        $mail_subject = "Order #$order->order_no";
        $mail_body = <<<EOT
Hello {$order->customerContact->contact_name},

please find your order confirmation #$order->order_no attached.

Let me know if you have any questions,
EOT;
        $mail_body .= "\n"; //$order->user->signature;

//        get the data for order history section
        $order_history = OrderHistory::where('order_id',$order->id)->get();
        $the_user_created_this_order = User::find($order->created_by)->username;
        $the_user_updated_this_order = User::find($order->updated_by)->username;

        return compact('mail_to', 'mail_cc', 'mail_bcc', 'mail_subject', 'mail_body', 'order',
            'customer', 'select_status','order_history','the_user_created_this_order','the_user_updated_this_order');
    }

    public function getOrderDetialData($id){
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

        $created_by_user = User::find($order->created_by)->username;
        $updated_by_user =User::find($order->updated_by)->username;

        return compact('select_status','select_customer_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','order','select_taxcodes','select_containers','customer','total_cost','total_sales',
            'commission_percent','commission','profit','profit_percent', 'created_by_user','updated_by_user');
    }

    public function getLineItemUpdateData($line_item_id){

        $line_item = OrderItem::findOrFail($line_item_id);
        $order = Order::findOrFail($line_item->order_id);
        $customer = Customer::findOrFail($order->customer_id);

        if(Auth::user()->company_id != $order->company_id){
            die("Access violation E01");
        }

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_customer_contacts = $customer->contacts->pluck('name','name');
        $select_status = array(
            "draft" => "draft",
            "open" => "open",
            "closed" => "closed",
            "void" => "void"
        );
        $product_code = $line_item->product->pluck('product_code')->implode(',');
        $product_name = $line_item->product->pluck('product_name')->implode(',');

        return compact('select_status','select_customer_contacts','select_payment_terms','select_currency_codes',
            'select_shipping_methods' ,'select_shipping_terms','order','customer','line_item','product_code','product_name');
    }
}