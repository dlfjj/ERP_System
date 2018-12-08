<?php

namespace App\Http\Controllers;

use App\Components\Purchase\Repositories\PurchaseRepository;
use App\Components\Purchase\Services\PurchaseService;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDelivery;
use App\Models\Vendor;
use App\Models\User;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\ChartOfAccount;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;

class PurchaseController extends Controller
{
    private $purchaseRepository;
    private $purchaseService;

    public function __construct(PurchaseRepository $PurchaseRepository, PurchaseService $PurchaseService){
        $this->middleware('auth');
        has_role('purchases',1);

        $this->purchaseRepository = $PurchaseRepository;
        $this->purchaseService = $PurchaseService;
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

    public function getPurchaseData(){

        try {
            return Datatables::of($this->purchaseRepository->getPurchaseOrderData())
                ->addColumn('action', function ($purchase) {
                    return '<a href="/purchases/' . $purchase->id . '" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
                })
                ->make(true);
        } catch (\Exception $e) {
            echo $e;
            return redirect('/home');
        }
    }

    public function anyDtAvailableProducts(){

        try {
            return Datatables::of($this->purchaseRepository->getPurchaseProduct())
                ->removeColumn('id')
                ->addColumn('action', function ($product) {
                    return \Form::open(['method' => 'POST', 'action' => ['PurchaseController@postLineItemAdd'], 'class' => 'form']) . '
                <input type="hidden" name="product_id" value="' . $product->id . '" />
                <input type="number" class="qty_picker_input" name="quantity" value="" step="1" min="0" size="3"/>
                <input type="submit" name="submit" value="Add" class="btn pull-right add_this_item" />
                ' . \Form::close();
                })->make(true);
        } catch (\Exception $e) {
            echo $e;
            return redirect('/home');
        }
    }

    //    before creating a new purchase, you will get a list of vendor
    public function vendorsList() {
        return view('purchases.vendorsList');
    }

    public function getVendorslist(){
        //Anonymous and callback function
        try {
            return Datatables::of($this->purchaseRepository->getVendorListData())
                ->addColumn('action', function ($vendor) {
                    return \Form::open(['method' => 'GET', 'action' => 'PurchaseController@create', 'class' => 'form']) . '
                    <input type="hidden" name="id" value="' . $vendor->id . '" />
                    <input type="submit" name="submit" value="Choose" class="btn center-block" />
                    ' . \Form::close();
                })
                ->make(true);
        } catch (\Exception $e) {
            echo $e;
            return redirect('/home');
        }
    }

    public function getVendorslistChange($id){

        try {
            return Datatables::of($this->purchaseRepository->getVendorListData(),$id)
                ->addColumn('action', function ($vendor) use ($id) {
                    return \Form::open(['method' => 'POST', 'action' => ['PurchaseController@postChangeVendor',$id], 'class' => 'form']) . '
                    <input type="hidden" name="id" value="' . $vendor->id . '" />
                    <input type="submit" name="submit" value="Choose" class="btn center-block" />
                    ' . \Form::close();
                })
                ->make(true);
        } catch (\Exception $e) {
            return $this->redirectWithErrors($e);
        }
    }


    public function getReceive($id) {

//        $user =User::where('id',$delivery->created_by)->first()->username;

        return view('purchases.receive',$this->purchaseRepository->getProductReceive($id));

    }

    public function getChangeStatus($id) {

        $purchase =  $this->purchaseRepository->getPurchaseDataById($id);

        return view('purchases.change_status',compact('purchase'));

    }


    public function getPayments($id) {

        return view('purchases.payments', $this->purchaseRepository->getPayments($id));

    }

    public function getRecords($id){

        return view('purchases.records',$this->purchaseRepository->getEmailRecords($id));

    }

    public function getDuplicate($purchase_id){

        return Redirect::to("/purchases/".$this->purchaseService->getDuplicate($purchase_id))
            ->with('flash_success','Operation success');
    }

    public function getChangeVendor($id){

        $purchase = $this->purchaseRepository->getPurchaseDataById($id);

        return view('purchases.change_vendor', compact('purchase'));

    }


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

    public function postChangeVendor(Request $request, $id){

        $this->purchaseService->postChangeVendor($request,$id);

        return Redirect::to('purchases/'.$id)
            ->with('flash_success','Operation success');
    }

    public function postPaymentAdd(Request $request,$id)
    {
        $rules = array(
            'amount' => 'required|numeric',
            'account_id' => 'required|numeric',
            'currency_code' => 'required|max:255',
            'date_created' => 'required|date',
            'transaction_reference' => 'max:255',
            'remark' => 'max:255',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to("/purchases/payments/".$id)
                ->with('flash_error','Operation failed')
                ->witherrors($validation->messages())
                ->withinput();
        } else {

            $purchase = Purchase::findOrFail($id);

            if($purchase->company_id != return_company_id()){
                die("Access Violation");
            }

            if($request->transaction_reference === NULL) {
                $transaction_reference = '';
            }else{
                $transaction_reference = $request->transaction_reference;
            }

            $new = new PurchasePayment();
            $new->fill($input);
            $new->transaction_reference = $transaction_reference;
            $new->created_by = Auth::user()->id;
            $new->updated_by = Auth::user()->id;
            $new->purchase_id= $purchase->id;
            $new->company_id = return_company_id();
            $new->save();

            updatePurchaseStatus($id);

            return Redirect::to("/purchases/payments/".$id)
                ->with('flash_success','Operation success');
        }
//        $this->purchaseService->postPaymentAdd($request,$id);
    }

        public function postLineItemAdd(Request $request){

        $purchase_id = $request->purchase_id;
        $purchase = $this->purchaseRepository->getPurchaseDataById($purchase_id);

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

    public function postChangeStatus(Request $request, $purchase_id) {

        $purchase = $this->purchaseRepository->getPurchaseDataById($purchase_id);
        $new_status = $request->status;
        $status_allowed = array("VOID");

        #warehouse_transaction(0,1100,$product_id,Input::get('quantity',0),$purchase);
        #warehouse_transaction(1100,0,$line_item->product_id,$line_item->quantity,$purchase);

        if(!in_array($new_status,$status_allowed)){
            die("Error - New Status not allowed");
        }

        if($new_status == "VOID"){
            if($purchase->deliveries->count() > 0 || $purchase->payments->count() > 0){
                return Redirect::to('purchases/'.$purchase_id)
                    ->with('flash_error','Operation failed');
            }
            /*
                foreach($purchase->items as $purchase_item){
                    warehouse_transaction(1100,0,$purchase_item->product_id,$purchase_item->quantity,$purchase);
                }
            */
        }

        /*
        if($purchase->date_required == "0000-00-00"){
            return Redirect::to("/orders/show/{$id}")
                ->with('flash_error','Invalid Required Date');
        }
        */

        $purchase->status = $new_status;
        $purchase->save();

        return Redirect::to("/purchases/{$purchase_id}")
            ->with('flash_success','Operation success');
    }

    public function postReceive(Request $request, $purchase_id){

//        $purchasedeliveries = PurchaseDelivery::where('purchase_id','=',$purchase_id)->get();

        $purchase_item_ids 	= $request->purchase_item_ids;
        $delivered_quantities 	= $request->delivered_quantities;
        $reconciled_quantities  = $request->reconciled_quantities;
        $remark 			= $request->remark;
        $date               = $request->date;

        if(array_sum($delivered_quantities) + array_sum($reconciled_quantities) == 0){
            return redirect("/purchases/receive/{$purchase_id}")
                ->with('flash_error',"Nothing to do");
        }

        if(!is_array($purchase_item_ids)){
            return Redirect::to("/purchases/receive/{$purchase_id}")
                ->with('flash_error','Invalid Data');
        }

        if($remark == null){
            return redirect()->back()
                ->with('flash_error','Remark cannot let it blank');
        }

        $uid = time();

        $purchase = Purchase::findOrFail($purchase_id);

        // Don't allow processing goods unless P.O has the right status
        if(strstr($purchase->status,"DRAFT") != FALSE){
            return Redirect::to("/purchases/receive/{$purchase_id}")
                ->with('flash_error','P.O Status does not allow processing goods');
        }

        // Loop trough data and do validation
        foreach($purchase_item_ids as $i=> $purchase_item_id){

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($this->purchaseRepository->getPurchaseDataById($id)->company_id != return_company_id()){
            die("Access violation. Click <a href='/purchases'>here</a> to get back.");
        }

        return view('purchases.show',$this->purchaseRepository->getPurchasesDetail($id));
    }

    public function showLineItemAdd($id){

        return view('purchases.lineItem.add_line_item',$this->purchaseRepository->getPurchasesDetail($id));

    }

    public function getLineItemUpdate($line_item_id){

        return view('purchases.lineItem.edit_line_item', $this->purchaseRepository->getLineItemUpdateData($line_item_id));
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
            $purchase = $this->purchaseRepository->getPurchaseDataById($id);
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

        $purchase_item = $this->purchaseRepository->getPurchaseItemDataById($purchase_item_id);
        $purchase = $this->purchaseRepository->getPurchaseDataById($purchase_item->purchase_id);

        /*
            if($purchase->status != "DRAFT"){
                return Redirect::to("/purchases/show/{$purchase->id}")
                    ->with('flash_error','Operation requires status DRAFT');
            }
        */

//        $today = date("Y-m-d");

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

        $purchase_item = $this->purchaseRepository->getPurchaseItemDataById($purchase_item_id);
        $purchase = $this->purchaseRepository->getPurchaseDataById($purchase_item->purchase_id);

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

    public function getDeliveryDelete($id){

        $delivery = $this->purchaseRepository->getPurchaseDeliveryDataById($id);
        $purchase_id = $delivery->purchase_id;
        $purchase = $this->purchaseRepository->getPurchaseDataById($purchase_id);
        $purchase_item = $this->purchaseRepository->getPurchaseItemDataById($delivery->purchase_item_id);

        if(!has_role('admin')){
            if($delivery->created_by != Auth::user()->id){
                return Redirect::to('purchases/receive/'.$purchase->id)
                    ->with('flash_error','User Issue');
            }
        }
//        $quantity_delivered  = $purchase_item->getQuantityDelivered();
//        $quantity_reconciled = $purchase_item->getQuantityReconciled();

        if($delivery->delivered > 0){
            warehouse_transaction($purchase_item->product_id,-$delivery->delivered,"(Received - P.O " . $purchase_id . ")");
        }


        $delivery->delete();

        updatePurchaseStatus($purchase_id);

        return Redirect::to('purchases/receive/'.$purchase_id)
            ->with('flash_success','Operation success');
    }

    public function getPaymentDelete($id){

        return Redirect::to('purchases/payments/'.$this->purchaseService->getPaymentDelete($id))
            ->with('flash_success','operation success');
    }

//    Receive Modules within Purchase

}
