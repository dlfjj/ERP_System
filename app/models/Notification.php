<?php

class Notification extends Eloquent {

	protected $guarded = array();

	public function notifications()
	{
		return $this->morphTo();
	}

}
