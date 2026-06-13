<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AtmosItem extends Model
{
    public function atmosCart() {
        return $this->belongsTo('App\AtmosCart', 'atmos_cart_id', 'id');
    }
}
