<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model {

	protected $guarded = array();

	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

}
