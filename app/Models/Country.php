<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country',
    ];

    /**
     * 🔹 Relation: Country has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'country_id');
    }
}
