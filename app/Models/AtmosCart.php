<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtmosCart extends Model
{
    protected $table = 'atmos_carts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'quotation_no',
        'article_number',
        'full_article_number',
        'bare_shaft_article_number',
        'ksa_full_article_number',
        'country_origin',
        'is_bareshaft_selection',
        'flow',
        'head',
        'impeller_standard_size',
        'required_impeller_size',
        'pump_id',
        'pump_name',
        'material_id',
        'master_id',
        'is_bare_manual',
        'bare_pump_price',
        'brand',
        'power',
        'motor_height',
        'frame_size',
        'no_of_pole',
        'no_of_phase',
        'voltage',
        'frequency',
        'efficiency',
        'is_accesories_manual',
        'accesories_price',
        'application',
        'master_price',
        'insulate_bearing_price',
        'adder_ids',
        'adder_ids_prices',
        'total_adders_price',
        'assembly_charge',
        'painting_charge',
        'packing_charge',
        'overhead_price',
        'shipping_cost_price',
        'shipping_cost_percentage',
        'inter_company_margin_price',
        'qty',
        'price',
        'total_price',
        'user_id',
    ];

    /**
     * 🔹 Relation: AtmosCart belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔹 Relation: AtmosCart has many Quotations
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'cp_cart_id');
    }
}
