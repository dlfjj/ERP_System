<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;
use App\Models\VendorContact;
use Illuminate\Http\Request;
use Validator;
use View;
use App\Models\Vendor;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\User;

class VendorController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

    public $layout = 'layouts.default';

    public function index()
    {
//        $vendors = Vendor::all();


        return view('vendors.index');
    }
    public function getVendorData()
    {
        $vendors = Vendor::all();

        return Datatables::of($vendors)->addColumn('action', function ($vendor) {
            return '<a href="/vendors/'.$vendor->id .'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
        })->make(true);
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->company_id != return_company_id()) {
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }

        $select_payment_terms = ValueList::where('uid', '=', 'payment_terms')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_currency_codes = ValueList::where('uid', '=', 'currency_codes')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_taxcodes = Taxcode::orderBy('sort_no', 'asc')->pluck('name', 'id');
        $created_by_user = User::select('username')->where('created_by', $vendor->created_by)->first();
        $updated_by_user = User::select('username')->where('updated_by', $vendor->created_by)->first();

        return view('vendors.show', compact('select_currency_codes', 'select_payment_terms', 'select_taxcodes', 'vendor', 'created_by_user', 'updated_by_user'));
    }

    public function update(Request $request, int $id)
    {
        $rules = array(
            'company_name' => 'required|between:1,50',
            'company_name_localized' => 'nullable|between:1,50',
            'status' => 'required|between:1,50',
            'email' => 'nullable|email'
        );
        $input = $request->all();

        $validation = Validator::make($input, $rules);

        if ($validation->fails()) {
            return $this->redirectWithErrors('vendors/' . $id, $validation->getMessageBag()->getMessages(), ['flash_error', 'Operation failed']);
        } else {
            $vendor = Vendor::findOrFail($id);
            $vendor->fill($input);
            $vendor->save();

            return $this->redirectWithSuccessMessage('vendors/' . $id);
        }
    }

    public function getContact(int $id, int $contactId)
    {
        $vendor_contact = VendorContact::findOrFail($contactId);

        if ($vendor_contact->vendor_id != $id) {
            return $this->redirectWithErrors('vendors/' . $id, [], ['flash_error', 'Invalid vendor contact.']);
        }

        $vendor = Vendor::findOrFail($id);

        return view('vendors.edit_vendor_contact', compact('vendor', 'vendor_contact'));
    }

    public function addContact(Request $request, int $id)
    {
        $rules = array(
            'vendor_id' => 'required|between:1,50',
            'name' => 'required|between:1,50',
            'email' => 'required|email|between:1,50',
            'mobile' => 'required|between:1,50',
            'skype' => 'between:1,50',
            'position' => 'between:1,50'
        );

        $input = $request->all();
        $validation = Validator::make($input, $rules);
        if ($validation->fails()) {
            return $this->redirectWithErrors('vendors/' . $id, $validation->getMessageBag()->getMessages());
        } else {
            VendorContact::create($input);

            return $this->redirectWithSuccessMessage('vendors/' . $id);
        }
    }

    public function updateContact(Request $request, int $id, int $contactId)
    {
        $vendor_contact = VendorContact::findOrFail($contactId);
        $vendor = Vendor::findOrFail($id);

        if ($vendor_contact->vendor_id != $id) {
            return $this->redirectWithErrors('vendors/' . $id, [], ['flash_error', 'Invalid vendor contact.']);
        }

        $rules = array(
            'id' => 'Required|integer',
            'vendor_id' => 'required|integer',
            'name' => 'required',
            'email' => 'email'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if ($validation->fails()) {
            return $this->redirectWithErrors('/vendors/' . $id, $validation->getMessageBag()->getMessages(), 'flash_error', 'Operation failed');
        } else {
            $vendor_contact->fill($input);
            $vendor_contact->save();

            return $this->redirectWithSuccessMessage('/vendors/' . $vendor->id);
        }
    }

    public function deleteContact(int $id, int $contactId) {
        $contact = VendorContact::findOrFail($contactId);
        $contact->delete();

        return $this->redirectWithSuccessMessage('vendors/' . $id);
    }

    /** ========================================================================================================= */
    /** ========================================================================================================= */

    public function anyDatatable(){
        $vendors = Vendor::select(
            array(
				'vendors.status',
                'vendors.id',
                'vendors.company_name',
                'vendors.company_name_localized',
                'vendors.city',
                'vendors.country'
            ))
            ->where('vendors.company_id',return_company_id())
        ;
        return Datatables::of($vendors)
        ->remove_column('id')
        ->add_column('operations','<ul class="table-controls"><li><a href="/vendors/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->make();
    }

	public function createNew() {
        $select_payment_terms = ValueList::where('uid', '=', 'payment_terms')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_currency_codes = ValueList::where('uid', '=', 'currency_codes')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_taxcodes = Taxcode::orderBy('sort_no', 'asc')->pluck('name', 'id');

        return view('vendors.create', compact('select_payment_terms', 'select_currency_codes', 'select_taxcodes'));

    }

	public function store(Request $request)
    {
        $input = $request->all();
        $input['company_id'] = return_company_id();

        $vendor = Vendor::create($input);

        return $this->redirectWithSuccessMessage('/vendors/' . $vendor->id);
    }

    public function anyDtPurchases($vendor_id){
        $vendor = Vendor::find($vendor_id);
        if($vendor->company_id != return_company_id()){
            die("Access violation");
        }

        $purchases = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
			->Leftjoin('products','purchase_items.product_id','=','products.id')
			->select(
            array(
				'purchases.id',
                'purchases.status',
                'purchases.date_placed',
                'products.product_code',
                'purchases.currency_code',
                'purchase_items.gross_total'
            ))
            ->where("purchases.vendor_id",$vendor_id);
        return Datatables::of($purchases)
			->add_column('operations','<ul class="table-controls"><li><a href="/purchases/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
			->make();
    }

	public function getHistory($id) {
	    $vendor = Vendor::findOrFail($id);

        if($vendor->company_id != return_company_id()){
            die("Access violation");
        }

        $this->layout->content = View::make('vendors.history')
            ->with('vendor',$vendor)
        ;
	}

	public function getChangelog($id){
	    $vendor = Vendor::findOrFail($id);

        if($vendor->company_id != return_company_id()){
            die("Access violation");
        }

        $this->layout->content = View::make('vendors.changelog')
            ->with('vendor',$vendor)
            ;
	}

}
