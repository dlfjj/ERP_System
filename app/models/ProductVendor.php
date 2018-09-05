<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVendor extends Eloquent {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		ProductVendor::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductVendor';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Vendor {$record->vendor->company_name}";
			$changelog->save();
		});

		ProductVendor::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductVendor';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Vendor {$record->vendor->company_name}";
			$changelog->save();
		});

		ProductVendor::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Product';
					$changelog->parent_id 	 = $record->product_id;
					$changelog->model_type = 'ProductVendor';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata} for {$record->vendor->company_name}";
					$changelog->save();
				}
			}
		});
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('Product');
	}

	public function vendor() {
		return $this->belongsTo('Vendor');
	}

}
