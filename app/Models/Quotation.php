<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'quotation_number',
        'cp_cart_id',
        'cart_model_name',
        'user_id',
        'customer_id',
        'total_quotation_value',
        'status',
        'reason',
        'modification',
    ];

    /**
     * Quotation belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quotation belongs to a Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Quotation belongs to Booster Cart
     */
    public function boosterCart()
    {
        return $this->belongsTo(BoosterCart::class, 'cp_cart_id');
    }

    /**
     * Quotation belongs to Atmos Cart
     */
    public function atmosCart()
    {
        return $this->belongsTo(AtmosCart::class, 'cp_cart_id');
    }

    /**
     * Quotation belongs to SCP Cart
     */
    public function scpCart()
    {
        return $this->belongsTo(ScpCart::class, 'cp_cart_id');
    }

    // A Code: 23-02-2026 Start

    /**
     * Quotation belongs to SCPV Cart
     */
    public function scpvCart()
    {
        return $this->belongsTo(ScpvCart::class, 'cp_cart_id');
    }
    
    // A Code: 23-02-2026 End

    /**
     * Quotation belongs to Control Panel Cart
     */
    public function controlPanelCart()
    {
        return $this->belongsTo(ControlPanelCart::class, 'cp_cart_id');
    }

    /**
     * Quotation belongs to Firefighting Cart
     */
    public function firefightingCart()
    {
        return $this->belongsTo(FirefightingCart::class, 'cp_cart_id');
    }
}
