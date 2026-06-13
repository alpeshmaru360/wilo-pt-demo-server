<?php

namespace App\Imports;

use App\Models\FireFighting\DieselPump;
use Maatwebsite\Excel\Concerns\ToArray;

class ExcelImportData implements ToArray
{
    /**
    * @param Collection $collection
    */
    private $data = [];

    public function array(array $row)
    {
        $save_data = array_map(function ($val, $k)
        {
            if ($k != 0) {
                array_shift($val);
                return $val;
            }
        }, $row, array_keys($row));

        $save_data = array_filter($save_data);
        $this->data = $save_data;
    }

    public function getDataList(): array
    {
        return $this->data;
    }
}
