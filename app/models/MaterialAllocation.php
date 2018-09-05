<?php

class MaterialAllocation extends Eloquent {

	protected $guarded = array();

	public function order() {
		return $this->belongsTo('Order');
	}

	public function orderitem() {
		return $this->belongsTo('OrderItem');
	}

	public function product() {
		return $this->belongsTo('Product');
	}
}
