<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseHistory1 extends Model {

	protected $guarded = array();
	protected $table = 'purchase_history';

	public function purchase() {
		return $this->belongsTo('Purchase');
	}

}
