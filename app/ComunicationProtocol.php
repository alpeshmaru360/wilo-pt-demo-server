<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComunicationProtocol extends Model {

    public function contolPanels() {
        return $this->hasMany('App\ControlPanel', 'comunication_protocol_id', 'id');
    }

}
