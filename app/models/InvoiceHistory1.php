<?php

class InvoiceHistory1 extends Eloquent {

	protected $guarded = array();
	protected $table = 'invoice_history';

	public function invoice() {
		return $this->belongsTo('Invoice');
	}

}
