<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class OptionalMaster extends Model
{
    protected $table = 'firefighting_optional_master';

    protected $fillable = [
        'description', 'model', 'category', 'min_power', 'max_power', 'unit_price', 'nema3', 'nema3r', 'nema4', 'nema4x', 'nema12', 'ip54', 'ip55', 'ip65', 'terminal_box' 
    ];
}
