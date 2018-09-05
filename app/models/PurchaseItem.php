<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseItem extends Model {

	public static function boot(){
		parent::boot();

		PurchaseItem::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Purchase';
			$changelog->parent_id 	 = $record->purchase_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Added Product #{$record->product_id} to P.O {$record->purchase_id}";
			$changelog->save();
		});

		PurchaseItem::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Purchase';
			$changelog->parent_id 	 = $record->purchase_id;
			$changelog->model_type = get_class($record);
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Product #{$record->product_id} from P.O {$record->purchase_id}";
			$changelog->save();
		});

		PurchaseItem::updated(function($record){
			$ignored_fields = ['net_price','net_total','tax','tax_total','quantity_open','line_no','gross_total','updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Purchase';
					$changelog->parent_id 	 = $record->purchase_id;
					$changelog->model_type = get_class($record);
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

	protected $guarded = array();

	public function purchase() {
		return $this->belongsTo('App\Models\Purchase');
	}

	public function product() {
		return $this->belongsTo('App\Models\Product','product_id','id');
	}

	public function transactions() {
		return $this->hasMany('App\Models\WarehouseTransaction');
	}

	public function deliveries() {
		return $this->hasMany('App\Models\PurchaseDelivery');
	}

	public function getQuantityOpen(){
		return $this->quantity_open;
	}

	public function getQuantityOrdered(){
		return $this->quantity;
	}

	public function getQuantityDelivered(){
		$result = PurchaseDelivery::where('purchase_item_id',$this->id)
			->sum('delivered');

		return $result;
	}

    public function getQuantityReconciled(){
		$result = PurchaseDelivery::where('purchase_item_id',$this->id)
			->sum('reconciled');

		return $result;
	}
}
