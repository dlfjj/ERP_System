<?php

/*
    Possible statuses:

    DRAFT

    OPEN,UNSENT
    OPEN,SENT
    OPEN,ACCEPTED
    OPEN,REJECTED
    OPEN,REQUOTE

    CLOSED,UNSENT
    CLOSED,SENT
    CLOSED,ACCEPTED
    CLOSED,REJECTED
    CLOSED,REQUOTE

    VOID
*/
    namespace  App\Models;
use Illuminate\Database\Eloquent\Model;//change name space 

use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model {
	use SoftDeletes;

    public static function boot(){
		parent::boot();

		Quotation::created(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Quotation';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Quotation';
			$changelog->model_id   = $record->id;
			$changelog->action = 'created';
			$changelog->message = "Created Quotation {$record->id}";
			$changelog->save();
		});

		Quotation::deleted(function($record){
			$changelog = new Changelog();
			$changelog->parent_model = 'Quotation';
			$changelog->parent_id 	 = $record->id;
			$changelog->model_type = 'Quotation';
			$changelog->model_id   = $record->id;
			$changelog->action = 'deleted';
			$changelog->message = "Removed Quotation {$record->id}";
			$changelog->save();
		});

		Quotation::updated(function($record){
			$ignored_fields = ['total','sub_total','updated_by','line_no','updated_at'];
			$dirty = $record->getDirty();
			foreach ($dirty as $field_name => $newdata){
				$olddata = $record->getOriginal($field_name);
				if(in_array($field_name,$ignored_fields)){ continue; }
				if ($olddata != $newdata){
					$changelog = new Changelog();
					$changelog->parent_model = 'Quotation';
					$changelog->parent_id 	 = $record->id;
					$changelog->model_type = 'Quotation';
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
		return $this->hasMany('QuotationItem');
	}

	public function customer() {
		return $this->belongsTo('Customer');
	}

	public function user() {
		return $this->belongsTo('User');
	}

	public function history() {
		return $this->hasMany('QuotationHistory1');
	}

    public function times(){
        return $this->hasMany('QuotationTime');
    }

    public function getTimes(){
        $records = $this->times;
        $seconds = [
            'SALES' => 0,
            'PURCHASING' => 0,
            'ENGINEERING' => 0,
            'CLOSED' => 0,
            'DRAFT' => 0
        ];

        foreach($records as $record){
            if(!isset($seconds[$record->status])){
                $seconds[$record->status] = $record->seconds;
            } else {
                $seconds[$record->status] += $record->seconds;
            }
        }

        $time = array();
        foreach($seconds as $status=>$s){
            $days  =   floor($s / 86400);
            $s     =   $s-($days*86400);
            $hours =   floor($s / 3600);
            $s     =   $s-($hours*3600);
            $minutes = floor($s / 60);
            $s       = $s-($minutes*60);
            $seconds = $s;
            $time[$status] = sprintf("D:%02d H:%02d M:%02d", $days, $hours, $minutes);
        }

        return $time;
    }

    public function updateTime($old_status=""){
        $next = QuotationTime::where('quotation_id',$this->id)->orderBy('id','DESC')->first();
        if(!$next){
            $first = new QuotationTime();
            $first->quotation_id = $this->id;
            $first->created_by = Auth::user()->id;
            $first->updated_by = Auth::user()->id;
            $first->status     = $old_status;
            $first->time_in    = strtotime($this->created_at);
            $first->time_out   = time();
            $first->seconds    = $first->time_out - $first->time_in;
            $first->save();

            $next = new QuotationTime();
            $next->quotation_id = $this->id;
            $next->created_by = Auth::user()->id;
            $next->updated_by = Auth::user()->id;
            $next->status     = $this->status;
            $next->time_in    = time();
            $next->time_out   = null;
            $next->seconds    = null;
            $next->save();
        } else {
            $next->quotation_id = $this->id;
            $next->updated_by = Auth::user()->id;
            $next->time_out   = time();
            $next->seconds    = $next->time_out - $next->time_in;
            $next->status     = $old_status;
            $next->save();

            $next = new QuotationTime();
            $next->quotation_id = $this->id;
            $next->created_by = Auth::user()->id;
            $next->updated_by = Auth::user()->id;
            $next->status     = $this->status;
            $next->time_in    = time();
            $next->time_out   = null;
            $next->seconds    = null;
            $next->save();
        }
    }

}
