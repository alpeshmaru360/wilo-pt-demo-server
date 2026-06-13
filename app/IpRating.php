<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpRating extends Model
{
   public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'ip_rating_id', 'id');
    }
}
