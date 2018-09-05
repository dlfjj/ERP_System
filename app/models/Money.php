<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class Money extends Eloquent {

	protected $guarded = array();

    public function accountable() {
        return $this->morphTo();
    }

	public function account() {
		return $this->belongsTo('ChartOfAccount');
	}

}
