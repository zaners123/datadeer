<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model {
//    public $handout_code,$general_location,$longitude,$latitude;
    protected $fillable = ["handout_code","general_location","longitude","latitude"];

    function isPlacedExactly() {
        return $this->longitude != null || $this->latitude!=null;
    }
    function isPlacedAtAll() {
        return $this->isPlacedExactly() || $this->general_location != null;
    }
}
