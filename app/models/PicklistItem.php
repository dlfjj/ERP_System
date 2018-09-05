<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class PicklistItem extends Eloquent {

	protected $guarded = array();

	public function picklist() {
		return $this->belongsTo('Picklist');
	}

	public function product() {
		return $this->belongsTo('Product');
	}
}
