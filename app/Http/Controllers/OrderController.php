<?php

namespace App\Http\Controllers;
use App\BankCharges;
use App\Models\Container;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerPayment;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use App\Models\ShippingTerm;
use App\Models\Taxcode;
use App\Models\User;
use App\Models\ValueList;
use App\Models\ChartOfAccount;
use App\Models\OrderStatus;
use App\OrderHistory;
use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Order;
use Auth;
use Validator;
use Mail;
use Illuminate\Support\Facades\Session;

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

    public function customersList(){
        return view('orders.customersList');
    }

    public function getCustomerslist(){
        $customers = Customer::Select(
            array(
                'customers.id',
                'customers.customer_code',
                'customers.customer_name'
            ))
            ->where('status','ACTIVE')
            ->where('company_id',return_company_id())
        ;

        return Datatables::of($customers)
            ->addColumn('action',function($customer){
                return \Form::open(['method'=>'GET','action'=>'OrderController@create','class'=>'form']).'
				<input type="hidden" name="id" value="'.$customer->id.'" />
				<input type="submit" name="submit" value="Create" class="btn center-block" />
		        '.\Form::close();
            })
            ->make(true);
    }



//    table of products need to be added to the order item
    public function anyDtAvailableProducts(){
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
        return Datatables::of($products)
            ->removeColumn('id')
            ->addColumn('action',function($product){
                return \Form::open(['method'=>'POST','action'=>['OrderController@postLineItemAdd'],'class'=>'form']).'
            <input type="hidden" name="product_id" value="'.$product->id.'" />
            <input type="number" class="qty_picker_input" name="quantity" value="" step="1" min="0" size="3"/>
            <input type="submit" name="submit" value="Add" class="btn pull-right add_this_item" />
            '.\Form::close();
            })->make(true);
    }

    public function getPayments($id) {

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
        return view('orders.payments',compact('select_status','select_payment_methods','select_customer_contacts',
            'select_payment_terms', 'select_currency_codes', 'select_shipping_methods', 'select_shipping_terms', 'select_accounts',
            'order','customer','select_bank_accounts'));
    }

    public function getRecords($id)
    {
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

        return view('orders.records', compact('mail_to', 'mail_cc', 'mail_bcc', 'mail_subject', 'mail_body', 'order',
            'customer', 'select_status','order_history','the_user_created_this_order','the_user_updated_this_order'));
    }

    public function getChangelog($id){
        $order = Order::findOrFail($id);

        return view('orders.changelog',compact('order'));
//        $this->layout->module_title = "";
//        $this->layout->module_sub_title = "";
//        $this->layout->content = View::make('orders.changelog')
//            ->with('order',$order);
    }

    public function getInvoices($id){
        $order = Order::findOrFail($id);

        return $order->items;
        return view('orders.invoices',compact('order'));
//        $this->layout->module_title = "";
//        $this->layout->module_sub_title = "";
//        $this->layout->content = view::make('orders.invoices')
//            ->with('order',$order);
    }

    public function showLineItemAdd($id)
    {
        $order = Order::findOrFail($id);
        $customer = Customer::findOrFail($order->customer_id);

        if (Auth::user()->company_id != $order->company_id) {
            die("Access violation E01");
        }
        return view('orders.lineItem.add_line_item', compact('order', 'customer'));
    }



    public function create(Request $request)
    {
        $rules = array(
            'id' => 'Required|integer',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect()->back()
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $customer_id = $request->id;
            $customer = Customer::find($customer_id);

            if(!$customer){
                return redirect()->back()
                    ->with('flash_error','Customer not found');
            }

            if($customer->taxcode_id == ""){
                return redirect()->back()
                    ->with('flash_error','Customer has no Taxcode');
            }

            if($customer->contacts->count() == 0){
                return redirect()->back()
                    ->with('flash_error','Customer has no Contacts');
            }

            if($customer->credit > 0){
                if($customer->getOutstandingMoney($customer->currency_code) > $customer->credit){
                    if(!has_role('admin') && !has_role('company_admin')){
                        return Redirect::to('orders/create')
                            ->with('flash_error','Customer exceeded credit');
                    }
                }
            }

            $ship_to = "";

            if($customer->inv_address1 != ""){
                $ship_to .= $customer->inv_address1 . "\n";
            }
            if($customer->inv_address2 != ""){
                $ship_to .= $customer->inv_address2 . "\n";
            }
            if($customer->inv_postal_code != ""){
                $ship_to .= $customer->inv_postal_code . " ";
            }
            if($customer->inv_city != ""){
                $ship_to .= $customer->inv_city . "\n";
            }
            if($customer->inv_province != ""){
                $ship_to .= $customer->inv_province . ", ";
            }
            if($customer->inv_country != ""){
                $ship_to .= $customer->inv_country . "\n";
            }
            $ship_to = trim($ship_to);

            $order = New Order;
            $order->status_id = 1;
            $order->order_no = getNewOrderNo(return_company_id());
            $order->customer_id = $customer_id;
            $order->container_type = 1;
            $customer_contact = $customer->contacts->first();
            $order->customer_contact_id = $customer_contact->id;
            $order->taxcode_id 	  = $customer->taxcode_id;
            $order->currency_code = $customer->currency_code;
            $order->delivery_address = $ship_to;
            $order->billing_address  = $ship_to;
            //$order->payment_terms = $customer->payment_terms;
            $order->created_by  = Auth::user()->id;
            $order->updated_by = Auth::user()->id;
            $order->user_id = Auth::user()->id;
            $order->order_date = date("Y-m-d");
            $order->company_id = return_company_id();
            $order->ff_name = $customer->ff_name;
            $order->ff_contact = $customer->ff_contact;
            $order->ff_email = $customer->ff_email;
            $order->ff_phone = $customer->ff_phone;
            $order->ff_fax = $customer->ff_fax;
            $order->save();

            $id = $order->id;
            return redirect('orders/'.$id)
                ->with('flash_success','Operation success');
        }
    }

    public function postPayments(Request $request,  $id) {

//        return dd((float) $request->bank_charges);

        $order = Order::findOrFail($id);
        $rules = array(
            'amount' => "required",
            'date_created'   => "required",
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $uid = Auth::user()->id;
        $sup = Auth::user()->superior_id;

        if($order->created_by != $uid){
            if($order->customer->salesman_id != $uid){
                if(!has_role('company_admin')){
                    return redirect('orders/'.$id)
                        ->with('flash_error','Permission Denied')
                        ->withErrors($validation->Messages())
                        ->withInput();
                }
            }
        }
        if($validation->fails()){
            return redirect('orders/payments/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $payment = new CustomerPayment();
            $payment->company_id = return_company_id();
            $payment->order_id   = $order->id;
            $payment->type       = "Bank";

            if($payment->transaction_reference === NULL) {
                $transaction_reference = '';
            }else{
                $transaction_reference = $request->input('transaction_reference');
            }
            if($payment->remark === NULL) {
                $remark = '';
            }else{
                $remark = $request->input('remark');
            }
            $payment->transaction_reference = $transaction_reference;
            $payment->currency_code = $request->input('currency_code','USD');
            $payment->amount        = $request->input('amount');
            $payment->bank_charges  = $request->input('bank_charges');
            $payment->created_by    = Auth::user()->id;
            $payment->account_id    = $request->input('account_id',176);
            $payment->remark        = $remark;
            $payment->date          = $request->input('date_created');
            $payment->save();

            //after created a payment record, record the bank_charges
            if($request->bank_charges != null){
                $bank_charge = new BankCharges();
                $bank_charge->bank_customer_id = $payment->id;
                //set the account categories for accounting purpose
                $bank_charge->account_id = 186;
                $bank_charge->amount = (float) $request->bank_charges;
                $bank_charge->save();

                return $request;
            }else{
                return $id;
            }


            updateOrderStatus($id);

            return redirect('orders/payments/'.$id)
                ->with('flash_success','Operation success');
        }
    }

//    public function postRecord(Request $request, $id){

//
//        if(count($_POST) == 0){
//            die("Illegal Request");
//        }
//        $private_folder = config('app.private_folder') . return_company_id() . "/orders/";
//
//        $order_id = $id;
//        $comment  = $request->mail_body;
//
//        $order 		= Order::findOrFail($id);
//        $customer 	= Customer::findOrFail($order->customer_id);
//        $customer_contact = CustomerContact::find($order->customer_contact_id);
//
//        $contacts = $customer->contacts;
//        $mail_to = $request->mail_to;
//
//        $uid = Auth::user()->id;
//        $sup = Auth::user()->superior_id;
//
//        if($order->created_by != $uid){
//            if($order->customer->salesman_id != $uid){
//                if(!has_role('company_admin')){
//                    return redirect('orders/'.$id)
//                        ->with('flash_error','Permission Denied')
//                        ->withErrors($validation->Messages())
//                        ->withInput();
//                }
//            }
//        }
//
//        if(!$customer_contact){
//            die("Invalid contact info for this customer");
//        }
//
//        $comment = str_replace("<<MAIN_CONTACT>>",$customer_contact->contact_name,$comment);
//        $comment = str_replace("<<VESSEL_ETD>>",$order->vessel_etd,$comment);
//        $comment = str_replace("<<VESSEL_ETA>>",$order->vessel_eta,$comment);
//        $comment = str_replace("<<DAYS_OVERDUE>>",$order->getDaysOverdue(),$comment);
//        $comment = str_replace("<<CUSTOMER_ORDER_NUMBER>>",$order->customer_order_number,$comment);
//        $comment = str_replace("<<CUSTOMER_ORDER_ID>>",$order->customer_order_number,$comment);
//        $comment = str_replace("<<ORDER_ID>>",$order->order_no,$comment);
//        $comment = str_replace("<<ESTIMATED_FINISH_DATE>>",$order->estimated_finish_date,$comment);
//
//        $preserve_current_order_status = false;
//        if(($request->status_id) == 9){
//            $preserve_current_order_status = true;
//            $original_order_status = $order->status_id;
//        }
//
//        if($request->has('status_id')){
//            $order->status_id = $request->input('status_id');
//        }
//
//        $status = OrderStatus::findOrFail($order->status_id);
//
//        $order_status = $status->name;
//        $order_status = strtolower($order_status);
//
//
//        if($request->has('inform_customer')){
//            $inform_customer = 1;
//        } else {
//            $inform_customer = 0;
//        }
//
//        $unique_id = uniqid();

//        $order_id = $order->id;
//        $order_no = $order->order_no;
//
//        if($status->id == 7){
//            if($order->stock_booked == 0){
//                // Check first...
//                foreach($order->items as $oi){
//                    if($oi->product->track_stock == 0){ continue; }
//                    if($oi->product->stock - $oi->quantity < 0){
//                        return redirect('orders/records/'.$order->id)
//                            ->with('flash_error','Insufficient stock')
//                            ->withInput()
//                            ;
//                    }
//                }

                // Now book stock
//                foreach($order->items as $oi){
//                    if($oi->product->track_stock == 0){ continue; }
//                    warehouse_transaction($oi->product_id, -$oi->quantity,"Booked for order {$order->order_no}");
//                    $order->stock_booked = 1;
//                    $order->save();
//                }
//            }
//        }

//        $file_to_store = "";
//        if(stristr($order_status,"quotation")){
//            $file_to_store = URL::to('/pdf/print-order/quote/'.$order_id);
//            $filename = "quotation-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"confirmation")){
//            $file_to_store = URL::to('/pdf/print-order/sc/'.$order_id);
//            $filename = "sc-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"acknowledged")){
//            $file_to_store = URL::to('/pdf/print-order/con/'.$order_id);
//            $filename = "acknowledged-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"pending")){
//            $file_to_store = URL::to('/pdf/print-order/con/'.$order_id);
//            $filename = "pending-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"processing")){
//            $file_to_store = URL::to('/pdf/print-order/sc/'.$order_id);
//            $filename = "processing-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"proforma invoice")){
//            $file_to_store = URL::to('/pdf/print-order/pi/'.$order_id);
//            $filename = "pi-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"shipped out")){
//            $file_to_store = URL::to('/pdf/print-order/ci/'.$order_id);
//            $filename = "ci-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"canceled")){
//            $file_to_store = URL::to('/pdf/print-order/ci/'.$order_id);
//            $filename = "canceled-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        } elseif(stristr($order_status,"overdue")){
//            $file_to_store = URL::to('/pdf/print-order/invoice_reminder/'.$order_id);
//            $filename = "reminder-{$order_no}-{$unique_id}.pdf";
//            $mail_subject = "Oemserv Gentle Payment Reminder #$order_no";
//        } else {
//            $filename = md5(uniqid()) . ".pdf";
//            $mail_subject = "Oemserv Order Info for Order #$order_no";
//        }
//
//        $filepath = "";
//
//        if($request->has('record_file') && $file_to_store != ""){
//            $record_file = 1;
//            $printurl = $file_to_store;
//            $filepath = $private_folder . $filename;
//            print_pdf($printurl,$filename,$filepath);
//        } else {
//            $record_file = 0;
//            $filename = "";
//        }

//        if($inform_customer == 1){
//            $mail_data = array(
//                'from_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//                'from_email' => Auth::user()->email,
//                'reply_to_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//                'reply_to_email' => Auth::user()->email,
//                'to_email' => $customer_contact->username,
//                'subject' => $mail_subject,
//                'mail_body' => $comment,
//                'file_path' => $filepath,
//                'signature' => Auth::user()->signature,
//                'mail_to'  => $mail_to,
//                'order' => $order
//            );
//            Mail::send(array('text' => 'emails.orders'), $mail_data, function($message) use ($mail_data){
//                //$message->from($mail_data['from_email'],$mail_data['from_name']);
//                $message->replyTo($mail_data['reply_to_email']);
//                foreach($mail_data['mail_to'] as $mail_to_address){
//                    $message->to($mail_to_address);
//                }
//                //$message->to($mail_data['to_email']);
//                $message->cc($mail_data['from_email']);
//                $message->subject($mail_data['subject']);
//                if($mail_data['file_path'] != ""){
//
//                    $o = $mail_data['order'];
//
//                    if($o->status_id == 7){
//                        $message->bcc('account@oemserv.hk');
//                    }
//
//                    if(file_exists($mail_data['file_path'])){
//                        $message->attach($mail_data['file_path']);
//                    }
//                }
//            });
//        }

        // If record_file is set, create the file based on
        // Order status, and save it

        // If notify_customer is set, inform the customer

//        $record = New OrderHistory;
//        $record->order_id = $order_id;
//        $record->date_added = date("Y-m-d");
//        $record->notify_customer = $inform_customer;
//        $record->record_file = $record_file;
//        $record->file_name = $filename;
//        $record->comment = $comment;
//        $record->username = Auth::user()->username;
//        $record->order_status_id = $order->status_id;
//        $record->created_by = Auth::user()->id;
//        $record->save();
//
//        if($preserve_current_order_status){
//            $order->status_id = $original_order_status;
//        }
//        $order->save();
//
//        return redirect("/orders/records/$order_id")
//            ->with('flash_success','Operation success');
//    }



    public function store(Request $request)
    {
        //
    }

    public function postLineItemAdd(Request $request) {

//        return $request;
        $rules = array(
            'product_id' => 'required|integer|digits_between:1,6',
            'quantity' => 'required|integer'
        );

        $validation = Validator::make($request->all(), $rules);

        $order   = Order::findOrFail($request->order_id);

        $uid = Auth::user()->id;
        $sup = Auth::user()->superior_id;

        if($order->created_by != $uid){
            if($order->customer->salesman_id != $uid){
                if(!has_role('company_admin')){
                    return Redirect::to('orders/'.$request->order_id)
                        ->with('flash_error','Permission Denied')
                        ->withErrors($validation->Messages())
                        ->withInput();
                }
            }
        }
        if($validation->fails()){
            return redirect('orders/'.$request->order_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $customer = $order->customer;
            $product = Product::findOrFail($request->product_id);

            $oi = OrderItem::where('order_id',$order->id)->where('product_id',$product->id)->get();

            if(!$oi){
            }
            $order->line_no += 1;
            $order_item = New OrderItem();
            $order_item->line_no = $order->line_no;
            $order_item->order_id = $order->id;
            $order_item->product_id = $product->id;
            $order_item->product_code = $product->product_code;
            $order_item->product_name = $product->product_name;
            $order_item->quantity += $request->get('quantity',1);
            $order_item->unit_price_net = $product->getSalePrice($order,$customer,$order->currency_code);

            if($order->container->code == '40hq'){
                $order_item->base_price = $product->sales_base_40;
                $order_item->pack_unit = $product->pack_unit_hq;
                $order_item->units_per_pallette = $product->units_per_pallette_hq;
                $order_item->cbm = ($product->carton_size_w_hq * $product->carton_size_d_hq * $product->carton_size_h_hq);
            } else {
                $order_item->base_price = $product->sales_base_20;
                $order_item->pack_unit = $product->pack_unit;
                $order_item->units_per_pallette = $product->units_per_pallette;
                $order_item->cbm = ($product->carton_size_w * $product->carton_size_d * $product->carton_size_h);
            }

            $order_item->save();

            $order->save();

            updateOrderStatus($order->id);

            return redirect()->back()
                ->with('flash_success','Operation success');
        }
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

        $created_by_user = User::find($order->created_by)->username;
        $updated_by_user =User::find($order->updated_by)->username;
//        return $created_by_user;

        return view('orders.show',compact('select_status','select_customer_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','order','select_taxcodes','select_containers','customer','total_cost','total_sales',
            'commission_percent','commission','profit','profit_percent', 'created_by_user','updated_by_user'));
    }

    public function getLineItemUpdate($line_item_id){

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

        return view('orders..lineItem.edit_line_item',compact('select_status','select_customer_contacts','select_payment_terms','select_currency_codes',
            'select_shipping_methods' ,'select_shipping_terms','order','customer','line_item','product_code','product_name'));
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

    public function postLineItemUpdate(Request $request,$line_item_id)
    {
        $oi = OrderItem::findOrFail($line_item_id);
        $order = Order::findOrFail($oi->order_id);

        $uid = Auth::user()->id;
        $sup = Auth::user()->superior_id;
        $rules = array(
            'order_id' => 'required|integer',
            'id' => 'required|integer',
            'quantity' => 'required|integer',
            'remarks' => ''
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if ($order->created_by != $uid) {
            if ($order->customer->salesman_id != $uid) {
                if (!has_role('company_admin')) {
                    return redirect()->back()
                        ->with('flash_error', 'Permission Denied')
                        ->withErrors($validation->Messages())
                        ->withInput();
                }
            }
        }

        if ($validation->fails()) {
            return redirect()->back()
                ->with('flash_error', 'Operation failed')
                ->witherrors($validation->messages())
                ->withinput();
        } else {
//            $customer = Customer::findOrFail($order->customer_id);
            $oi->fill($input);
            $oi->save();

            updateOrderStatus($order->id);
            return redirect('/orders/'.$order->id)
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

    public function deletePayment($id){

        $record = CustomerPayment::findOrFail($id);
        $order = Order::findOrFail($record->order_id);

        $record->delete();

        updateOrderStatus($order->id);

        Session::flash('deleted_post','The post has been taken care of');

        return redirect('orders/payments/'.$order->id)
            ->with('flash_success','Operation success');
    }

    public function lineItemDelete($line_item_id){

        $line_item = OrderItem::findOrFail($line_item_id);
        $order = Order::findOrFail($line_item->order_id);


            $line_item->delete();

            updateOrderStatus($order->id);

            return redirect('orders/'.$order->id)
                ->with('flash_success','Operation success');
//        }
    }

}