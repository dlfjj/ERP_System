<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {

	public static function boot(){
		parent::boot();

		OrderItem::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Order';
			$changelog->parent_id 	 = $record->order_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Product #{$record->product_id}";
			$changelog->save();
		});

		OrderItem::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Order';
			$changelog->parent_id 	 = $record->order_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Product #{$record->product_id}";
			$changelog->save();
		});

		OrderItem::updated(function($record){
			$ignored_fields = ['net_price','net_total','tax','tax_total','quantity_open','line_no','gross_total','updated_at','total'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Order';
					$changelog->parent_id 	 = $record->order_id;
					$changelog->model_type = get_class($record);
					$changelog->model_id   = $record->id;
					$changelog->action = 'updated';
					$changelog->field_name = $field_name;
					$changelog->old_value  = $olddata;
					$changelog->new_value  = $newdata;
					$changelog->message    = "Changed {$field_name} from {$olddata} to {$newdata} for {$record->product->pluck('part_number')->implode(',')}";
					$changelog->save();
				}
			}
		});
	}

	protected $guarded = array();

	public function order() {
		return $this->belongsTo('App\Models\Order','order_id');
	}

	public function product() {
		return $this->hasMany('App\Models\Product','id','product_id');
	}

    public function getNumberOfPackages(){
        if($this->product->pack_unit > 0){
            $cartons = $this->quantity / $this->product->pack_unit;
        } else {
            $cartons = 0;	
        }

        if($this->order->container_type == 4){
            if($this->product->pack_unit_hq>0){
                $cartons = $this->quantity / $this->product->pack_unit_hq;
            } else {
                $cartons = 0;	
            }
        }
        return ceil($cartons);
    }

//move this to helper function

//    public function getNetWeight(){
//        $nt_weight_total = 0;
//
//        if($this->product->pack_unit > 0){
//            $cartons = $this->quantity / $this->product->pack_unit;
//        } else {
//            $cartons = 0;
//        }
//
//        if($this->order->container_type == 4){
//            if($this->product->pack_unit_hq > 0){
//                $cartons = $this->quantity / $this->product->pack_unit_hq;
//            } else {
//                $cartons = 0;
//            }
//        }
//
//        if($this->order->container_type == 4){
//            $unit_nt_weight = $this->product->pack_unit_net_weight_hq;
//            $line_nt_weight = $unit_nt_weight * $cartons;
//        } else {
//            $unit_nt_weight = $this->product->pack_unit_net_weight;
//            $line_nt_weight = $unit_nt_weight * $cartons;
//        }
//        $nt_weight_total += $line_nt_weight;
//
//        return $nt_weight_total;
//    }


    public function getGrossWeight(){
        $gr_weight_total = 0;

        if($this->product->pack_unit>0){
            $cartons = $this->quantity / $this->product->pack_unit;
        } else {
            $cartons = 0;
        }

        if($this->order->container_type == 4){
            if($this->product->pack_unit_hq>0){
                $cartons = $this->quantity / $this->product->pack_unit_hq;
            } else {
                $cartons = 0;	
            }
        }

        if($this->order->container_type == 4){
            $unit_gr_weight = $this->product->pack_unit_gross_weight_hq;
            $line_gr_weight = $unit_gr_weight * $cartons;
        } else {
            $unit_gr_weight = $this->product->pack_unit_gross_weight;
            $line_gr_weight = $unit_gr_weight * $cartons;
        }

        $gr_weight_total += $line_gr_weight;

        return $gr_weight_total;
    }

	public function getLineTotal(){
		return $this->amount_net;
	}

    public function getCbm(){
        if($this->product->pack_unit>0){
            $cartons = $this->quantity / $this->product->pack_unit;
        } else {
            $cartons = 0;	
        }

        if($this->order->container_type == 4){
            if($this->product->pack_unit_hq>0){
                $cartons = $this->quantity / $this->product->pack_unit_hq;
            } else {
                $cartons = 0;	
            }
        }

        if($this->order->container_type == 4){
            $cbm          = $this->product->carton_size_w_hq * $this->product->carton_size_d_hq * $this->product->carton_size_h_hq / 1000000;
        } else {
            $cbm          = $this->product->carton_size_w * $this->product->carton_size_d * $this->product->carton_size_h / 1000000;
        }
        return $cbm;
    }
}
