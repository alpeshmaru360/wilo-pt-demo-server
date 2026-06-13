<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    
  
    public function contolPanelCart() {
        return $this->belongsTo('App\ControlPanelCart', 'cp_cart_id', 'id');
    }
}
