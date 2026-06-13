<?php

use Illuminate\Database\Seeder;
use App\ScpMaterial;

class ScpMaterialSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $scpMaterial = [
            [
                'name' => 'Cast Iron',
                'code' => '08',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'name' => 'Bronze',
                'code' => '44',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'name' => 'Stainless Steel',
                'code' => '48',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]
        ];

        ScpMaterial::insert($scpMaterial);
    }

}
