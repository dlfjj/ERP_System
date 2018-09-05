<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model {

	public static function boot(){
		parent::boot();
	}

	protected $guarded = array(
	);

	public function customer() {
		return $this->belongsTo('Customer');
	}
}
