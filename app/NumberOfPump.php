<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NumberOfPump extends Model
{
    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'no_of_pump_id', 'id');
    }
}
