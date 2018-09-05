<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class PmcsheetItem extends Eloquent {

	protected $guarded = array();

	public function pmcsheet() {
		return $this->belongsTo('Pmcsheet');
	}

	public function product() {
		return $this->belongsTo('Product');
	}
}
