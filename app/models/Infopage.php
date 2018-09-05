<?php

use Illuminate\Database\Eloquent\SoftDeletes;

class Infopage extends Eloquent {

	public static function boot(){
		parent::boot();
	}

	protected $guarded = array();

}
