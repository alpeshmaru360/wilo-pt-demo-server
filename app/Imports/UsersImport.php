<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\ControlPanel;

class UsersImport implements ToModel, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        return new \App\ControlPanel([
            'no_of_pump_id' => $row[0] ?? null,
            'power_id' => $row[1] ?? null,
            'voltage_id' => $row[2] ?? null,
            'application_id' => $row[3] ?? null,
            'price' => 0,
            'user_id' => 1,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000; // 1000 rows at a time
    }

    public function batchSize(): int
    {
        return 1000;
    }
}