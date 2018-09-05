<?php


namespace  App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends model {

	protected $guarded = array();

	public function purchase() {
		return $this->belongsTo('App\Models\Purchase');
	}

	public function product() {
		return $this->belongsTo('App\Models\Product');
	}

	public function account() {
		return $this->belongsTo('App\Models\ChartOfAccount');
	}
}
