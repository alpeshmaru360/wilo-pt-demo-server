<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Range extends Model {

    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'range', 'id');
    }

}
