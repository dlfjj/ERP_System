<?php
namespace App\Models;//add namespace or models
use Auth; //add auth facade
use Illuminate\Database\Eloquent\Model;
use Request;// request facade added

class Changelog extends Model {


	public static function boot(){
		parent::boot();
		Changelog::creating(function($record){
			if(Auth::check()){
				$record->created_by = Auth::user()->id;
			} else {
				$record->created_by = 1;
			}
			$record->ip_address = Request::ip();
 		});
	}

	protected $guarded = array();

	public function user() {
		return $this->belongsTo('App\Models\User','created_by');
	}

	//public function createdBy() {
		//return User::find($this->created_by)->username;
	//}

}
