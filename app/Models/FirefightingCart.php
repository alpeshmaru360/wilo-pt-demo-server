<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirefightingCart extends Model
{
    protected $table = 'firefighting_carts';

    protected $fillable = [
        'quotation_no',
        'article_number',
        'full_article_number',
        'pump_id',
        'category',
        'jockey_article_number',
        'pump_models',
        'pump_type',
        'power',
        'frequency',
        'pump_approval',
        'engine_approval',
        'flow',
        'head',
        'speed_rpm',
        'wilo_article_number',
        'adder_ids',
        'adder_ids_prices',
        'total_adders_price',
        'overhead_price',
        'inter_company_margin_price',
        'qty',
        'price',
        'total_price',
        'all_prices',
        'field_val',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'cp_cart_id');
    }
}
