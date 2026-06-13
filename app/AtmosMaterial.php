<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AtmosMaterial extends Model
{
    public function atmos_materials_code(){
        return $this->hasMany('App\AtmosCart', 'material_id', 'code');
    }
}
