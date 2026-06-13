<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Power extends Model {

    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'power_id', 'id');
    }

}
