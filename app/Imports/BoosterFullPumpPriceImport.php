<?php

namespace App\Imports;

use App\Models\BoosterFullPumpPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BoosterFullPumpPriceImport implements ToModel, WithStartRow, WithBatchInserts,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        if( strpos($row[2], "HELIX") === 0)
            $model_no = substr($row[2], 0, strpos($row[2], "-"));
        else
            $model_no = substr($row[2],0,strpos($row[2], '-', strpos($row[2], '-') + 1));

        return new BoosterFullPumpPrice([
                 'pump_article_no_helix_pump'     => $row[1],
                 'description'    => $row[2],
                 'model_no' => $model_no,
                'pump_height'     => $row[4],
                'pump_weight'    => $row[5],
                'power' => $row[6],
            'no_of_phase'     => $row[7],
            'voltage'    => $row[8],
            'frequency' => $row[9],
            'unit_price'     => $row[10]
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
