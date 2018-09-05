<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductDownload extends Model {

    public function download() {
        return $this->belongsTo('Product');
    }

}
