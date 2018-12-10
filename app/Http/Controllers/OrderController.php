<?php

namespace App\Http\Controllers;
use App\BankCharges;
use App\Components\Order\Repositories\OrderRepository;
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
    private $orderRepository;

    public function __construct(OrderRepository $OrderRepository){

        $this->middleware('auth');
        has_role('orders',1);

        $this->orderRepository = $OrderRepository;
    }


    public function index()
    {

        $outstanding_balance_currency_code = "USD";
        $outstanding_balance_amount = 0; //$this->_getOutstandingBalance($outstanding_balance_currency_code);

        return view('orders.index', compact('outstanding_balance_currency_code','outstanding_balance_amount'));

    }

    public function getOrderData(){


        return Datatables::of($this->orderRepository->getOrderDataForIndex())
            ->addColumn('action', function ($order) {
                return '<a href="/orders/'.$order->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
            })
            ->make(true);
    }

    public function customersList(){
        return view('orders.customersList');
    }

    public function getCustomerslist(){

        return Datatables::of($this->orderRepository->getCustomerList())
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

        return Datatables::of($this->orderRepository->getProductList())
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

        return view('orders.payments',$this->orderRepository->getPaymentById($id));
    }

    public function getRecords($id)
    {

        return view('orders.records', $this->orderRepository->getEmailRecordByOrderId($id));
    }

    public function getChangelog($id){
        $order = Order::findOrFail($id);

        return view('orders.changelog',compact('order'));

    }


    //do not have invoice modules yet
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

        $order = $this->orderRepository->getOrderById($id);
        $customer = $this->orderRepository->getCustomerByOrderId($order->customer_id);

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
            'bank_charges' =>'numeric|nullable'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $uid = Auth::user()->id;
//        $sup = Auth::user()->superior_id;


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
            $payment->created_by    = Auth::user()->id;
            $payment->account_id    = $request->input('account_id',176);
            $payment->remark        = $remark;
            $payment->date          = $request->input('date_created');
            $payment->save();


            //after created a payment record, record the bank_charges
            if($request->bank_charges == null){
                $request->bank_charges = 0;
            }
            $bank_charge = new BankCharges();

            $bank_charge->customer_payment_id = $payment->id;
            $bank_charge->created_by = Auth::user()->id;
            //set the account categories for accounting module, integrate it to the setting module later
            $bank_charge->account_id = 186;
            $bank_charge->amount = (float) $request->bank_charges;

            $bank_charge->save();

//            $payment = CustomerPayment::findorfail($payment->id);
            $payment->bank_charges  = $bank_charge->id;
            $payment->save();

            updateOrderStatus($id);

            return redirect('orders/payments/'.$id)
                ->with('flash_success','Operation success');
        }
    }



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
        return view('orders.show',$this->orderRepository->getOrderDetialData($id));
    }

    public function getLineItemUpdate($line_item_id){

        return view('orders..lineItem.edit_line_item',$this->orderRepository->getLineItemUpdateData($line_item_id));
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

        $order = $this->orderRepository->getOrderById($id);

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
        $bank_charge_record = BankCharges::findOrFail($record->bank_charges);
        $order = Order::findOrFail($record->order_id);

        $bank_charge_record->delete();
        $record->delete();

        updateOrderStatus($order->id);

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