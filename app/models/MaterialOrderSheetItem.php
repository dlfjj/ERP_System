<?php

class MaterialOrderSheetItem extends Eloquent {

	public function materialordersheet() {
		return $this->belongsTo('MaterialOrderSheet');
	}

	public function product() {
		return $this->belongsTo('Product');
	}

}
