<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/*
	Legal States
		DRAFT
		UNPAID,UNSHIPPED
		PARTIAL,UNSHIPPED
		PAID,UNSHIPPED
		UNPAID,SHIPPED
		PARTIAL,SHIPPED
		PAID,SHIPPED
		VOID
*/

class Invoice extends Model {

	public static function boot(){
		parent::boot();

		Invoice::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Invoice';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Invoice {$record->id}";
			$changelog->save();
		});

		Invoice::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Invoice';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Invoice';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Invoice {$record->id}";
			$changelog->save();
		});

		Invoice::updated(function($record){
			$ignored_fields = ['updated_at','net_shipping_amount','net_sub_total','net_total','tax_total','taxcode_percent','taxcode_id','line_no','gross_sub_total','gross_total','net_handling_amount','net_shipping_amount'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Invoice';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Invoice';
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

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function items() {
		return $this->hasMany('InvoiceItem');
	}

    public function payments() {
        return $this->morphMany('App\Models\Money', 'accountable');
    }

	public function history() {
		return $this->hasMany('App\Models\InvoiceHistory1');
	}
public function invoice_payments(){
	return $this->hasmany('App\Models\InvoicePayment');
}

	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

    public function order() {
		return $this->belongsTo('App\Models\Order');
    }

	public function shipments(){
		return $this->morphMany('Shipment', 'shippable');
	}

	public function transactions() {
		return $this->morphMany('WarehouseTransaction', 'transaction');
	}

	public function getOpenBalance($currency_code="",$date=null){
        if($this->status == "PAID,SHIPPED" || $this->status == "PAID,UNSHIPPED" || $this->status == "VOID"){
            return 0;
        }
		$open_balance = $this->gross_total;
		foreach($this->payments as $payment){
			$payment_amount = convert_currency($payment->currency_code,$this->currency_code,$payment->amount,$payment->payment_date);
			$open_balance -= $payment_amount;
		}
		if($open_balance < 0){
			$open_balance = 0;
		}

		if($currency_code == ""){
			return $open_balance;
		} else {
			$open_balance = convert_currency($this->currency_code,$currency_code,$open_balance,$date);
			return $open_balance;
		}
	}

	public function getPaidTillNow($currency_code="",$date=""){
		$paid = 0;
		foreach($this->payments as $payment){
			$payment_amount = convert_currency($payment->currency_code,$this->currency_code,$payment->amount,$payment->payment_date);
			$paid += $payment_amount;
		}

		if($currency_code == ""){
			return ($paid);
		} else {
			$paid = convert_currency($this->currency_code,$currency_code,$paid,$date);
			return $paid;
		}
	}

	public function getGrossTotal($currency_code="",$date=""){
		if($currency_code == ""){
			return $this->gross_total;
		} else {
			return convert_currency($this->currency_code,$currency_code,$this->gross_total,$date);
		}
	}
//relate invoice with invoice status
	public function invoice_status(){
		return $this->hasMany('App\Models\Invoice_Status');
	}

}
