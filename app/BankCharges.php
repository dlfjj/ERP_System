<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankCharges extends Model
{
    //
    //specified which database is gonna be used
    protected $table ='bank_charges';

    protected $fillable = ['amount','customer_payment_id','account_id'];


}
