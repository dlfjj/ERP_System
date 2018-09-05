<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
	protected $guarded = array();

	public function account() {
		return $this->belongsTo('ChartOfAccount');
	}

}
