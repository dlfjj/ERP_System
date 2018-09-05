<?php

class MaterialDemandTotal extends Eloquent {

    public function material_demand_totals() {
    }

	public function product() {
		return $this->belongsTo('Product');
	}
}
