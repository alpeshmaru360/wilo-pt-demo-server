<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingAdders extends Model
{
    protected $table = 'firefighting_adders';
    protected $fillable = [
        'adder_list','version','code', 'type'
    ];
}
