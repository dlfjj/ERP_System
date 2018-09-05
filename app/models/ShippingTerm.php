<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTerm extends Model {

	protected $table = 'shipping_terms';

	protected $guarded = array();


	public function orders() {
		return $this->hasMany('App\Models\Order');
	}

}
