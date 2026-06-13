<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterFullPumpPrice extends Model
{
    //
    protected $table = 'booster_full_pump_price';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'pump_article_no_helix_pump',
        'description',
        'model_no',
        'pump_height',
        'pump_weight',
        'power',
        'no_of_phase',
        'voltage',
        'frequency',
        'unit_price',

    ];
}
