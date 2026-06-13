<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ScpvPumpType extends Model
{
    protected function scpv_shipping_percentage(){
        return DB::table('setup_fields')->where('name','scpv_shipping_percentage')->pluck('value')[0];
    }

    protected function scpv_over_head(){
        return DB::table('setup_fields')->where('name','scpv_over_head')->pluck('value')[0];
    }

    protected function scpv_adder_code_no_4(){
        return DB::table('setup_fields')->where('name','scpv_adder_code_no_4')->pluck('value')[0];
    }

    // A Code: 05-03-2026 Start 
    protected function scpv_ksa_over_head(){
        return DB::table('setup_fields')->where('name','scpv_ksa_over_head')->pluck('value')[0];
    }

    protected function scpv_morrocco_over_head(){
        return DB::table('setup_fields')->where('name','scpv_morrocco_over_head')->pluck('value')[0];
    }
    // A Code: 05-03-2026 End
    
}
