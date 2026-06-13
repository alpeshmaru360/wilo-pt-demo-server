<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class JockeyPump extends Model
{
    protected $table = 'firefighting_jockey_pump';

    protected $fillable = [
        'pump_article_no', 'description', 'model_no', 'pump_height', 'pump_weight', 'power', 'no_of_phase', 'voltage', 'frequency', 'unit_price'
    ];
}
