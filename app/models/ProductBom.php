<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductBom extends Eloquent {

	public static function boot(){
		parent::boot();

		ProductBom::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductBom';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Product {$record->product->part_number}";
			$changelog->save();
		});

		ProductBom::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Product';
			$changelog->parent_id 	 = $record->product_id;
			$changelog->model_type = 'ProductBom';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Product {$record->product->part_number}";
			$changelog->save();
		});

		ProductBom::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Product';
					$changelog->parent_id 	 = $record->product_id;
					$changelog->model_type = 'ProductBom';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata} for {$record->product->part_number}";
					$changelog->save();
				}
			}
		});
	}

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('Product','bom_product_id');
	}

	public function productmaster() {
		return $this->belongsTo('Product','product_id');
	}

	public function getConsumedQuantity(){

		if(!isset($product) || $product->id != $this->product_id){
			static $product;
			$product = $this->productmaster;
		}

		$consumed = $this->quantity * $this->unit;

		if($this->divider == 'U'){
			$divider = 1;
		} elseif($this->divider == 'I'){
			$divider = $product->unit_ic;
		} elseif($this->divider == 'M'){
			$divider = $product->unit_mc;
		}
		if($divider > 0){
			$consumed /= $divider;
		} else {
			$consumed = 0;
		}
		return $consumed;
	}
}
