<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		Customer::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Customer';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Customer';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Customer {$record->id}";
			$changelog->save();
		});

		Customer::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Customer';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Customer';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Customer {$record->id}";
			$changelog->save();
		});

		Customer::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Customer';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Customer';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->save();
				}
			}
		});
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function contacts() {
		return $this->hasMany(CustomerContact::class);
	}

	public function addresses() {
		return $this->hasMany(CustomerAddress::class);
	}

	public function invoices(){
		return $this->hasMany('App\Models\Invoice');
	}

	public function orders(){
		return $this->hasMany('App\Models\Order');
	}

	public function taxcode() {
		return $this->belongsTo('App\Models\Taxcode');
	}

	public function group() {
		return $this->belongsTo('App\Models\CustomerGroup');
	}

	public function getOpenBalance($currency_code){
		$invoices_amount = 0;
		$invoices = Invoice::whereIn('status',['UNPAID,UNSHIPPED','PARTIAL,UNSHIPPED','UNPAID,SHIPPED','PARTIAL,SHIPPED'])
			->where('customer_id',$this->id)
			->get();

		foreach($invoices as $invoice){
			$invoices_amount += $invoice->getOpenBalance($currency_code);
		}
		return $invoices_amount;
	}

	public function getShipTo($address_id=null){
		$ship_to = "";

		if($address_id == null){
			if($this->inv_address1 != ""){
				$ship_to .= $this->inv_address1 . "\n";
			}
			if($this->inv_address2 != ""){
				$ship_to .= $this->inv_address2 . "\n";
			}
			if($this->inv_postal_code != ""){
				$ship_to .= $this->inv_postal_code . " ";
			}
			if($this->inv_city != ""){
				$ship_to .= $this->inv_city . "\n";
			}
			if($this->inv_province != ""){
				$ship_to .= $this->inv_province . ", ";
			}
			if($this->inv_country != ""){
				$ship_to .= $this->inv_country . "\n";
			}
		} else {
			$address = CustomerAddress::find($address_id);
			if(!$address){
				return "";
			}
			if($address->address1 != ""){
				$ship_to .= $address->address1 . "\n";
			}
			if($address->address2 != ""){
				$ship_to .= $address->address2 . "\n";
			}
			if($address->postal_code != ""){
				$ship_to .= $address->postal_code . " ";
			}
			if($address->city != ""){
				$ship_to .= $address->city . "\n";
			}
			if($address->province != ""){
				$ship_to .= $address->province . ", ";
			}
			if($address->country != ""){
				$ship_to .= $address->country . "\n";
			}
			if($address->phone != ""){
				$ship_to .= "Tel:" . $address->phone . "\n";
			}
			if($address->fax != ""){
				$ship_to .= "Fax:" . $address->fax . "\n";
			}
		}
		return $ship_to;
	}

	public function getOverdueMoney($currency_code){
		// echo $currency_code;die;
        $overdue_ids = Order::select(
				'id'
			)
			->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
			->where("customer_id",$this->id)
			->pluck('id');

		$orders = Order::whereIn('id',$overdue_ids)->get();

		$total 		= 0;

		foreach($orders as $order){
			if($order->open_amount <= 0){continue;};

			$due   = $order->getDueDate();
			if($due > date("Y-m-d")){
				continue;
			}

			$total += convert_currency($order->currency_code,$currency_code,$order->open_amount);
		}
		// print_R($total);die;

		return $total;
	}


	public function getOutstandingMoney($currency_code){
		// echo $currency_code;die;
        $overdue_ids = Order::select(
				'id'
			)
			->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
			->where("customer_id",$this->id)
			->pluck('id');

		$orders = Order::whereIn('id',$overdue_ids)->get();
		// print_r($orders);die;

		$total 		= 0;

		foreach($orders as $order){
			if($order->open_amount <= 0){continue;};
			// print_r($order->currency_code);die;
			$total += convert_currency($order->currency_code,$currency_code,$order->open_amount);
		}
		  // print_r($total);die;

		return $total;
	}

    public function setCreditAttribute($value){
        if(strlen($value)<1){
            $this->attributes['credit'] = NULL;
        } else {
            $this->attributes['credit'] = $value;
        }
    }


}
