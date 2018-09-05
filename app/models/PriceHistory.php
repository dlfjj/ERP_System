<?php
namespace App\Models; //change name space

use Illuminate\Database\Eloquent\Model;
class PriceHistory extends Model {

	protected $guarded = array();
	
	public function product() {
		return $this->belongsTo('Product');
	}

	public function company() {
		return $this->belongsTo('Company');
	}
}
