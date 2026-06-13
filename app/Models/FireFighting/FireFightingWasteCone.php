<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingWasteCone extends Model
{
    protected $table = 'firefighting_waste_cone';

    protected $fillable = [
        'item_article_number', 'description', 'size', 'unit_price'
    ];
}
