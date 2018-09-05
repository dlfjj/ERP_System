<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Pmr extends Eloquent {

	protected $guarded = array();

    public static function boot(){
		parent::boot();

		Pmr::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Pmr';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Pmr';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Order {$record->id}";
			$changelog->save();
		});

		Pmr::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Pmr';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Pmr';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Pmr {$record->id}";
			$changelog->save();
		});

		Pmr::updated(function($record){
			$ignored_fields = ['updated_at','updated_by'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Pmr';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Pmr';
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

    public function setProductIdAttribute($value) {
        $this->attributes['product_id'] = empty($value)?null:$value;
    }

    public function setOrderIdAttribute($value) {
        $this->attributes['order_id'] = empty($value)?null:$value;
    }

	public function items() {
		return $this->hasMany('PmrItem');
	}

    public function order() {
		return $this->belongsTo('Order');
    }

	public function product() {
		return $this->belongsTo('Product');
	}

	public function updatedBy() {
		return User::find($this->updated_by)->username;
	}

	public function createdBy() {
		return User::find($this->updated_by)->username;
	}

    public function getParentPartnumber(){
        $parent = Product::find($this->product_id);
        if($parent){
            return $parent->part_number;
        } else {
            return;
        }
    }

    public function getChildPartnumber($child_id){
        $child = Product::find($child_id);
        if($child){
            return $child->part_number;
        } else {
            return;
        }
    }

}
