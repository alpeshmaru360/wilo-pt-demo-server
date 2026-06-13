<?php

namespace App\Imports;

use App\Models\PN25MechanicalComponent;
use Maatwebsite\Excel\Concerns\ToModel;

class PN25MechanicalComponentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PN25MechanicalComponent([
            //
        ]);
    }
}
