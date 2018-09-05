<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReportSubscription extends Model {

	protected $guarded = array();

	public function user(){
		return $this->belongsTo('User');
	}
}
