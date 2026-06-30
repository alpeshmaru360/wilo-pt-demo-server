<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ControlPanel extends Model {

    protected $fillable = [
        'no_of_pump_id',
        'power_id',
        'voltage_id',
        'application_id',
        'price',
        'user_id',
    ];

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

    // A Code: 26-06-2026 Start Comment
    // public function ranges() {
    //     return $this->belongsTo('App\Range', 'range', 'id');
    // }
    // A Code: 26-06-2026 End Comment

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

    public static function isIntegerColumn($power) {
        $integerColumn = array(3, 4, 9, 11, 15, 22, 30, 37, 45, 55, 75);
        if (in_array($power, $integerColumn)) {
            return true;
        }
        return false;
    }

    public static function controlpanel_over_head() {
        return DB::table('setup_fields')->where('name', 'controlpanel_over_head')->pluck('value')[0];
    }

}
