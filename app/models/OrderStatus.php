<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model {

	public static function boot(){
		parent::boot();
	}

	protected $guarded = array();
    protected $table   = "order_status";

}
