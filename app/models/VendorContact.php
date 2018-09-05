<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VendorContact extends Model {

	protected $guarded = array();

	public function vendor() {
		return $this->belongsTo('App\Models\Vendor');
	}

}
