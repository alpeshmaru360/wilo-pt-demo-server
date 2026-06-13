<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlPanelCart extends Model {

     public function items() {
        return $this->hasMany('App\Item', 'cp_cart_id', 'id');
    }

    public function powers() {
        return $this->belongsTo('App\Power', 'power_id', 'id');
    }

     public function noofpumps() {
        return $this->belongsTo('App\NumberOfPump', 'no_of_pump_id', 'id');
    }

    public function voltages() {
        return $this->belongsTo('App\Voltage', 'voltage_id', 'id');
    }

    public function applications() {
        return $this->belongsTo('App\Application', 'application_id', 'id');
    }

    public function ambienttemps() {
        return $this->belongsTo('App\AmbientTemp', 'ambient_temp_id', 'id');
    }

    public function components() {
        return $this->belongsTo('App\Component', 'components_id', 'id');
    }

    public function ranges() {
        return $this->belongsTo('App\Range', 'range', 'id');
    }

    public function enclousres() {
        return $this->belongsTo('App\Enclousre', 'enclosure_id', 'id');
    }

    public function comunicationprotocols() {
        return $this->belongsTo('App\ComunicationProtocol', 'communication_protocol_id', 'id');
    }

    public function ipratings() {
        return $this->belongsTo('App\IpRating', 'ip_rating_id', 'id');
    }

    public function startertypes() {
        return $this->belongsTo('App\StarterType', 'stater_type_id', 'id');
    }

    public function documents() {
        return $this->hasMany('App\ArticleFile', 'article_number', 'article_number');
    }



}
