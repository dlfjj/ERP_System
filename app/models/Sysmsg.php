<?php
namespace App\Models; //change name space

use Illuminate\Database\Eloquent\Model;
class Sysmsg extends Model {

	protected $guarded = array();
	
	public function company() {
		return $this->belongsTo('Company');
	}
}
