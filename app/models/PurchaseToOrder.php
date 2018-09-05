<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseToOrder extends Eloquent {

	protected $guarded = array();

	public function purchase() {
		return $this->belongsTo('Purchase');
	}

	public function order() {
		return $this->belongsTo('Order');
	}
}
