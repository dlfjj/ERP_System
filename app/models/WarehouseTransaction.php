<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransaction extends Model {

	protected $guarded = array();

	public function warehouse() {
		return $this->belongsTo('Warehouse');
	}

	public function scopeOnorder($query) {
		return $query->where('warehouse_id', '=', 3);
	}

	public function transaction() {
		return $this->morphTo();
	}

	public function createdBy() {
		return User::find($this->created_by)->username;
	}


}
