<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmbientTemp extends Model
{
  public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'ambient_temp_id', 'id');
    }
}
