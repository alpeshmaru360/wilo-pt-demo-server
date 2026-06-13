<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterBareshaftPumpPrice extends Model
{
    //
    protected $table = 'booster_bareshaft_pump_price';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'bareshaft_article_no_helix_pump',
        'description',
        'model_no',
        'pump_height',
        'pump_weight',
        'actual_power',
        'no_of_phase',
        'voltage',
        'frequency',
        'unit_price',
    ];

}
