<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseDelivery extends Model {

	protected $guarded = array();

	public function purchase() {
		return $this->belongsTo('App\Models\Purchase');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function product() {
		return $this->belongsTo('App\Models\Product');
	}

	public function warehouse() {
		return $this->belongsTo('App\Models\Warehouse');
	}

}
