<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class ControlPanelMaster extends Model
{
    protected $table = 'firefighting_control_panel_master';

    protected $fillable = [
        'item_article_number', 'description', 'model', 'enclosure', 'type', 'brand', 'approval', 'category', 'motor_power', 'frequency', 'voltage', 'unit_price'
    ];
}
