<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterMasterSheetMechanicalComponent extends Model
{
    //
    protected $table = 'booster_master_sheet_mechanical_component';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'description',
        'weight',
        'wilo_article_no',
        'brand_code',
        'function_code',
        'range',
        'price'
    ];
}
