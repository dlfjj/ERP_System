<?php

namespace App\Http\Controllers;

use App\Components\Exceptions\StatusChangeDeniedException;
use App\Components\Customer\Services\CustomerService;
use App\Models\CustomerAddress;
use  Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Throwable;
use View;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\User;
use App\Models\CustomerContact;
use App\Models\PaymentTerm;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\OrderHistory;
use App\Models\Order;
use App\Models\Product;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    private $customerService;
    public $layout = 'layouts.default';

    public function __construct(CustomerService $service)
    {
        $this->middleware('auth');

        $this->customerService = $service;
    }

    public function index()
    {
        $contents = $this->customerService->getAllCustomersByCompanyId(return_company_id());
//        $customers = $this->customerService->getAllCustomersByCompanyId(return_company_id())['customers'];
//        foreach($customers as $cs){
//            return $cs->inv_country;
//
//        }
//        $customers = Customer::select(
//            array(
//                'customers.status',
//                'customers.id',
//                'customers.customer_code',
//                'customers.customer_name',
//                'customers.inv_city',
//                'customers.inv_country'
//            ))
//            ->where('customers.company_id',return_company_id())->get()
//        ;
//        return $customers;
        return view('customers.index', $contents);
    }
    public function getCustomerData()
    {
        $customers = $this->customerService->getAllCustomersByCompanyId(return_company_id())['customers'];
//        $customers = Customer::select(
//            array(
//                'customers.status',
//                'customers.id',
//                'customers.customer_code',
//                'customers.customer_name',
//                'customers.inv_city',
//                'customers.inv_country'
//            ))
//            ->where('customers.company_id',return_company_id())->get()
//        ;


        return Datatables::of($customers)->addColumn('action', function ($customer) {
                    return '<a href="/customers/'.$customer->id .'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
                })->make(true);
    }

    public function show(int $id)
    {
        $contents = $this->customerService->getOneCustomerById($id);

        return view('customers.show', $contents);
    }

    public function update(Request $request, $id)
    {
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

        try {
            $input = $request->all();
            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return $this->redirectWithErrors('customers/' . $id, $validation->getMessageBag()->getMessages());
            }

            $this->customerService->update($id, $input, $request->get('status'));
            return Redirect::to('customers/' . $id)
                ->with('flash_success', 'Operation success');
        } catch (StatusChangeDeniedException $e) {
            return Redirect::to('customers/' . $id)
                ->with('flash_error', 'Operation failed - Status change denied')
                ->withErrors($validation->getMessageBag()->getMessages())
                ->withInput();

        } catch (Throwable $e) {
            $this->redirectWithErrors('customers/' . $id);
        }
    }

    public function createNew() {
        $contents = $this->customerService->getProductRelated(currentUserId(), currentUserCompanyId());

        return view('customers.create', $contents);
    }

    public function store(Request $request){
        $input = $request->all();

        $rules = [
            'customer_name' => 'Required|Between:1,150',
            'customer_name_localized' => 'Between:1,50',
            'currency_code' => 'required|alpha|between:3,3',
            'status' => 'Required|Between:1,50',
            'telephone_1' => 'between:1,50',
            'telephone_2' => 'between:1,50',
            'fax' => 'between:1,50',
            'currency_code' => 'required|between:3,3',
            'inv_address1' => 'required',
            'inv_city' => 'required',
            'inv_province' => 'required',
            'inv_fax' => 'required',
            'group_id' => 'required',
            'status' => 'required',
            'salesman_commission' => 'required',
            'ff_name' => 'required',
        ];

        try {
            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return $this->redirectWithErrors('customers/create', $validation->getMessageBag()->getMessages());
            }

            $customer = Customer::create($input);

            return $this->redirectWithSuccessMessage('/customers/' . $customer->id);
        } catch (StatusChangeDeniedException $e) {
            return Redirect::to('customers/create')
                ->with('flash_error', 'Operation failed - Status change denied')
                ->withErrors($validation->getMessageBag()->getMessages())
                ->withInput();

        } catch (Throwable $e) {
            dd($e);
            $this->redirectWithErrors('customers/create');
        }
    }

    public function getContact(int $id, int $contactId)
    {
        $contents = $this->customerService->getContactById($contactId);

        return view('customers.edit_customer_contact', $contents);
    }

    public function updateContact(Request $request, int $id, int $contactId)
    {
        $rules = array(
            'id' => 'Required|integer',
            'customer_id' => 'required|integer',
            'contact_name' => 'required',
            'contact_email' => 'email',
            'reset_password' => 'nullable|min:10'
        );

        try {
            $input = $request->all();
            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return $this->redirectWithErrors('/customers/'.$id.'/contacts/' . $contactId, $validation->getMessageBag()->getMessages());
            }

            $customerId = $this->customerService->updateContactById($contactId, $input);

            return Redirect::to('/customers/' . $customerId)
                ->with('flash_success', 'Operation success');
        } catch (Throwable $e) {
            return $this->redirectWithErrors('customers/' . $id);
        }
    }

    public function getAddress(int $id, int $addressId)
    {
        $contents = $this->customerService->getAddressById($addressId);

        return view('customers.edit_customer_address', $contents);
    }

    public function updateAddress(Request $request, int $id, int $addressId)
    {
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

        try {
            $input = $request->all();
            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return $this->redirectWithErrors('/customers/'.$id.'/addresss/' . $addressId, $validation->Messages());
            }

            $customerId = $this->customerService->updateAddressById($addressId, $input);

            return Redirect::to('/customers/' . $customerId)
                ->with('flash_success', 'Operation success');
        } catch (Throwable $e) {
            $this->redirectWithErrors('customers/' . $id);
        }
    }

    public function addContact(Request $request, int $id) {
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
            return Redirect::to('customers/'.$customer_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $new_record = New CustomerContact;
            $new_record->fill($input);
            $new_record->save();

            return redirect()->back()->with('flash_success','Operation success');
//            $this->redirectWithSuccessMessage('customers/'.$customer_id);
        }
    }

    public function deleteContact(int $id, int $contactId) {
        $contact = CustomerContact::findOrFail($contactId);
        $customer_id = $contact->customer_id;
        $contact->delete();

        return $this->redirectWithSuccessMessage('customers/'.$customer_id);
    }

    public function addAddress(Request $request, int $id) {
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
            return Redirect::to('customers/'.$customer_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $new_record = New CustomerAddress;
            $new_record->fill($input);
            $new_record->save();
            return Redirect::to('customers/'.$customer_id)
                ->with('flash_success','Operation success');
        }
    }

    public function deleteAddress(int $id, int $addressId) {

        $address = CustomerAddress::findOrFail($addressId);
        $customer_id = $address->customer_id;
        $address->delete();
        return Redirect::to('customers/'.$customer_id)
            ->with('flash_success','Operation success');

    }
    /** ========================================================================================================== */
    /** ========================================================================================================== */
    /** ========================================================================================================== */

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
        ->add_column('operations','<ul class="table-controls"><li><a href="/customers/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
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

//	Product tab within the customer modules
	public function getProducts($id) {
	    $customer = Customer::findOrFail($id);
        $select_groups   = CustomerGroup::where('company_id',return_company_id())->pluck('group','id');
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
        $created_user = User::find($customer->created_by)->username;
		$updated_user = User::find($customer->updated_by)->username;
		$select_payment_terms  = PaymentTerm::orderBy('name', 'asc')->pluck('name','name');
		$select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');
		$select_taxcodes  	   = Taxcode::orderBy('sort_no', 'asc')->pluck('name','id');

		$outstanding_currency  = Auth::user()->company->currency_code;

		$outstandings 		   = $customer->getOutstandingMoney($outstanding_currency);

		$overdue_currency  = Auth::user()->company->currency_code;
		$overdue 		= $customer->getOverdueMoney($outstanding_currency);
        return view('customers.products.show',compact('select_currency_codes','select_payment_terms','select_taxcodes','select_groups','select_users','select_contacts','customer','outstandings','outstanding_currency ','overdue','overdue_currency','years','customer_products','product','created_user','updated_user'));
	}

    public function getRelatedProducts($id){

        $orders = Order::where('customer_id', $id)->get();

        $product = '';

        return \DataTables::of($products)
            ->addColumn('action', function ($product) {
                return '<a href="/products/'.$product->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';})
            ->make(true);
    }

	public function postDestroy($id) {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return Redirect::to('customers/')
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
