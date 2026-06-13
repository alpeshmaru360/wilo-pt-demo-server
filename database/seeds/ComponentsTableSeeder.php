<?php

use Illuminate\Database\Seeder;
use App\Component;

class ComponentsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $components = [
            [
                'id' => 1,
                'value' => 'Standard',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => 2,
                'value' => 'Economic',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
        ];

        Component::insert($components);
    }

}
