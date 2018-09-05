<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;
class Warehouse extends Model {

	protected $guarded = array();

	public function stocks() {
		return $this->hasMany('WarehouseStock');
	}
}
