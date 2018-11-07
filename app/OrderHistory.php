<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $guarded = array();
    protected $table = 'order_history';

    public function order() {
        return $this->belongsTo('Order');
    }

    public function status(){
        return $this->belongsTo('App\OrderStatus','order_status_id');
    }
}
