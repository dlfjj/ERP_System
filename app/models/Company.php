<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		Company::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Company';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Company';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Company {$record->id}";
			$changelog->save();
		});

		Company::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Company';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Company';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Company {$record->id}";
			$changelog->save();
		});

		Company::updated(function($record){
			$ignored_fields = ['updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Company';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Company';
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->save();
				}
			}
		});
	}

	protected $dates = ['deleted_at'];

	protected $guarded = array();

	public function users() {
		return $this->hasMany('App\Models\User');
	}

	public function invoices(){
		return $this->hasMany('App\Models\Invoice');
	}

	public function taxcode() {
		return $this->belongsTo('App\Models\Taxcode');
	}

	public function getOpenBalance($currency_code){
		$invoices_amount = 0;
		$invoices = Invoice::whereIn('status',['UNPAID,UNSHIPPED','PARTIAL,UNSHIPPED','UNPAID,SHIPPED','PARTIAL,SHIPPED'])
			->where('customer_id',$this->id)
			->get();

		foreach($invoices as $invoice){
			$invoices_amount += $invoice->getOpenBalance($currency_code);
		}
		return $invoices_amount;
	}

    public function getPurchaseOrdersWritten($date_start,$date_end,$currency_code){
        $purchases = Purchase::select('currency_code','gross_total')
			->where('date_placed','>=',$date_start)
			->where('date_placed','<=',$date_end)
            ->where('company_id',$this->id)
            ->whereIn('purchases.status',[
                "DELIVERED,UNPAID",
                "DELIVERED,PARTIAL",
                "DELIVERED,PAID",
                "UNDELIVERED,UNPAID",
                "UNDELIVERED,PARTIAL",
                "UNDELIVERED,PAID",
                "PARTIAL,UNPAID",
                "PARTIAL,PARTIAL",
                "PARTIAL,PAID"
            ])
			->get();

		$po_placed_total = 0;
		foreach($purchases as $purchase){
			$po_placed_total += convert_currency($purchase->currency_code,$currency_code,$purchase->gross_total);
		}

		return number_format($po_placed_total,2);
    }

    public function getPurchaseOrdersPaid($date_start,$date_end,$currency_code){
        $payments = Money::Leftjoin('purchases','purchases.id','=','money.accountable_id')
		->select(
			'money.date_created',
			'money.currency_code',
			'money.amount'
		)
        ->where('money.accountable_type','Purchase')
        ->where('money.company_id',$this->id)
		->where('date_created','>=',$date_start)
		->where('date_created','<=',$date_end)
        ->whereIn('purchases.status',[
            "DELIVERED,UNPAID",
            "DELIVERED,PARTIAL",
            "DELIVERED,PAID",
            "UNDELIVERED,UNPAID",
            "UNDELIVERED,PARTIAL",
            "UNDELIVERED,PAID",
            "PARTIAL,UNPAID",
            "PARTIAL,PARTIAL",
            "PARTIAL,PAID"
        ])
		->get();

		$payments_total = 0;
		foreach($payments as $payment){
			$payments_total += convert_currency($payment->currency_code,$currency_code,$payment->amount);
		}

		return number_format($payments_total,2);
    }

    public function getExpenses($date_start,$date_end,$currency_code){
        $expenses = Money::select('currency_code','amount')
			->where('date_created','>=',$date_start)
			->where('date_created','<=',$date_end)
            ->where('accountable_type','Expense')
            ->where('company_id',$this->id)
			->whereIn('status',["Active"])
			->get();

		$expenses_total = 0;
		foreach($expenses as $expense){
			$expenses_total += convert_currency($expense->currency_code,$currency_code,$expense->amount);
		}

		return number_format($expenses_total,2);
    }

    public function getInvoicesWritten($date_start,$date_end,$currency_code){
        $invoices = Invoice::select('currency_code','gross_total')
			->where('date_issued','>=',$date_start)
			->where('date_issued','<=',$date_end)
            ->where('company_id',$this->id)
			->whereIn('status',["UNPAID,UNSHIPPED","UNPAID,SHIPPED","PARTIAL,UNSHIPPED","PARTIAL,SHIPPED","PAID,UNSHIPPED","PAID,SHIPPED"])
			->get();

		$issued_amount = 0;
		foreach($invoices as $invoice){
			$issued_amount += convert_currency($invoice->currency_code,$currency_code,$invoice->gross_total);
		}

		return number_format($issued_amount,2);
    }

    public function getPaymentsReceived($date_start,$date_end,$currency_code){
        $payments = Money::Leftjoin('invoices','invoices.id','=','money.accountable_id')
		->select(
            'invoices.status',
			'money.date_created',
			'money.currency_code',
			'money.amount'
		)
		->where('date_created','>=',$date_start)
		->where('date_created','<=',$date_end)
        ->where('money.company_id',$this->id)
        ->where('accountable_type','Invoice')
		->whereIn('invoices.status',["UNPAID,UNSHIPPED","UNPAID,SHIPPED","PARTIAL,UNSHIPPED","PARTIAL,SHIPPED","PAID,UNSHIPPED","PAID,SHIPPED"])
		->get();

		$payments_total = 0;
		foreach($payments as $payment){
			$payments_total += convert_currency($payment->currency_code,$currency_code,$payment->amount);
		}

		return number_format($payments_total,2);
    }

    public function getGrossProfit($date_start,$date_end,$currency_code){
        $expenses = $this->getExpenses($date_start,$date_end,$currency_code);
        $incoming = $this->getPaymentsReceived($date_start,$date_end,$currency_code);
        $purchases = $this->getPurchaseOrdersPaid($date_start,$date_end,$currency_code);

        $profit = $incoming - $expenses - $purchases;

        return number_format($profit,2);
    }

}
