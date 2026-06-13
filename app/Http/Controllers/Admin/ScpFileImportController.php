<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use DB;
use Session;
use App\ScpPumpType;
use App\ScpPump;
use App\ScpMaterial;
use App\Traits\ComponentModelIdValueGet;
use App\Helpers\ScpGigaDynamicTableCreateHelper;
use App\ScpMasterMotorPrice;
use App\ScpAssemblyCostPcPk;
use App\ScpAdder;

class ScpFileImportController extends Controller {

    use ComponentModelIdValueGet;

    public function pumpTypeImport(Request $request) {
        return view('admin.scp_import.pump_type_import');
    }

    public function pumpTypeImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/app/public/';
            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }
            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);
            $data = new \SpreadsheetReader($filePath . $file_excel);
            ScpPumpType::truncate();
            ScpPump::truncate();
            $materialCode = [];
            foreach ($data as $key => $d) {
                unset($d[0]);
                if ($key == 0) {
                    for ($i = 3; $i <= count($d); $i += 2) {
                        $materialCode[] = $d[$i];
                    }
                }
                if ($key > 1) {
                    $scpPumpType = new ScpPumpType();
                    $scpPumpType->bare_shaft_article_number = $d[1];
                    $scpPumpType->name = $d[2];
                    $scpPumpType->save();
                    $scpPumpTypeId = $scpPumpType->id;
                    $a = 3;
                    foreach ($materialCode as $code) {
                        $getMaterialCode = $this->getIdByValue('App\ScpMaterial', 'code', $code);
                        $scpPump = new ScpPump();
                        $scpPump->pump_id = $scpPumpTypeId;
                        $scpPump->material_id = $getMaterialCode;
                        $scpPump->gland_packed_price = $d[$a];
                        $a++;
                        $scpPump->mechanical_seal_price = $d[$a];
                        $scpPump->save();
                        $a++;
                    }
                }
            }
        }
        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function importAccessories(Request $request) {
        return view('admin.scp_import.accesories_price_import');
    }

    public function importAccessoriesUpload(Request $request) {
        set_time_limit(0);
        $tableName = 'scp_accessories_price';
        $file = $request->file_import;
        if (!empty($file)) {
            //
                $path = '/storage/app/public/';
                if(!File::exists(storage_path() . '/' . $path)){
                    $this->make_directory(storage_path() . '/' . $path);
                }

                $filePath = storage_path() . $path . "/";
                $file_excel = $this->uploadFile($file, $filePath);


            //
            $data = new \SpreadsheetReader($filePath . $file_excel);

            $row1Data = [];
            $row2Data = [];
            $row3Data = [];
            foreach ($data as $key => $d) {

                   //                 unset($d[0]);
                if ($key == 0) {
                   //                     echo print_r($d) . "<br>";
                    unset($d[0]);
                    unset($d[1]);
                    unset($d[2]);
                    unset($d[3]);
                    foreach ($d as $row1) {
                        if ($row1) {
                            $row1Data[] = $this->getIdByValue('App\ScpPumpType', 'name', $row1);
                        }
                    };
                }
                if ($key == 1) {
                    unset($d[0]);
                    unset($d[1]);
                    unset($d[2]);
                    unset($d[3]);
                    foreach ($d as $row2) {
                        if ($row2) {
                            $row2Data[] = str_replace(".", "__", $row2);
                        }
                    };
                }
                if ($key == 2) {
                    foreach ($d as $k => $row3) {
                        if ($k < 4) {
                            $row3Data[] = $row3;
                        }
                    };
                }
            }

            $combineRows = [];

            foreach ($row1Data as $key => $val) {
                $combineRows[] = $val . 'x' . $row2Data[$key];
                   //                    
            }
				foreach ($row3Data as $key => $rd) {
                $check_space = strpos($rd, ' ');
                if ($check_space > 0) {

                    $rd = str_replace(" ", "_", $rd);
                    // dd($rd);
                    $row3Data[$key] = $rd;
                }
            }
            $createTableFieldArray = ScpGigaDynamicTableCreateHelper::createDynamic($tableName, $combineRows, $row3Data);
            unset($createTableFieldArray[0]);

            foreach ($data as $key => $d) {

                if ($key > 2 && is_array($createTableFieldArray)) {

                    $insertData = [];
                    for ($column = 0; $column < count($createTableFieldArray); $column ++) {

                        $insertData[$createTableFieldArray[$column + 1]['name']] = isset($d[$column]) ? $d[$column] : 0; // $d[$column] = $row  
                    }

                    DB::table($tableName)->insert(
                            array($insertData)
                    );
                }
            }
        }
        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function masterPriceImport(Request $request) {
        return view('admin.scp_import.master_price_import');
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
            ScpMasterMotorPrice::truncate();
            foreach ($data as $key => $d) {


                if ($key > 0) {
                    $scpMasterMotorPrice = new ScpMasterMotorPrice();
                    $scpMasterMotorPrice->brand = $d[0];
                    $scpMasterMotorPrice->power = $d[1];
                    $scpMasterMotorPrice->item_desc = $d[2];
                    $scpMasterMotorPrice->wilo_article_number = $d[3];
                    $scpMasterMotorPrice->motor_height = $d[4];
                    $scpMasterMotorPrice->frame_size = $d[5];
                    $scpMasterMotorPrice->no_of_pole = $d[6];
                    $scpMasterMotorPrice->no_of_phase = $d[7];
                    $scpMasterMotorPrice->voltage = $d[8];
                    $scpMasterMotorPrice->frequency = $d[9];
                    $scpMasterMotorPrice->efficiency = $d[10];
                    $scpMasterMotorPrice->price = $d[11];
                    $scpMasterMotorPrice->insulate_bearing = $d[12];
                    $scpMasterMotorPrice->forwinding = $d[13];
                    $scpMasterMotorPrice->forbearing = $d[14];
                    $scpMasterMotorPrice->space_heater = $d[15];
                    $scpMasterMotorPrice->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function costPaintPackImport(Request $request) {
        return view('admin.scp_import.costpaint_price_import');
    }

    public function costPaintPackImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/app/public/';

            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);
            ScpAssemblyCostPcPk::truncate();
            $data = new \SpreadsheetReader($filePath . $file_excel);
            foreach ($data as $key => $d) {
                if ($key > 1) {

                    $scpAssemblyCostPcPk = new ScpAssemblyCostPcPk();
                    $scpAssemblyCostPcPk->power = $d[0];
                    $scpAssemblyCostPcPk->assembly_charge = $d[1];
                    $scpAssemblyCostPcPk->painting_charge = $d[2];
                    $scpAssemblyCostPcPk->packing_charge = $d[3];
                    $scpAssemblyCostPcPk->labour_hour = $d[4];
                    $scpAssemblyCostPcPk->shipping = $d[5];
                    $scpAssemblyCostPcPk->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function adderImport(Request $request) {
        return view('admin.scp_import.adder_import');
    }

    public function adderImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/app/public/';

            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);
            ScpAdder::truncate();
            $data = new \SpreadsheetReader($filePath . $file_excel);
            foreach ($data as $key => $d) {
                if ($key > 0) {

                    $scpAdder = new ScpAdder();
                    $scpAdder->adder_list = $d[1];
                    $scpAdder->version = $d[2];
                    $scpAdder->code = $d[3];
                    $scpAdder->save();
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
