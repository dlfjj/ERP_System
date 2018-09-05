<?php

class WarehouseStock extends Eloquent {

	protected $guarded = array();
	
	public function warehouse() {
		return $this->belongsTo('Warehouse');
	}

	public function product() {
		return $this->belongsTo('Product');
	}

}
