<?php
use App\Models\ExchangeRate;// import Exchange rate model
use App\Models\OrderItem;//import OrderItem model
use Illuminate\Support\Facades\Cache;//add cache facade
use App\models\Company;//import model class
use Illuminate\Support\Facades\Auth;
// use Auth;
/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

// ClassLoader::addDirectories(array(
//
// 	app_path().'/commands',
// 	app_path().'/controllers',
// 	app_path().'/models',
// 	app_path().'/database/seeds',
//
// ));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

// Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

// App::missing(function($exception)
// {
//     //$layout = 'layouts.default';
// 	//$this->layout->content = View::make('errors.404');
// 	//return Response::view('errors.404', array(), 404);
// 	return "Sorry, not found";
// });
//
// App::error(function(Exception $exception, $code)
// {
// 	Log::error($exception);
// });

function print_pdf($url,$filename,$save_to_path = ""){
	require 'pdfcrowd.php';

	$counter = 0;
	while($counter < 5){
		$counter++;
		try {
			$client = new Pdfcrowd("dschulz", "31f4df80b106d52e577b9e803512027c");
			$client->setPageWidth("210mm");
			$client->setPageHeight("297mm");

			// convert a web page and store the generated PDF into a $pdf variable
			$pdf = $client->convertURI($url);

			if($save_to_path == ""){
				// Download the PDF
				header("Content-Type: application/pdf");
				header("Cache-Control: no-cache");
				header("Accept-Ranges: none");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				echo $pdf;
			} else {
				// Save PDF to File
				file_put_contents($save_to_path,$pdf);
			}
			return true;
		} catch(PdfcrowdException $why) {
			sleep(2);
		}
	}
	echo "Pdfcrowd Error: " . $why;
	exit;
}

function print_pdf_landscape($url,$filename,$save_to_path = ""){
	require 'pdfcrowd.php';
	try {
		$client = new Pdfcrowd("dschulz", "31f4df80b106d52e577b9e803512027c");
		$client->setPageHeight("210mm");
		$client->setPageWidth("297mm");

        // convert a web page and store the generated PDF into a $pdf variable
		$pdf = $client->convertURI($url);

		if($save_to_path == ""){
            // Download the PDF
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			echo $pdf;
		} else {
            // Save PDF to File
			file_put_contents($save_to_path,$pdf);
		}
		return true;
	}
	catch(PdfcrowdException $why) {
		echo "Pdfcrowd Error: " . $why;
		exit;
	}
}

function has_role($checkrole,$redirect=0){
	static $roles;
	static $roles_def = false;
  // if (!Auth::user()) {
  //   return Redirect::to("/auth/login");
  //   	// ->with('flash_error','Not logged in');
  //  }
  // if(!Auth::check())
  // {
  //   return Redirect::to("/auth/login")
	// 		->with('flash_error','Not logged in');
  // }
	if(Auth::user()){
		if($roles_def == false){
			$roles_def = true;
      // SELECT * FROM `roles` left join role_user on roles.id=role_user.role_id left join users on users.id=role_user.user_id where user_id=15
			$roles = Auth::user()->roles;
      // $roles = Role::select('roles.id','roles.name')->leftjoin('role_user','roles.id','=','role_user.role_id')
      // ->leftjoin('users','users.id','=','role_user.user_id')->where('id',$user_id)->get();

       // print_r($roles);die;
		}

		foreach($roles as $role){
			if(is_array($checkrole)){
				if($role->name == 'admin' || in_array($role,$checkrole)) {
					return true;
				}
			} else {
				if($role->name == 'admin' || $role->name == $checkrole){
					return true;
				}
			}
		}
		if($redirect == 1){
			die("Permission Problem. Try <a href='/'>to click here</a>");
		}
	}
	return false;
}

function return_net_price($price, $percent){
	if($percent == 0){ return $price; }
	$price = $price / $percent;
	return $price;
}

function return_gross_price($price, $percent){
	if($percent == 0){ return $price; }
	$price = $price * $percent;
	return $price;
}

function selectbox_array($name){
	if($name == "currency_codes"){
		return ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->lists('name','name');
	}

	return array();
}

function setting_get($name,$default_return_value = false){
	if($name == 'company_name'){
		$v = Auth::user()->company->name;
		return $v;
	}

	if($name == 'company_bill_to'){
		$v = Auth::user()->company->bill_to;
		return $v;
	}

	if($name == 'company_deliver_to'){
		$v = Auth::user()->company->deliver_to;
		return $v;
	}

	if($name == 'document_footer'){
		$v = Auth::user()->company->document_footer;
		return $v;
	}

	if($name == 'company_bank_info'){
		$v = Auth::user()->company->bank_info;
		return $v;
	}

	$deprecated = ["auto_part_numbers","mrp_running","default_currency_code"];
	if(in_array($name, $deprecated)){
		die("Deprecated: ". $name);
	}

	$setting = Setting::where('name',$name)
	->where('company_id',return_company_id())
	->first();
	if(!$setting){
		$setting = new Setting();
		$setting->name  = $name;
		$setting->company_id = return_company_id();
		if($default_return_value !== false){
			$setting->value = $default_return_value;
		} else {
			$setting->value = '';
		}
		$setting->save();
	}
	return $setting->value;
}

function setting_set($name,$value){
	$setting = Setting::where('name',$name)->first();
	if(!$setting){
		$setting = new Setting();
	}
	$setting->name  = $name;
	$setting->value = $value;
	$setting->save();
	return true;
}



function user_setting_set($name,$value){
	$user = User::find(Auth::user()->id);
	$settings = json_decode($user->settings,TRUE);
	$settings[$name] = $value;
	$settings = json_encode($settings);
	$user->settings = $settings;
	$user->save();
}

function user_setting_get($name,$default_return_value = false){
	$setting = json_decode(Auth::user()->settings,TRUE);
	if(!is_array($setting) && $default_return_value != false){
		return $default_return_value;
	}
	if(!isset($setting[$name]) && $default_return_value != false){
		return $default_return_value;
	}
	return $setting[$name];
}

function warehouse_transaction($product_id,$quantity,$remark = ""){
	if(!isset($user_id)){
		static $user_id;
		if(Auth::guest()){
			$user_id = 1;
		} else {
			$user_id = Auth::user()->id;
		}
	}

	$remark = trim($remark);


	$new = new WarehouseTransaction();
	$new->created_by		= $user_id;
	$new->company_id        = return_company_id();
	$new->product_id		= $product_id;
	$new->quantity			= $quantity;
	$new->remark			= $remark;
	$new->save();

	$product = Product::find($product_id);
	$product->stock 		+= $quantity;
	$product->save();

	return true;
}

function getNewOrderNo($company_id){
	$company = Company::findOrFail($company_id);
	if($company->ids_orders == null){
		$company->ids_orders = 1;
	} else {
		$company->ids_orders += 1;
	}
	$company->save();

	return $company->ids_orders;
}


function getNewPurchaseNo($company_id){
	$company = Company::findOrFail($company_id);
	if($company->ids_purchases == null){
		$company->ids_purchases = 1;
	} else {
		$company->ids_purchases += 1;
	}
	$company->save();

	return $company->ids_purchases;
}

function convert_currency($from_cur,$to_cur,$amount,$date_code=null){
	// echo $date_code;die;
	if($date_code !== null && $date_code != ""){
		$minutes = 10;
		$rate = Cache::remember('exchange_rates', $minutes, function () {
			return DB::table('exchange_rates')->where('date_code',$date_code)->get()->toArray();
		});
		// $rate = ExchangeRate::where('date_code',$date_code)->paginate();//remove remember fucntion from query
		if(!$rate){
			$minutes = 10;
			$rate = Cache::remember('exchange_rates', $minutes, function () {
				return DB::table('exchange_rates')->orderBy('date_code','desc')->get()->toArray();
			});

			// $rate = ExchangeRate::orderBy('date_code','desc')->paginate();//remove remember fucntion from query
		}
	} else {
		$minutes = 10;
		$rate = Cache::remember('exchange_rates', $minutes, function () {
			return DB::table('exchange_rates')->orderBy('date_code','desc')->get()->toArray();
		});

		// $rate = ExchangeRate::orderBy('date_code','desc')->paginate();//remove remember fucntion from query
	}
	// echo "<pre>";
 //  	print_r($rate->rates);die;
	for($i=0;$i<count($rate);$i++){
		$rate_data= $rate[$i]->rates;
		// echo "<pre>";
		//  print_r($rate_data);
		$data = json_decode($rate_data,TRUE);
		 // echo "<pre>";
		 // print_r($data);
	}
	// die('sdfs');

	$from_cur = strtoupper($from_cur);
	$to_cur = strtoupper($to_cur);
	// print_R($data['rates'][$from_cur]);die;

	if(!isset($data['rates'][$from_cur])){
		// print_R($data['rates'][$from_cur]);die;
		die("Unknown currency code $from_cur\n");
	}

	if(!isset($data['rates'][$to_cur])){
		// print_R($data['rates'][$to_cur]);die;
		die("Unknown currency code $to_cur\n");
	}

	if(!is_numeric($amount)){
		return 0;
		die("Currency conversion failed (Input does not seem to be numeric!)\n");
	}

	$result = $amount / $data['rates'][$from_cur];
	$result = $result * $data['rates'][$to_cur];
	return $result;
}

function convert_turnover_currency($res = [],$date_code=null,$to_cur){
	// echo "<pre>";
	// print_R($res);die;
		$sum = 0;
		if($date_code !== null && $date_code != ""){
			$minutes = 10;
			$rate = Cache::remember('exchange_rates', $minutes, function () {
				return DB::table('exchange_rates')->where('date_code',$date_code)->get()->toArray();
			});
		// $rate = ExchangeRate::where('date_code',$date_code)->paginate();//remove remember fucntion from query
			if(!$rate){
				$minutes = 10;
				$rate = Cache::remember('exchange_rates', $minutes, function () {
					return DB::table('exchange_rates')->orderBy('date_code','desc')->get()->toArray();
				});

			// $rate = ExchangeRate::orderBy('date_code','desc')->paginate();//remove remember fucntion from query
			}
		} else {
			$minutes = 10;
			$rate = Cache::remember('exchange_rates', $minutes, function () {
				return DB::table('exchange_rates')->orderBy('date_code','desc')->get()->toArray();
			});

		// $rate = ExchangeRate::orderBy('date_code','desc')->paginate();//remove remember fucntion from query
		}
	// echo "<pre>";
 //  	print_r($rate);die;
		for($i=0;$i<count($rate);$i++){
			$rate_data= $rate[$i]->rates;
		// echo "<pre>";
		//  print_r($rate_data);
			$data = json_decode($rate_data,TRUE);
		 // echo "<pre>";
		 // print_r($data);
		}
	// die('sdfs');
		for($i=0;$i<count($res);$i++){
			$from_cur = strtoupper($res[$i]['currency_code']);
			$to_cur = strtoupper($to_cur);
	// print_R($data['rates'][$from_cur]);die;

			if(!isset($data['rates'][$from_cur])){
		// print_R($data['rates'][$from_cur]);die;
				die("Unknown currency code $from_cur\n");
			}

			if(!isset($data['rates'][$to_cur])){
		// print_R($data['rates'][$to_cur]);die;
				die("Unknown currency code $to_cur\n");
			}

			if(!is_numeric($res[$i]['total_net'])){
				return 0;
				die("Currency conversion failed (Input does not seem to be numeric!)\n");
			}

			$result = $res[$i]['total_net'] / $data['rates'][$from_cur];
			$result = $res[$i]['total_net'] * $data['rates'][$to_cur];
			$sum += $result;
		}
		// print_r($sum);die;
		return $sum;
	}
	function printTree($root){
		$html = '';
		$html .= "<ul>";
		foreach($root as $r){

			$html .= "<li>". $r->name;
			if(count($r->children) > 0) {
				$html .= printTree($r->children);
			}
			$html .= "</li>";
		}
		$html .= "</ul>";
		return $html;
	}


	function printSelect($root,$category_id,$field_name='category_id'){
		$html = '';
		$html .= "<select class='form-control' name='$field_name'>";
		foreach($root as $r){
			$html .= printOption($r,$category_id);
		}
		$html .= "</select>";
		return $html;
	}

	function printOption($node,$category_id=NULL){
	// $level = $node->getLevel();  //
	// $indent = "";  //
	// for($i = 0; $i < $level; $i++){  //
	// $indent .= "&nbsp;|-&nbsp;";  //
	// }  //
	// if($category_id == $node->id){  //
	// 	$checked = 'selected="selected"';  //
	// } else {  //
	// 	$checked = "";  //
	// }  //
	// $html = '<option ' . $checked . ' value="'.$node->id . '">'.$indent . $node->name . '</option>';  //
	// foreach($node->children as $child){  //
	// 		$html .= printOption($child,$category_id);  //
	// }  //
	// return $html;
	}

	function display_number($number, $decimals=3,$strip_zeros=0){
		if($number == ""){ return ""; }
		if(!is_numeric($number)){ return ""; }
		$n = "0.";
		for($i=0; $i<$decimals-1; $i++){
			$n.="0";
		}
		$n.="1";
		$f = $n*1;
		if($number < $f ){
			$result = "<$n";
		} else {
			$result = number_format($number, $decimals);
		}

		if($strip_zeros){
			return rtrim(rtrim($result, "0"),".");
		} else {
			return $result;
		}
	}

	function changelog($model){
		return 1;
		$changelog = new Changelog();
		$changelog->user_id = Auth::user()->id;
		$changelog->remarks = $model->id;
		$changelog->table_name = $model->username;
		$changelog->save();
	}

	function updateOrderStatus($order_id){
		$order = Order::findOrFail($order_id);

	// Loop trough Line Items and update their counters
		foreach($order->items as $order_item){
			$order_item->amount_net 		= $order_item->quantity * $order_item->unit_price_net;

			$order_item->unit_price_gross 	= return_gross_price($order_item->unit_price_net, $order->taxcode->percent);
			$order_item->amount_gross 		= $order_item->quantity * $order_item->unit_price_gross;

			$order_item->tax 			= $order_item->unit_price_gross - $order_item->unit_price_net;
			$order_item->tax_amount		= ($order_item->unit_price_gross - $order_item->unit_price_net) * $order_item->quantity;

			if($order->container->code == '40hq'){
				$order_item->pack_unit = $order_item->product->pack_unit_hq;
				$order_item->units_per_pallette = $order_item->product->units_per_pallette_hq;
			} else {
				$order_item->pack_unit = $order_item->product->pack_unit;
				$order_item->units_per_pallette = $order_item->product->units_per_pallette;
			}

			if($order->container->code == '40hq'){
				$order_item->cbm = ($order_item->product->carton_size_w_hq * $order_item->product->carton_size_d_hq * $order_item->product->carton_size_h_hq);
			} else {
				$order_item->cbm = ($order_item->product->carton_size_w * $order_item->product->carton_size_d * $order_item->product->carton_size_h);
			}
			$order_item->save();
		}


	// End Loop

		$order->sub_total_net 		= $order->items->sum('amount_net');
		if($order->discount > 0){
			$order->sub_total_net 	= $order->sub_total_net - (($order->sub_total_net / 100) * $order->discount);
		}
		$order->total_net 			= $order->sub_total_net + $order->shipping_cost;

		$order->sub_total_gross 	= return_gross_price($order->total_net, $order->taxcode->percent);
		$order->total_gross			= $order->sub_total_gross;


		$order->tax_total 			= ($order->total_gross - $order->total_net);

    //$order->total += $order->shipping_cost;

	//$order->net_handling_amount  = return_net_price($order->gross_handling_amount, $order->taxcode_percent);
	//$order->net_shipping_amount  = return_net_price($order->gross_shipping_amount, $order->taxcode_percent);

	///$order->gross_total 		= $order->gross_sub_total;
	///$order->gross_total     += $order->gross_handling_amount;
	//$order->gross_total 	   += $order->gross_shipping_amount;
	//$order->net_total 		= return_net_price($order->gross_total, $order->taxcode_percent);

		$order_total_cbm = Orderitem::where("order_id","=",$order->id)->sum('calc_cbm');
		$order_total_paid = DB::table('customer_payments')->where("order_id","=",$order->id)->sum('amount');
		$order_total_paid += DB::table('customer_payments')->where("order_id","=",$order->id)->sum('bank_charges');



		$order->total_paid = $order_total_paid;
		$order->open_amount = $order->total_gross - $order_total_paid;
		$order->calc_cbm = $order_total_cbm;

	//$order->tax_total 		= $order->gross_total - $order->net_total;
		$order->save();
	}

	function setupCompany($id){
		$private_folder = Config::get('app.private_folder') . $id . "/";

		@mkdir($private_folder);
		@mkdir($private_folder . "customers");
		@mkdir($private_folder . "employees");
		@mkdir($private_folder . "import");
		@mkdir($private_folder . "invoices");
		@mkdir($private_folder . "opos");
		@mkdir($private_folder . "orders");
		@mkdir($private_folder . "pricelists");
		@mkdir($private_folder . "products");
		@mkdir($private_folder . "purchases");
		@mkdir($private_folder . "quotations");
		@mkdir($private_folder . "tmp");
		@mkdir($private_folder . "user_expenses");
		@mkdir($private_folder . "users");
		@mkdir($private_folder . "vendors");
		@mkdir($private_folder . "sliders");
		@mkdir($private_folder . "reports");

		return true;
	}

	function formatSizeUnits($bytes) {
		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	function syncProductAcrossCompanies(&$original,$companies = array()){
		if($original->company_id != 1){
			return 1;
		}
		if($original->company_sync == 0){
			return 0;
		}

		if(count($companies) == 0){
			$companies = Company::where('status','Active')->where('name','!=',"")->get();
		}

		$last_sync = date("Y-m-d H:i:s");

		$skip_fields = [
			"id",
			"description_local",
			"remarks",
			"moq",
			"track_stock",
			"stock",
			"stock_min",
			"location",
			"is_webshop",
			"is_pricelist",
			"company_id",
			"updated_at",
			"base_price_20",
			"base_price_40",
			"sales_base_20",
			"sales_base_40",
			"landed_20",
			"landed_40",
			"parent_id",
			"last_sync",
		];

		$original_array = $original->toArray();
		$original_array = array_diff_key($original_array, array_flip($skip_fields));

		foreach($companies as $company){
			if($company->id == 1){
				continue;
			}

			$slave = Product::where('company_id',$company->id)
			->where('parent_id',$original->id)
			->first();

			if(!$slave){
				$slave = New Product();
            $slave->created_by = 1;//Auth::user()->id;
            $slave->updated_by = 1;//Auth::user()->id;
            $slave->parent_id  = $original->id;
            $slave->company_id = $company->id;
            $slave->fill($original_array);
            $slave->last_sync = $last_sync;
            $slave->base_price_20 = $original->getPriceByCustomerId($company->customer_id,20);
            $slave->base_price_40 = $original->getPriceByCustomerId($company->customer_id,40);
            $slave->save();
        } else {
        	if($original->updated_at > $slave->last_sync){
        		$slave->fill($original_array);
        		$slave->last_sync = $last_sync;
        		$slave->base_price_20 = $original->getPriceByCustomerId($company->customer_id,20);
        		$slave->base_price_40 = $original->getPriceByCustomerId($company->customer_id,40);
        		$slave->save();
        	}
        }

		// sync product_images
        $slave_folder = Config::get('app.private_folder') . $company->id . "/products/" . $slave->id . "/";
        $original_folder = Config::get('app.private_folder') . $original->company_id . "/products/" . $original->id . "/";

        if(!file_exists($slave_folder)){
        	@mkdir($slave_folder);
        }

        $original_images = ProductImage::where('product_id',$original->id)->get();
        foreach($original_images as $original_image){
        	$slave_image = ProductImage::where('parent_id',$original_image->id)
        	->where('company_id',$company->id)
        	->first();
        	$original_image_array = $original_image->toArray();
        	unset($original_image_array['id']);
        	unset($original_image_array['company_id']);
        	unset($original_image_array['parent_id']);
        	unset($original_image_array['product_id']);

        	if(!$slave_image){
        		$slave_image = new ProductImage();
        		$slave_image->fill($original_image_array);
        		$slave_image->created_by = 1;
        		$slave_image->updated_by = 1;
        		$slave_image->company_id = $company->id;
        		$slave_image->parent_id  = $original_image->id;
        		$slave_image->product_id = $slave->id;
        		$slave_image->save();

        		if(file_exists($original_folder . $original_image->picture)){
        			if(!file_exists($slave_folder . $original_image->picture)){
        				link($original_folder . $original_image->picture, $slave_folder . $original_image->picture);
        			}
        		}
        	} else {
        		if($original_image->updated_at > $slave_image->updated_at){
        			$slave_image->fill($original_image_array);
        			$slave_image->save();

        			if(file_exists($slave_folder . $original_image->picture)){
        				print "Relinking\n";
        				unlink($slave_folder . $original_image->picture);
        				link($original_folder . $original_image->picture, $slave_folder . $original_image->picture);
        			}
        		}
        	}
        }

        if(SYS_DEBUG){
        	print "Syncing {$original->id} <> {$slave->id}\n";
        }
    }

    return 0;
}

function excel_bold_row(&$worksheet, $row_id){
	$max_column = $worksheet->getHighestColumn();
	$worksheet->getStyle("A{$row_id}:{$max_column}{$row_id}")->getFont()->setBold(true);
}

function excel_bold_column(&$worksheet, $column_id){
	$max_column = $worksheet->getHighestColumn();
	$worksheet->getStyle("{$column_id}")->getFont()->setBold(true);
}


function cellColor(&$worksheet,$cells,$color){
	$worksheet->getStyle($cells)->getFill()->applyFromArray(array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array(
			'rgb' => $color
		)
	));
}

function updatePurchaseStatus($purchase_id){
	$purchase = Purchase::findOrFail($purchase_id);

	$is_delivered 	= true;
	$is_partial 	= false;

	// Loop trough Line Items and update their counters
	foreach($purchase->items as $purchase_item){
		$purchase_item->net_price  	= return_net_price($purchase_item->gross_price, $purchase->taxcode_percent);
		$purchase_item->net_total  	= $purchase_item->quantity * $purchase_item->net_price;
		$purchase_item->gross_total = $purchase_item->quantity * $purchase_item->gross_price;
		$purchase_item->tax 		= $purchase_item->gross_price - $purchase_item->net_price;
		$purchase_item->tax_total 	= ($purchase_item->gross_price - $purchase_item->net_price) * $purchase_item->quantity;

		$quantity_delivered  = $purchase_item->getQuantityDelivered();
		$quantity_reconciled = $purchase_item->getQuantityReconciled();

		if($purchase_item->product->track_stock > 0){
			$quantity_open 		= $purchase_item->quantity - $quantity_delivered - $quantity_reconciled;
		} else {
			$quantity_open = 0;
		}

		if($quantity_open < $purchase_item->quantity){
			$is_partial = true;
		}

		if($quantity_open > 0){
			$is_delivered = false;
		}

		$purchase_item->quantity_delivered  = $quantity_delivered;
		$purchase_item->quantity_reconciled = $quantity_reconciled;
		$purchase_item->quantity_open 		= $quantity_open;

		$purchase_item->save();
	}
	// End Loop

	if($is_delivered === true){
		$purchase->status = preg_replace("/^.+,/","DELIVERED,",$purchase->status);
	} elseif ($is_partial === true){
		$purchase->status = preg_replace("/^.+,/","PARTIAL,",$purchase->status);
	} else {
		$purchase->status = preg_replace("/^.+,/","UNDELIVERED,",$purchase->status);
	}

	$purchase->net_sub_total 	= $purchase->items->sum('net_total');
	$purchase->gross_sub_total  = $purchase->items->sum('gross_total');

	$purchase->net_handling_amount 	= return_net_price($purchase->gross_handling_amount, $purchase->taxcode_percent);
	$purchase->net_shipping_amount  = return_net_price($purchase->gross_shipping_amount, $purchase->taxcode_percent);

	$purchase->gross_total 		= $purchase->gross_sub_total;
	$purchase->gross_total     += $purchase->gross_handling_amount;
	$purchase->gross_total 	   += $purchase->gross_shipping_amount;
	$purchase->net_total 		= return_net_price($purchase->gross_total, $purchase->taxcode_percent);

	$purchase->tax_total 		= $purchase->gross_total - $purchase->net_total;
	$purchase->save();

	// Start calculate payment_status
	$paid_total = $purchase->getPaidUntilNow();
	$paid_total = round($paid_total, 2);

	if($paid_total == 0){
		$purchase->status = preg_replace("/,.+$/",",UNPAID",$purchase->status);
	} else if($paid_total >= $purchase->gross_total){
		$purchase->status = preg_replace("/,.+$/",",PAID",$purchase->status);
	} else if($paid_total > 0){
		$purchase->status = preg_replace("/,.+$/",",PARTIAL",$purchase->status);
	}
	$purchase->save();
	// End calculate payment_status
    //
	return 1;
}

function getDateDifferenceInDays($date1,$date2){
	$date1 = new DateTime($date1);
	$date2 = new DateTime($date2);
	$interval = $date1->diff($date2);
	return $interval->days;
}

function getMemoryUsage() {
	$mem_usage = memory_get_usage(true);
	return round($mem_usage/1048576,2); //." megabytes";
}

function autoFitColumnWidthToContent($sheet, $fromCol, $toCol) {
    if (empty($toCol) ) {//not defined the last column, set it the max one
    	$toCol = $sheet->getColumnDimension($sheet->getHighestColumn())->getColumnIndex();
    }
    for($i = $fromCol; $i <= $toCol; $i++) {
    	$sheet->getColumnDimension($i)->setAutoSize(true);
    }
    $sheet->calculateColumnWidths();
}


function return_company_id(){
	return Auth::user()->company_id;
}


function logMsg($message,$severity="info",$module="general"){

	$new = new Sysmsg();
	$new->message = $message;
	$new->severity = $severity;
	$new->module = $module;
	$new->save();

	return true;
}

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/
//
// App::down(function()
// {
// 	return Response::make("System is currently undergoing maintenance. Check back later!", 503);
// });

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/
//
// require app_path().'/filters.php';
