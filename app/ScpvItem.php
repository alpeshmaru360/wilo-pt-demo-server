<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScpvItem extends Model
{
     public function scpvCart() {
        return $this->belongsTo('App\ScpvCart', 'scpv_cart_id', 'id');
    }
}
