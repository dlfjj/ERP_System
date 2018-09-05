<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model {

	public static function boot(){
		parent::boot();

		ProductPrice::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductPrice';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Group Price{$record->id}";
			$changelog->save();
		});

		ProductPrice::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductPrice';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Group Price{$record->id}";
			$changelog->save();
		});

		ProductPrice::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Product';
					$changelog->parent_id 	 = $record->product_id;
					$changelog->model_type = 'ProductPrice';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata} for {$record->id}";
					$changelog->save();
				}
			}
		});
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('App\Models\Product');
	}

	public function group() {
		return $this->belongsTo('App\Models\CustomerGroup','customer_group_id');
	}

}
