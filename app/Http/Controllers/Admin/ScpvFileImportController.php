<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;   // ADD THIS
use File;
use DB;
use Session;
use App\ScpvPumpType;
use App\ScpvPump;
use App\Traits\ComponentModelIdValueGet;
use App\Helpers\ScpvGigaDynamicTableCreateHelper;
use App\ScpvMasterMotorPrice;
use App\ScpvAssemblyCostPcPk;

class ScpvFileImportController extends Controller {

    use ComponentModelIdValueGet;

    public function pumpTypeImport(Request $request) {
        return view('admin.scpv_import.pump_type_import');
    }

    public function pumpTypeImportUpload(Request $request) {

        $file = $request->file_import;

        if (!empty($file)) {

            $path = storage_path('app/public');

            // Create directory if not exists
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file_excel = $this->uploadFile($file, $path . '/');

            $data = new \SpreadsheetReader($path . '/' . $file_excel);

            ScpvPumpType::truncate();
            ScpvPump::truncate();

            $materialCode = [];

            foreach ($data as $key => $d) {

                unset($d[0]);

                if ($key == 0) {
                    for ($i = 3; $i <= count($d); $i += 2) {
                        $materialCode[] = $d[$i];
                    }
                }

                if ($key > 1) {

                    $scpvPumpType = new ScpvPumpType();
                    $scpvPumpType->bare_shaft_article_number = $d[1];
                    $scpvPumpType->name = $d[2];
                    $scpvPumpType->save();

                    $scpvPumpTypeId = $scpvPumpType->id;

                    $a = 3;

                    foreach ($materialCode as $code) {

                        $getMaterialCode = $this->getIdByValue('App\ScpvMaterial', 'code', $code);

                        $scpvPump = new ScpvPump();
                        $scpvPump->pump_id = $scpvPumpTypeId;
                        $scpvPump->material_id = $getMaterialCode;
                        $scpvPump->gland_packed_price = $d[$a];
                        $a++;

                        $scpvPump->mechanical_seal_price = $d[$a];
                        $scpvPump->save();

                        $a++;
                    }
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }


    public function importAccessories(Request $request) {
        return view('admin.scpv_import.accesories_price_import');
    }

    public function importAccessoriesUpload(Request $request)
    {
        set_time_limit(0);

        $tableName = 'scpv_accessories_price';
        $file = $request->file_import;

        if (!empty($file)) {

            /* ================= Upload File ================= */

            $path = '/storage/app/public/';

            if (!File::exists(storage_path() . '/' . $path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            $row1Data = [];
            $row2Data = [];
            $row3Data = [];

            /* ================= Read Header Rows ================= */

            foreach ($data as $key => $d) {

                if ($key == 0) {
                    unset($d[0], $d[1], $d[2], $d[3]);

                    foreach ($d as $row1) {
                        if (!empty($row1)) {                        
                            $row1Data[] = $this->getIdByValue('App\ScpvPumpType', 'name', $row1);
                        }
                    }
                }

                if ($key == 1) {
                    unset($d[0], $d[1], $d[2], $d[3]);

                    foreach ($d as $row2) {
                        if (!empty($row2)) {

                            $row2Data[] = str_replace(".", "__", $row2);

                            // A Code: 05-03-2026 Start
                            // // Remove last character (s/m/l etc.)
                            // $cleanValue = preg_replace('/[a-zA-Z]+$/', '', $row2);
                            // $row2Data[] = str_replace(".", "__", $cleanValue);
                            // A Code: 05-03-2026 End
                        }
                    }
                }             

                if ($key == 2) {
                    foreach ($d as $k => $row3) {
                        if ($k < 4) {
                            $row3Data[] = str_replace(' ', '_', $row3);
                        }
                    }
                }
            }

            /* ================= Combine Column Headers ================= */

            $combineRows = [];

            foreach ($row1Data as $key => $val) {
                if (isset($row2Data[$key])) {
                    $combineRows[] = $val . 'x' . $row2Data[$key];
                }
            }

            /* Remove duplicate column names */
            $combineRows = array_values(array_unique($combineRows));

            /* Validate duplicate header issue */
            if (count($combineRows) == 0) {
                return back()->withErrors('Invalid Excel format or empty header.');
            }

            /* ================= Recreate Table Safely ================= */

            if (Schema::hasTable($tableName)) {
                Schema::drop($tableName);
            }

            $createTableFieldArray = ScpvGigaDynamicTableCreateHelper::createDynamic(
                $tableName,
                $combineRows,
                $row3Data
            );

            unset($createTableFieldArray[0]);

            /* Extra safety for duplicate fields */
            $temp = [];
            $uniqueFields = [];

            foreach ($createTableFieldArray as $field) {
                if (!in_array($field['name'], $temp)) {
                    $temp[] = $field['name'];
                    $uniqueFields[] = $field;
                }
            }

            $createTableFieldArray = array_values($uniqueFields);

            /* ================= Insert Data ================= */

            foreach ($data as $key => $d) {

                if ($key > 2 && is_array($createTableFieldArray)) {

                    // Check if entire row is empty
                    $rowHasData = false;
                    foreach ($d as $cell) {
                        if (!empty($cell)) {
                            $rowHasData = true;
                            break;
                        }
                    }

                    // Skip blank row
                    if (!$rowHasData) {
                        continue;
                    }

                    $insertData = [];

                    for ($column = 0; $column < count($createTableFieldArray); $column++) {

                        $fieldName = $createTableFieldArray[$column]['name'];

                        $insertData[$fieldName] = isset($d[$column]) && $d[$column] !== ''
                            ? $d[$column]
                            : 0;
                    }

                    DB::table($tableName)->insert($insertData);
                }
            }            
        }

        Session::flash('message', "Success! Your file has been imported");
        return redirect()->back();
    }

    public function masterPriceImport(Request $request) {
        return view('admin.scpv_import.master_price_import');
    }

    public function masterPriceImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/storage/app/public/';
            if(!File::exists(storage_path() . '/' . $path)){
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);
            ScpvMasterMotorPrice::truncate();
            foreach ($data as $key => $d) {


                if ($key > 0) {
                    $scpvMasterMotorPrice = new ScpvMasterMotorPrice();
                    $scpvMasterMotorPrice->brand = $d[0];
                    $scpvMasterMotorPrice->power = $d[1];
                    $scpvMasterMotorPrice->item_desc = $d[2];
                    $scpvMasterMotorPrice->wilo_article_number = $d[3];
                    $scpvMasterMotorPrice->motor_height = $d[4];
                    $scpvMasterMotorPrice->frame_size = $d[5];
                    $scpvMasterMotorPrice->no_of_pole = $d[6];
                    $scpvMasterMotorPrice->no_of_phase = $d[7];
                    $scpvMasterMotorPrice->voltage = $d[8];
                    $scpvMasterMotorPrice->frequency = $d[9];
                    $scpvMasterMotorPrice->efficiency = $d[10];
                    $scpvMasterMotorPrice->price = $d[11];
                    $scpvMasterMotorPrice->insulate_bearing = $d[12];
                    $scpvMasterMotorPrice->forwinding = $d[13];
                    $scpvMasterMotorPrice->forbearing = $d[14];
                    $scpvMasterMotorPrice->space_heater = $d[15];
                    $scpvMasterMotorPrice->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function costPaintPackImport(Request $request) {
        return view('admin.scpv_import.costpaint_price_import');
    }

    public function costPaintPackImportUpload(Request $request)
    {
        $file = $request->file('file_import');

        if (!empty($file)) {

            $path = storage_path('app/public');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $file_excel = $this->uploadFile($file, $path);

            ScpvAssemblyCostPcPk::truncate();

            $data = new \SpreadsheetReader($path . '/' . $file_excel);

            foreach ($data as $key => $d) {

                if ($key > 1) {

                    $scpvAssemblyCostPcPk = new ScpvAssemblyCostPcPk();
                    $scpvAssemblyCostPcPk->power = $d[0];
                    $scpvAssemblyCostPcPk->assembly_charge = $d[1];
                    $scpvAssemblyCostPcPk->painting_charge = $d[2];
                    $scpvAssemblyCostPcPk->packing_charge = $d[3];
                    $scpvAssemblyCostPcPk->labour_hour = $d[4];
                    $scpvAssemblyCostPcPk->shipping = $d[5];
                    $scpvAssemblyCostPcPk->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function make_directory($path) {
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    }

    public function uploadFile($file, $path, $type = '') {
        $is_uploaded = false;

        if (!empty($file)) {

            $fileName = $file->getClientOriginalName();

            if ($file->move($path, $fileName)) {
                $is_uploaded = $fileName;
            }
        }
        return $is_uploaded;
    }

}
