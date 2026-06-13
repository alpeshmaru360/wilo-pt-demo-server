<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarterType extends Model
{
    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'stater_type_id', 'id');
    }
}
