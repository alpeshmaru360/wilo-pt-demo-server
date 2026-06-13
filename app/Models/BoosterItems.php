<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterItems extends Model
{
    protected $table = 'booster_items';

    protected $fillable = [
        'booster_cart_id',
        'item_description',
        'wilo_artilce_no',
        'material_number',
        'weight',
        'height',
        'width',
        'depth',
        'brand_code',
        'function_code',
        'ranges',
        'qty',
        'price',
        'total_price',
    ];

    public function boosterCart() {
        return $this->belongsTo('App\Models\BoosterCart', 'booster_cart_id', 'id');
    }
}
