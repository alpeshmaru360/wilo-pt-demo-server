<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ScpPumpType extends Model
{
    //

    protected function scp_shipping_percentage(){
        return DB::table('setup_fields')->where('name','scp_shipping_percentage')->pluck('value')[0];
    }

    protected function scp_over_head(){
        return DB::table('setup_fields')->where('name','scp_over_head')->pluck('value')[0];
    }

    protected function scp_adder_code_no_4(){
        return DB::table('setup_fields')->where('name','scp_adder_code_no_4')->pluck('value')[0];
    }

}
