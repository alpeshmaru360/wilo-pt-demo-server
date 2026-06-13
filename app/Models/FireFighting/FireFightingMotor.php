<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingMotor extends Model
{
    protected $table = 'firefighting_motor';

    protected $fillable = [
        'description', 'motor_power', 'frequency', 'voltage', 'number_of_pole', 'unit_price'
    ];
}
