<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class Picklist extends Eloquent {

	protected $guarded = array();

	public function items() {
		return $this->hasMany('PicklistItem');
	}

	public function user() {
		return $this->belongsTo('User');
	}

	public function workorder() {
		return $this->belongsTo('WorkOrder','work_order_id');
	}

	public function order() {
		return $this->belongsTo('Order');
	}

	public function updatedBy() {
		return User::find($this->updated_by)->username;
	}

	public function createdBy() {
		return User::find($this->updated_by)->username;
	}

	public function processedBy() {
		if($this->processed_by > 0){
			return User::find($this->processed_by)->username;
		}
		return "";
	}

}
