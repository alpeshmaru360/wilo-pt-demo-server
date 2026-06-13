<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScpItem extends Model
{
     public function scpCart() {
        return $this->belongsTo('App\ScpCart', 'scp_cart_id', 'id');
    }
}
