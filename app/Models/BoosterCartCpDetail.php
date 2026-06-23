<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BoosterCart;

class BoosterCartCpDetail extends Model
{
    protected $table = 'booster_carts_cp_details';

    protected $fillable = [
        'booster_cart_id',
        'no_of_pump',
        'power',
        'voltage',
        'application',
        'ambient_temp',
        'stater_type',
        'communication_protocol',
        'ip_rating',
        'component',
        'enclosure',
        'range',
    ];

    public function boosterCart()
    {
        return $this->belongsTo(
            BoosterCart::class,
            'booster_cart_id',
            'id'
        );
    }

}