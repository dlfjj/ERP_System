<?php

namespace App\Http\Controllers;

use App\Components\Pdf\Services\PdfService;
use App\Mail\OrderEmail;
use App\Mail\PurchaseEmail;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use App\Models\Purchase;
use App\Models\PurchaseHistory1;
use App\OrderHistory;

use Illuminate\Http\File;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Mail;
use Auth;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Redirect;
use PDF;


class EmailController extends Controller
{
    private $pdfService;
    public function __construct(PdfService $PdfService){
        $this->middleware('auth');
//        has_role('orders',1);

        $this->pdfService = $PdfService;
    }

    public function sendOrderEmail(Request $request, $id)
    {

        if(count($_POST) == 0){
            die("Illegal Request");
        }
//        $private_folder = config('app.private_folder') . return_company_id() . "/orders/";

        $order 		= Order::findOrFail($id);
        $customer 	= Customer::findOrFail($order->customer_id);
        $customer_contact = CustomerContact::find($order->customer_contact_id);
//        $contacts = $customer->contacts->all();
        $mail_to = $request->mail_to;
        $uid = Auth::user()->id;

        if($order->created_by != $uid AND $order->customer->salesman_id != $uid AND !has_role('company_admin')){
                    return \redirect('orders/'.$id)
                        ->with('flash_error','Permission Denied')
                        ->withInput();
        }
        if(!$customer_contact){
            die("Invalid contact info for this customer");
    }

//        convert the text body value
        $comment  = $request->mail_body;
        $comment = str_replace("<<MAIN_CONTACT>>",$customer_contact->contact_name,$comment);
        $comment = str_replace("<<VESSEL_ETD>>",$order->vessel_etd,$comment);
        $comment = str_replace("<<VESSEL_ETA>>",$order->vessel_eta,$comment);
        $comment = str_replace("<<DAYS_OVERDUE>>",$order->getDaysOverdue(),$comment);
        $comment = str_replace("<<CUSTOMER_ORDER_NUMBER>>",$order->customer_order_number,$comment);
        $comment = str_replace("<<CUSTOMER_ORDER_ID>>",$order->customer_order_number,$comment);
        $comment = str_replace("<<ORDER_ID>>",$order->order_no,$comment);
        $comment = str_replace("<<ESTIMATED_FINISH_DATE>>",$order->estimated_finish_date,$comment);

        $preserve_current_order_status = false;

        if($request->status_id == 9){
            $preserve_current_order_status = true;
            $original_order_status = $order->status_id;
        }

        if($request->has('status_id')){
            $order->status_id = $request->input('status_id');
        }

        $status = OrderStatus::findOrFail($order->status_id);
        $order_status = strtolower($status->name);
//        $status = $order->status;

        if($request->has('inform_customer')){
            $inform_customer = 1;
        } else {
            $inform_customer = 0;
        }

        $order_no = $order->order_no;

        //book the stock from inventory when order shipped out
        if($status->id == 7){
            if($order->stock_booked == 0){
                // Check first...
                foreach($order->items as $oi){
                    if($oi->product[0]->track_stock == 0){ continue; }
                    if($oi->product[0]->stock - $oi->quantity < 0){
                        return redirect('orders/records/'.$id)
                            ->with('flash_error','Insufficient stock')
                            ->withInput()
                            ;
                    }
                }
                // Now book stock
                foreach($order->items as $oi){
                    if($oi->product[0]->track_stock == 0){ continue; }
                    warehouse_transaction($oi->product_id, -$oi->quantity,"Booked for order {$order->order_no}");
                    $order->stock_booked = 1;
                    $order->save();
                }
            }
        }

//      everytime send a email, system will generate the pdf file attach with the email without saving on server
        $customer = Customer::findOrFail($order->customer_id);
        $customers_details = Customer::leftJoin('orders','orders.customer_id','=','customers.id')->join('companies','companies.id','=','customers.company_id')->where('orders.id',$id)->get()->toArray();
        $order_items  = OrderItem::LeftJoin('orders','orders.id','=','order_items.order_id')->where('orders.id',$id)->get()->toArray();
        $payment_terms = PaymentTerm::leftjoin('orders','orders.payment_term_id','=','payment_terms.id')->where('orders.id',$id)->get()->toArray();
        $net_weight = getNetWeight($order);
        $gross_weight =  getGrossWeight($order);
        $package_count = getNumberOfPackages($order);
        $volumn = getCbm($order);

//        $pdf = PDF::loadView('printouts.quotations', compact('order','customer','customers_details','order_items','payment_terms'));
//        $mail_subject = "American Dunnage Order Info for Order #$order_no";
        $timestamp = time();
        if($request->has('record_file')) {
            $record_file = 1;
            if (stristr($order_status, "quotation")) {
                $filename = "quotations-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('quotations', $id)->inline());
//            $pdf = PDF::loadView('printouts.quotations', compact('order','customer','customers_details','order_items','payment_terms'));
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "confirmation")) {
//                $pdf = PDF::loadView('printouts.order_confirmation', compact('order', 'customer', 'payment_terms'));
                $filename = "sc-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('confirmation', $id)->inline());
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "acknowledged")) {
//                $pdf = PDF::loadView('printouts.acknowledgement', compact('order', 'customer', 'customers_details', 'order_items', 'order_status', 'payment_terms'));
                $filename = "acknowledged-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('acknowledgement', $id)->inline());
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "pending")) {
//                $pdf = PDF::loadView('printouts.acknowledgement', compact('order', 'customer', 'customers_details', 'order_items', 'order_status', 'payment_terms'));
                $filename = "pending-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('acknowledgement', $id)->inline());
                $mail_subject = "American Dunnage Pending Order Info for Order #$order_no";
            } elseif (stristr($order_status, "processing")) {
                $filename = "processing-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('confirmation', $id)->inline());
//                $pdf = PDF::loadView('printouts.order_confirmation', compact('order', 'customer', 'payment_terms'));
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "proforma invoice")) {
                $filename = "pi-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('acknowledgement', $id)->inline());
//                $pdf = PDF::loadView('printouts.proforma_invoice', compact('order', 'customer', 'customers_details', 'order_items', 'order_status', 'payment_terms'));
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "shipped out")) {
//                $pdf = PDF::loadView('printouts.commercial_invoice', compact('order', 'volumn', 'customer', 'customers_details', 'order_status', 'payment_terms', 'package_count', 'net_weight', 'gross_weight'));
                $filename = "ci-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('invoice', $id)->inline());
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "canceled")) {
//                $pdf = PDF::loadView('printouts.commercial_invoice', compact('order', 'volumn', 'customer', 'customers_details', 'order_status', 'payment_terms', 'package_count', 'net_weight', 'gross_weight'));
                $filename = "canceled-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('invoice', $id)->inline());
                $mail_subject = "American Dunnage Order Info for Order #$order_no";
            } elseif (stristr($order_status, "overdue")) {
//                $pdf = PDF::loadView('printouts.commercial_invoice', compact('order', 'volumn', 'customer', 'customers_details', 'order_status', 'payment_terms', 'package_count', 'net_weight', 'gross_weight'));
                $filename = "reminder-{$order_no}-{$timestamp}.pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('invoice', $id)->inline());
                $mail_subject = "American Dunnage Inc. Gentle Payment Reminder #$order_no";
            } else {
                $filename = md5($timestamp) . ".pdf";
                Storage::put('public/pdf_files/' . $filename, $this->pdfService->getOrderPdf('quotations', $id)->inline());
                $mail_subject = "American Dunnage Inc. Info for Order #$order_no";
            }
        }else {
            $record_file = 0;
            $filename = "";
        }



        if($inform_customer == 1){
            $mail_data = array(
//                'from_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//                'from_email' => Auth::user()->email,
                'reply_to_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                'reply_to_email' => Auth::user()->email,
                'to_email' => $customer_contact->username,
                'subject' => $mail_subject,
                'mail_body' => $comment,
                'order_status' => $order_status,
//                'signature' => Auth::user()->signature,
                'mail_to'  => $mail_to,
                'order' => $order
            );
            $message = new OrderEmail($mail_data);
            $message->attachData(Storage::get('public/pdf_files/'.$filename),$filename);
//            $message->attachData($pdf->output(),'quotations.pdf');
            Mail::to('dlfjj123@gmail.com')->send($message);
        }

        // Send an email to
//        Mail::to('dlfjj123@gmail.com')->send(new OrderEmail($mail_data));
//        return "You did it again";
//        record the message to the system



        //after sending the email, if user choose to store file, the file system will save the file afterward
//        $file_to_store = "";
//        if(stristr($order_status,"quotations")){
//            $timestamp = time();
//            $filename = "quotations-{$order_no}-{$timestamp}.pdf";
//            $file_to_store = Storage::url($filename);
//        }

//        return $file_to_store;


/*        if($request->has('record_file')){
            $record_file = 1;
            Storage::put('/public/pdf_files/'.$filename, $pdf->output());
//            $url = Storage::url($filename);
        } else {
            $record_file = 0;
            $filename = "";
        }*/

        $record = New OrderHistory;
        $record->order_id = $id;
        $record->date_added = date("Y-m-d");
        $record->notify_customer = $inform_customer;
        $record->record_file = $record_file;
        $record->file_name = $filename;
        $record->comment = $comment;
        $record->username = Auth::user()->username;
        $record->order_status_id = $order->status_id;
        $record->created_by = Auth::user()->id;
        $record->save();

        if($preserve_current_order_status){
            $order->status_id = $original_order_status;
        }

        $order->save();

        return redirect("/orders/records/$id")
            ->with('flash_success','Operation success');
    }




    public function sendPurchaseEmail(Request $request, $id){


//        $private_folder = Config::get('app.private_folder') . "/purchases/";
//        if(!file_exists($private_folder) || !is_dir($private_folder)){
//            if(!mkdir($private_folder)){
//                die("Could not create needed Folder to store Purchase History");
//            }
//        }

//        $purchase = Purchase::findOrFail($id);
//        $vendor = Vendor::findOrFail($purchase->vendor_id)
//        $filename = "quotations-330002-1542242303.pdf";
//        $filepath = Storage::url('pdf_files/'.$filename);
//        $filepath = Storage::url('app/public/pdf_files/'.$filename);




//        return dd(Storage::disk('public')->exists('pdf_files/ci-330002-1542830428.pdf'.$filename));

        if($request->get('attach_pdf') == 1){
            $filename = "po-$id-".time().".pdf";
            Storage::put('public/pdf_files/'.$filename, $this->pdfService->getPurchaseOrderPdf($id)->inline());
//            $this->pdfService->getPurchaseOrderPdf($id)->save(Storage::put('public/pdf_files/'.$filename));
//            $filepath = Storage::url('public/pdf_files/'.$filename);
        } else {
            $filepath = "";
            $filename = "";
        }

        $mail_to = $request->mail_to;
        $mail_cc = $request->mail_cc;
        if(is_array($mail_to) && count($mail_to)>0){
            $notify_vendor = 1;
        } else {
            $notify_vendor = 0;
            $mail_to = array();
        }

        if(!is_array($mail_cc)){
            $mail_cc = array();
        }

        if($notify_vendor == 1){
            $mail_data = array(
                'from_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                'from_email' => Auth::user()->email,
                'to_email' => $mail_to,
                'cc_email' => $mail_cc,
                'bcc_email' => $request->mail_bcc,
                'subject' => $request->mail_subject,
                'mail_body' => $request->mail_body,
            );

            $message = new PurchaseEmail($mail_data);
            //get the pdf store in the server
            $message->attachData(Storage::get('public/pdf_files/'.$filename),$filename);
            Mail::to('dlfjj123@gmail.com')->send($message);

        }

        $record = New PurchaseHistory1();
        $record->purchase_id = $id;
        $record->notify_vendor = $notify_vendor;
        $record->attach_pdf = $request->attach_pdf;
        $record->file_name = $filename;
        $record->mail_body = $request->mail_body;
        $record->mail_subject = $request->mail_subject;
        $record->mail_to = implode(",",$mail_to);
        $record->mail_cc = implode(",",$mail_cc);
        $record->mail_bcc = $request->mail_bcc;
        $record->created_by = Auth::user()->id;
        $record->save();

        return Redirect::to('purchases/records/'.$id)
            ->with('flash_success','Operation success');
    }
}
