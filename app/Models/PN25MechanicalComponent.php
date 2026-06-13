<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PN25MechanicalComponent extends Model
{
    //
    protected $table = 'booster_pn25_mechanical_component';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'pump_model_range1',
        'pump_model_range2',
        'no_of_pumps',
        'ptp'
    ];
}
