<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ProductImage extends Model {

	protected $guarded = array();

    public function image() {
        return $this->belongsTo('Product');
    }

}
