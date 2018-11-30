<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDelivery;
use App\Models\Vendor;
use App\Models\User;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\ChartOfAccount;
use App\Models\Product;
use Yajra\Datatables\Datatables;
use Validator;
use Auth;

class PurchaseController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        has_role('purchases',1);
    }
    public $layout = 'layouts.default';

    public function index($index_type=0)
    {
//        $purchase_index_msg = "Purchase Index";
//        return view('purchases.index',compact('purchase_index_msg','index_type'));


        /*
            $index_type 0 == Default
            $index_type 1 == Waiting for Approval
            $index_type 2 == Waiting for Confirmation
            $index_type 3 == Delivery overdue
            $index_type 4 == P.O supposed to be incoming in the next 3 days
            $index_type 5 == P.O placed TODAY
        */



        $purchase_index_msg = "Purchases Record";
        return view('purchases.index',compact(array('purchase_index_msg',$purchase_index_msg),array('index_type',$index_type)));
    }

    public function getPurchaseData($index_type=0){
        $purchases = Purchase::Leftjoin('vendors','vendors.id','=','purchases.vendor_id')
            ->select(
                'purchases.id',
                'purchases.status',
                'purchases.date_placed',
                'purchases.date_required',
                'vendors.company_name',
                'purchases.currency_code',
                'purchases.gross_total'
            )->where('purchases.company_id',return_company_id());
        return Datatables::of($purchases)
            ->addColumn('action', function ($purchase) {
                return '<a href="/purchases/'.$purchase->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
            })
            ->make(true);
    }

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
            ->where('products.company_id',return_company_id());
        return Datatables::of($products)
            ->removeColumn('id')
            ->addColumn('action',function($product){
                return \Form::open(['method'=>'POST','action'=>['PurchaseController@postLineItemAdd'],'class'=>'form']).'
            <input type="hidden" name="product_id" value="'.$product->id.'" />
            <input type="number" class="qty_picker_input" name="quantity" value="" step="1" min="0" size="3"/>
            <input type="submit" name="submit" value="Add" class="btn pull-right add_this_item" />
            '.\Form::close();
            })->make(true);
    }

    //    before creating a new purchase, you will get a list of vendor
    public function vendorsList() {
        return view('purchases.vendorsList');
    }

    public function getVendorslist(){
        $vendors = Vendor::Select(
            array(
                'vendors.id',
                'vendors.company_name'
            ))
            ->where('vendors.company_id',return_company_id())
            ->where('status','Active')
        ;

        return Datatables::of($vendors)
            ->addColumn('action',function($vendor){
                return \Form::open(['method'=>'GET','action'=>'PurchaseController@create','class'=>'form']).'
				<input type="hidden" name="id" value="'.$vendor->id.'" />
				<input type="submit" name="submit" value="Create" class="btn center-block" />
		        '.\Form::close();
            })
            ->make(true);
    }


    public function getReceive($id) {
        $purchase = Purchase::findOrFail($id);

        $vendor = Vendor::findOrFail($purchase->vendor_id);

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendor_contacts = $vendor->contacts->pluck('name','name');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );
//        $user =User::where('id',$delivery->created_by)->first()->username;

        return view('purchases.receive',compact(['select_status',$select_status],['select_vendor_contacts',$select_vendor_contacts],
            ['select_payment_terms',$select_payment_terms],['select_currency_codes',$select_currency_codes],['select_shipping_methods',$select_shipping_methods],
            ['select_shipping_terms',$select_shipping_terms],['purchase', $purchase],['vendor',$vendor]));

    }
    public function postReceive(Request $request, $purchase_id){

        return $request;
        $purchasedeliveries = PurchaseDelivery::where('purchase_id','=',$purchase_id)->get();

//        return $purchasedeliveries[0];

        $purchase_item_ids 	= $request->purchase_item_ids;
        $delivered_quantities 	= $request->delivered_quantities;
        $reconciled_quantities  = $request->reconciled_quantities;
        $remark 			= $request->remark;
//        $date               = Input::get('date',date('Y-m-d'));
        $date               = $request->date;

//        return $request;
        if(array_sum($delivered_quantities) + array_sum($reconciled_quantities) == 0){
            return redirect("/purchases/receive/{$purchase_id}")
                ->with('flash_error',"Nothing to do");
        }

        if(!is_array($purchase_item_ids)){
            return Redirect::to("/purchases/receive/{$purchase_id}")
                ->with('flash_error','Invalid Data');
        }

        $uid = time();

        $purchase = Purchase::findOrFail($purchase_id);

        // Don't allow processing goods unless P.O has the right status
        if(strstr($purchase->status,"DRAFT") != FALSE){
            return Redirect::to("/purchases/receive/{$purchase_id}")
                ->with('flash_error','P.O Status does not allow processing goods');
        }

        // Loop trough data and do validation
        foreach($purchase_item_ids as $i=>$purchase_item_id){
            $delivered              = $delivered_quantities[$i];
            $reconciled             = $reconciled_quantities[$i];

            if($delivered < 0 || $reconciled < 0){
                return Redirect::to("/purchases/receive/{$purchase_id}")
                    ->with('flash_error',"Please do not enter negative values");
            }

        }

        // Prepare to process and stock move
        foreach($purchase_item_ids as $i=>$purchase_item_id){
            $purchase_item = PurchaseItem::findOrFail($purchase_item_id);
            if(!is_numeric($delivered_quantities[$i])){
                $delivered_quantities[$i] = 0;
            }

            $order_quantity         = $purchase_item->quantity;
            $delivered              = $delivered_quantities[$i];
            $reconciled             = $reconciled_quantities[$i];

            $new = new PurchaseDelivery();
            $new->created_by		= Auth::user()->id;
            $new->updated_by		= Auth::user()->id;
            $new->uid				= $uid;
            $new->purchase_item_id 	= $purchase_item->id;
            $new->purchase_id		= $purchase_item->purchase_id;
            $new->product_id		= $purchase_item->product_id;
            $new->delivered         = $delivered;
            $new->reconciled        = $reconciled;
            $new->remarks			= $remark;
            $new->created           = $date;
            $new->save();

            if(is_numeric($delivered)){
                warehouse_transaction($purchase_item->product_id,$delivered,"Received - P.O " . $new->purchase_id);
            }
            if(is_numeric($reconciled)){
                //warehouse_transaction($purchase_item->product_id,$reconciled,"Reconciled - P.O " . $new->purchase_id);
            }

            $quantity_delivered = $purchase_item->getQuantityDelivered();
            $quantity_reconciled = $purchase_item->getQuantityReconciled();
            $quantity_open 		= $order_quantity - $quantity_delivered - $quantity_reconciled;
        }

        // Update PO Status marker
        updatePurchaseStatus($purchase_id);

        return redirect("/purchases/receive/{$purchase_id}")
            ->with('flash_success','Operation success');
    }

    public function getPayments($id) {

        $purchase = Purchase::findOrFail($id);
        $vendor = Vendor::findOrFail($purchase->vendor_id);

        $select_bank_accounts  = ValueList::where('uid','=','BANK_ACCOUNTS')->orderBy('name', 'asc')->pluck('name','name');

        $open_balance = $purchase->total;
        foreach($purchase->payments as $payment){
            $payment_amount = convert_currency($payment->currency_code,$purchase->currency_code,$payment->amount,$payment->payment_date);
            $open_balance -= $payment_amount;
        }
        $open_balance = round($open_balance,2);
        if($open_balance < 0){
            $open_balance = 0;
        }



        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendor_contacts = $vendor->contacts->pluck('name','name');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );

        $tree = ChartOfAccount::where('company_id',return_company_id())->get()->toHierarchy();
        $select_accounts = printSelect($tree, setting_get('purchase_account_id'),'account_id');

//        $this->layout->module_title = "vendor Details";
//        $this->layout->module_sub_title = "vendor Details";

        return view('purchases.payments',compact('select_status','select_vendor_contacts','select_payment_terms',
            'select_currency_codes','select_shipping_methods', 'select_shipping_terms','select_accounts','select_bank_accounts',
            'purchase','open_balance','vendor'));

    }

    public function getRecords($id){
        $purchase = Purchase::findOrFail($id);
        $vendor   = Vendor::findOrFail($purchase->vendor_id);

        $mail_to  = $purchase->vendor->email;
        $mail_cc  = "";
        $mail_bcc = Auth::user()->email;
        $mail_subject = "PO #$purchase->id";
        $mail_body = <<<EOT
Hello $purchase->vendor_contact,

please find your purchase order #$purchase->id attached.

Let me know if you have any questions,
EOT;
        $mail_body .= "\n".$purchase->user->signature;

        return view('purchases.records',compact('mail_to','mail_cc','mail_bcc','mail_subject','mail_body','purchase','vendor'));

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */




//    create new purchase
    public function create(Request $request)
    {
        $rules = array(
            'id' => 'Required|integer'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect('purchases/vendorsList')
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $vendor_id = $request->id;
            $vendor = Vendor::findOrFail($vendor_id);

            if($vendor->taxcode_id == ""){
                return redirect('purchases/vendorsList')
                    ->with('flash_error','Illegal Taxcode');
            }

            $purchase = New Purchase();
            $purchase->purchase_no = getNewPurchaseNo(return_company_id());
            $purchase->vendor_id   = $vendor_id;
            $purchase->status      = 'Draft';
            $purchase->currency_code = $vendor->currency_code;
            $purchase->payment_terms = $vendor->payment_terms;
            $purchase->created_by  = Auth::user()->id;
            $purchase->updated_by = Auth::user()->id;
            $purchase->user_id = Auth::user()->id;
            $purchase->date_placed = date("Y-m-d");
            $purchase->taxcode_id = $vendor->taxcode_id;
            $purchase->company_id = return_company_id();
            $purchase->save();

            $id = $purchase->id;
            return redirect('purchases/'.$id)
                ->with('flash_success','Operation success');
        }
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

    public function postLineItemAdd(Request $request){

        $purchase_id = $request->purchase_id;
        $purchase = Purchase::findOrFail($purchase_id);

        $rules = array(
            'product_id' => 'required|integer|digits_between:1,6',
            'quantity'   => 'required|numeric'
        );
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return redirect('purchases/'.$purchase_id)
                ->with('flash_error','Validation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {

            $product_id = $request->product_id;
            $product = Product::findOrFail($product_id);

            $purchase_item = New PurchaseItem();
            $purchase_item->purchase_id = $purchase->id;
            $purchase_item->product_id = $product->id;
            $purchase_item->quantity = $request->get('quantity',1);
            $purchase_item->gross_price = $product->base_price_20;
            $purchase_item->net_price   = return_net_price($purchase_item->gross_price, $purchase->taxcode->percent);
            $purchase_item->net_total = $purchase_item->quantity * $purchase_item->net_price;
            $purchase_item->gross_total = $purchase_item->quantity * $purchase_item->gross_price;
            $purchase_item->save();

            $purchase->save();

            updatePurchaseStatus($purchase_id);

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
        $purchase = Purchase::findOrFail($id);

        $vendor = Vendor::findOrFail($purchase->vendor_id);

        if($purchase->company_id != return_company_id()){
            die("Access violation. Click <a href='/purchases'>here</a> to get back.");
        }

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


        return view('purchases.show',compact('select_status','select_vendor_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','select_taxcodes','purchase','vendor','created_by_user','updated_by_user'));
    }

    public function showLineItemAdd($id){

        $purchase = Purchase::findOrFail($id);
        $vendor = Vendor::findOrFail($purchase->vendor_id);

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendor_contacts = $vendor->contacts->pluck('name','name');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "VOID"
        );
        return view('purchases.lineItem.add_line_item',compact('select_status','select_vendor_contacts','select_payment_terms',
            'select_currency_codes','select_shipping_methods','select_shipping_terms','purchase','vendor'));

    }

    public function getLineItemUpdate($line_item_id){
        $line_item = PurchaseItem::findOrFail($line_item_id);
        $purchase = Purchase::findOrFail($line_item->purchase_id);
        $vendor = Vendor::findOrFail($purchase->vendor_id);

        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_payment_terms  = ValueList::where('uid','=','payment_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_terms = ValueList::where('uid','=','shipping_terms')->orderBy('name', 'asc')->pluck('name','name');
        $select_shipping_methods = ValueList::where('uid','=','shipping_methods')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendor_contacts = $vendor->contacts->pluck('name','name');
        $select_status = array(
            "DRAFT" => "DRAFT",
            "OPEN" => "OPEN",
            "CLOSED" => "CLOSED",
            "VOID" => "void"
        );

        return view('purchases.lineItem.edit_line_item', compact('select_currency_codes','select_payment_terms','select_shipping_terms',
            'select_shipping_terms','select_shipping_methods','select_vendor_contacts','select_status','purchase','line_item'));
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
            'id'    => 'integer'
        );
        $input = $request->all();

        $validation = Validator::make($input, $rules);

        if($validation->fails()){
//            return Redirect::to('purchases/show/'.$id)
            return redirect('purchases/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $purchase = Purchase::findOrFail($id);
            $purchase->fill($input);
            $purchase->updated_by = Auth::user()->id;
//            return $request->input('taxcode_id');
//            return $input['taxcode_id'];
            $taxcode = Taxcode::findOrFail($input['taxcode_id']);
            $purchase->taxcode_id   = $taxcode->id;
            $purchase->taxcode_name = $taxcode->name;
            $purchase->taxcode_percent = $taxcode->percent;

            $purchase->save();

            updatePurchaseStatus($id);

            return redirect('purchases/'.$id)
                ->with('flash_success','Operation success');
        }
    }

    public function postLineItemUpdate(Request $request,$purchase_item_id){
        $purchase_item = PurchaseItem::findOrFail($purchase_item_id);
        $purchase = Purchase::findOrFail($purchase_item->purchase_id);

        /*
            if($purchase->status != "DRAFT"){
                return Redirect::to("/purchases/show/{$purchase->id}")
                    ->with('flash_error','Operation requires status DRAFT');
            }
        */

        $today = date("Y-m-d");

        $rules = array(
            'purchase_id' => 'required|integer',
            'id' => 'required|integer',
            'quantity' => 'required|numeric',
            'gross_price'   => 'required|numeric',
            'sort_no' => 'integer',
            'remarks' => ''
        );

        $validation = Validator::make($request->all(), $rules);

        if($validation->fails()){
            return redirect("/purchases/update_line_item/{$purchase_item_id}")
                ->with('flash_error','Operation failed')
                ->witherrors($validation->messages())
                ->withinput();
        } else {
//            $new_quantity = $request->get('quantity',0);

            // If new_quantity < already delivered or passed, then cancel operation
            /*
            if($new_quantity < $purchase_item->quantity_open){
                return Redirect::to("/purchases/line-item-update/{$purchase_item_id}")
                    ->with('flash_error','Quantity cannot < delivered quantity');
            }
            */

            // First deduct old Quantity
            // warehouse_transaction(1100,0,$line_item->product_id,$line_item->quantity,$purchase);

//            $vendor = Vendor::findOrFail($purchase->vendor_id);
            $purchase_item->fill($request->all());
            $purchase_item->save();

            updatePurchaseStatus($purchase->id);

            return redirect('purchases/'.$purchase_item->purchase_id)
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

    public function lineItemDelete ($purchase_item_id, $purchase_id){

        $purchase_item = PurchaseItem::findOrFail($purchase_item_id);
        $purchase = Purchase::findOrFail($purchase_item->purchase_id);

        /*
        if($purchase->status != "DRAFT"){
            return Redirect::to("/purchases/show/{$purchase->id}")
                ->with('flash_error','Operation requires status DRAFT');
        }
        */

        if($purchase_item->getQuantityDelivered() > 0){
            return Redirect::to("/purchases/{$purchase->id}")
                ->with('flash_error','Cannot remove Item with deliveries');
        }

//        $input = array_map('intval',array($purchase_id, $purchase_item_id));
        $input = array('purchase_id'=>$purchase_id,'purchase_item_id'=> $purchase_item_id);
        $rules = array(
            'purchase_id' => 'required|integer',
            'purchase_item_id' => 'required|integer'
        );


        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect()->back()
                ->with('flash_error','Operation failed')
                ->witherrors($validation->messages())
                ->withinput();
        } else {
            $purchase_item->delete();

            updatePurchaseStatus($purchase->id);

            return redirect('purchases/'.$purchase_item->purchase_id)
                ->with('flash_success','Operation success');
        }
    }

//    Receive Modules within Purchase

}
