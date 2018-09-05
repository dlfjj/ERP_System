<?php

class QuotationTime extends Eloquent {

	protected $guarded = array();

	public function quotation() {
		return $this->belongsTo('Quotation');
	}

}
