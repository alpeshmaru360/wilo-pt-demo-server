<?php

namespace App\Imports;

use App\Models\PN16MechanicalComponent;
use App\Models\PTPDistanceMechanicalComponent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PTPDistanceMechanicalComponentImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PTPDistanceMechanicalComponent([
            //
            'pump_model_range1'     => $row[1],
            'pump_model_range2'    => $row[2],
            'no_of_pumps' => $row[3],
            'ptp'     => $row[4]
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
