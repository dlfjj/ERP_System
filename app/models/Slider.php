<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Slider extends Model {

	protected $guarded = array();

	function returnFullPath(){
		return trim(app_path() . "{$this->company_id}/sliders/" . $this->picture);
	}

	function returnPath(){
		return trim(app_path() . "{$this->company_id}/sliders/");
	}

	function removeFile(){
		if($this->picture != ""){
			$full_path = trim(app_path() . "{$this->company_id}/sliders/" . $this->picture);
			if(file_exists($full_path)){
				unlink($full_path);
			}
		}

		return true;
	}
}
