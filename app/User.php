<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    protected $guarded = array('password_conf','roles');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }

    public function getRememberToken(){
        return $this->remember_token;
    }

    public function setRememberToken($value){
        $this->remember_token = $value;
    }

    public function getRememberTokenName(){
        return 'remember_token';
    }

    public function locks() {
        return $this->hasMany('App\Models\ProductLock');
    }

    public function expenses() {
        return $this->hasMany('App\Models\UserExpense');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function getMoneyIn($currency_code = "CNY"){
        $amount = UserExpense::where('user_id',$this->id)
            ->where('account_id','52')
            ->sum('amount');
        return $amount;
    }

    public function getMoneyOut($currency_code = "CNY"){
        $amount = UserExpense::where('user_id',$this->id)
            ->where('account_id','!=','52')
            ->sum('amount');
        return $amount;
    }
}
