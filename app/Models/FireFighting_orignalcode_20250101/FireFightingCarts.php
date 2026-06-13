<?php

namespace App\Models\FireFighting;

use Illuminate\Database\Eloquent\Model;

class FireFightingCarts extends Model
{
    protected $table = 'firefighting_carts';
    protected $fillable = [
        'quotation_no', 'article_number', 'full_article_number', 'pump_id', 'category', 'jockey_article_number', 'pump_models', 'pump_type', 'power', 'frequency', 'pump_approval', 'engine_approval', 'flow', 'head', 'speed_rpm', 'wilo_article_number', 'adder_ids', 'adder_ids_prices', 'total_adders_price', 'overhead_price', 'inter_company_margin_price', 'qty', 'price', 'total_price', 'all_prices', 'field_val', 'user_id'
    ];

    protected $casts = [
        'adder_ids' => 'array',
        'all_prices' => 'array',
        'field_val' => 'array'
    ];

    public static function cartDataByQuotation($ids) {
        $firefightingCartData = FireFightingCarts::whereIn('id', $ids)->get();
        return $firefightingCartData;
    }

}
