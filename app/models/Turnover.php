<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnover extends Model
{
    protected $fillable  = ['id','current_year','last_year','previous_last_year','created_at','updated_at'];
}
