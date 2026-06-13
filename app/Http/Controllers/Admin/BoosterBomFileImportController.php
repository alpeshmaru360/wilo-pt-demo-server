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
use App\Helpers\BoosterBomDynamicTableCreateHelper;
use App\ScpMasterMotorPrice;
use App\ScpAssemblyCostPcPk;
use App\ScpAdder;
use SpreadsheetReader;

class BoosterBomFileImportController extends Controller {

    use ComponentModelIdValueGet;

    public function import(Request $request) {

        return view('admin.booster.bom_import');
    }

    public function upload(Request $request) {

        
        $file = $request->file_import;
        $type = $request->type;
        if ($type == 'pn16') {
            $tableName = 'booster_pn16_mechanical_component';
        } else {
            $tableName = 'booster_pn25_mechanical_component';
        }


        $booster_ptp_distance_mechanical_component = DB::table('booster_ptp_distance_mechanical_component')->get();



        if (!empty($file)) {
            $path = '/app/public/';

            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new SpreadsheetReader($filePath . $file_excel);

            $pumpModelRanges = [];
            $pumpNumberRanges = [];
            $combinePumpAndRange = [];
            $defaultColumn = [];

            foreach ($data as $key => $d) {

                unset($d[0]);
                unset($d[1]);
                unset($d[2]);
                unset($d[3]);
                unset($d[4]);
                unset($d[5]);
                unset($d[6]);
                unset($d[7]);
                unset($d[8]);
                unset($d[9]);
                if ($key == 5) {

                }
                if ($key == 0) { //Only first row
                    for ($x = 10; $x <= count($d) + 9; $x++) {
                        DB::enableQueryLog();
                        $pumpNumberRanges[] = trim($d[$x]);

//dd(DB::getQueryLog());
//  echo "The number is: $d[$x] <br>";
                    }
                }
                if ($key == 2) { //Only first row
                    for ($x = 10; $x <= count($d) + 9; $x++) {
                        DB::enableQueryLog();
                        $a = DB::table('booster_ptp_distance_mechanical_component')
                                ->select('id')
                                ->where('pump_model_range1', trim($d[$x]))
                                ->first();
                        if (isset($a->id)) {
                            $pumpModelRanges[] = $a->id;
                        } else {

                        }
                    }
                }
            }
            foreach ($pumpNumberRanges as $key => $val) {
                $combinePumpAndRange[] = $val . 'x' . $pumpModelRanges[$key];
            }
                 $createTableFieldArray = BoosterBomDynamicTableCreateHelper::createDynamic($tableName, $combinePumpAndRange); // $d equal to first row
//dd($createTableFieldArray);
            foreach ($data as $key => $d) {

                if ($key == 0) { //Only first row
                    $createTableFieldArray = BoosterBomDynamicTableCreateHelper::createDynamic($tableName, $combinePumpAndRange); // $d equal to first row


                    unset($createTableFieldArray[0]); //Remove Id column
                    unset($d[0]); // Remove S.No Rows
                } else if ($key > 5 && is_array($createTableFieldArray)) {
//                 echo "<pre>" . print_r(  $createTableFieldArray, 1);
                    $insertData = [];
                    for ($column = 1; $column <= count($createTableFieldArray); $column ++) {

                        $insertData[$createTableFieldArray[$column]['name']] = isset($d[$column]) ? $d[$column] : 0; // $d[$column] = $row
                    }
//                    echo "<pre>" . print_r($insertData, 1);
                    DB::table($tableName)->insert(
                            array($insertData)
                    );
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function getPptFindId($column) {
        $columnExplode = explode("_", $column);

        $booster_ptp_distance_mechanical_component = DB::table('booster_ptp_distance_mechanical_component')
                ->where('pump_model_range1', $columnExplode[0])
                ->where('pump_model_range1', $columnExplode[2])
                ->get();
        if ($booster_ptp_distance_mechanical_component) {
            return $booster_ptp_distance_mechanical_component[0]->id;
        }
        return 0;
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
