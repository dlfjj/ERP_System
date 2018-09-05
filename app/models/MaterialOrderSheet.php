<?php

class MaterialOrderSheet extends Eloquent {

	public function items() {
		return $this->hasMany('MaterialOrderSheetItem');
	}
}
