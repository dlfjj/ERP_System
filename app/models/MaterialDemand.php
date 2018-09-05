<?php

class MaterialDemand extends Eloquent {

    public function material_demands() {
    }

	public function product() {
		return $this->belongsTo('Product');
	}

	public function order() {
		return $this->belongsTo('Order');
	}

	public function orderItem() {
		return $this->belongsTo('OrderItem');
	}

}
