<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomerSpecific extends Model {

	public static function boot(){
		parent::boot();
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('App\Models\Product');
	}

	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

}
