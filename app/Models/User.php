<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Laravel\Sanctum\HasApiTokens; // Uncomment if using Sanctum

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'atmos_access',
        'scp_access',
        'fire_fighting_access',
        'booster_access',
        'sch_access',
        'control_panel_access',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'atmos_access' => 'integer',
        'scp_access' => 'integer',
        'fire_fighting_access' => 'integer',
        'booster_access' => 'integer',
        'sch_access' => 'integer',
        'control_panel_access' => 'integer',
    ];

    /**
     * 🔹 Relation: User belongs to a Country
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * 🔹 Relation: User has many Quotations
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'user_id');
    }
}
