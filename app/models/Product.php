<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		Product::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Product';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Product {$record->id}";
			$changelog->save();
		});

		Product::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Product';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Product {$record->id}";
			$changelog->save();
		});

		Product::updated(function($record){
			$ignored_fields = ['updated_at','updated_by'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Product';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Product';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata}";
					$changelog->save();
				}
			}
		});
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function vendors() {
		return $this->hasMany('App\Models\ProductVendor');
	}

	public function locks() {
		return $this->hasMany('App\Models\ProductLock');
	}

	public function customers() {
		return $this->hasMany('App\Models\ProductCustomer');
	}

    public function prices(){
		return $this->hasMany('App\Models\ProductPrice');
    }

    public function priceOverrides(){
		return $this->hasMany('App\Models\ProductPriceOverride');
    }

	public function attachments() {
		return $this->hasMany('App\Models\ProductAttachment');
	}

	public function bom() {
		return $this->hasMany('App\Models\ProductBom');
	}

	public function stocks() {
		return $this->hasMany('App\Models\WarehouseStock');
	}

	public function purchases() {
		return $this->hasMany('App\Models\PurchaseItem');
	}

	public function transactions(){
		return $this->hasMany('App\Models\WarehouseTransaction');
	}

	public function attributes(){
		return $this->hasMany('App\Models\ProductAttribute');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function getStockOnHand($warehouses = array(1000)){
		return $this->stock;
	}

    public function company(){
		return $this->belongsTo('App\Models\Company');
    }

	public function getVendorInfo($vendor_id=0,$quantity=0,$to_currency_code=""){


		if($vendor_id == 0){
			$query = DB::table('product_vendors')
				->where("product_id",$this->id)
				->where("status","ACTIVE")
				->where("preferred","YES")
				->where("moq","<=",$quantity)
				->where("deleted_at",NULL)
				->orderBy("price")
				->first();


			if(!$query){
				$query = DB::table('product_vendors')
					->where("product_id",$this->id)
					->where("status","ACTIVE")
					->where("preferred","YES")
					->where("deleted_at",NULL)
					->orderBy(DB::raw("abs(moq - $quantity)"))
					->first();
			}
		} else {
			$query = DB::table('product_vendors')
				->where("product_id",$this->id)
				->where("status","ACTIVE")
				->where("vendor_id",$vendor_id)
				->where("deleted_at",NULL)
				->orderBy(DB::raw("abs(moq - $quantity)"))
				->first();
		}

		$results = array(
			"net_price"		=> null,
			"gross_price" 	=> null,
			"lead_time"		=> null,
			"moq"			=> null,
			"part_number"	=> null,
			"vendor_id"		=> null,
			"cheapest_price"=> null,
			"cheapest_moq"  => null
		);

		if($query){
            $vendor = Vendor::findOrFail($query->vendor_id);
			if($vendor->currency_code != $to_currency_code){
				$price_converted = convert_currency($vendor->currency_code,$to_currency_code,$query->price);
				$price_converted = round($price_converted,4);
				$query->price	 = $price_converted;
			}

			// Improve this somehow and reduce queries used
			$taxcode_id 	 = DB::table('vendors')->where('id', $query->vendor_id)->pluck('taxcode_id');
			$taxcode_percent = DB::table('taxcodes')->where('id', $taxcode_id)->pluck('percent');

			$results['net_price']   = return_net_price($query->price, $taxcode_percent);
			$results['gross_price'] = $query->price;
			$results['lead_time']	= $query->lead_time;
			$results['moq']			= $query->moq;
			$results['part_number'] = $query->part_number;
			$results['vendor_id']	= $query->vendor_id;
		}

		return $results;
	}

	public function getCustomerInfo($customer_id,$to_currency_code){
		$query = DB::table('product_customers')
			->where("product_id",$this->id)
			->where("status","ACTIVE")
            ->where('company_id',return_company_id())
			->first();

		$results = array(
			"price"			=> 0,
			"part_number"	=> "",
			"customer_id"	=> 0
		);

		if($query){
            $customer = Customer::findOrFail($query->customer_id);
			if($customer->currency_code != $to_currency_code){
				$price_converted = convert_currency(Auth::user()->company->currency_code,$to_currency_code,$query->price);
				$price_converted = round($price_converted,4);
				$query->price	 = $price_converted;
			}
			$results['price']		= $query->price;
			$results['part_number'] = $query->part_number;
			$results['customer_id']	= $query->customer_id;
		}

		return $results;
	}

    public function getOnOrder(){
        $result = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
        	->where('purchase_items.product_id',$this->id)
			->whereIn('purchases.status',["UNDELIVERED,UNPAID","UNDELIVERED,PARTIAL","UNDELIVERED,PAID","PARTIAL,UNPAID","PARTIAL,PARTIAL","PARTIAL,PAID"])
        	->sum('quantity_open');

		return $result;
	}



	public function get_quantity_on_order(){
        $result = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
        	->where('purchase_items.product_id',$this->id)
			->whereIn('purchases.status',["UNDELIVERED,UNPAID","UNDELIVERED,PARTIAL","UNDELIVERED,PAID","PARTIAL,UNPAID","PARTIAL,PARTIAL","PARTIAL,PAID"])
        	->sum('quantity_open');

		return $result;
	}

	public function get_quantity_delivered(){
        $result = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
        	->where('purchase_items.product_id',$this->id)
			->whereIn('purchases.status',["UNDELIVERED,UNPAID","UNDELIVERED,PARTIAL","UNDELIVERED,PAID","PARTIAL,UNPAID","PARTIAL,PARTIAL","PARTIAL,PAID"])
        	->sum('quantity_delivered');

		return $result;
	}

	public function get_quantity_in_iqc(){
        $result = PurchaseItem::Leftjoin('purchases','purchase_items.purchase_id','=','purchases.id')
        	->where('purchase_items.product_id',$this->id)
			->whereIn('purchases.status',["UNDELIVERED,UNPAID","UNDELIVERED,PARTIAL","UNDELIVERED,PAID","PARTIAL,UNPAID","PARTIAL,PARTIAL","PARTIAL,PAID"])
        	->sum('quantity_delivered');

		return $result;
	}

	public function get_quantity_required_all(){
		$result = MaterialDemandTotal::where('product_id',$this->id)->sum('quantity_required');
		return $result;
	}

    public function getLastPriceUpdate(){
        $last_update = ProductVendor::where('product_id',$this->id)
            ->where('preferred','YES')
            ->where('status','ACTIVE')
            ->orderBy('updated_at')
            ->first();
        if($last_update){
            $timestamp = strtotime($last_update->updated_at);
            return date('Y-m-d',$timestamp);
        } else {
            return '0000-00-00';
        }
    }

	public function getLastPurchase(){
		$last_purchase = DB::table('purchases')
				->select(
					'purchase_items.purchase_id',
					'purchase_items.quantity',
					'purchase_items.gross_price',
					'purchases.currency_code',
					'purchases.vendor_id',
					'purchases.date_placed'
				)
				->leftJoin('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
				->where('purchase_items.product_id',$this->id)
				->where('purchases.status','!=','VOID')
				->where('purchases.status','!=','DRAFT')
				->orderBy('purchases.id','DESC')
				->first();

		if($last_purchase){
			return $last_purchase;
		} else {
			return false;
		}
	}

	public function getLockStatus(){
		// return 0 = no locks
		// return 1 = current user lock
		// return 2 = other user lock

		$user_id = Auth::user()->id;
		if($this->locks->count() == 0){
			return 0;
		}

		foreach($this->locks as $lock){
			if($lock->user_id == $user_id){
				return 1;
			}
		}

		return 2;
	}

    public function slaves(){
        return Product::where('parent_id',$this->id)->get();
    }

	public function getLocks(){
		$usernames = "Locks by: ";
		foreach($this->locks as $lock){
			$usernames .= $lock->user->username . ",";
		}
		$usernames = rtrim($usernames,",");
		return $usernames;
	}

    public function customerSpecifics(){
        return $this->hasMany('App\Models\ProductCustomerSpecific');
    }

    public function getSalePrice(&$order,&$customer,$currency_code=null){
        $container = $order->container;
        $product   = $this;
        $price = 0;
        $product_id = $this->id;
        $customer_id = $customer->id;


		if($currency_code == null){
			$currency_code = "USD";
		}

        // First Get the Base Price and the Group Price, add them together
        if($order->container->base_price == '20'){
        	$price = $product->sales_base_20;
        	$base_price = $product->sales_base_20;

			$group_prices = ProductPrice::where('product_id',$product_id)->where('customer_group_id', $customer->group_id)->first();
			if($group_prices && is_numeric($group_prices->surcharge_20)){
				$price /= $group_prices->surcharge_20;
			} else {
				// if there was no product price, get price from customer group
				$customer_group_prices = CustomerGroup::where('id',$customer->group_id)->first();
				if($container->base_price == '20'){
					$group_surcharge = $customer_group_prices->surcharge_20;
				} else {
					$group_surcharge = 0;
				}
				$price /= $group_surcharge;
			}
		} elseif($container->base_price == '40'){
			$price = $product->sales_base_40;
        	$base_price = $product->sales_base_40;

			$group_prices = ProductPrice::where('product_id',$product_id)->where('customer_group_id', $customer->group_id)->first();
			if($group_prices && is_numeric($group_prices->surcharge_40)){
				$price /= $group_prices->surcharge_40;
			} else {
				// if there was no product price, get price from customer group
				$customer_group_prices = CustomerGroup::where('id',$customer->group_id)->first();
				if($container->base_price == '40'){
					$group_surcharge = $customer_group_prices->surcharge_40;
				} else {
					$group_surcharge = 0;
				}
				$price /= $group_surcharge;
			}
		}

		// Or, If there is an Override set on the product level, just use that price
		$customer_override = ProductPriceOverride::where('customer_id',$customer_id)->where('product_id',$product_id)->first();
		if($customer_override != NULL){
			if($container->base_price == '20'){
				$price_override = $customer_override->base_price_20;
			} elseif($container->base_price == '40'){
				$price_override = $customer_override->base_price_40;
			}
			$price = $price_override;
		}

        return convert_currency($order->company->currency_code,$currency_code,$price);
    }

    public function getProductPicture($h=125,$w=125){

		if($this->picture != ""){
            return "/view-main-image/".$this->id;
		}

		static $match = array();
		static $cat_match = array();

		if(isset($match[$this->id])){
			return $match[$this->id];
		}

		$ancestor_ids = array();

		if($this->category_id > 0){
			$category = Category::where('id', '=', $this->category_id)->first();
			$ancestors = $category->ancestorsAndSelf()->get();

			foreach($ancestors as $a){
				if($a->picture != ""){
					$ancestor_ids[$a->id] = "/public/categories/{$a->picture}";
					$match[$this->id] = "/public/categories/{$a->picture}";
				}
			}

			if(isset($match[$this->id])){
				return $match[$this->id];
			}

		}

		return "http://placehold.it/{$h}x{$w}";
	}

    public function getPriceByCustomerId($customer_id,$type){
		$customer = Customer::select(["id","group_id"])->where('id',$customer_id)->first();
		$group    = CustomerGroup::where('id',$customer->group_id)->remember(10)->first();

		$group_prices_raw = ProductPrice::where("customer_group_id",$customer->group_id)->where('product_id',$this->id)->get();

		$group_prices = array();
		foreach($group_prices_raw as $group_price_raw){
			$group_prices[$group_price_raw->product_id]['20'] = $group_price_raw->surcharge_20;
			$group_prices[$group_price_raw->product_id]['40'] = $group_price_raw->surcharge_40;
		}

		$price_overrides_raw = ProductPriceOverride::select(array("product_id","customer_id","base_price_20","base_price_40"))
			->where('customer_id',$customer_id)
			->get();
		$price_overrides = array();
		if($price_overrides_raw){
			foreach($price_overrides_raw as $price_override){
				$price_overrides[$price_override->product_id]['base_price_20'] = $price_override->base_price_20;
				$price_overrides[$price_override->product_id]['base_price_40'] = $price_override->base_price_40;
			}
		}

        if(isset($group_prices[$this->id])){
            $price_20 = round($this->sales_base_20 / $group_prices[$this->id]['20'],2);
            $price_40 = round($this->sales_base_40 / $group_prices[$this->id]['40'],2);
        } else {
            $price_20 = round($this->sales_base_20 / $group->surcharge_20,2);
            $price_40 = round($this->sales_base_40 / $group->surcharge_40,2);
        }

        if(isset($price_overrides[$this->id])){
            if(is_numeric($price_overrides[$this->id]['base_price_20'])){
                $price_20 = $price_overrides[$this->id]['base_price_20'];
            }
            if(is_numeric($price_overrides[$this->id]['base_price_40'])){
                $price_40 = $price_overrides[$this->id]['base_price_40'];
            }
        }

        $prices[$this->id]['20'] = number_format($price_20,2);
        $prices[$this->id]['40'] = number_format($price_40,2);

		return $prices[$this->id][$type];
	}

    public function getPrice($type){
		static $logged_in;
		static $prices = array();

		if($logged_in === false){
			return "";
		}
		if($logged_in == ""){
			if(Auth::guest()){
				$logged_in = false;
				return "";
			}
		}

		if(isset($prices[$this->id][$type])){
			return $prices[$this->id][$type];
		}

		$customer_id = Auth::user()->customer_id;
		$customer = Customer::findOrFail($customer_id);
		$group    = CustomerGroup::findOrFail($customer->group_id);

		$group_prices_raw =ProductPrice::where("customer_group_id","=",$customer->group_id)->get();
		$group_prices = array();
		foreach($group_prices_raw as $group_price_raw){
			$group_prices[$group_price_raw->product_id]['20'] = $group_price_raw->surcharge_20;
			$group_prices[$group_price_raw->product_id]['40'] = $group_price_raw->surcharge_40;
		}

		$price_overrides_raw = ProductPriceOverride::select(array("product_id","customer_id","base_price_20","base_price_40"))
			->where('customer_id',$customer_id)
			->get();
		$price_overrides = array();
		if($price_overrides_raw){
			foreach($price_overrides_raw as $price_override){
				$price_overrides[$price_override->product_id]['base_price_20'] = $price_override->base_price_20;
				$price_overrides[$price_override->product_id]['base_price_40'] = $price_override->base_price_40;
			}
		}

		$category  = Category::find($this->category_id);
		$ancestors = $category->ancestorsAndSelf()->get();
		$category_ids = array();
		foreach($ancestors as $a){
			$category_ids[] = $a->id;
		}

		$products = Product::whereIn('category_id',$category_ids)->get();

		foreach($products as $p){
			if(isset($group_prices[$p->id])){
				$price_20 = round($p->sales_base_20 / $group_prices[$p->id]['20'],2);
				$price_40 = round($p->sales_base_40 / $group_prices[$p->id]['40'],2);
			} else {
				$price_20 = round($p->sales_base_20 / $group->surcharge_20,2);
				$price_40 = round($p->sales_base_40 / $group->surcharge_40,2);
			}

			if(isset($price_overrides[$p->id])){
				if(is_numeric($price_overrides[$p->id]['base_price_20'])){
					$price_20 = $price_overrides[$p->id]['base_price_20'];
				}
				if(is_numeric($price_overrides[$p->id]['base_price_40'])){
					$price_40 = $price_overrides[$p->id]['base_price_40'];
				}
			}

			$prices[$p->id]['20'] = number_format($price_20,2);
			$prices[$p->id]['40'] = number_format($price_40,2);
		}

		return $prices[$this->id][$type];
	}


    public function images(){
        return $this->hasMany('App\Models\ProductImage');
    }
    public function downloads(){
        return $this->hasMany('App\Models\ProductDownload');
    }


}
