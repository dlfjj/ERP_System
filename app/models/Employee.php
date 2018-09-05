<?php

class Employee extends Eloquent {

	protected $dates = ['deleted_at'];

	protected $guarded = array();
	
}
