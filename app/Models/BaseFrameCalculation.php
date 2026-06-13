<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseFrameCalculation extends Model
{
    //
    protected $table = 'booster_base_frame_calculation';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'item_description',
        'no_of_pumps',
        'pump_model_range1',
        'pump_model_range2',
        'ptp',
        'base_frame_length',
        'material_number',
        'wilo_article_number',
        'brand_code',
        'function_code',
        'range',
        'unit_price',
        'qty'
    ];
}
