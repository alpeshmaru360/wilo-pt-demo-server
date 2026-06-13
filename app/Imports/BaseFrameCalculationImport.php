<?php

namespace App\Imports;

use App\Models\BaseFrameCalculation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BaseFrameCalculationImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BaseFrameCalculation([
            //
            'item_description'     => $row[1],
            'no_of_pumps'     => $row[2],
            'pump_model_range1'     => $row[3],
            'pump_model_range2'     => $row[4],
            'ptp'     => $row[5],
            'base_frame_length'     => $row[6],
            'material_number'    => $row[7],
            'wilo_article_number' => $row[8],
            'brand_code'     => $row[9],
            'function_code'     => $row[10],
            'range'     => $row[11],
            'unit_price'     => $row[12],
            'qty'     => $row[13],
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
