<?php

namespace App\Models; //change name space

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;//change softdelete  facade

/*
	VALID STATES: DRAFT, OPEN, CLOSED, VOID, SCHEDULED
*/

class Order extends Model {
	use SoftDeletes;

	public static function boot(){
		parent::boot();

		Order::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Order';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Order';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Order {$record->id}";
			$changelog->save();
		});

		Order::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Order';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Order';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Order {$record->id}";
			$changelog->save();
		});

		Order::updated(function($record){
			$ignored_fields = ['updated_at','net_shipping_amount','net_sub_total','net_total','tax_total','taxcode_percent','taxcode_id','line_no','gross_sub_total','gross_total'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Order';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Order';
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

    public function setDueDateOverrideAttribute($value){
        if(strlen($value)<1){
            $this->attributes['due_date_override'] = NULL;
        } else {
            $this->attributes['due_date_override'] = $value;
        }
    }

    public function setEstimatedFinishDateAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['estimated_finish_date'] = NULL;
        } else {
            $this->attributes['estimated_finish_date'] = $value;
        }
    }

    public function setOrderDateAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['order_date'] = NULL;
        } else {
            $this->attributes['order_date'] = $value;
        }
    }
    public function setTelexDateAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['telex_date'] = NULL;
        } else {
            $this->attributes['telex_date'] = $value;
        }
    }
    public function setActualEtdAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['actual_etd'] = NULL;
        } else {
            $this->attributes['actual_etd'] = $value;
        }
    }
    public function setVendorOrderReleaseDateAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['vendor_order_release_date'] = NULL;
        } else {
            $this->attributes['vendor_order_release_date'] = $value;
        }
    }
    public function setLoadedDateAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['loaded_date'] = NULL;
        } else {
            $this->attributes['loaded_date'] = $value;
        }
    }
    public function setEtaAttribute($value) {
        if(strlen($value)<1){
            $this->attributes['eta'] = NULL;
        } else {
            $this->attributes['eta'] = $value;
        }
    }

	public function items() {
		return $this->hasMany('App\Models\OrderItem','order_id')->orderBy('line_no');
	}

	public function company(){
		return $this->belongsTo('App\Models\Company');
	}

	public function taxcode(){
		return $this->belongsTo('App\Models\Taxcode');
	}

	public function customer() {
		return $this->belongsTo('App\Models\Customer');
	}

	public function customerContact() {
		return $this->belongsTo('App\Models\CustomerContact');
	}

	public function container() {
		return $this->belongsTo('App\Models\Container','container_type');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function status() {
        return $this->belongsTo('App\Models\OrderStatus');
	}

	public function shippingTerm() {
		return $this->belongsTo('App\Models\ShippingTerm');
	}

	public function paymentTerm() {
		return $this->belongsTo('App\Models\PaymentTerm');
	}

	public function history() {
		return $this->hasMany('App\OrderHistory');
	}

	public function getGrossTotal($currency_code="",$date=""){
		if($currency_code == ""){
			return round($this->gross_total,2);
		} else {
			return round(convert_currency($this->currency_code,$currency_code,$this->gross_total,$date),2);
		}
	}

    public function getStockOnHand($warehouses = array(1000)){
		return $stock;
	}

//	move this function to helper

//    public function getNetWeight(){
//        $nt_weight_total = 0;
//        foreach($this->items as $value=>$orderitem){
////            return dd($orderitem->product->pluck('pack_unit'));
//            if($orderitem->product->pluck('pack_unit')[0] > 0){
//                $cartons = $orderitem->quantity / $orderitem->product->pluck('pack_unit')[0];
//            } else {
//                $cartons = 0;
//            }
////            return "you got here";
//            if($this->container_type == 4){
//                if($orderitem->product->pluck('pack_unit_hq')[0] > 0){
//                    $cartons = $orderitem->quantity / $orderitem->product->pluck('pack_unit_hq')[0];
//                } else {
//                    $cartons = 0;
//                }
//            }
//            if($this->container_type == 4){
//                $unit_nt_weight = $orderitem->product->pluck('pack_unit_net_weight_hq')[0];
//                $line_nt_weight = $unit_nt_weight * $cartons;
//            } else {
//                $unit_nt_weight = $orderitem->product->pluck('pack_unit_net_weight')[0];
//                $line_nt_weight = $unit_nt_weight * $cartons;
//            }
//            $nt_weight_total += $line_nt_weight;
//        }
//
//        return $nt_weight_total;
//    }

//    public function getGrossWeight(){
//        $gr_weight_total = 0;
//
//        foreach($this->items as $okey=>$orderitem){
//            if($orderitem->product->pack_unit>0){
//                $cartons = $orderitem->quantity / $orderitem->product->pack_unit;
//            } else {
//                $cartons = 0;
//            }
//
//            if($this->container_type == 4){
//                if($orderitem->product->pack_unit_hq>0){
//                    $cartons = $orderitem->quantity / $orderitem->product->pack_unit_hq;
//                } else {
//                    $cartons = 0;
//                }
//            }
//
//            if($this->container_type == 4){
//                $unit_gr_weight = $orderitem->product->pack_unit_gross_weight_hq;
//                $line_gr_weight = $unit_gr_weight * $cartons;
//            } else {
//                $unit_gr_weight = $orderitem->product->pack_unit_gross_weight;
//                $line_gr_weight = $unit_gr_weight * $cartons;
//            }
//
//            $gr_weight_total += $line_gr_weight;
//        }
//
//        $gr_weight_total += $this->weight_of_pallets;
//
//        return $gr_weight_total;
//    }

//    public function getNumberOfPackages(){
//        $cartons_total = 0;
//        foreach($this->items as $okey=>$orderitem){
//            $cartons_total += $orderitem->getNumberOfPackages();
//        }
//        return $cartons_total;
//    }


//    public function getNumberOfPallets(){
//        $no_of_pallets = $this->number_of_pallettes;
//        return $no_of_pallets;
//    }

//    public function getCbm(){
//        $cbm_total = 0;
//
//        if($this->real_cbm > 0){
//            return $this->real_cbm;
//        }
//
//        foreach($this->items as $okey=>$orderitem){
//            if($orderitem->product->pack_unit>0){
//                $cartons = $orderitem->quantity / $orderitem->product->pack_unit;
//            } else {
//                $cartons = 0;
//            }
//
//            if($this->container_type == 4){
//                if($orderitem->product->pack_unit_hq>0){
//                    $cartons = $orderitem->quantity / $orderitem->product->pack_unit_hq;
//                } else {
//                    $cartons = 0;
//                }
//            }
//
//            // carton_size_w carton_size_w_hq
//            if($this->container_type == 4){
//                $cbm          = $orderitem->product->carton_size_w_hq * $orderitem->product->carton_size_d_hq * $orderitem->product->carton_size_h_hq / 1000000;
//            } else {
//                $cbm          = $orderitem->product->carton_size_w * $orderitem->product->carton_size_d * $orderitem->product->carton_size_h / 1000000;
//            }
//            $cbm_total   += $cbm * $cartons;
//        }
//
//        return $cbm_total;
//    }

    public function getDaysOverdue(){
        if($this->status_id == 6 || $this->status_id == 7){
            if($this->estimated_finish_date != null && $this->estimated_finish_date != "0000-00-00"){
                $credit = $this->paymentTerm->credit;
                $credit += 3;
                $due_date = date('Y-m-d', strtotime($this->estimated_finish_date . " + $credit days"));

                $today = date("Y-m-d");
                $diff = getDateDifferenceInDays($due_date,$today);

                return $diff;
            }
        }
        return 0;
    }

    public function getDueDate(){
        if($this->status_id == 6 || $this->status_id == 7){
            if($this->estimated_finish_date != null && $this->estimated_finish_date != "0000-00-00"){
                $credit = $this->paymentTerm->credit;
                $credit += 3;
                $due_date = date('Y-m-d', strtotime($this->estimated_finish_date . " + $credit days"));

                if($this->due_date_override != "0000-00-00" && $this->due_date_override != null){
                    return $this->due_date_override;
                }

                return $due_date;
            }
        }
        return "0000-00-00";
    }

    public function getOpenBalance($currency_code="",$date=null){
        $open_balance = $this->total_gross - $this->getPaidTillNow($currency_code);
        $open_balance = round($open_balance,2);

		if($open_balance < 0){
			$open_balance = 0;
		}

		if($currency_code == ""){
			return $open_balance;
		} else {
			$open_balance = convert_currency($this->currency_code,$currency_code,$open_balance,$date);
			return round($open_balance,3);
		}
	}

    public function payments(){
		return $this->hasMany('App\Models\CustomerPayment');
    }

    public function getTotals(){
        $total_sales = 0;
        $total_cost  = 0;

        if($this->commission == ""){
            $commission  = $this->customer->salesman_commission;
        } else {
            $commission = $this->commission;
        }
        $discount    = $this->discount;

        if(count($this->items)>0){
            foreach($this->items as $okey=>$orderitem){
                $total_cost += $orderitem->quantity * $orderitem->base_price;
                $total_sales += $orderitem->quantity * $orderitem->unit_price_net;
            }
        }

        if($discount > 0){
            $total_sales -= $total_sales / 100 * $discount;
        }
        //$total_cost += $this->shipping_cost;
        $total_cost += $order->shipping_cost_actual;
		$total_sales += $order->shipping_cost;



        if($total_sales > 0 && $total_cost > 0){
            $balance_before_commissions = $total_sales;
            $commission_percent = $commission;
            $commission = $balance_before_commissions / 100 * $commission_percent;
            $profit = $balance_before_commissions - $commission - $total_cost;
            $profit_percent = ($profit + $commission) / $total_cost * 100;
        } else {
            $balance_before_commissions = 0;
            $commission_percent = 0;
            $commission = 0;
            $profit = 0;
            $profit_percent = 0;
        }
        //$total_cost += $this->shipping_cost;

        $res['total_sales'] = round($total_sales,2);
        $res['total_cost'] = round($total_cost,2);
        $res['balance_before_commissions'] = $balance_before_commissions;
        $res['commission_percent'] = $commission_percent;
        $res['commission'] = $commission;
        $res['profit'] = round($profit,2);
        $res['profit_percent'] = round($profit_percent,2);

        return $res;
    }

    public function getPaidTillNow($currency_code="",$date=""){
		$paid = 0;
		foreach($this->payments as $payment){
			$payment_amount = convert_currency($payment->currency_code,$this->currency_code,$payment->amount,$payment->payment_date);
			$bank_charges   = convert_currency($payment->currency_code,$this->currency_code,$payment->bank_charges,$payment->payment_date);
			$paid += $payment_amount+$bank_charges;
		}

		if($currency_code == ""){
			return ($paid);
		} else {
			$paid = convert_currency($this->currency_code,$currency_code,$paid,$date);
			return $paid;
		}
	}

	public function getLineTotal(){
		return $this->items->sum('amount_net');
	}

}
