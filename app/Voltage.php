<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voltage extends Model {

    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'voltage_id', 'id');
    }

}
