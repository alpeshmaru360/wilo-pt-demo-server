<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class DieselTankMaster extends Model
{
    protected $table = 'firefighting_diesel_tank_master';

    protected $fillable = [
        'description','tank_size','unit_price'
    ];
}
