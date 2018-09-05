<?php

class QuotationHistory1 extends Eloquent {

	protected $guarded = array();
	protected $table = 'quotation_history';

	public function quotation() {
		return $this->belongsTo('Quotation');
	}

}
