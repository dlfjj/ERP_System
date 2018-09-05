<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model {

	public static function boot(){
		parent::boot();
	}

	protected $guarded = array();
	
}
