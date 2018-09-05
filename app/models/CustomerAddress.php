<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CustomerAddress extends Model {

	protected $guarded = array();

	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

}
