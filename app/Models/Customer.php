<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'cp_cart_id',
        'name',
        'project_name',
        'country',
        'revision_number',
        'segment_category',
        'project_location',
        'email_id',
        'phone_no',
        'address',
        'enquiry_form_number',
        'consultant',
        'contractor',
        'notes',
    ];

    /**
     * 🔹 Relation: Customer has many Quotations
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'customer_id');
    }

    /**
     * 🔹 Relation: Customer belongs to a cart (any type linked via cp_cart_id)
     * Adjust if you need to point to specific cart models.
     */
    public function cart()
    {
        // Assuming cp_cart_id refers to one cart entry
        return $this->belongsTo(ControlPanelCart::class, 'cp_cart_id');
    }
}
