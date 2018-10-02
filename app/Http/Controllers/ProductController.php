<?php

namespace App\Http\Controllers;

use App\Components\Exceptions\StatusChangeDeniedException;
use App\Components\Product\Exceptions\MPNAlreadyExistExceptions;
use App\Components\Product\Services\ProductService;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Throwable;
use View;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\Sysmsg;
use App\Models\Category;//import category model
use App\Models\ValueList;//import value lsit
use App\Models\User;//import user model
use App\Models\ProductPrice;//impor product price model
use App\Models\Customer;//add cutomer model
use App\Models\CustomerGroup;//add cutomer model
use Illuminate\Support\Facades\Input;//  add input facade
use Illuminate\Support\Facades\Validator;//add validator faacde
use Illuminate\Support\Facades\Redirect;//add redirect facade
use Auth;
use App\Models\ProductAttribute;//import product attribute
use App\Models\ProductDownload;//import product Downloads
use App\Models\WarehouseTransaction;//import warehouse transactions
use App\Models\Company;//import company model
use App\Models\ProductImage;//import product image

class ProductController extends Controller {

    public $layout = 'layouts.default';

    private $productService;

    public function __construct(ProductService $productService){
        $this->middleware('auth');
        has_role('products',1);

        $this->productService = $productService;
    }

    public function index()
    {
//        $products = $this->productService->getAllProductsByCompanyId(return_company_id());

        return view('products.index');

//        return view('products.index', ['products' => $products]);
    }

    public function getProductData(){

        $products = $this->productService->getAllProductsByCompanyId(return_company_id());

        return Datatables::of($products)
            ->addColumn('action', function ($product) {
                return '<a href="/products/'.$product->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';})->make(true);

    }

//    public function getAttributes($id) {
//        $product = Product::findOrFail($id);
//        $attributes = ProductAttribute::where('product_id',$product->id)->orderBy('group','DESC')->orderBy('name','ASC')->get();
//        return view('products.attributes',compact('product','attributes'));
        // $this->layout->content = View::make('products.attributes')
        //     ->with('product',$product)
        // ;
//    }

//    public function getPrices($id,$product_customer_id=null) {
//        $product = Product::findOrFail($id);
//
//        if($product_customer_id != null){
//            $product_customer = ProductCustomer::findOrFail($product_customer_id);
//        } else {
//            $product_customer = null;
//        }
//
//        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
//        $select_customers = Customer::where('company_id',return_company_id())
//            ->where('status','=','ACTIVE')->orderBy('customer_name', 'asc')->pluck('customer_name','id');
//
//        $select_groups   = CustomerGroup::where('company_id',return_company_id())
//            ->pluck('group','id')
//        ;
//        $group_prices = ProductPrice::where('product_id',$product->id)->where('company_id',return_company_id())->orderBy('customer_group_id','DESC')->get();
//
//        return view('products.prices',compact('product','product_customer','select_groups','select_customers','select_currency_codes','group_prices'));
//    }




    public function show($id)
    {
//        $product = Product::findOrFail($id);
//
//        return $product->priceOverrides->sortBy('customer_id')->first();
//
//        foreach($product->priceOverrides->sortBy('customer_id') as $customer){
//        return $customer->customer;
//    }
        $contents = $this->productService->getProductById($id);

        return view('products.show', $contents);
    }

    public function update(Request $request, int $id)
    {

        $rules = array(
            'id' => 'required|integer|digits_between:1,6',
            'category_id' => 'required|integer|digits_between:1,6',
            'description' => 'nullable|between:1,5000',
            'packing_instructions' => 'nullable|between:1,5000',
            'package' => 'nullable|between:1,50',
            'uom' => 'nullable|:1,50',
            'pack_unit' => 'required|integer|digits_between:1,6|min:1',
            'unit_mc' => 'integer|digits_between:1,6',
            'unit_ic' => 'integer|digits_between:1,6',
            'ic_per_mc' => 'integer|digits_between:1,6',
            'net_weight_unit' => 'nullable|numeric|digits_between:1,50',
            'gross_weight_unit' => 'nullable|numeric|digits_between:1,50',
            'origin' => 'nullable|between:1,50',
            'commodity_code' => 'nullable|between:1,50',
            'sales_moq' => 'nullable|numeric|digits_between:1,6',
            'sales_currency_code' => 'nullable|alpha|between:3,3',
            'sales_price' => 'nullable|numeric|digits_between:1,12'
        );

        try {
            $input = $request->all();
            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return $this->redirectWithErrors('products/' . $id, $validation->getMessageBag()->getMessages());
            }

            $this->productService->update($id, $input, $request);

            return $this->redirectWithSuccessMessage('products/' . $id);
        } catch (StatusChangeDeniedException $e) {
            return $this->redirectWithErrors('products/' . $id, $validation->getMessageBag()->getMessages());
        } catch (MPNAlreadyExistExceptions $e) {
            return $this->redirectWithErrors('products/' . $id, [], ['flash_error', 'MPN already exists']);
        } catch (Throwable $e) {
            return $this->redirectWithErrors('products/' . $id);
        }
    }

    /* ==================================================================================================== */
    /* ==================================================================================================== */

    public function anyDtIndex(){
        $products = Product::select(
                'products.id',
                'products.product_code',
                'products.status',
                'products.mpn',
                'products.product_name',
        				'products.pricelist_sort',
        				'products.stock'
            )
            ->where('products.company_id',return_company_id())
            ;
        return Datatables::of($products)
        ->add_column('operations','<ul class="table-controls"><li><a href="/products/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->remove_column('id')
        ->make()
        ;
    }

	public function getIndex() {
    $products = Product::where('products.company_id',return_company_id())->get();
    return view('products.index',compact('products'));// change view returning syntax
        // $this->layout->content = View::make('products.index');
	}

	public function getExport() {
    return view('products.export');
        // $this->layout->content = View::make('products.export');
	}

	public function postExport(){
			$report = Input::get('action');

			$allowed = ["Export Prices","Export Product"];

			if(!in_array($report,$allowed)){
				die("Data error");
			}

			if($report == "Export Prices"){
				$this->postExportPrices();
			}

			if($report == "Export Product"){
				$this->postExportProducts();
			}


			die("End of the road");
	}

	public function postExportPrices(){
		// has_role('products_export',1);

		$sheet_name = "products";
		$file_title = "Product";
		$file_name  = "prices_" . date("Y-m-d");

		$company_id = return_company_id();

        $products = Product::where('company_id',$company_id)
            ->orderBy('product_code')->get();

		$phpexcel = new PHPExcel;
		$phpexcel->setActiveSheetIndex(0);

		$worksheet = $phpexcel->getActiveSheet();
		$worksheet->setTitle($file_title);

		$styleArray = array(
			'font' => array(
				'bold' => true
		));


        $field_names = [
            "Serial (dont change!)",
            "Product Code",
            "MPN",
			"Category ID",
			"Short Product Name",
			"Cost Price 20'",
			"Cost Price 40'",
			"Landed Factor 20'",
			"Landed Factor 40'",
			"Sales Basis 20'",
			"Sales Basis 40'"
        ];

		$customer_groups = CustomerGroup::where('company_id',$company_id)->get();

		foreach($customer_groups as $customer_group){
			$field_names[] = $customer_group->group . " Factor 20";
			$field_names[] = $customer_group->group . " Price 20";

			$field_names[] = $customer_group->group . " Factor 40";
			$field_names[] = $customer_group->group . " Price 40";
		}

        $column = 'A';
        $row  	= 1;
		foreach($field_names as $field_name){
			$worksheet->setCellValue($column++.$row,$field_name);
		}

		foreach($products as $product){
			$fields = [
				$product->id,
				$product->product_code,
				$product->mpn,
				$product->category_id,
				substr(trim($product->product_name),0,40),
				$product->base_price_20,
				$product->base_price_40,
				$product->landed_20,
				$product->landed_40,
				$product->sales_base_20,
				$product->sales_base_40,
			];

			foreach($customer_groups as $customer_group){
				$price_record = ProductPrice::where('company_id',$company_id)
					->where('product_id',$product->id)
					->where('customer_group_id',$customer_group->id)
					->first();

				if(!$price_record){
					$fields[] = "";
					$fields[] = "";
					continue;
				}

				$factor_20 = $price_record->surcharge_20;
				$factor_40 = $price_record->surcharge_40;

				$price_20  = $product->sales_base_20 / $factor_20;
				$price_40  = $product->sales_base_20 / $factor_40;

				$fields[] = round($factor_20,4);
				$fields[] = round($price_20,2);

				$fields[] = round($factor_40,4);
				$fields[] = round($price_40,2);
			}

			$column = 'A';
			$row++;
			foreach($fields as $field){
				$worksheet->setCellValue($column++.$row,$field);
			}
		}

		excel_bold_row($worksheet,1);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename={$file_name}.xlsx");
		header('Cache-Control: max-age=0');
		ob_clean();
		flush();

		$objWriter = new PHPExcel_Writer_Excel2007($phpexcel);
		$objWriter->save('php://output');
		exit;
	}

	public function postExportProducts(){
		has_role('products_export',1);

		$sheet_name = "products";
		$file_title = "Product";
		$file_name  = "products_" . date("Y-m-d");

        $products = Product::where('company_id',return_company_id())
            ->orderBy('product_code')->get();

		$phpexcel = new PHPExcel;
		$phpexcel->setActiveSheetIndex(0);

		$worksheet = $phpexcel->getActiveSheet();
		$worksheet->setTitle($file_title);

		$styleArray = array(
			'font' => array(
				'bold' => true
		));

        $field_names = [
            "Serial (dont change!)",
            "Status",
            "Product Code",
            "MPN",
            "Category ID",
            "Product Name",
            "Product Name Localized",
            "UOM",
            "Size",
            "CSC",
            "Manufacturer",
            "Unit Weight",
            "MOQ",
            "Origin",
            "Pack Unit",
            "PU/Pallet",
            'PU Nt Weight',
            'PU Gr Weight',
            'Pallet Nt Weight',
            'Ctn Width',
            'Ctn Depth',
            'Ctn Height',
            'Pallet Size',
            'Has HQ Pack?',
            'Pack Unit HQ',
            'PU/Pallet HQ',
            'PU Nt Weight HQ',
            'PU Gr Weight HQ',
            'Pallet Nt Weight HQ',
            'HQ MPN',
            'HQ Ctn Width',
            'HQ Ctn Depth',
            'HQ Ctn Height',
            'HQ Pallet Size',
            'Track Stock',
            'Min Stock',
            'Stock Location',
            'Show in Webshop / Pricelist',
            'Pricelist Sort',
            'Company Sync',
        ];

        $column = 'A';
        $row  	= 1;

		foreach($field_names as $field_name){
			$worksheet->setCellValue($column++.$row,$field_name);
		}

		$worksheet->getStyle('A1')->applyFromArray($styleArray);
		//$worksheet->getColumnDimension('B')->setWidth((int) 20);

		foreach($products as $product){
            $row++;
            $column = 'A';

			$field_values = [
				$product->id,
                $product->status,
                $product->product_code,
                $product->mpn,
                $product->category_id,
                $product->product_name,
                $product->product_name_local,
                $product->uom,
                $product->size,
                $product->commodity_code,
                $product->manufacturer,
                $product->weight,
                $product->moq,
                $product->origin,
                $product->pack_unit,
                $product->units_per_pallette,
                $product->pack_unit_net_weight,
                $product->pack_unit_gross_weight,
                $product->pallet_weight,
                $product->carton_size_w,
                $product->carton_size_d,
                $product->carton_size_h,
                $product->pallet_size,
                $product->is_hq_pack,
                $product->pack_unit_hq,
                $product->units_per_pallette_hq,
                $product->pack_unit_net_weight_hq,
                $product->pack_unit_gross_weight_hq,
                $product->pallet_weight_hq,
                $product->mpn_hq,
                $product->carton_size_w_hq,
                $product->carton_size_d_hq,
                $product->carton_size_h_hq,
                $product->pallet_size_hq,
                $product->track_stock,
                $product->stock_min,
                $product->location,
                $product->is_visible,
                $product->pricelist_sort,
                $product->company_sync
			];

			foreach($field_values as $field_value){
				$worksheet->setCellValue($column++.$row,$field_value);
			}

			//$worksheet->getStyle('M'.$rowNumber)->getNumberFormat()->setFormatCode('00000000');
		}


		$worksheet->getStyle('A1:'.$column.$row)
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //autoFitColumnWidthToContent($worksheet,'A');
        //

        excel_bold_row($worksheet,1);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename={$file_name}.xlsx");
		header('Cache-Control: max-age=0');
		ob_clean();
		flush();

		$objWriter = new PHPExcel_Writer_Excel2007($phpexcel);
		$objWriter->save('php://output');
		exit;
	}

	public function _autoFitColumnWidthToContent($sheet, $fromCol, $toCol) {
	if (empty($toCol) ) {//not defined the last column, set it the max one
	$toCol = $sheet->getColumnDimension($sheet->getHighestColumn())->getColumnIndex();
	}
	for($i = $fromCol; $i <= $toCol; $i++) {
	$sheet->getColumnDimension($i)->setAutoSize(true);
	}
	$sheet->calculateColumnWidths();
	}

	public function postCreate() {
        $product = New Product;
        $product->status = "Draft";
        $product->created_by = Auth::user()->id;
        $product->updated_by = Auth::user()->id;
        $product->category_id = 1;
        $product->company_id = return_company_id();
        if(return_company_id() == 1){
            $product->company_sync = 1;
        }
        $product->save();

        $id = $product->id;
        return Redirect::to('products/show/'.$id)
            ->with('flash_success','Operation success');
	}

	public function postDuplicate($original_id){
        $original = Product::findOrFail($original_id);
		$original_array = Product::findOrFail($original_id)->toArray();

		$duplicate = New Product();
		$duplicate->fill($original_array);
		$duplicate->id = null;
		$duplicate->created_at = null;
		$duplicate->updated_at = null;
        $duplicate->created_by = Auth::user()->id;
        $duplicate->updated_by = Auth::user()->id;
        $duplicate->status     = "Draft";
		$duplicate->save();

        $group_prices = ProductPrice::where('product_id',$original->id)->where('company_id',return_company_id())->orderBy('customer_group_id','DESC')->get();
        foreach($group_prices as $group){
            $new = new ProductPrice();
            $new->company_id        = $group->company_id;
            $new->product_id        = $duplicate->id;
            $new->customer_group_id = $group->customer_group_id;
            $new->surcharge_20      = $group->surcharge_20;
            $new->surcharge_40      = $group->surcharge_40;
            $new->save();
        }
        unset($new);

        foreach($original->customerSpecifics as $specific){
            $new = new ProductCustomerSpecific();
            $new->product_id        = $duplicate->id;
            $new->customer_id       = $specific->customer_id;
            $new->save();
        }
        unset($new);

        foreach($original->priceOverrides as $override){
            $new = new ProductPriceOverride();
            $new->company_id        = $override->company_id;
            $new->product_id        = $duplicate->id;
            $new->base_price_20     = $override->base_price_20;
            $new->base_price_40     = $override->base_price_40;
            $new->customer_id       = $override->customer_id;
            $new->save();
        }
        unset($new);

        return Redirect::to('products/show/'.$duplicate->id)
            ->with('flash_success','Duplication success');
	}

	public function getChangelog($id){
	    $product = Product::findOrFail($id);

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('products.changelog')
            ->with('product',$product);
	}

	public function getStocks($id) {
	    $product = Product::findOrFail($id);
      $transactions = WarehouseTransaction::where('product_id',$product->id)
          ->where('company_id',return_company_id())
          ->orderBy('id', 'desc')->limit(100)->get();

		$select_inventory_adjustment = ValueList::where('uid','=','inventory_adjustment')->orderBy('name', 'asc')->pluck('name','name');

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        // $this->layout->content = View::make('products.stocks')
        //     ->with('product',$product)
        //     ->with('select_uom',$select_uom)
        //     ->with('select_package',$select_package)
        //     ->with('select_currency_codes',$select_currency_codes)
        //     ->with('select_inventory_adjustment',$select_inventory_adjustment)
        //     ;
        return view("products.stocks",compact('transactions','product','select_uom','select_package','select_currency_codes','select_inventory_adjustment'));
	}

    public function postStockAdjust($id){
        $rules = array(
            'quantity' => 'required|integer|digits_between:1,6',
            'remark' => 'required',
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/products/stocks/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {

            $quantity = Input::get('quantity');
            if(!is_numeric($quantity)){
                return Redirect::to('/products/stocks/'.$id)
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }
            if($quantity == 0){
                return Redirect::to('/products/stocks/'.$id)
                    ->with('flash_error','Operation failed')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }

            $product = Product::findOrFail($id);

            $current_stock = $product->getStockOnHand();
            if($current_stock + $quantity < 0){
                return Redirect::to('/products/stocks/'.$id)
                    ->with('flash_error','Cannot allow negative Stock')
                    ->withErrors($validation->Messages())
                    ->withInput();
            }

            warehouse_transaction($product->id,Input::get('quantity',0), Input::get('remark'));

            return Redirect::to('/products/stocks/'.$id)
                ->with('flash_success','Operation success');
        }

    }

	public function postStocks($id) {
        $rules = array(
            'id' => 'required|integer|digits_between:1,6'
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/products/stocks/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $product = Product::findOrFail($id);
            $input = Input::get();
            $product->fill($input);
            if(Input::get('stock_min') == ""){
				$product->stock_min = null;
			}
            $product->save();

            return Redirect::to('/products/stocks/'.$id)
                ->with('flash_success','Operation success');
        }
	}

	public function postChangeProductStatus($id){
		$product = Product::findOrFail($id);
		$product->status = Input::get('status');
		$product->save();
		return Redirect::to('products/show/'.$id)
			->with('flash_success','Operation success');
	}

	public function getChangeProductStatus($id) {
	    $product = Product::findOrFail($id);

		$tree = Category::all()->toHierarchy();
		$select_categories = printSelect($tree,$product->category_id);

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.change_status')
            ->with('product',$product)
            ->with('select_uom',$select_uom)
            ->with('select_package',$select_package)
            ->with('select_categories',$select_categories)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function postApprove($id){
		$product = Product::findOrFail($id);
		$product->status = "ACTIVE";
		$product->save();
		return Redirect::to('products/show/'.$id)
			->with('flash_success','Operation success');
	}

	public function getApprove($id) {
	    $product = Product::findOrFail($id);


		$tree = Category::all()->toHierarchy();
		$select_categories = printSelect($tree,$product->category_id);

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.approve')
            ->with('product',$product)
            ->with('select_uom',$select_uom)
            ->with('select_package',$select_package)
            ->with('select_categories',$select_categories)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function getSubmit($id) {
	    $product = Product::findOrFail($id);


		$tree = Category::all()->toHierarchy();
		$select_categories = printSelect($tree,$product->category_id);

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.submit')
            ->with('product',$product)
            ->with('select_uom',$select_uom)
            ->with('select_package',$select_package)
            ->with('select_categories',$select_categories)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function postSubmit($id){
		$product = Product::findOrFail($id);
		$product->status = "Pending";
		$product->save();
		return Redirect::to('products/show/'.$id)
			->with('flash_success','Operation success');
	}

	public function getRequest($id) {
	    $product = Product::findOrFail($id);

		$tree = Category::all()->toHierarchy();
		$select_categories = printSelect($tree,$product->category_id);

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.request')
            ->with('product',$product)
            ->with('select_uom',$select_uom)
            ->with('select_package',$select_package)
            ->with('select_categories',$select_categories)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function postRequest($id){
		$product = Product::findOrFail($id);
		$product->edit_request_by = Auth::user()->id;
		$product->edit_request_ts = time();
		$product->save();
		return Redirect::to('products/show/'.$id)
			->with('flash_success','Operation success');
	}

    public function postSync($id) {
        $rules = array(
            'action'     => 'required'
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('products/sync/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $action = Input::get('action','save');
            unset($input['action']);

            if($action == 'save'){
                if(Input::has('company_sync')){
                    $input['company_sync'] = 1;
                } else {
                    $input['company_sync'] = 0;
                }
                $product = Product::findOrFail($id);
                $product->fill($input);
                $product->save();
            }

            if($action == 'sync'){
                $product   = Product::findOrFail($id);
                $res = syncProductAcrossCompanies($product);
                if($res != 0){
                    return Redirect::to('products/sync/'.$id)
                        ->with('flash_error','Operation failed');
                }
            }

            return Redirect::to('products/sync/'.$id)
                ->with('flash_success','Operation success');
        }
	}

    public function getSync($id) {
	   $product = Product::findOrFail($id);
		$tree = Category::all()->ToArray();
		$select_categories = printSelect($tree,$product->category_id);

        if($product->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }
    $companies =   Company::all();
    foreach($companies as $company){
      $slave = Product::where('company_id',$company->id)->where('parent_id',$product->id)->first();
    }
    $user_created =     User::where('created_at',$product->created_by)->pluck('username');
    $user_updated = User::where('updated_at',$product->updated_by)->pluck('username');
    $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');
		$select_origin = ValueList::where('uid','=','origin')->orderBy('name', 'asc')->pluck('name','name');
	  $select_users = User::pluck('username','id');
    return view('products.sync',compact('slave','companies','user_created','user_updated','product','select_uom','select_package','select_origin','select_users','select_categories','select_currency_codes'));
        //
        // $this->layout->module_title = "Product Details";
        // $this->layout->module_sub_title = "Product Details";
        // $this->layout->content = View::make('products.sync')
        //     ->with('product',$product)
        //     ->with('select_uom',$select_uom)
        //     ->with('select_package',$select_package)
        //     ->with('select_origin',$select_origin)
        //     ->with('select_users',$select_users)
        //     ->with('select_categories',$select_categories)
        //     ->with('select_currency_codes',$select_currency_codes);
	}



    public function getSetup($id) {
	    $product = Product::findOrFail($id);
		$tree = Category::all()->toArray();
		$select_categories = printSelect($tree,$product->category_id);

        if($product->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }
    $user_created =     User::where('created_at',$product->created_by)->pluck('username');
    $user_updated = User::where('updated_at',$product->updated_by)->pluck('username');
		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');
		$select_origin = ValueList::where('uid','=','origin')->orderBy('name', 'asc')->pluck('name','name');
	    $select_users = User::pluck('username','id');

      return view('products.setup',compact('user_created','user_updated','product','select_uom','select_package','select_origin','select_users','select_categories','select_currency_codes'));
        //
        // $this->layout->module_title = "Product Details";
        // $this->layout->module_sub_title = "Product Details";
        // $this->layout->content = View::make('products.setup')
        //     ->with('product',$product)
        //     ->with('select_uom',$select_uom)
        //     ->with('select_package',$select_package)
        //     ->with('select_origin',$select_origin)
        //     ->with('select_users',$select_users)
        //     ->with('select_categories',$select_categories)
        //     ->with('select_currency_codes',$select_currency_codes);
	}

    public function postSetup($id) {
        $rules = array(
            'id' => 'required|integer|digits_between:1,6',
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return Redirect::to('products/setup/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $product = Product::findOrFail($id);
            $input = Input::get();
            $product->fill($input);
            $product->updated_by = Auth::user()->id;
            if(Input::get('company_sync')){
				$product->company_sync = 1;
			} else {
				$product->company_sync = 0;
			}
            $product->save();

            return Redirect::to('products/setup/'.$id)
                ->with('flash_success','Operation success');
        }
	}


	public function getDownloadEdit($id) {
		$attachment 	= ProductAttachment::findOrFail($id);
	    $product 		= Product::findOrFail($attachment->id);

        if($product->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }

        $company_id = return_company_id();

        $this->layout->content = View::make('products.download_edit')
            ->with('product',$product)
            ->with('attachment',$attachment)
            ->with('company_id',$company_id)
        ;
	}

	public function _update_product_part_number($id){
        return 1;
		if(setting_get('auto_part_numbers') != 1){
			return true;
		}

		$product = Product::findOrFail($id);

		if($product->old_item_id != ""){ return true; }

		$category = Category::findOrFail($product->category_id);
		$ancestors = $category->getAncestorsAndSelf();
		$part_number = "";

		foreach($ancestors as $a){
			$cat_name = $a->name;
			$cat_code = $a->code;
			$cat_name = preg_replace("/[^A-Za-z0-9\- ]+/i", "-", $cat_name);
			if($a->getLevel() == 0){
				$words = explode(" ", $cat_name);
				$acronym = "";
				foreach ($words as $w) {
					$acronym .= $w[0];
				}
			} else {
				$acronym = substr($cat_name, 0,3);
			}
			if($cat_code != ""){
				$part_number .= strtoupper($cat_code);
			} else {
				$part_number .= strtoupper($acronym);
			}
			$part_number .= "-";
		}
		$part_number .= $id;
		$product->part_number = $part_number;
		$product->save();
		return TRUE;
	}

	public function postDestroy($id) {
        $product = Product::findOrFail($id);
        $product->delete();
        return Redirect::to('products/')
            ->with('flash_success','Operation success');
	}

	public function getBom1($id){
	    $product = Product::findOrFail($id);
		print $product->id . "<br />";
	    $bom_items = ProductBom::where("product_id","=",$product->id)->orderBy("sort_no","asc")->get();
	    $this->displayBom($product);
		exit;
	}

	public function displayBom($product,$depth = null){

		static $conflicts = array();
		if(in_array($product->id,$conflicts)){
			print "Conflict detected, shutting down<br />";
			exit;
		} else {
			$conflicts[] = $product->id;
		}

		if($depth === null){
			$depth = 0;
		} else {
			$depth++;
		}

		if($depth > 10){
			print "Infinite Recursion detected. Shutting down<br />";
			echo "<pre>";
			print_r($conflicts);
			echo "</pre>";
			exit;
		}

		if($product->bom->count()>0){
			foreach($product->bom as $bom_line){
				$string = "";
				for($i=0;$i<$depth+1;$i++){
					$string .= "-";
				}
				$vendor_info  = $bom_line->product->getVendorInfo(0,0,"CNY");
				$price = $vendor_info['net_price'];
				if($bom_line->product->bom->count() > 0){
					$string .= "$bom_line->bom_product_id $price(B)<br />";
				} else {
					$string .= "$bom_line->bom_product_id $price<br />";
				}
				print $string;
				if($bom_line->product->bom->count() > 0){
					$this->displayBom($bom_line->product,$depth);
				}
			}
		}
	}

	public function getBom($id){
	    $product = Product::findOrFail($id);
	    $bom_items = ProductBom::where("product_id","=",$product->id)->orderBy("sort_no","asc")->get();
		$select_users = User::pluck('username','id');
		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');

		$bom_quantity = Session::get('bom_quantity', 1);
		$bom_currency_code = Session::get('bom_currency_code', "CNY");
		$bom_sales_currency_code = Session::get('bom_sales_currency_code', "CNY");
		$bom_sales_price = Session::get('bom_sales_price');

		$bom_extra_cost_label_1 = Session::get('bom_extra_cost_label_1');
		$bom_extra_cost_label_2 = Session::get('bom_extra_cost_label_2');
		$bom_extra_cost_value_1 = Session::get('bom_extra_cost_value_1');
		$bom_extra_cost_value_2 = Session::get('bom_extra_cost_value_2');

		/* Calculate Totals */
		$category_totals = array();
		$bom_net_total = 0;
		$bom_gross_total = 0;
		foreach($bom_items as $bom_item){
			$category = Category::find($bom_item->product->category_id);
			$category_root = $category->getRoot();
			if(!isset($category_totals[$category_root->name])){
				$category_totals[$category_root->name]['total'] = 0;
				$category_totals[$category_root->name]['percent'] = 0;
			}

			$consumed_quantity = $bom_item->getConsumedQuantity();

			$vendor_info = $bom_item->product->getVendorInfo(0,$bom_quantity*$consumed_quantity,$bom_currency_code);
			$bom_item_price = $vendor_info['net_price'];

			$bom_item_price = $bom_item_price * $consumed_quantity;
			$bom_net_total += $bom_item_price;
			$bom_gross_total += $vendor_info['gross_price'] * $consumed_quantity;
			$category_totals[$category_root->name]['total'] += $bom_item_price;
		}
		foreach($category_totals as &$total){
			if($bom_net_total > 0){
				$total['percent'] = round($total['total'] / $bom_net_total * 100,2);
			} else {
				$total['percent'] = 0;
			}
		}
		/* End Calculate Totals */

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('products.bom')
            ->with('bom_items',$bom_items)
            ->with('product',$product)
            ->with('select_users',$select_users)
			->with('select_currency_codes',$select_currency_codes)
			->with('bom_net_total',$bom_net_total)
			->with('bom_gross_total',$bom_gross_total)
			->with('bom_quantity',$bom_quantity)
			->with('bom_currency_code', $bom_currency_code)
			->with('bom_sales_currency_code', $bom_sales_currency_code)
			->with('bom_sales_price', $bom_sales_price)
			->with('bom_extra_cost_label_1', $bom_extra_cost_label_1)
			->with('bom_extra_cost_label_2', $bom_extra_cost_label_2)
			->with('bom_extra_cost_value_1', $bom_extra_cost_value_1)
			->with('bom_extra_cost_value_2', $bom_extra_cost_value_2)
			->with('category_totals',$category_totals);
	}

	public function getBomUpdateSort($id){
		$time_start = microtime(true);

		$product = Product::findOrFail($id);
	    $bom_items = ProductBom::where("product_id","=",$product->id)->orderBy("sort_no","asc")->get();

		$part_numbers = array();
		foreach($bom_items as $bom_item){
			$part_numbers[] = $bom_item->product->part_number;
		}
		sort($part_numbers);

		foreach($bom_items as $bom_item){
			$part_number = $bom_item->product->part_number;
			foreach($part_numbers as $k=>$v){
				if($part_number == $v){
					$bom_item->sort_no = $k;
					$bom_item->save();
				}
			}
		}

		$time_end = microtime(true);
		$time = $time_end - $time_start;

		return Redirect::to('products/bom/'.$id)
			->with('flash_success','Operation success');
	}

    public function anyDtBomAvailableProducts(){
        $products = Product::select(
            array(
                'products.id',
                'products.part_number',
                'products.mpn',
                'products.title',
                'products.uom'
            ));
            //->where('products.status','=',"Active");
        return Datatables::of($products)
		->add_column('quantity','
			<input style="width: 30px; padding: 0px !important; margin: 0px !important; text-align: center;" type="text" name="qty" value="1" />
		')
        ->add_column('operations','
			<form method="POST" action="">
				<input type="hidden" name="bom_product_id" value="{{$id}}" />
				<input type="hidden" name="quantity" value="1" />
				<input type="submit" value="Add" class="btn btn-xs btn-success ajax-btn-bom-add">
			</form>
		')
		->remove_column('id')
        ->make();
    }

	public function getBomParameters($id){
	    $product = Product::findOrFail($id);
		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('products.bom_parameters')
            ->with('select_currency_codes',$select_currency_codes)
            ->with('product',$product);
	}

	public function postBomParameters($id){
		$input = Input::get();

		if(Input::has('bom_quantity')){
			Session::put('bom_quantity', Input::get('bom_quantity'));
		}

		if(Input::has('bom_currency_code')){
			Session::put('bom_currency_code', Input::get('bom_currency_code'));
		}

		if(Input::has('bom_sales_currency_code')){
			Session::put('bom_sales_currency_code', Input::get('bom_sales_currency_code'));
		}

		if(Input::has('bom_sales_price')){
			Session::put('bom_sales_price', Input::get('bom_sales_price'));
		}

		if(Input::has('bom_extra_cost_label_1')){
			Session::put('bom_extra_cost_label_1', Input::get('bom_extra_cost_label_1'));
		}

		if(Input::has('bom_extra_cost_label_2')){
			Session::put('bom_extra_cost_label_2', Input::get('bom_extra_cost_label_2'));
		}

		if(Input::has('bom_extra_cost_value_1')){
			Session::put('bom_extra_cost_value_1', Input::get('bom_extra_cost_value_1'));
		}

		if(Input::has('bom_extra_cost_value_2')){
			Session::put('bom_extra_cost_value_2', Input::get('bom_extra_cost_value_2'));
		}

		return Redirect::to('products/bom/'.$id)
			->with('flash_success','Operation success');
	}

	public function getBomItemAdd($id){
	    $product = Product::findOrFail($id);

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('products.bom_item_add')
            ->with('product',$product);
	}

    public function postBomItemAdd($id){
    	$product = Product::findOrFail($id);

        $rules = array(
            'bom_product_id' => 'Required|integer',
            'quantity' => 'required|integer'
        );

		if($product->id == Input::get('bom_product_id')){
			return Redirect::to('products/bom/'.$id)
				->with('flash_error','Cannot add Product to itself');
		}

        $input = Input::get();

        $conflict = ProductBom::where('product_id',$input['bom_product_id'])
			->where('bom_product_id',$product->id)
			->first();
        if($conflict){
			return Redirect::to('products/bom/'.$id)
				->with('flash_error','First Level conflict detected');
		}

		$conflict = ProductBom::where('product_id',$product->id)
			->where('bom_product_id',$input['bom_product_id'])
			->first();
		if($conflict){
			return Redirect::to('products/bom/'.$id)
				->with('flash_error','Item already on BOM');
		}

        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('products/bom/',$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
			$product_id = Input::get('product_id');

			$new_bom_item = new ProductBom();
			$new_bom_item->fill($input);
			$new_bom_item->product_id = $id;
			$new_bom_item->unit = 1;
			$new_bom_item->save();

			return Redirect::to('products/bom-item-add/'.$id)
				->with('flash_success','Operation success');
		}
	}

	public function getBomItemUpdate($line_item_id){
	    $line_item = ProductBom::findOrFail($line_item_id);
	    $product = $line_item->productmaster;

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = view::make('products.bom_item_update')
            ->with('product',$product)
            ->with('line_item',$line_item);
	}

	public function postBomItemUpdate($line_item_id){
		$bom_item = ProductBom::findOrFail($line_item_id);
		$input = Input::get();
		$bom_item->fill($input);
		$bom_item->save();

		return Redirect::to('/products/bom/'.$bom_item->productmaster->id)
			->with('flash_success','Operation success');
	}

	public function getBomItemDelete($line_item_id){
	    $line_item = ProductBom::findOrFail($line_item_id);
	    $product = $line_item->productmaster;

        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = view::make('products.bom_item_delete')
            ->with('product',$product)
            ->with('line_item',$line_item);
	}

	public function postBomItemDelete($line_item_id){
		$bom_item = ProductBom::findOrFail($line_item_id);
		$product_id = $bom_item->productmaster->id;
		$bom_item->delete();

		return Redirect::to('/products/bom/'.$product_id)
			->with('flash_success','Operation success');
	}



    public function getCustomerSpecificDelete($record_id){
        $spec = ProductCustomerSpecific::findOrFail($record_id);
        $product = $spec->product;
        $customer = $spec->customer;
        if($product->company_id != return_company_id()){
            return Redirect::to('/products')
                ->with('flash_error','Access violation');
        }

        $spec->delete();
        return Redirect::to('/products/prices/'.$product->id)
            ->with('flash_success','Operation success');
    }



    public function getPricesGroupDelete($id){
        $group_price = ProductPrice::findOrFail($id);
        $product_id  = $group_price->product_id;
        $group_price->delete();

        return Redirect::to('/products/prices/'.$product_id)
            ->with('flash_success','Operation success');
    }

	public function postCustomersUpdate($id) {
		$product = Product::findOrFail($id);

        $rules = array(
            'id' => 'integer|digits_between:1,6',
            'customer_id' => 'required|integer|digits_between:1,6',
            'part_number' => "between:1,50",
            'currency_code' => 'between:3,3',
            'price' => 'numeric|required|digits_between:1,50',
        );
        $validation = Validator::make(Input::get(), $rules);

        if($validation->fails()){
            return Redirect::to('/products/customers/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = Input::get();
            unset($input['currency_code']);
            if(Input::get('id')){
                $customer = Customer::findOrFail(Input::get('customer_id'));
				$product_customer = ProductCustomer::findOrFail(Input::get('id'));
				$product_customer->fill($input);
                $product_customer->price = convert_currency(Input::get('currency_code'), $customer->currency_code,Input::get('price'));
				$product_customer->save();
			} else {
                $customer = Customer::findOrFail(Input::get('customer_id'));
				$new_product_customer = New ProductCustomer();
				$new_product_customer->fill($input);
				$new_product_customer->product_id = $product->id;
                $new_product_customer->price = convert_currency(Input::get('currency_code'), $customer->currency_code,Input::get('price'));
				$new_product_customer->save();
			}
            return Redirect::to('/products/customers/'.$id)
                ->with('flash_success','Operation success');
        }
	}





	public function postCustomersDelete($record_id) {
		$product_customer = ProductCustomer::findOrFail($record_id);
		$product_id = $product_customer->product_id;
		$customer_id = $product_customer->customer_id;
		$product_customer->delete();

        return Redirect::to('/products/customers/'.$product_id)
            ->with('flash_success','Operation success');
	}

	public function getVendors($id,$product_vendor_id=null) {
	    $product = Product::findOrFail($id);
	    if($product_vendor_id != null){
	    	$product_vendor = ProductVendor::findOrFail($product_vendor_id);
		} else {
			$product_vendor = null;
		}

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_vendors = Vendor::where('status','=','ACTIVE')
            ->where('company_id',return_company_id())
            ->orderBy('company_name', 'asc')->pluck('company_name','id');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.vendors')
            ->with('product',$product)
            ->with('product_vendor',$product_vendor)
            ->with('select_vendors',$select_vendors)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function postVendorsUpdate($id) {
		$product = Product::findOrFail($id);

        $rules = array(
            'id' => 'integer|digits_between:1,6',
            'vendor_id' => 'required|integer|digits_between:1,6',
            'part_number' => "between:1,50",
            'lead_time' => 'required|integer|digits_between:1,6',
            'currency_code' => 'between:3,3',
            'price' => 'numeric|required|digits_between:1,50',
        );
        $validation = Validator::make(Input::get(), $rules);

        if($validation->fails()){
            return Redirect::to('/products/vendors/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = Input::get();
            unset($input['currency_code']);

            if(Input::get('id')){
                $vendor = Vendor::findOrFail(Input::get('vendor_id'));
				$product_vendor = ProductVendor::findOrFail(Input::get('id'));
				$product_vendor->fill($input);
                $product_vendor->company_id = return_company_id();
                $product_vendor->price = convert_currency(Input::get('currency_code'), $vendor->currency_code,Input::get('price'));
				$product_vendor->save();
			} else {
                $vendor = Vendor::findOrFail(Input::get('vendor_id'));
				$product_vendor = New ProductVendor();
				$product_vendor->fill($input);
                $product_vendor->company_id = return_company_id();
				$product_vendor->product_id = $product->id;
                $product_vendor->price = convert_currency(Input::get('currency_code'), $vendor->currency_code,Input::get('price'));
				$product_vendor->save();
			}
			$vendor_id = $input['vendor_id'];
			$preferred = $input['preferred'];

			if($preferred == "Yes"){
				foreach($product->vendors as $product_vendor){
					if($product_vendor->vendor_id == $vendor_id){
						$product_vendor->preferred = "Yes";
					} else {
						$product_vendor->preferred = "No";
					}
					$product_vendor->save();
				}
			}

            return Redirect::to('products/vendors/'.$id)
                ->with('flash_success','Operation success');
        }
	}

	public function postVendorsDelete($record_id) {
		$product_vendor = ProductVendor::findOrFail($record_id);
		$product_id = $product_vendor->product_id;
		$vendor_id = $product_vendor->vendor_id;
		$product_vendor->delete();

        return Redirect::to('/products/vendors/'.$product_id)
            ->with('flash_success','Operation success');
	}

	public function getAttachments($id,$product_attachment_id=null) {
	    $product = Product::findOrFail($id);

	    if($product_attachment_id != null){
	    	$product_attachment = ProductAttachment::findOrFail($product_attachment_id);
		} else {
			$product_attachment = null;
		}

		$select_attachment_categories = ValueList::where('module','=','product_attachments')
			->where('uid','=','categories')
			->orderBy('name', 'asc')
			->pluck('name','name');

        $this->layout->content = View::make('products.attachments')
            ->with('select_categories',$select_attachment_categories)
            ->with('product',$product)
            ->with('product_attachment',$product_attachment);
	}

	public function postAttachmentsUpdate($id) {
		$product = Product::findOrFail($id);

        $rules = array(
            'id' => 'integer|digits_between:1,6',
            'category' => "required|between:1,50",
            'description' => 'between:1,100',
			'file' => 'max:50000'
        );
        $validation = Validator::make(Input::all(), $rules);

		if(Input::hasFile('file')){
			$file = Input::file('file');
		} else {
			$file = false;
		}

        if($validation->fails()){
            return Redirect::to('/products/attachments/'.$id)
                ->with('flash_error',$msg)
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = Input::get();
			$private_folder = Config::get('app.private_folder') . "products/".$product->id;
			if(!file_exists($private_folder)){
				mkdir($private_folder);
			}

            if(Input::hasFile('file')){
                $file = Input::file('file');
                $file_extension = $file->getClientOriginalExtension();
                $file_name = uniqid() . ".".$file_extension;
				$file_original_name = Input::file('file')->getClientOriginalName();
				$file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                $file_size = $file->getSize();
                $file->move($private_folder, $file_name);
            }

            if(Input::get('id')){
				$product_attachment = ProductAttachment::findOrFail(Input::get('id'));
				$product_attachment->fill($input);
				if($file){
					$product_attachment->name = $file_name;
					$product_attachment->original_name = $file_original_name;
					$product_attachment->size = $file_size;
				}
				$product_attachment->updated_by = Auth::user()->id;
				$product_attachment->save();
			} else {
				$new_product_attachment = New ProductAttachment();
				$new_product_attachment->fill($input);
				$new_product_attachment->product_id = $product->id;
				if($file){
					$new_product_attachment->name = $file_name;
					$new_product_attachment->original_name = $file_original_name;
					$new_product_attachment->size= $file_size;
				}
				$new_product_attachment->created_by = Auth::user()->id;
				$new_product_attachment->updated_by = Auth::user()->id;
				$new_product_attachment->save();
			}

            return Redirect::to('products/attachments/'.$id)
                ->with('flash_success','Operation success');
        }
	}

	public function postAttachmentsDelete($attachment_id) {
        $record = ProductAttachment::findOrFail($attachment_id);
        $product_id = $record->product_id;

		if($record->name != ""){
			$path_to_file = Config::get('app.private_folder') . "products/".$product_id."/".$record->name;
			if(file_exists($path_to_file)){
				unlink($path_to_file);
			}
		}

        $record->delete();
        return Redirect::to('/products/attachments/'.$product_id)
            ->with('flash_success','Operation success');
	}

	public function getAttachmentDownload($id){
		$private_folder = Config::get('app.private_folder') . "/products/";

		$attachment = ProductAttachment::findOrFail($id);

		$full_path = $private_folder . $attachment->product_id . "/" . $attachment->name;
		if(file_exists($full_path)){
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($full_path));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
			flush();
			readfile($full_path);
			exit;
		} else {
			print "File not found!";
			exit;
		}
	}

    public function anyDtPurchases($product_id){
        $purchases = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
			->Leftjoin('vendors','purchases.vendor_id','=','vendors.id')
			->select(
            array(
				'purchases.id',
                'purchases.status',
                'purchases.date_placed',
                'vendors.code',
                'purchases.currency_code',
                'purchase_items.quantity',
                'purchase_items.gross_price',
                'purchase_items.gross_total'
            ))
            ->where("purchase_items.product_id",$product_id);
        return Datatables::of($purchases)
        ->add_column('operations','<ul class="table-controls"><li><a href="/purchases/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->make();
    }

    public function anyDtOrders($product_id){
        $sales = OrderItem::Leftjoin('orders','order_items.order_id','=','orders.id')
			->Leftjoin('customers','orders.customer_id','=','customers.id')
			->select(
            array(
				'orders.id',
                'orders.status',
                'orders.date_placed',
                'customers.code',
                'orders.currency_code',
                'order_items.quantity',
                'order_items.gross_price',
                'order_items.gross_total'
            ))
            ->where("order_items.product_id",$product_id);
        return Datatables::of($sales)
        ->add_column('operations','<ul class="table-controls"><li><a href="/orders/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->make();
    }

    public function anyDtInvoices($product_id){
        $invoices = InvoiceItem::Leftjoin('invoices','invoice_items.invoice_id','=','invoices.id')
			->Leftjoin('customers','invoices.customer_id','=','customers.id')
			->select(
            array(
				'invoices.id',
                'invoices.status',
                'invoices.date_issued',
                'customers.code',
                'invoices.currency_code',
                'invoice_items.quantity',
                'invoice_items.gross_price',
                'invoice_items.gross_total'
            ))
            ->where("invoice_items.product_id",$product_id);
        return Datatables::of($invoices)
        ->add_column('operations','<ul class="table-controls"><li><a href="/invoices/show/{{ $id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li></ul>')
        ->make();
    }

    public function postUpdatePicture($id){
		$product = Product::findOrFail($id);

		$public_folder = Config::get('app.public_folder') . "products/";
		if($product->picture != ""){
			if(is_file($public_folder . $product->picture)){
				@unlink($public_folder . $product->picture);
				$product->picture = "";
			}
		}

		if(Input::hasFile('picture')){
			$picture = Input::file('picture');
			$picture_extension = $picture->getClientOriginalExtension();
			$picture->move($public_folder, md5($id) .".". $picture_extension);
			$product->picture = md5($id) .".". $picture_extension;
		}
		$product->save();

		return Redirect::to('products/attachments/'.$id)
			->with('flash_success','Operation success');
	}


    public function getMarkAsMainImage($image_id){
        $image   = ProductImage::where('id',$image_id)->first();
        $product = Product::findOrFail($image->product_id);

        $product->picture = $image->picture;
        $product->save();

        return Redirect::to('products/images/'.$product->id)
            ->with('flash_success','Operation success');
    }

    public function getUnmarkAsMainImage($image_id){
        $image   = ProductImage::where('id',$image_id)->first();
        $product = Product::findOrFail($image->product_id);

        $product->picture = "";
        $product->save();

        return Redirect::to('products/images/'.$product->id)
            ->with('flash_success','Operation success');
    }

	public function getHistory($id) {
	    $product = Product::findOrFail($id);

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.history')
            ->with('product',$product);
	}

	public function getBomItemReplace($id) {
	    $product = Product::findOrFail($id);

		$tree = Category::all()->toHierarchy();
		$select_categories = printSelect($tree,$product->category_id);

		$select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
		$select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
		$select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');
		$select_origin = ValueList::where('uid','=','origin')->orderBy('name', 'asc')->pluck('name','name');

        $this->layout->module_title = "Product Details";
        $this->layout->module_sub_title = "Product Details";
        $this->layout->content = View::make('products.bom_item_replace')
            ->with('product',$product)
            ->with('select_uom',$select_uom)
            ->with('select_package',$select_package)
            ->with('select_origin',$select_origin)
            ->with('select_categories',$select_categories)
            ->with('select_currency_codes',$select_currency_codes);
	}

	public function postBomItemReplace($id) {
        $rules = array(
            'product_id' => 'required|integer|digits_between:6,6',
            'new_product_id' => 'integer|digits_between:6,6|required_if:submit,Replace|exists:products,id',
            'new_product_id_confirm' => 'integer|digits_between:6,6|same:new_product_id|required_with:new_product_id',
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('/products/bom-item-replace/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
        	if(Input::get('submit',0) == 'Replace'){
        		$bom_items = ProductBom::where('bom_product_id',Input::get('product_id'))->get();
        		foreach($bom_items as $bom_item){
					$bom_item->bom_product_id = Input::get('new_product_id');
					$bom_item->save();
				}
				return Redirect::to('/products/bom-item-replace/'.$id)
					->with('flash_success','Operation success')
					->withErrors($validation->Messages())
					->withInput();
			}

        	if(Input::get('submit',0) == 'Delete'){
				return Redirect::to('/products/bom-item-replace/'.$id)
					->with('flash_error','Operation failed')
					->withErrors($validation->Messages())
					->withInput();
			}


			return Redirect::to('/products/bom-item-replace/'.$id)
				->with('flash_error','Operation failed')
				->withErrors($validation->Messages())
				->withInput();
		}
	}

//    public function getImages($id){
//	    $product = Product::findOrFail($id);
//        $select_yesno = [0 => 'No', 1 => 'Yes'];

		//$download_folder = Config::get('app.file_folder');
		//$extensions = array("txt");
		//$downloads= find_recursive_images($download_folder,$extensions);

//        $images = ProductImage::where('product_id',$product->id)->get();

        /*
		foreach($downloads as $i => &$download){
			$download = explode("public",$download);
			$download = $download[1];
		}

	    $linked_downloads = Product::find($id)->downloads;
        */
//        return view('products.images',compact('product','images','select_yesno'));
        // $this->layout->content = View::make('products.images')
        //     ->with('product', $product)
        //     ->with('images', $images)
        //     ->with('select_yesno', $select_yesno)
        // ;
//    }


    public function getDownloads($id){
      // echo $id;die;
	    $product = Product::findOrFail($id);
        $select_yesno = ["No" => 'No', "Yes" => 'Yes'];

		//$download_folder = Config::get('app.file_folder');
		//$extensions = array("txt");
		//$downloads= find_recursive_images($download_folder,$extensions);

        $downloads = ProductDownload::where('product_id',$product->id)->get();

        /*
		foreach($downloads as $i => &$download){
			$download = explode("public",$download);
			$download = $download[1];
		}

	    $linked_downloads = Product::find($id)->downloads;
        */
        return view('products.downloads',compact('product','downloads','select_yesno'));
        // $this->layout->content = View::make('products.downloads')
        //     ->with('product', $product)
        //     ->with('downloads', $downloads)
        //     ->with('select_yesno', $select_yesno)
        // ;
    }

    public function postImages($id){
        $product = Product::findOrFail($id);
        $rules = array(
            'id' => 'integer|digits_between:1,6',
			'file' => 'max:50000|required'
        );
        $validation = Validator::make(Input::all(), $rules);

		if(Input::hasFile('file')){
			$file = Input::file('file');
		} else {
			$file = false;
		}

        if($file == false){
            return Redirect::to('/products/images/'.$id)
                ->with('flash_error',"No file selected")
                ;
        }

        if($validation->fails()){
            return Redirect::to('/products/images/'.$id)
                ->with('flash_error',$msg)
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = Input::get();
			$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id;

			if(!file_exists($private_folder)){
                @mkdir($private_folder);
			}
			if(!file_exists($private_folder)){
                die("Fatal error ERR_MKDIR_DENIED");
			}

            if(Input::hasFile('file')){
                $file = Input::file('file');
                $file_extension = $file->getClientOriginalExtension();
                $file_name = uniqid() . ".".$file_extension;
				$file_original_name = Input::file('file')->getClientOriginalName();
				$file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                $file_size = $file->getSize();
                $mime_type = $file->getMimeType();
                $file->move($private_folder, $file_name);
            }

            $new_product_attachment = New ProductImage();
            $new_product_attachment->product_id = $product->id;
            if($file){
                $new_product_attachment->picture = $file_name;
                $new_product_attachment->original_file_name = $file_original_name;
                $new_product_attachment->file_size= $file_size;
                $new_product_attachment->login_required = "No";
				$new_product_attachment->mime_type = $mime_type;
				$new_product_attachment->seo_keyword = Input::get('seo_keyword');
            }
            $new_product_attachment->created_by = Auth::user()->id;
            $new_product_attachment->updated_by = Auth::user()->id;
            $new_product_attachment->save();

            return Redirect::to('products/images/'.$id)
                ->with('flash_success','Operation success');
        }

    }

    public function postDownloads($id){
        $product = Product::findOrFail($id);
        $rules = array(
            'id' => 'integer|digits_between:1,6',
            'description' => 'between:1,100',
			'file' => 'max:50000|required'
        );
        $validation = Validator::make(Input::all(), $rules);

		if(Input::hasFile('file')){
			$file = Input::file('file');
		} else {
			$file = false;
		}

        if($validation->fails()){
            return Redirect::to('/products/downloads/'.$id)
                ->with('flash_error',$msg)
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $input = Input::get();
			$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id;
			if(!file_exists($private_folder)){
                @mkdir($private_folder);
			}
			if(!file_exists($private_folder)){
                die("Fatal error ERR_MKDIR_DENIED");
			}

            if(Input::hasFile('file')){
                $file = Input::file('file');
                $file_extension = $file->getClientOriginalExtension();
                $file_name = uniqid() . ".".$file_extension;
				$file_original_name = Input::file('file')->getClientOriginalName();
				$file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                $file_size = $file->getSize();
                $mime_type = $file->getMimeType();
                $file->move($private_folder, $file_name);
            }

            $new_product_attachment = New ProductDownload();
            $new_product_attachment->description = Input::get('description');
            $new_product_attachment->product_id = $product->id;
            if($file){
                $new_product_attachment->file_name = $file_name;
                $new_product_attachment->original_file_name = $file_original_name;
                $new_product_attachment->file_size= $file_size;
                $new_product_attachment->login_required = Input::get('login_required');
				$new_product_attachment->mime_type = $mime_type;
            }
            $new_product_attachment->created_by = Auth::user()->id;
            $new_product_attachment->updated_by = Auth::user()->id;
            $new_product_attachment->date_added = date("Y-m-d");
            $new_product_attachment->save();

            return Redirect::to('products/downloads/'.$id)
                ->with('flash_success','Operation success');
        }

    }

	public function postUpdateImage($id){
		$attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

		$company_id 	= return_company_id();
		if($company_id != $product->company_id){
			die("Permission issue");
		}

        $rules = array(
            'id' => 'integer|digits_between:1,6',
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);

		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

		if(!file_exists($private_folder)){
			@mkdir($private_folder);
		}
		if(!file_exists($private_folder)){
			die("Fatal error ERR_MKDIR_DENIED");
		}


        if($validation->fails()){
            return Redirect::to('products/update-image/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            if(Input::hasFile('file')){
                $file = Input::file('file');
                $file_extension = $file->getClientOriginalExtension();
                $file_name = uniqid() . ".".$file_extension;
				$file_original_name = Input::file('file')->getClientOriginalName();
				$file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                $file_size = $file->getSize();
                $mime_type = $file->getMimeType();
                $file->move($private_folder, $file_name);
				if($file){

					if(file_exists($private_folder . $attachment->picture) && $attachment->picture != ""){
						@unlink($private_folder . $attachment->picture);
					}

					$attachment->picture = $file_name;
					$attachment->original_file_name = $file_original_name;
					$attachment->file_size= $file_size;
					$attachment->mime_type = $mime_type;
				}
			}
			$attachment->seo_keyword = Input::get('seo_keyword');
			$attachment->updated_by = Auth::user()->id;
			$attachment->save();


            return Redirect::to('products/images/'.$product->id)
                ->with('flash_success','Operation success')
				;
		}
	}

	public function getUpdateImage($id){
		$attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);
		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
		$full_path = $private_folder . $attachment->picture;

		$company_id 	= return_company_id();
		if($company_id != $product->company_id){
			die("Permission issue");
		}

        $this->layout->content = View::make('products.update_image')
            ->with('product',$product)
            ->with('attachment',$attachment)
            ->with('company_id',$company_id)
        ;
	}

    public function getImageDownload($id){
		$attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

		$full_path = $private_folder . $attachment->picture;

		if(file_exists($full_path)){
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($full_path));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
			flush();
			readfile($full_path);
			exit;
		} else {
            return Redirect::to('products/images/'.$product->id)
                ->with('flash_error','File not found');
		}
	}

    public function getDownloadDownload($id){
		$attachment = ProductDownload::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

		$full_path = $private_folder . $attachment->file_name;

		if(file_exists($full_path)){
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($full_path));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
			flush();
			readfile($full_path);
			exit;
		} else {
            return Redirect::to('products/downloads/'.$product->id)
                ->with('flash_error','File not found');
		}
	}

    public function postDownloadDelete($id){
        $download = ProductDownload::findOrFail($id);
        $product_id = $download->product_id;
        $product = Product::findOrFail($product_id);

		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
        $full_path = $private_folder . $download->file_name;

        if(file_exists($full_path)){
            unlink($full_path);
        }

        $download->delete();

        return Redirect::to('products/downloads/'.$product->id)
            ->with('flash_success','Operation success');
    }

    public function postImageDelete($id){
        $download = ProductImage::findOrFail($id);
        $product_id = $download->product_id;
        $product = Product::findOrFail($product_id);

        if($download->picture == $product->picture){
            $product->picture = "";
        }

		$private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
        $full_path = $private_folder . $download->picture;

        if(file_exists($full_path)){
            unlink($full_path);
        }

        $product->save();
        $download->delete();

        return Redirect::to('products/images/'.$product->id)
            ->with('flash_success','Operation success');
    }

	public function getImport(){
        has_role('products_import',1);

		$upload_file 	= app_path() . return_company_id() . "/import/update_products.xlsx";

		$has_file 		= false;

		if(file_exists($upload_file)){
			$has_file 	= true;
		}

		$todays_updates = PriceHistory::where('created',date("Y-m-d"))
			->where('company_id',1)->get();

		// $this->layout->content = View::make('products.import')
		// 	->with('upload_file',$upload_file)
		// 	->with('has_file',$has_file)
		// 	->with('todays_updates',$todays_updates)
		// ;

          $messages = Sysmsg::limit(100)
          ->orderBy('id','DESC')
          ->get();
            return view('products.import',compact('upload_file','has_file','todays_updates','messages'));
	}

	public function postImport(){
        has_role('products_import',1);

		$target_folder = Config::get('app.private_folder') . return_company_id() . "/import/";

		if(!file_exists($target_folder)){
			if(mkdir($target_folder) == false){
				return Redirect::to("/products/import")
					->with('flash_error',"Unable to create input folder");
			}
		}

		if(!Input::hasFile('file')){
			return Redirect::to("/products/import")
				->with('flash_error',"Operation failed. No File uploaded.");
		}

		if(Input::hasFile('file')){
			$file 			= Input::file('file');
			$file_extension = $file->getClientOriginalExtension();

			if($file_extension != "xlsx"){
				return Redirect::to("/products/import")
					->with('flash_error',"File must be Excel with .xlsx extension!");
			}

			$file_name  	= "update_products." . $file_extension;
			$file_original_name = Input::file('file')->getClientOriginalName();
			$file_original_name = preg_replace('/\s+/', '_', $file_original_name);
			$file_size = $file->getSize();
			$mime_type = $file->getMimeType();
			$file->move($target_folder, $file_name);
		}

		return Redirect::to("/products/import")
			->with('flash_success',"Operation success");
	}

	public function postImportDo(){
        has_role('products_import',1);

		$upload_file 	= Config::get('app.private_folder') . return_company_id() . "/import/update_products.xlsx";
		if(!file_exists($upload_file)){
			return Redirect::to("/products/import")
				->with('flash_error',"No file detected.");
		}

		$action 	= strtolower(Input::get('action'));

		$allowed 	= [
			"process",
			"cancel"
		];

		if(!in_array($action,$allowed)){
			return Redirect::to("/products/import")
				->with('flash_error',"Operation failed");
		}

		if($action == "cancel"){
			if(file_exists($upload_file)){
				unlink($upload_file);
			}
			return Redirect::to("/products/import")
				->with('flash_success',"Operation success");
		}

		if($action == "process"){
			$res = $this->_process_imported_file();
			if($res){
				return Redirect::to("/products/import")
					->with('flash_success',"Operation success!");
			} else {
				return Redirect::to("/products/import")
					->with('flash_error',"Operation failed. See log.");
			}
		}

		return Redirect::to("/products/import")
			->with('flash_success',"Operation success");
	}

	public function _process_imported_file(){
        has_role('products_import',1);

		$errors = [];

		$upload_file 	= Config::get('app.private_folder') . "1/import/update_products.xlsx";
		if(!file_exists($upload_file)){
			$errors[] = "No file to process";
		}

		if(count($errors) == 0){
			$objPHPExcel = PHPExcel_IOFactory::load($upload_file);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			//$highestRow = $objWorksheet->getHighestRow();
			//$highestColumn = $objWorksheet->getHighestDataColumn();
			$highestRow    = $objWorksheet->getHighestDataRow();
			$highestColumn = $objWorksheet->getHighestDataColumn();

			$ignore = [
				8012007,
				8011010,
				8182006,
				8240103,
				8200124,
				8230202
			];

			for($row = 1; $row <= $highestRow; $row++){
				$rowData = null;

				$rowData = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, FALSE, FALSE, FALSE);
				$rowData = array_shift($rowData);

				foreach ($rowData as $key => &$value) {
					$value = trim($value);
				}

				list(
					$mpn,
					$base_price_20,
					$base_price_40
				) = $rowData;

				if($mpn == ""){
					continue;
				}

				if($row == 1){
					if($mpn != "mpn" || $base_price_20 != "base_price_20" || $base_price_40 != "base_price_40"){
						$errors[] = "Import failed due to file formatting issue. Please follow format Guidelines!";
						break;
					}
				}

				if(in_array($mpn, $ignore)){
					continue;
				}

				$base_price_20 	= round($base_price_20,4);
				$base_price_40 	= round($base_price_40,4);


				$product 	= Product::where('mpn',$mpn)
					->where('company_id',1)
					->get();

				if($product->count() > 1){
					$errors[] = "Duplicate MPN: {$mpn}";
					continue;
				}

				$product 	= Product::where('mpn',$mpn)
					->where('company_id',1)
					->first();

				if(!$product){
					//print "Product not found! {$mpn}\n";
					continue;
				}

				if($base_price_20 != $product->base_price_20 || $base_price_40 != $product->base_price_40){

					$history = PriceHistory::where('product_id',$product->id)
						->where('created',date("Y-m-d"))
						->first();
					if(!$history){
						$history = new PriceHistory();
					}
					$history->base_price_20 	= $product->base_price_20;
					$history->base_price_40 	= $product->base_price_40;
					$history->created 			= date("Y-m-d");
					$history->product_id 		= $product->id;
					$history->save();

					$product->base_price_20 	= $base_price_20;
					$product->base_price_40 	= $base_price_40;
					$product->save();
				}
			}
		}

		unlink($upload_file);

		foreach($errors as $error){
			logMsg($error);
		}

		if(count($errors) == 0){
			logMsg("Import file processed without errors!");
			return true;
		}

		return false;
	}

    public function getViewImage($product_id,$image_id){
        $product    = Product::findOrFail($product_id);
		$image 		= ProductImage::findOrFail($image_id);

        $full_path = Config::get('app.private_folder') . $product->company_id . "/products/" . $product->id . "/" . $image->picture;

		if(file_exists($full_path)){
            header("Content-Type: image/png");
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
			flush();
			readfile($full_path);
			exit;
		} else {
			print "File not found!";
			exit;
		}
    }


    public function getViewMainImage($product_id){
        $product    = Product::findOrFail($product_id);
        $full_path = Config::get('app.private_folder') . $product->company_id . "/products/" . $product->id . "/" . $product->picture;


		if(file_exists($full_path)){
            header("Content-Type: image/png");
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
			flush();
			readfile($full_path);
			exit;
		} else {
			print "File not found!";
			exit;
		}
    }
    // show product by //
  	public function showProduct($id){
      echo "in product";die;
  	}

}
