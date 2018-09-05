<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
 Model States
	UNDELIVERED,UNPAID
	UNDELIVERED,PARTIAL
	UNDELIVERED,PAID
	PARTIAL,UNPAID
	PARTIAL,PARTIAL
	PARTIAL,PAID
	DELIVERED,UNPAID
	DELIVERED,PARTIAL
	DELIVERED,PAID
*/

class Purchase extends Model {

	public static function boot(){
		parent::boot();

		Purchase::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Purchase';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Purchase';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created P.O {$record->id}";
			$changelog->save();
		});

		Purchase::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Purchase';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Purchase';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed P.O {$record->id}";
			$changelog->save();
		});

		Purchase::updated(function($record){
			$ignored_fields = ['updated_at','net_shipping_amount','net_sub_total','net_total','tax_total','taxcode_percent','taxcode_id','line_no','gross_sub_total','gross_total'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Purchase';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Purchase';
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

	public function items() {
		return $this->hasMany('App\Models\PurchaseItem')->orderBy('sort_no');
	}

	public function payments() {
		return $this->hasMany('App\Models\PurchasePayment');
	}

	public function orders() {
		return $this->hasMany('App\Models\PurchaseToOrder');
	}

	public function deliveries() {
		return $this->hasMany('App\Models\PurchaseDelivery');
	}

	public function vendor() {
		return $this->belongsTo('App\Models\Vendor');
	}

	public function company() {
		return $this->belongsTo('App\Models\Company');
	}

	public function taxcode() {
		return $this->belongsTo('App\Models\Taxcode');
	}


	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function transactions() {
		return $this->morphMany('App\Models\WarehouseTransaction', 'transaction');
	}

	public function getPaidUntilNow(){
		$paid_till_now = 0;
		foreach($this->payments as $payment){
			$payment_amount = convert_currency($payment->currency_code,$this->currency_code,$payment->amount,$payment->payment_date);
			$paid_till_now += $payment_amount;
		}
		return $paid_till_now;
	}

	public function getOpenBalance(){
		if(strstr($this->status,",PAID")){
			return 0;
		}
		$open_balance = $this->gross_total;
		foreach($this->payments as $payment){
			$payment_amount = convert_currency($payment->currency_code,$this->currency_code,$payment->amount,$payment->payment_date);
			$open_balance -= $payment_amount;
		}
		if($open_balance < 0){
			return 0;
		}

		return round($open_balance,2);
	}

	public function history() {
		return $this->hasMany('PurchaseHistory1');
	}

	public function watchers(){
		return $this->morphMany('Watcher', 'watchers');
	}

	public function isWatching(){
		$watcher = Watcher::where('watchers_type','Purchase')
			->where('watchers_id',$this->id)
			->where('user_id',Auth::user()->id)
			->first();
		if($watcher){
			return true;
		} else {
			return false;
		}
	}

	public function getGrossTotal($currency_code="",$date=""){
		if($currency_code == ""){
			return round($this->gross_total,2);
		} else {
			return round(convert_currency($this->currency_code,$currency_code,$this->gross_total,$date),2);
		}
	}
	


}
