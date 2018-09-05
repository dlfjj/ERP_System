<?php
use Illuminate\Database\Eloquent\SoftDeletes;
class QuotationItem extends Eloquent {
	use SoftDeletes;
	protected $guarded = array();

	public function quotation() {
		return $this->belongsTo('Quotation');
	}

	public function product() {
		return $this->belongsTo('Product');
	}

}
