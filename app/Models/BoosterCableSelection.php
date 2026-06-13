<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterCableSelection extends Model
{
    //
    protected $table = 'booster_cable_selection';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'cable',
        'material_number',
        'wilo_article_number',
        'brand_code',
        'function_code',
        'range',
        'unit_price'

    ];
}
