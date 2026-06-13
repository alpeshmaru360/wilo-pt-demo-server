<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class DieselPump extends Model
{
    protected $table = 'firefighting_diesel_pump';

    protected $fillable = [
        'item_article_number', 'pump_models', 'pump_type', 'frequency', 'pump_approval', 'engine_approval', 'flow', 'head', 'speed_rpm', 'unit_price', 'control_panel_model', 'diesel_tank_us', 'battery_rating', 'battery_qty', 'flow_meter_size', 'pressure_releif_valve', 'waste_cone_brand', 'engine_power', 'no_of_phase', 'voltage', 'engine_model', 'engine_type', 'engine_brand', 'engine_make', 'engine_origin', 'tank_brand', 'approval', 'battery_brand', 'control_panel_type', 'control_panel_brand', 'control_panel_approval', 'moc_casing', 'moc_shaft', 'moc_impeller', 'flange_size', 'flange_class', 'casing_relief_valve', 'suction_pressure_gauge', 'discharge_pressure_gauge', 'air_release_valve', 'air_release_valve_brand', 'flow_meter_brand', 'pressure_releif_valve_brand', 'waste_cone', 'terminal_box', 'diesel_pump_code', 'diesel_pump_controller_code', 'diesel_tank_code', 'battery_code', 'terminal_box_code', 'flow_meter_code', 'pressure_relief_valve_code', 'waste_cone_code', 'wilo_model', 'wilo_article_number',
    ];
}
