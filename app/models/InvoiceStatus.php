<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceStatus extends Model
{
  //relate invoice status with invoice
    public function invoice(){
      return $this->belongsTo('App\Models\Invoice');
    }
}
