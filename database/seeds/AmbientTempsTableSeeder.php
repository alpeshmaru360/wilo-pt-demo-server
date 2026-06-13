<?php

use Illuminate\Database\Seeder;
use App\AmbientTemp;

class AmbientTempsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $ambientTemps = [
            [
                'id' => 1,
                'value' => 40,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => 2,
                'value' => 50,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
        ];

        AmbientTemp::insert($ambientTemps);
    }

}
