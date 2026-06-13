<?php

namespace App\Imports;

use App\Models\BoosterCableSelection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BoosterCableSelectionImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BoosterCableSelection([
            //
            'cable'     => $row[1],
            'material_number'    => $row[2],
            'wilo_article_number' => $row[3],
            'brand_code'     => $row[4],
            'function_code'     => $row[5],
            'range'     => $row[6],
            'unit_price'     => $row[7],
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
