<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model {

	protected $guarded = array(
	);

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function createdBy(){
        return $this->belongsTo('App\Models\User','created_by');
    }

    public function bankCharge(){
        return $this->hasOne('App\BankCharges','customer_payment_id');
    }

}
