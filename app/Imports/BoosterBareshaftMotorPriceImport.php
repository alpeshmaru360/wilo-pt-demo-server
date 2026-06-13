<?php

namespace App\Imports;

use App\Models\BoosterBareshaftMotorPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BoosterBareshaftMotorPriceImport implements  ToModel, WithStartRow, WithBatchInserts,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BoosterBareshaftMotorPrice([
            //
            'brand'     => $row[0],
            'power'    => $row[1],
            'motor_article_number' => $row[2],
            'wilo_article_number'     => $row[3],
            'motor_height'    => $row[4],
            'motor_weight' => $row[5],
            'no_of_pole'     => $row[6],
            'no_of_phase'    => $row[7],
            'voltage' => $row[8],
            'frequency'     => $row[9],
            'frame'     => $row[10],
            'efficiency'     => $row[11],
            'price'     => $row[12]
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 300;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
