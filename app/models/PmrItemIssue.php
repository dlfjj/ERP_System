<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class PmrItemIssue extends Eloquent {

    public static function boot(){
		parent::boot();

		PmrItem::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Pmr';
			$changelog->parent_id 	 = $record->pmr_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Issued Product #{$record->product_id}";
			$changelog->save();
		});

		PmrItem::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Pmr';
			$changelog->parent_id 	 = $record->pmr_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Reverted Product #{$record->product_id}";
			$changelog->save();
		});

		PmrItem::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Pmr';
					$changelog->parent_id 	 = $record->pmr_id;
					$changelog->model_type = get_class($record);
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

	public function pmrItem() {
		return $this->belongsTo('PmrItem');
	}
}
