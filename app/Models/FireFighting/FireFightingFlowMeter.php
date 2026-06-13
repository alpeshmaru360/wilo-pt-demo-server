<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingFlowMeter extends Model
{
    protected $table = 'firefighting_flow_meter';

    protected $fillable = [
        'item_article_number', 'description', 'size', 'min_gpm', 'max_gpm', 'unit_price'
    ];
}
