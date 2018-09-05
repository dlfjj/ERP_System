<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxcode extends Model {

	protected $guarded = array();


	public function vendors() {
		return $this->hasMany('Vendor');
	}

}
