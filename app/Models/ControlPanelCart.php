<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlPanelCart extends Model
{
    use SoftDeletes;

    protected $table = 'control_panel_carts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'control_panel_id',
        'quotation_no',
        'article_number',
        'full_article_number',
        'no_of_pump_id',
        'power_id',
        'voltage_id',
        'application_id',
        'ambient_temp_id',
        'stater_type_id',
        'communication_protocol_id',
        'ip_rating_id',
        'components_id',
        'enclosure_id',
        'range',
        'folder_name',
        'file_name_under_folder',
        'price',
        'qty',
        'total_price',
        'tax',
        'starter_code',
        'user_id',
        'adder_ids',
        'overhead',
        'intercompany_margin',
    ];

    /**
     * 🔹 Relation: ControlPanelCart belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔹 Relation: ControlPanelCart has many Quotations
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'cp_cart_id');
    }
}
