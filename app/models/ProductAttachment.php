<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductAttachment extends Eloquent {

	protected $guarded = array();

	public function product() {
		return $this->belongsTo('Product');
	}

}
