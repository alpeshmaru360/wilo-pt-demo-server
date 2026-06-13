<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enclousre extends Model {

    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'enclousre_id', 'id');
    }

}
