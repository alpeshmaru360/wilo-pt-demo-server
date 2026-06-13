<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingPressureReliefValve extends Model
{
    protected $table = 'firefighting_pressure_relief_valve';

    protected $fillable = [
        'description', 'size', 'unit_price'
    ];
}
