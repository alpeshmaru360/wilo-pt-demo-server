<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterBareshaftMotorPrice extends Model
{
    //
    protected $table = 'booster_motor_price';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'brand',
        'power',
        'motor_article_number',
        'wilo_article_number',
        'motor_height',
        'motor_weight',
        'no_of_pole',
        'no_of_phase',
        'voltage',
        'frequency',
        'frame',
        'efficiency',
        'price'
    ];
}
