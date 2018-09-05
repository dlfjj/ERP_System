<?php
namespace model;

use Illuminate\Database\Eloquent\Model;
class BaseModel extends eloquent {

    public function setAttribute($property,$value) {
        //$this->$property = empty($value) ? null : $value;
        $this->$property = strtoupper($value);
    }

    public function getAttribute($key) {
    }

}
