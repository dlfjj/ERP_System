<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
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
use App\Models\Company;

class PDFController extends Controller
{
    /* function for purchase details pdf*/
    public function samplePDF($id)
    {

        $purchase = Purchase::findOrFail($id);

        $vendor = Vendor::findOrFail($purchase->vendor_id);

        if($purchase->company_id != return_company_id()){
            die("Access violation. Click <a href='/purchases'>here</a> to get back.");
        }
        $company_details = Company::leftJoin('purchases','purchases.company_id','=','companies.id')->where('purchases.id',$id)->get()->toArray();
        // echo "<pre>";
        // print_r($company_details);die;

        $select_users = User::pluck('username','id');
        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendor_contacts = $vendor->contacts->pluck('name','name');
        $select_taxcodes  	   = Taxcode::orderBy('sort_no', 'asc')->pluck('name','id');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );

        $created_by_user = User::find($purchase->created_by)->username;
        $updated_by_user = User::find($purchase->updated_by)->username;
        $html_content = '<h1>Generate a PDF using TCPDF in laravel </h1>
                            <h4>by<br/>Learn Infinity</h4>';
        //   echo "<pre>";
        // print_R($updated_by_user);die;

        // PDF::SetTitle('purchase PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.purchase_details',compact('purchase','vendor','select_vendor_contacts','select_currency_codes','select_users','select_payment_terms','select_taxcodes','created_by_user','updated_by_user','company_details')));
        // ob_end_clean();
        // PDF::Output('purchase.pdf');
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.purchase_details',compact('purchase','vendor','select_vendor_contacts','select_currency_codes','select_users','select_payment_terms','select_taxcodes','created_by_user','updated_by_user','company_details')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

        exit(0);
    }
    // function for order confrimation
    public function order_confirmation($id){
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();

        // PDF::SetTitle('confirmation PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.order_confirmation',compact('order','customer')));
        // ob_end_clean();
        // PDF::Output('order-confirm.pdf');
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.order_confirmation',compact('order','customer','payment_terms')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }
    public function commercial_invoice($id){
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);

        $order_status = OrderStatus::leftJoin('orders','orders.status_id','=','order_status.id')->where('orders.id',$id)->get()->toArray();
        // echo "<pre>";
        // print_r($order_status);die;
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();


        $net_weight = getNetWeight($order);


        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.commercial_invoice',compact('order','customer','customers_details','order_status','order_items','payment_terms','net_weight')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

        exit(0);}

    /**
     * @param $id
     */
    public function quotation($id){
        $order    = Order::findOrFail($id);

//        return $order->delivery_address;
        $customer = Customer::findOrFail($order->customer_id);


//        return $customer;

        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        // echo "<pre>";
        // print_r($order_items);die;
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        // echo "<pre>";
        //      print_r($payment_terms);die;
        // PDF::SetTitle('confirmation PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.quotation',compact('order','customer','customers_details','order_items','payment_terms')));
        // ob_end_clean();
        // PDF::Output('quotation.pdf');
        $dompdf = new Dompdf();

        $dompdf->loadHtml(view('printouts.quotation',compact('order','customer','customers_details','order_items','payment_terms')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);

    }

    public function order_acknowledgement($id){
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);
        $order_status = OrderStatus::leftJoin('orders','orders.status_id','=','order_status.id')->where('orders.id',$id)->get()->toArray();
        // echo "<pre>";
        // print_r($order_status);die;
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();

        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        // PDF::SetTitle('acknowledgement PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.acknowledgement',compact('order','customer','customers_details','order_items','order_status')));
        // ob_end_clean();
        // PDF::Output('acknowledgement.pdf');
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.acknowledgement',compact('order','customer','customers_details','order_items','order_status','payment_terms')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);

    }
    public function package_list($id){
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);
        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        // PDF::SetTitle('packing PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.package_list',compact('order','customer','customers_details','order_items','order_status','payment_terms')));
        // ob_end_clean();
        // PDF::Output('packing.pdf');
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.package_list',compact('order','customer','customers_details','order_items','order_status','payment_terms')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);
    }
    public function proforma_invoice($id){
        $order    = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);
        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        // PDF::SetTitle('performa_invoice PDF');
        // PDF::AddPage();
        // PDF::writeHTML(view('printouts.performa_invoice',compact('order','customer','customers_details','order_items','order_status','payment_terms')));
        // ob_end_clean();
        // PDF::Output('performa_invoice.pdf');
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.proforma_invoice',compact('order','customer','customers_details','order_items','order_status','payment_terms')));
        // ob_end_clean(););

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);
    }



}
