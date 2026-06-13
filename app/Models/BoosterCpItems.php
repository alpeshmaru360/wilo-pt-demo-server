<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterCpItems extends Model
{

    protected $table = 'booster_cp_items';
    public function boosterCart() {
        return $this->belongsTo('App\Models\BoosterCart', 'booster_cart_id', 'id');
    }
}
