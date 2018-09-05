<?php
namespace App\Models; //change name space

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;//change softdelete  facade
class Vendor extends Model {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		Vendor::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Vendor';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Vendor';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Vendor {$record->id}";
			$changelog->save();
		});

		Vendor::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Vendor';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Vendor';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Vendor {$record->id}";
			$changelog->save();
		});

		Vendor::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Vendor';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Vendor';
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
		return $this->hasMany('App\Models\VendorContact');
	}

	public function taxcode() {
		return $this->belongsTo('App\Models\Taxcode');
	}

	public function getPaymentsOutstanding(){
		if(!has_role('admin')){
			return 0;
		}
		$po_amount = 0;
		$currency_code = Auth::user()->company->currency_code;
		$purchases = Purchase::where('vendor_id',$this->id)
			->whereIn('status',['UNDELIVERED,UNPAID','UNDELIVERED,PARTIAL','PARTIAL,UNPAID','PARTIAL,PARTIAL'])
			->where('vendor_id',$this->id)
			->get();

		foreach($purchases as $purchase){
			$po_amount += convert_currency($purchase->currency_code,$currency_code,$purchase->gross_total,date("Y-m-d"));
			foreach($purchase->payments as $payment){
				$po_amount -= convert_currency($payment->currency_code,$currency_code,$payment->amount,date("Y-m-d"));
			}
		}
		return $po_amount;
	}

	public function products() {
		return $this->hasMany('App\Models\ProductVendor');
	}


}
