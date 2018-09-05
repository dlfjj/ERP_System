<?php

class Watcher extends Eloquent {

	protected $guarded = array();

	    public function watchers()
		{
			return $this->morphTo();
		}

		public function user(){
			return $this->belongsTo('User');
		}

}
