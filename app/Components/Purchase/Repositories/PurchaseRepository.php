<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/30
 * Time: 8:47 PM
 */

namespace App\Components\Purchase\Repositories;


use App\Models\Product;
use App\Models\Purchase;
use App\Models\Taxcode;
use App\Models\ValueList;
use App\Models\Vendor;
use App\Models\ChartOfAccount;
use App\Models\PurchaseHistory1;
use App\User;
use Auth;

class PurchaseRepository
{
    /**
     * @return mixed
     */
    private $products;
    private $purchases;
    private $vendors;

    public function getPurchaseDataById($id){
        $this->purchases = Purchase::findOrFail($id);
        return $this->purchases;
    }

    public function getPurchaseOrderData(){
        $this->purchases = Purchase::Leftjoin('vendors','vendors.id','=','purchases.vendor_id')
            ->select(
                'purchases.id',
                'purchases.status',
                'purchases.date_placed',
                'purchases.date_required',
                'vendors.company_name',
                'purchases.currency_code',
                'purchases.gross_total'
            )->where('purchases.company_id',return_company_id())->get();
        return $this->purchases;
    }
    public function getPurchaseProduct(){
        $this->products = Product::select(
            array(
                'products.id',
                'products.product_code',
                'products.product_name',
                'products.pack_unit',
                'products.pack_unit_hq'
            ))
            ->where('products.status','Active')
            ->where('products.company_id',return_company_id())->get();
        return $this->products;
    }
    public function getVendorListData(){
        $this->vendors = Vendor::Select(
            array(
                'vendors.id',
                'vendors.company_name'
            ))
            ->where('vendors.company_id',return_company_id())
            ->where('status','Active')->get()
        ;
        return $this->vendors;
    }

    public function getPurchasesDetail($id){
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

        return compact('select_status','select_vendor_contacts',
            'select_payment_terms','select_currency_codes','select_shipping_methods','select_shipping_terms',
            'select_users','select_taxcodes','purchase','vendor','created_by_user','updated_by_user');
    }

    public function getProductReceive($id){
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
        return compact(['select_status',$select_status],['select_vendor_contacts',$select_vendor_contacts],
            ['select_payment_terms',$select_payment_terms],['select_currency_codes',$select_currency_codes],['select_shipping_methods',$select_shipping_methods],
            ['select_shipping_terms',$select_shipping_terms],['purchase', $purchase],['vendor',$vendor]);
    }


    public function getPayments($id){
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

        return compact('select_status','select_vendor_contacts','select_payment_terms',
            'select_currency_codes','select_shipping_methods', 'select_shipping_terms','select_accounts','select_bank_accounts',
            'purchase','open_balance','vendor');
    }

    public function getEmailRecords($id){
        $purchase = Purchase::findOrFail($id);
        $vendor   = Vendor::findOrFail($purchase->vendor_id);

        $purchase_history = PurchaseHistory1::where('purchase_id',$id)
            ->orderBy('id','DESC')
            ->get();


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

        $created_by_user = User::find($purchase->created_by)->username;
        $updated_by_user = User::find($purchase->updated_by)->username;

        return compact('mail_to','mail_cc','mail_bcc','mail_subject','mail_body','purchase','vendor','purchase_history','created_by_user','updated_by_user');
    }
}