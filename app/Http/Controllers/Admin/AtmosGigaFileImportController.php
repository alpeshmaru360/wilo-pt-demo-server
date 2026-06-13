<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use DB;
use Session;
use App\AtmosPumpType;
use App\AtmosPump;
use App\AtmosPumpBOM;
use App\AtmosPumpAssemblyCost;
use App\AtmosMaterial;
use App\Traits\ComponentModelIdValueGet;
use App\Helpers\AtmosGigaDynamicTableCreateHelper;
use App\AtmosMasterMotorPrice;
use App\AtmosAssemblyCostPcPk;
use App\AtmosAdder;
use App\AtmosMasterPumpPrice;
use Illuminate\Support\Facades\Schema;


class AtmosGigaFileImportController extends Controller {

    use ComponentModelIdValueGet;

    // Assemebly cost starts
    public function pumpAssmeblyCostImport(Request $request){
        return view('admin.atmos_giga_import.pump_assmebly_cost');
    }

    public function pumpAssmeblyCostImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/storage/app/public/';
            if(!File::exists(storage_path() . '/' . $path)){
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            $materialCode = [];
            AtmosPumpAssemblyCost::truncate();
            
            foreach ($data as $key => $d) {
                if ($key > 0) {
                    $atmosPumpType = new AtmosPumpAssemblyCost();
                    $atmosPumpType->model_name = $d[0];
                    $atmosPumpType->material_code = $d[1];
                    $atmosPumpType->standard_impeller_size = $d[2];
                    $atmosPumpType->impeller_minimum_size = $d[3];
                    $atmosPumpType->impeller_maximum_size = $d[4];
                    $atmosPumpType->impeller_material_code = $d[5];
                    $atmosPumpType->assmebly_cost = $d[6];
                    $atmosPumpType->testing_cost = $d[7];
                    $atmosPumpType->balancing_cost = $d[8];
                    $atmosPumpType->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function pumpBOMImport(Request $request){
        return view('admin.atmos_giga_import.pump_bom');
    }

    public function pumpBOMImportUpload(Request $request) {
        $file = $request->file('file_import');
        if (!empty($file)) {
            $path = 'storage/app/public/';
            if (!File::exists(storage_path($path))) {
                $this->make_directory1(storage_path($path));
            }
            $filePath = storage_path($path);
            $file_excel = $this->uploadFile1($file, $filePath);
            $data = new \SpreadsheetReader($filePath . $file_excel);
            $columnNames = [];
            $firstRow = '';
            $secondRow = '';
            AtmosPumpBOM::truncate();
            foreach ($data as $key => $row) {
                if ($key == 0) {
                    $firstRow = $row;
                }
                if ($key == 1) {
                    $secondRow = $row;
                }
                if ($key == 2) {
                        $columnNames = array_map(function($name,$firstRow,$secondRow) {
                            $name = str_replace([' ','.' ,'-', '/'], '_', $name.'X'.$firstRow.'X'.$secondRow);
                            return strtolower(trim($name));
                        }, $row,$firstRow,$secondRow);
                                        
                $columnNames = array_filter($columnNames);
                Schema::table('atmos_bom', function (\Illuminate\Database\Schema\Blueprint $table) use ($columnNames) {
                    foreach ($columnNames as $columnName) {
                    $dataType = 'text';
                        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $columnName) && !Schema::hasColumn('atmos_bom', $columnName)) {
                            $table->$dataType($columnName)->nullable();
                        }
                    }
                });
                } elseif ($key > 2) {
                    $atmosPumpType = new AtmosPumpBOM();

                    // Dynamically set the properties using the column names
                    foreach ($columnNames as $index => $columnName) {
                        if (isset($row[$index])) {
                            $atmosPumpType->$columnName = $row[$index];
                        }
                    }

                    $atmosPumpType->save();
                }
            }
        }
        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    private function make_directory1($path) {
        // Make directory if not exists
        return File::makeDirectory($path, 0755, true, true);
    }

    private function uploadFile1($file, $filePath) {
        // Handle file upload
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($filePath, $fileName);
        return $fileName;
    }

    public function pumpMasterPumpPriceImport(Request $request){
        return view('admin.atmos_giga_import.pump_master_sheet');
    }

    public function pumpMasterPumpPriceImportUpload(Request $request) {
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/storage/app/public/';
            if(!File::exists(storage_path() . '/' . $path)){
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            $materialCode = [];
            AtmosMasterPumpPrice::truncate();
            
            foreach ($data as $key => $d) {
                if ($key > 0) {
                    $atmosPumpType = new AtmosMasterPumpPrice();
                    $atmosPumpType->description = $d[1];
                    $atmosPumpType->china_article_number = $d[2];
                    $atmosPumpType->wme_article_number = $d[3];
                    $atmosPumpType->unit_price = $d[4];
                    $atmosPumpType->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function pumpTypeImport(Request $request) {
        return view('admin.atmos_giga_import.pump_type_import');
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

            $materialCode = [];
			AtmosPump::truncate();
			AtmosPumpType::truncate();
			
            foreach ($data as $key => $d) {
                unset($d[0]); // Remove S.No Rows

                if ($key == 2) {
                    for ($i = 2; $i <= count($d); $i += 2) {
                        $materialCode[] = $d[$i];
                    }
                }

                if ($key > 3) {

                    $atmosPumpType = new AtmosPumpType();
                    $atmosPumpType->name = $d[1];
                    $atmosPumpType->save();
                    $atmosPumpTypeId = $atmosPumpType->id;
                    $a = 2;
                    foreach ($materialCode as $code) {
                        $getMaterialCode = $this->getIdByValue('App\AtmosMaterial', 'code', $code);
                        $atmosPump = new AtmosPump();
                        $atmosPump->pump_id = $atmosPumpTypeId;
                        $atmosPump->material_id = $getMaterialCode;
                        $atmosPump->bare_pump_article_no = $d[$a];
                        $a++;
                        $atmosPump->tpl_fob_price = $d[$a];
                        $atmosPump->save();

                        $a++;
                    }
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function importAccessories(Request $request) {
        return view('admin.atmos_giga_import.accesories_price_import');
    }

    public function importAccessoriesUpload(Request $request) {
        set_time_limit(0);
        $tableName = 'atmos_accessories_price';
        $file = $request->file_import;
        if (!empty($file)) {
            
            // $path = '/app/public/' . $tableName;
            // if (!File::exists($path)) {
            //     $this->make_directory(storage_path() . '/' . $path);
            // }
            //
            $path = 'storage/app/public/';
            if (!File::exists(storage_path($path))) {
                $this->make_directory1(storage_path($path));
            }

            //

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

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
                            $row1Data[] = $this->getIdByValue('App\AtmosPumpType', 'name', $row1);
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
            $createTableFieldArray = AtmosGigaDynamicTableCreateHelper::createDynamic($tableName, $combineRows, $row3Data);
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

        //        redirect()->back()
    }

    public function masterPriceImport(Request $request) {
        return view('admin.atmos_giga_import.master_price_import');
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
			AtmosMasterMotorPrice::truncate();
            $materialCode = [];
            foreach ($data as $key => $d) {
                if ($key > 0) {
                    $atmosMasterMotorPrice = new AtmosMasterMotorPrice();
                    $atmosMasterMotorPrice->brand = $d[0];
                    $atmosMasterMotorPrice->power = $d[1];
                    $atmosMasterMotorPrice->item_desc = $d[2];
                    $atmosMasterMotorPrice->wilo_article_number = $d[3];
                    $atmosMasterMotorPrice->motor_height = $d[4];
                    $atmosMasterMotorPrice->frame_size = $d[5];
                    $atmosMasterMotorPrice->no_of_pole = $d[6];
                    $atmosMasterMotorPrice->no_of_phase = $d[7];
                    $atmosMasterMotorPrice->voltage = $d[8];
                    $atmosMasterMotorPrice->frequency = $d[9];
                    $atmosMasterMotorPrice->efficiency = $d[10];
                    $atmosMasterMotorPrice->price = $d[11];
                    $atmosMasterMotorPrice->insulate_bearing = $d[12];
                    $atmosMasterMotorPrice->forwinding = $d[13];
                    $atmosMasterMotorPrice->forbearing = $d[14];
                    $atmosMasterMotorPrice->space_heater = $d[15];

                    $atmosMasterMotorPrice->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function costPaintPackImport(Request $request) {
        return view('admin.atmos_giga_import.costpaint_price_import');
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
            AtmosAssemblyCostPcPk::truncate();
            $data = new \SpreadsheetReader($filePath . $file_excel);
            foreach ($data as $key => $d) {
                if ($key > 1) {

                    $atmosAssemblyCostPcPk = new AtmosAssemblyCostPcPk();
                    $atmosAssemblyCostPcPk->power = $d[0];
                    $atmosAssemblyCostPcPk->assembly_charge = $d[1];
                    $atmosAssemblyCostPcPk->painting_charge = $d[2];
                    $atmosAssemblyCostPcPk->packing_charge = $d[3];
                    $atmosAssemblyCostPcPk->labour_hour = $d[4];
                    $atmosAssemblyCostPcPk->shipping = $d[5];
                    $atmosAssemblyCostPcPk->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function adderImport(Request $request) {
        return view('admin.atmos_giga_import.adder_import');
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
            AtmosAdder::truncate();
            $data = new \SpreadsheetReader($filePath . $file_excel);
            foreach ($data as $key => $d) {
                if ($key > 0) {

                    $atmosAdder = new AtmosAdder();
                    $atmosAdder->adder_list = $d[1];
                    $atmosAdder->version = $d[2];
                    $atmosAdder->code = $d[3];
                    $atmosAdder->save();
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
