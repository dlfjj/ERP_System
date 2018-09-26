<?php

class OrderHistory extends Eloquent {

	protected $guarded = array();
	protected $table = 'order_history';

	public function order() {
		return $this->belongsTo('Order');
	}

    public function status(){
		return $this->belongsTo('OrderStatus','order_status_id');
    }

}
