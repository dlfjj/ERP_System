<?php

namespace App\Http\Controllers;// add controller namespace
use App\Models\CustomerAddress;
use  Auth;//add auth facade to access authorised users
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;// addbase controller class
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\Customer;//import customer model
use App\Models\CustomerGroup;//import custoemr group
use App\Models\User;//import user  model
use App\Models\CustomerContact;//import customer contact
use App\Models\PaymentTerm;//import payment term model
use App\Models\ValueList;//import value list model
use App\Models\Taxcode;//import taxcode model
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\Order;//import order model
use App\Models\Product;//import order model

class CustomerController extends BaseController {

    public function __construct(){
        $this->middleware('auth');
        //change default filter to middleware
         // has_role('customers',1);
    }

    public $layout = 'layouts.default';

    public function anyDatatable(){
        $customers = Customer::select(
            array(
				        'customers.status',
                'customers.id',
                'customers.customer_code',
                'customers.customer_name',
                'customers.inv_city',
                'customers.inv_country'
            ))
            ->where('customers.company_id',return_company_id())
        ;

        return Datatables::of($customers)
        ->remove_column('id')
        ->add_column('operations','<ul class="table-controls"><li><a href="/customer/getShow/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->make();
    }

	public function _getOutstandingBalance($currency_code="USD"){
		$invoices = Invoice::whereIn('status',array('UNPAID','PARTIAL'))
			->get();
		$total = 0;
		foreach($invoices as $invoice){
			$total += convert_currency($invoice->currency_code,$currency_code,$invoice->total_gross);
			foreach($invoice->payments as $payment){
				$total -= convert_currency($payment->currency_code,$currency_code,$payment->amount);
			}
		}
		return $total;
	}


	public function getIndex() {
		$customers = Customer::where('customers.company_id',return_company_id())->get();

		$outstanding_balance_currency_code = "USD";
		$outstanding_balance_amount = 0; //$this->_getOutstandingBalance($outstanding_balance_currency_code);
    //change view returning syntax
    return view('customers.index',compact('customers','outstanding_balance_currency_code','outstanding_balance_amount'));
        // $this->layout->content = View::make('customers.index')
        //     ->with('outstanding_balance_currency_code', $outstanding_balance_currency_code)
        //     ->with('outstanding_balance_amount', $outstanding_balance_amount)
        //     ->with('customers', $customers)
        // ;
	}

	public function postCreate() {
        $customer = New Customer;
        $customer->created_by = Auth::user()->id;
        $customer->updated_by = Auth::user()->id;
        $customer->company_id = return_company_id();
        $customer->taxcode_id = 100;
        $customer->currency_code = "USD";
        $customer->save();

        $id = $customer->id;
        return Redirect::to('customer/getShow/'.$id)
            ->with('flash_success','Operation success');
	}

	public function postDuplicate($id){
		$original = Customer::findOrFail($id)->toArray();
		$duplicate = new Customer();
		$duplicate->fill($original);
		$duplicate->customer_name = "";
		$duplicate->id = null;
		$duplicate->status = "Prospect";
		$duplicate->save();

        $id = $duplicate->id;
        return Redirect::to('customer/getShow/'.$id)
            ->with('flash_success','Operation success');
	}

	public function getProducts($id) {
	    $customer = Customer::findOrFail($id);
        $select_groups   = CustomerGroup::where('company_id',return_company_id())
            ->pluck('group','id')
        ;
	    $select_users = User::where('company_id',return_company_id())->pluck('username','id');
	    $select_contacts = CustomerContact::where('customer_id',$customer->id)->pluck('contact_name','id');

        if($customer->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }

		$start_year = 2015;

		$years = range($start_year,date("Y"));

		$customer_products = [];

		$orders = Order::where('company_id', return_company_id())
			->where('customer_id',$customer->id)
			->get();

		foreach($orders as $order){
			foreach($order->items as $order_item){
				$order_year = date("Y",strtotime($order->order_date));
				if(!in_array($order_year, $years)){ continue; }
				if(!isset($customer_products[$order_item->product_id][$order_year])){
					foreach($years as $year){
						$customer_products[$order_item->product_id][$year] = 0;
					}
				} else {
					$customer_products[$order_item->product_id][$order_year] += $order_item->quantity;
				}
			}
		}
        foreach($customer_products as $product_id=>$products){
          $product = Product::find($product_id);  
        }
        $created_user = User::find($customer->created_by)->pluck('username');
        $updated_user = User::find($customer->created_by)->pluck('username');
		$select_payment_terms  = PaymentTerm::orderBy('name', 'asc')->pluck('name','name');
		$select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');
		$select_taxcodes  	   = Taxcode::orderBy('sort_no', 'asc')->pluck('name','id');

		$outstanding_currency  = Auth::user()->company->currency_code;

		$outstandings 		   = $customer->getOutstandingMoney($outstanding_currency);

		$overdue_currency  = Auth::user()->company->currency_code;
		$overdue 		= $customer->getOverdueMoney($outstanding_currency);
    return view('customers.products',compact('select_currency_codes','select_payment_terms','select_taxcodes','select_groups','select_users','select_contacts','customer','outstandings','outstanding_currency ','overdue','overdue_currency','years','customer_products','product','created_user','updated_user'));
      //   $this->layout->content = View::make('customers.products')
			// ->with('select_currency_codes', $select_currency_codes)
			// ->with('select_payment_terms',$select_payment_terms)
			// ->with('select_taxcodes',$select_taxcodes)
			// ->with('select_groups',$select_groups)
			// ->with('select_users',$select_users)
			// ->with('select_contacts',$select_contacts)
      //       ->with('customer',$customer)
			// ->with('outstandings',$outstandings)
			// ->with('outstanding_currency',$outstanding_currency)
			// ->with('overdue',$overdue)
			// ->with('overdue_currency',$overdue_currency)
			// ->with('years',$years)
			// ->with('customer_products',$customer_products)
      //   ;
	}

	public function getShow($id) {
	    $customer = Customer::where('id',$id)->first();

        $select_groups   = CustomerGroup::where('company_id',return_company_id())
            ->pluck('group','id')
        ;

	    $select_users = User::where('company_id',return_company_id())->pluck('username','id');
	    $select_contacts = CustomerContact::where('customer_id',$customer->id)->pluck('contact_name','id');


        if($customer->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }


		$select_payment_terms  = PaymentTerm::orderBy('name', 'asc')->pluck('name','name');
		$select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');
		$select_taxcodes  	   = Taxcode::orderBy('sort_no', 'asc')->pluck('name','id');
        // $outstanding_currency = '';//pending
		$outstanding_currency  = User::Leftjoin('companies','users.company_id','=','companies.id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');
    // print_R($outstanding_currency[0]);die;
		$outstandings 		   = $customer->getOutstandingMoney($outstanding_currency[0]);
     // print_r("hlo".$outstandings);die;
        // $overdue_currency = '';//pending
		$overdue_currency  = User::Leftjoin('companies','users.company_id','=','companies.id')->where('users.id',Auth::user()->id)->pluck('companies.currency_code');
		$overdue 		= $customer->getOverdueMoney($outstanding_currency[0]);
    // print_r("hlo".$overdue);die;
    $created_by_user = User::select('username')->where('created_by',$customer->created_by)->first();
    $updated_by_user = User::select('username')->where('updated_by',$customer->created_by)->first();
        // echo "<pre>";
        // print_r($select_payment_terms);die;

   //      $this->layout->content = View::make('customers.show')
			// ->with('select_currency_codes', $select_currency_codes)
			// ->with('select_payment_terms',$select_payment_terms)
			// ->with('select_taxcodes',$select_taxcodes)
			// ->with('select_groups',$select_groups)
			// ->with('select_users',$select_users)
			// ->with('select_contacts',$select_contacts)
   //          ->with('customer',$customer)
			// ->with('outstandings',$outstandings)
			// ->with('outstanding_currency',$outstanding_currency)
			// ->with('overdue',$overdue)
			// ->with('overdue_currency',$overdue_currency)

   //      ;
        return view('customers.show',compact('select_currency_codes','select_payment_terms','select_taxcodes','select_groups','select_users','select_contacts','customer','outstandings','overdue','overdue_currency','created_by_user','updated_by_user','outstanding_currency'));
	}

	public function update(Request $request, $id) {
        $rules = array(
            'customer_name' => 'Required|Between:1,150',
            'customer_name_localized' => 'Between:1,50',
            'currency_code' => 'required|alpha|between:3,3',
            'status' => 'Required|Between:1,50',
            'telephone_1' => 'between:1,50',
            'telephone_2' => 'between:1,50',
            'fax' => 'between:1,50',
            'currency_code' => 'required|between:3,3'
        );
        $input = $request->all();

        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('customer/getShow/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $customer = Customer::findOrFail($id);

			if($customer->status != $request->get('status')){
				if(!has_role('customers_change_status')){
					return Redirect::to('customer/getShow/'.$id)
						->with('flash_error','Operation failed - Status change denied')
						->withErrors($validation->Messages())
						->withInput();
				}
			}

            $customer->fill($input);

			$bill_to = "";
			if($customer->address1 != ""){
				$bill_to .= $customer->address1;
				$bill_to .= "\n";
			}
			if($customer->address2!= ""){
				$bill_to .= $customer->address2;
				$bill_to .= "\n";
			}
			if($customer->postal_code != "" || $customer->city != ""){
				$bill_to .= $customer->postal_code . " " . $customer->city;
				$bill_to .= "\n";
			}
			if($customer->province != ""){
				$bill_to .= $customer->province . ",";
			}
			if($customer->country != ""){
				$bill_to .= $customer->country;
			}
			$customer->bill_to = $bill_to;

            $customer->save();
            return Redirect::to('customer/getShow/'.$id)
                ->with('flash_success','Operation success');
        }
	}

	public function postDestroy($id) {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return Redirect::to('customer/')
            ->with('flash_success','Operation success');
	}

	public function postContactAdd(Request $request) {
		$customer_id = $request->get('customer_id');
        $rules = array(
			'customer_id' => 'Required|Between:1,50',
            'contact_name' => 'Required|Between:1,50',
            'username' => 'Required|Email|Between:1,50',
            'contact_mobile' => 'Between:1,50',
            'contact_skype' => 'nullable|Between:1,50',
            'position' => 'nullable|Between:1,50'
        );
        $input = $request->all();

        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return Redirect::to('customer/getShow/'.$customer_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
			$new_record = New CustomerContact;
            $new_record->fill($input);
            $new_record->save();
			return Redirect::to('customer/getShow/'.$customer_id)
				->with('flash_success','Operation success');
		}
	}

	public function getContactEdit($id) {
		$customer_contact = CustomerContact::findOrFail($id);
		$customer = Customer::findOrFail($customer_contact->customer_id);

		$select_payment_terms  = ValueList::where('uid','=','PAYMENT_TERMS')->orderBy('name', 'asc')->pluck('name','name');
		$select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');

        return view('customers.edit_customer_contact',compact('select_currency_codes','select_payment_terms','customer','customer_contact'));
	}

	public function postContactEdit(Request $request, $id) {
		$customer_contact = CustomerContact::findOrFail($id);
		$customer = Customer::findOrFail($customer_contact->customer_id);

        $rules = array(
            'id' => 'Required|integer',
            'customer_id' => 'required|integer',
            'contact_name' => 'required',
            'contact_email' => 'email',
            'reset_password' => 'nullable|min:10'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/customer/contact-edit/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {

            $new_password = "";
            if($request->has('reset_password')){
                $new_password = $request->get('reset_password');
                if($new_password != ""){
                    $new_password = Hash::make($new_password);
                }
            }
            unset($input['reset_password']);

            $customer_contact->fill($input);
            if($new_password != ""){
                $customer_contact->password = $new_password;
            }
            $customer_contact->save();

            return Redirect::to('/customer/getShow/'.$customer->id)
                ->with('flash_success','Operation success');
        }
	}

	public function postContactDelete($contact_id) {
        $contact = CustomerContact::findOrFail($contact_id);
        $customer_id = $contact->customer_id;
        $contact->delete();
        return Redirect::to('customer/getShow/'.$customer_id)
            ->with('flash_success','Operation success');
	}

	public function postAddressAdd(Request $request) {
		$customer_id = $request->get('customer_id');
        $rules = array(
			'customer_id' => 'Required|Between:1,50',
			'description' => 'Required|Between:1,50',
            'address1' => 'Required|Between:1,50',
            'address2' => 'nullable|Between:1,50',
            'city' => 'Required|Between:1,50',
            'postal_code' => 'Between:1,50',
            'province' => 'Required|Between:1,50',
            'country' => 'Required|Between:1,50'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return Redirect::to('customer/getShow/'.$customer_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
			$new_record = New CustomerAddress;
            $new_record->fill($input);
            $new_record->save();
			return Redirect::to('customer/getShow/'.$customer_id)
				->with('flash_success','Operation success');
		}
	}

	public function getAddressEdit($id) {
		$customer_address = CustomerAddress::findOrFail($id);
		$customer = Customer::findOrFail($customer_address->customer_id);

		$select_payment_terms  = ValueList::where('uid','=','PAYMENT_TERMS')->orderBy('name', 'asc')->pluck('name','name');
		$select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');

        return view('customers.edit_customer_address',compact('select_currency_codes','select_payment_terms','customer','customer_address'));
	}

	public function postAddressEdit(Request $request, $id) {
		$customer_address = CustomerAddress::findOrFail($id);
		$customer = Customer::findOrFail($customer_address->customer_id);

        $rules = array(
			'id' => 'required|integer',
			'customer_id' => 'Required|Between:1,50',
			'description' => 'Required|Between:1,50',
            'address1' => 'Required|Between:1,50',
            'address2' => 'nullable|Between:1,50',
            'city' => 'Required|Between:1,50',
            'postal_code' => 'Between:1,50',
            'province' => 'Required|Between:1,50',
            'country' => 'Required|Between:1,50'
        );

        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/customer/address-edit/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $customer_address->fill($input);
            $customer_address->save();

            return Redirect::to('/customer/getShow/'.$customer->id)
                ->with('flash_success','Operation success');
        }
	}

	public function postAddressDelete($address_id) {
        $address = CustomerAddress::findOrFail($address_id);
        $customer_id = $address->customer_id;
        $address->delete();
        return Redirect::to('customer/getShow/'.$customer_id)
            ->with('flash_success','Operation success');
	}

    public function anyDtOrders($customer_id){
        $orders = Order::Leftjoin('customers','orders.customer_id','=','customers.id')
            ->Leftjoin('order_status','orders.status_id','=','order_status.id')
			->select(
            array(
				'orders.id',
				'orders.order_no',
                'order_status.name',
                'orders.order_date',
                'orders.customer_order_number',
                'orders.currency_code',
                'orders.total_gross',
                'orders.total_paid'
            ))
            ->where("orders.customer_id",$customer_id)
            ->where('orders.company_id',return_company_id())
        ;
        return Datatables::of($orders)
			->add_column('operations','<ul class="table-controls"><li><a href="/orders/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
            ->edit_column('total_paid', function($row){
                return round($row->total_gross - $row->total_paid,2);
            })
			->remove_column('id')
			->make();
    }

	public function getHistory($id) {
	    $customer = Customer::where('id',$id)->first();
        // echo Auth::user()->company_id;die;
       $orders_history = Order::Leftjoin('customers','orders.customer_id','=','customers.id')
            ->Leftjoin('order_status','orders.status_id','=','order_status.id')
            ->select(
            array(
                'orders.id',
                'orders.order_no',
                'order_status.name',
                'orders.order_date',
                'orders.customer_order_number',
                'orders.currency_code',
                'orders.total_gross',
                'orders.total_paid'
            ))
            ->where("orders.customer_id",$id)
            ->where('orders.company_id')->get();
            // echo "<pre>";
            // print_R($orders_history);die;
        // $this->layout->module_title = "";
        // $this->layout->module_sub_title = "";
        return view('customers.history',compact('customer'));
 //        $this->layout->content = View::make('customers.history')
 //            ->with('customer',$customer);
	}

	public function getChangelog($id){
	    $customer = Customer::findOrFail($id);

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('customers.changelog')
            ->with('customer',$customer);
	}

    public function getPricelist($id){
		$customer = Customer::findOrFail($id);
        print_R($customer);die;
        if($customer->company_id != return_company_id()){
            die("Access violation!");
        }

		$group    = $customer->group;

        $customer_specifics = ProductCustomerSpecific::where('customer_id','!=',$id)
            ->pluck('product_id');

		$products = Product::orderBy('pricelist_sort')->orderBy('product_code')
			->where('is_visible','=',1)
            ->where('company_id',return_company_id())
			->where('status','Active')
			->get();


		$phpexcel = new PHPExcel;
		$phpexcel->setActiveSheetIndex(0);

		$worksheet = $phpexcel->getActiveSheet();
		$worksheet->setTitle('Pricelist');

		$styleArray = array(
			'font' => array(
				'bold' => true
		));

		$worksheet->setCellValue('A1', "Confidential Pricelist, Generated " . date('Y-m-d') . "");
		$worksheet->getStyle('A1')->applyFromArray($styleArray);

		$worksheet
			->setCellValue('A3', 'Category / Description')
			->setCellValue('B3', 'Code')
			->setCellValue('C3', 'Size')
			->setCellValue('D3', 'PU')
			->setCellValue('E3', 'PU HQ')
			->setCellValue('F3', 'Units/Pallette')
			->setCellValue('G3', 'Price 20')
			->setCellValue('H3', 'Price 40');

		$worksheet->getStyle('A3')->applyFromArray($styleArray);
		$worksheet->getStyle('B3')->applyFromArray($styleArray);
		$worksheet->getStyle('C3')->applyFromArray($styleArray);
		$worksheet->getStyle('D3')->applyFromArray($styleArray);
		$worksheet->getStyle('E3')->applyFromArray($styleArray);
		$worksheet->getStyle('F3')->applyFromArray($styleArray);
		$worksheet->getStyle('G3')->applyFromArray($styleArray);
		$worksheet->getStyle('H3')->applyFromArray($styleArray);

		//$worksheet->getColumnDimension('A')->setWidth((int) 20);
		//$worksheet->getColumnDimension('B')->setWidth((int) 20);

		$rowNumber = 2;
		$old_category_name = '';
		foreach($products as $product){
			$category_name = "";
			if($product->category_id > 0){
				$category = Category::where('id',"=",$product->category_id)->first();
				foreach($category->getAncestorsAndSelf() as $ancestor){
					$category_name .= $ancestor->name . " ";
				}
				$category_name = trim($category_name);
			}
			if($category_name == ""){
				"Undefined";
			}

            // Continue if this is a customer specific product,
            // we dont need it in the group pricelist
            if(in_array($product->id, $customer_specifics)){
                continue;
            }

			if($category_name != $old_category_name){
				$rowNumber++;
				$old_category_name = $category_name;
				$worksheet
				->setCellValue('A'.$rowNumber, $category_name);
				$worksheet->getStyle('A'.$rowNumber)->applyFromArray($styleArray);
				$rowNumber++;
			}

			$customer_group_price = ProductPrice::where("customer_group_id","=",$customer->group_id)->where("product_id","=",$product->id)->first();
			if($customer_group_price){
				if(!is_numeric($product->sales_base_20) || !is_numeric($customer_group_price->surcharge_20) || $customer_group_price->surcharge_20 == 0){
					print "Issue with Product ID: $product->id - price calculation issue. Contact Sales Dept. Manager";
					exit;
				}
				$price_20 = round($product->sales_base_20 / $customer_group_price->surcharge_20,2);
				$price_40 = round($product->sales_base_40 / $customer_group_price->surcharge_40,2);
			} else {
				$price_20 = round($product->sales_base_20 / $group->surcharge_20,2);
				$price_40 = round($product->sales_base_40 / $group->surcharge_40,2);
			}

            $customer_override = ProductPriceOverride::where('product_id',$product->id)
                ->where('customer_id',$id)
                ->where('company_id',$customer->company_id)
                ->first();

            if($customer_override){
                $price_20 = $customer_override->base_price_20;
                $price_40 = $customer_override->base_price_40;
            }

			$worksheet
			->setCellValue('A'.$rowNumber, $product->product_name)
			->setCellValue('B'.$rowNumber, $product->product_code)
			->setCellValue('C'.$rowNumber, $product->size)
			->setCellValue('D'.$rowNumber, $product->pack_unit)
			->setCellValue('E'.$rowNumber, $product->pack_unit_hq)
			->setCellValue('F'.$rowNumber, $product->units_per_pallette)
			->setCellValue('G'.$rowNumber, $price_20)
			->setCellValue('H'.$rowNumber, $price_40);

			//$worksheet->getStyle('M'.$rowNumber)->getNumberFormat()->setFormatCode('00000000');
			$rowNumber++;
		}

		autoFitColumnWidthToContent($worksheet,'A','H');

		// Add Disclaimer
		$rowNumber++;
		$rowNumber++;
		$worksheet->getRowDimension($rowNumber)->setRowHeight(90);
		$worksheet->setCellValue('A'.$rowNumber, "Note:\nAll prices are valid upon receive of this list.\nAll previous price list are not longer valid.\nWe reserve the right of not to be responsible for the topically,\ncorrectness, completeness or quality of the information provided.\nAll offers are not-binding and without obligation.");
		$worksheet->getStyle('A'.$rowNumber)->getAlignment()->setWrapText(true);
		$worksheet->getStyle('A'.$rowNumber)->applyFromArray($styleArray);

		$file_name = "pl_" . $id;

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename={$file_name}.xlsx");
		header('Cache-Control: max-age=0');
		ob_clean();
		flush();

		$objWriter = new PHPExcel_Writer_Excel2007($phpexcel);
		$objWriter->save('php://output');
		exit;
	}

    public function getOpos($id){
        has_role('customers_opos',1);

        if(!is_numeric($id)){
            die("Invalid Customer ID");
        }

        $customer = Customer::findOrFail($id);

        if($customer->company_id != return_company_id()){
            die("Access violation!");
        }

        $orders = Order::where('customer_id',$id)
            ->whereIn('status_id',[5,6,7])
            ->where('company_id',return_company_id())
            ->get();

		$phpexcel = new PHPExcel;
		$phpexcel->setActiveSheetIndex(0);

		$worksheet = $phpexcel->getActiveSheet();
		$worksheet->setTitle('default');

		$styleArray = array(
			'font' => array(
			'bold' => true
		));

        $char = 'A';

		$worksheet
			->setCellValue($char++.'1', 'Order#')
			->setCellValue($char++.'1', 'C.O.N')
			->setCellValue($char++.'1', 'Order Date')
			->setCellValue($char++.'1', 'Payment Term')
			->setCellValue($char++.'1', 'Est. Finish Date')
			->setCellValue($char++.'1', 'Amount')
			->setCellValue($char++.'1', 'Open Balance')
			->setCellValue($char++.'1', 'Payment Date')
			->setCellValue($char++.'1', 'Payment Type')
			->setCellValue($char++.'1', 'Payment Amount')
			->setCellValue($char++.'1', 'Bank Charges')
			->setCellValue($char++.'1', 'Remarks')
        ;

		$rowNumber = 2;

		foreach($orders as $order){
            $days_overdue = $order->getDaysOverdue();
            $due_date = $order->getDueDate();

            if($order->payments->count() > 0){
                foreach($order->payments as $payment){
                    $char = 'A';
                    $worksheet
                    ->setCellValue($char++.$rowNumber, $order->order_no)
                    ->setCellValue($char++.$rowNumber, $order->customer_order_number)
                    ->setCellValue($char++.$rowNumber, $order->order_date)
                    ->setCellValue($char++.$rowNumber, $order->paymentterm->name)
                    ->setCellValue($char++.$rowNumber, $order->estimated_finish_date)
                    ->setCellValue($char++.$rowNumber, $order->total_gross)
                    ->setCellValue($char++.$rowNumber, $order->total_gross - $order->total_paid)
                    ->setCellValue($char++.$rowNumber, $payment->date)
                    ->setCellValue($char++.$rowNumber, $payment->type)
                    ->setCellValue($char++.$rowNumber, $payment->amount)
                    ->setCellValue($char++.$rowNumber, $payment->bank_charges)
                    ->setCellValue($char++.$rowNumber, $payment->remark)
                    ;
                    $rowNumber++;
                }
            } else {
                    $char = 'A';
                    $worksheet
                    ->setCellValue($char++.$rowNumber, $order->order_no)
                    ->setCellValue($char++.$rowNumber, $order->customer_order_number)
                    ->setCellValue($char++.$rowNumber, $order->order_date)
                    ->setCellValue($char++.$rowNumber, $order->paymentterm->name)
                    ->setCellValue($char++.$rowNumber, $order->estimated_finish_date)
                    ->setCellValue($char++.$rowNumber, $order->total_gross)
                    ->setCellValue($char++.$rowNumber, $order->total_gross - $order->total_paid)
                    ;
                    $rowNumber++;
            }
		}

		autoFitColumnWidthToContent($worksheet,'A',$char);

		$file_name = "opos_" . date("Y-m-d");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename={$file_name}.xlsx");
		header('Cache-Control: max-age=0');
		ob_clean();
		flush();

		$objWriter = new PHPExcel_Writer_Excel2007($phpexcel);
		$objWriter->save('php://output');
		exit;
	}

}
