<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model {

	public static function boot(){
		parent::boot();

		InvoicePayment::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->invoice_id;
			$changelog->model_type = 'InvoicePayment';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Payment #{$record->id} ({$record->currency_code} {$record->amount})";
			$changelog->save();
		});

		InvoicePayment::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->invoice_id;
			$changelog->model_type = 'InvoicePayment';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Payment #{$record->id} ({$record->currency_code} {$record->amount})";
			$changelog->save();
		});

		InvoicePayment::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Invoice';
					$changelog->parent_id 	 = $record->invoice_id;
					$changelog->model_type = 'InvoicePayment';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata}";
					$changelog->save();
				}
			}
		});
	}

	protected $guarded = array();

	public function invoice() {
		return $this->belongsTo('App\Models\Invoice');
	}

	public function account() {
		return $this->belongsTo('App\Models\ChartOfAccount');
	}

}
