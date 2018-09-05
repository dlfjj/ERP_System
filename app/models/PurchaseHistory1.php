<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseHistory1 extends Eloquent {

	protected $guarded = array();
	protected $table = 'purchase_history';

	public function purchase() {
		return $this->belongsTo('Purchase');
	}

}
