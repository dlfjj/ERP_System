<?php

class InvoiceItem extends Eloquent {

	public static function boot(){
		parent::boot();

		InvoiceItem::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->invoice_id;
			$changelog->model_type = 'InvoiceItem';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Product #{$record->product_id}";
			$changelog->save();
		});

		InvoiceItem::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->invoice_id;
			$changelog->model_type = 'InvoiceItem';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Product #{$record->product_id}";
			$changelog->save();
		});

		InvoiceItem::updated(function($record){
			$ignored_fields = ['net_price','net_total','tax','tax_total','quantity_open','line_no','gross_total','updated_at','total'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Invoice';
					$changelog->parent_id 	 = $record->invoice_id;
					$changelog->model_type = 'InvoiceItem';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata} for {$record->product->part_number}";
					$changelog->save();
				}
			}
		});
	}

	protected $guarded = array();

	public function invoice() {
		return $this->belongsTo('Invoice');
	}

	public function product() {
		return $this->belongsTo('Product');
	}

	public function orderItem() {
		return $this->belongsTo('OrderItem');
	}
}
