<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class ElectricalPump extends Model
{
    protected $table = 'firefighting_electrical_pump';    

    protected $fillable = [
        'item_article_number', 
        'wilo_pump_models', 
        'pump_type', 
        'frequency', 
        'pump_approval', 
        'flow', 
        'head', 
        'speed_rpm', 
        'unit_price', 
        'control_panel_model', 
        'flow_meter_size', 
        'terminal_box', 
        'motor_power', 
        'voltage', 
        'no_of_phase', 
        'motor_approval', 
        'motor_type', 
        'motor_brand', 
        'motor_make', 
        'motor_origin', 
        'control_panel_type', 
        'control_panel_approval', 
        'moc_casing', 
        'moc_shaft', 
        'moc_impeller', 
        'flange_size', 
        'flange_class', 
        'casing_relief_valve', 
        'suction_pressure_gauge', 
        'discharge_pressure_gauge', 
        'electrical_pump_ordering_code', 
        'electrical_pump_controller_ordering_code', 
        'terminal_box_ordering_code', 
        'flow_meter_ordering_code', 
        'pressure_relief_valve_ordering_code', 
        'waste_cone_ordering_code', 
        'wilo_model', 
        'wilo_article_number'
    ];
}
