<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountiresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            
        $country = [
            [
                "id" => 1,
                "country" => "lebanon"
            ],

            [
                "id" => 2,
                "country" => "syria",
            ],

            [
                "id" => 3,
                "country" => "jordan",
            ],

            [
                "id" => 4,
                "country" => "egypt",
            ],

            [
                "id" => 5,
                "country" => "uae",
            ],

            [
                "id" => 6,
                "country" => "ksa",
            ],

            [
                "id" => 7,
                "country" => "qatar",
            ],

            [
                "id" => 8,
                "country" => "pakistan",
            ],
            [
                "id" => 9,
                "country" => "morocco",
            ],
            
        ];
            DB::table('countries')->insert($country);


            $ic = DB::table('ic_margin')->get();
            
            $cid = 1;
            foreach($ic as $c){
                DB::table('ic_margin')->where('id',$c->id)->update(
                    ['country_id'=>$cid]
                );
                if($cid == 9)
                    $cid = 1;
                else
                    $cid++;    
            }

    
    }
}
