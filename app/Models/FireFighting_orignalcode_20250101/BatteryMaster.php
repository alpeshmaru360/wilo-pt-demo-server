<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class BatteryMaster extends Model
{
    protected $table = 'firefighting_battery_master';

    protected $fillable = [
        'model','description','unit_price'
    ];
}
