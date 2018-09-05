<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseIqc extends Eloquent {

	protected $guarded = array();

	public function purchase() {
		return $this->belongsTo('Purchase');
	}

	public function product() {
		return $this->belongsTo('Product');
	}
}
