<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/29
 * Time: 10:15 AM
 */

namespace App\Components\Pdf\Repositories;
use PDF;

use App\Models\Customer;
use Validator;
use Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentTerm;
use App\Models\Purchase;
use App\Models\Vendor;

class PdfRepository
{
    public function getSamplePdfData(int $id){

        $order    = Order::findOrFail($id);
        $order_status = OrderStatus::leftJoin('orders','orders.status_id','=','order_status.id')->where('orders.id',$id)->get()->toArray();
        $customer = Customer::findOrFail($order->customer_id);
        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        $net_weight = getNetWeight($order);
        $gross_weight =  getGrossWeight($order);
        $package_count = getNumberOfPackages($order);
        $volumn = getCbm($order);
        $nt_weight_total = getNetWeight($order);
        $gr_weight_total =  getGrossWeight($order);

        return compact('order','customer','customers_details','order_items','payment_terms','order_status','net_weight','gross_weight','package_count','volumn','nt_weight_total','gr_weight_total');
    }

    public function getPurchaseOrderPdfData(int $id){
        $purchase = Purchase::findOrFail($id);
        $vendor = Vendor::findOrFail($purchase->vendor_id);
        $headerHtml = view()->make('printouts.purchases.header')
            ->render();

        $footerHtml = view()->make('printouts.purchases.footer')
            ->with('purchase',  $purchase)
            ->render();

        return compact('purchase','vendor','headerHtml','footerHtml');
    }
}