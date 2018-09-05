<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductLock extends Eloquent {

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('Product');
	}

	public function user() {
		return $this->belongsTo('User');
	}
}
