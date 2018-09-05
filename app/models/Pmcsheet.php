<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Pmcsheet extends Eloquent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array();
	
	public function items() {
		return $this->hasMany('PmcsheetItem');
	}

    public function order() {
		return $this->belongsTo('Order');
    }

	public function product() {
		return $this->belongsTo('Product');
	}

	public function updatedBy() {
		return User::find($this->updated_by)->username;
	}

	public function createdBy() {
		return User::find($this->updated_by)->username;
	}


}
