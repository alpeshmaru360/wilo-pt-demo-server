<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class AtmosPumpType extends Model
{
    //
    protected function atmos_shipping_percentage(){
        return DB::table('setup_fields')->where('name','atmos_shipping_percentage')->pluck('value')[0];
    }

    protected function atmos_over_head(){
        return DB::table('setup_fields')->where('name','atmos_over_head')->pluck('value')[0];
    }

    protected function atmos_ksa_over_head(){
        return DB::table('setup_fields')->where('name','atmos_ksa_over_head')->pluck('value')[0];
    }

    protected function atmos_morrocco_over_head(){
        return DB::table('setup_fields')->where('name','atmos_morrocco_over_head')->pluck('value')[0];
    }

    protected function atmos_adder_code_no_4(){
        return DB::table('setup_fields')->where('name','atmos_adder_code_4')->pluck('value')[0];
    }
}
