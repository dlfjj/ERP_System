<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDelivery;
use App\Models\Vendor;
use App\Models\User;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\ChartOfAccount;
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

        return view('purchases.show',compact('select_status','select_vendor_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','select_taxcodes','purchase','vendor'));
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

//    Receive Modules within Purchase
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
        $select_accounts = printSelect($tree,setting_get('purchase_account_id'),'account_id');

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

//        $this->layout->module_title = "Details";
//        $this->layout->module_sub_title = "Details";
        return view('purchases.records',compact('mail_to','mail_cc','mail_bcc','mail_subject','mail_body','purchase','vendor'));
//        $this->layout->content = View::make('purchases.history')
//            ->with('mail_to',$mail_to)
//            ->with('mail_cc',$mail_cc)
//            ->with('mail_bcc',$mail_bcc)
//            ->with('mail_subject',$mail_subject)
//            ->with('mail_body',$mail_body)
//            ->with('purchase',$purchase)
//            ->with('vendor',$vendor);
    }
}
