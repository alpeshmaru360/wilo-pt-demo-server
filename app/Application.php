<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'application_id', 'id');
    }
}
