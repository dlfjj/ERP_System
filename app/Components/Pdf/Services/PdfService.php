<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/29
 * Time: 10:16 AM
 */

namespace App\Components\Pdf\Services;

use PDF;
use Dompdf\Dompdf;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\PurchaseDelivery;
use App\Models\Vendor;
use App\Models\User;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Customer;
use Yajra\Datatables\Datatables;
use Validator;
use Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentTerm;



class PdfService
{
    public function getSamplePDF(int $id){
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

    public function getPurchaseOrderPdf(int $id){

    }
}