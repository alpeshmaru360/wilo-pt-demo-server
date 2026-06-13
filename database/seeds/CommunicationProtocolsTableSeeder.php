<?php

use Illuminate\Database\Seeder;
use App\ComunicationProtocol;

class CommunicationProtocolsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $comunicationProtocols = [
            [
                'id' => 1,
                'value' => 'VFC',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => 2,
                'value' => 'Modbus RTU',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => 3,
                'value' => 'Modbus TCP',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => 4,
                'value' => 'Bacnet',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]
        ];

        ComunicationProtocol::insert($comunicationProtocols);
    }

}
