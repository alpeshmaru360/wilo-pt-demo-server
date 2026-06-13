<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Http\Requests\GetDataRequest;
use App\Cart;
use App\ControlPanel;
use App\Traits\ControlPanelModelIdGet;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;

class CPBasicController extends Controller {

    use ControlPanelModelIdGet;

//
    public function controlpanel_Basic(Request $request) {

        $number_of_pumps = $request->number_of_pump;
        $supply_voltage = $request->supply_voltage;
        $enclosure = ucwords($request->enclosure);
      
        $path = Storage::path('public/File Selection - Final (1).xlsx'); // url come from db
        $data = new \SpreadsheetReader($path);
        $Sheets = $data->Sheets();
        $unitprice = 0;
        $userfilterunit = '';
        $html = '<br>';

        $delta = array();
        $i = 0;

//        ControlPanel::query()->delete();
//        exit;
        set_time_limit(0);


        foreach ($data as $Row) {

            echo "<pre>";
//           echo  $Row[14];
//           die;
            $delta[$i] = $Row[1];

            $i++;
            if ($i > 1) {
                echo 'number of row' . $i . "**" . $this->getIdByValue('App\NumberOfPump', 'value', $Row[0]);
//                dd($this->getIdByValue('App\Power', 'value', $Row[1]));
                DB::connection()->disableQueryLog();
                $controlPanel = new ControlPanel;
                $controlPanel->no_of_pump_id = $this->getIdByValue('App\NumberOfPump', 'value', $Row[0]);
                $controlPanel->power_id = $this->getIdByValue('App\Power', 'value', $Row[1]); //Power Rating
                $controlPanel->voltage_id = $this->getIdByValue('App\Voltage', 'value', $Row[2]); //Power Supply
                $controlPanel->application_id = $this->getIdByValue('App\Application', 'value', $Row[3]);
                $controlPanel->ambient_temp_id = $this->getIdByValue('App\AmbientTemp', 'value', $Row[4]);
                $controlPanel->stater_type_id = $this->getIdByValue('App\StarterType', 'value', $Row[5]);
                $controlPanel->communication_protocol_id = $this->getIdByValue('App\ComunicationProtocol', 'value', $Row[6]);
                $controlPanel->ip_rating_id = $this->getIdByValue('App\IpRating', 'value', $Row[7]);
                $controlPanel->components_id = $this->getIdByValue('App\Component', 'value', $Row[8]);
                $controlPanel->enclosure_id = $this->getIdByValue('App\Enclousre', 'value', $Row[9]);
                $controlPanel->range = $this->getIdByValue('App\Range', 'value', $Row[10]);
                $controlPanel->folder_name = isset($Row[11]) ? $Row[11] : '';
                $controlPanel->file_name_under_folder = isset($Row[12]) ? $Row[12] : '';
                $controlPanel->table_name = isset($Row[13]) ? $Row[13] : '';
                $controlPanel->starter_code = isset($Row[14]) ? $Row[14] : '';
                $controlPanel->price = 0;
                $controlPanel->user_id = 1;
                $controlPanel->created_at = date("Y-m-d H:i:s");
                $controlPanel->updated_at = date("Y-m-d H:i:s");
                $controlPanel->save();
//                dd($controlPanel);
                DB::connection()->disableQueryLog();
//                echo $i;
            }
        }
        exit;


        if ($unitprice != 0) {
            $request['price'] = $unitprice[8];
        }
        if ($userfilterunit != '') {
            $html .= "<span>Control Panel Description: <b>" . $userfilterunit[1] . "</b></span><br>";
            $html .= "<span>Control Panel Number of Pumps: <b>" . $userfilterunit[3] . "</b></span><br>";
            $html .= "<span>Control Panel Motor Rating: <b>" . $userfilterunit[4] . "</b></span><br>";
            $html .= "<span>Control Panel Voltage: <b>" . $userfilterunit[6] . "</b></span><br>";
            $html .= "<span>Control Panel Enclosure: <b>" . $userfilterunit[7] . "</b></span><br>";
            $html .= "<span>Control Panel Brand code: <b>" . $userfilterunit[8] . "</b></span><br>";
            $html .= "<span>Control Panel Function code: <b>" . $userfilterunit[9] . "</b></span><br>";
            $html .= "<span>Control Panel Range: <b>" . $userfilterunit[10] . "</b></span><br>";
            $html .= "<span>Control Panel Price: <b>" . $unitprice[8] . "</span>";
            foreach ($request->all() as $key => $data1) {
                $html .= "<input type='hidden' name='result[$key]' value='$data1'>";
            }
            return response()->json(array('success' => true, 'html' => $html));
        } else {
            $html .= "<span>your selected value don't have any control panel in Sheet</span>";
            return response()->json(array('success' => false, 'html' => $html));
        }




//return $html;
//dd($userfilterunit);
        // dd(array_merge($userfilterunit,$unitprice));
    }

    public function get_price($row) {
        $addition_overhead = 1.1; // this data come from db
        $path = Storage::path('public/mastersheet/Master_price_sheet_electrical_components.xlsx');
        $data = new \SpreadsheetReader($path);
        $Sheets = $data->Sheets();
        $result_unit_price = 0;
        foreach ($data as $Row) {
            // echo "<pre>";
            if ($Row[5] == $row[8]) {
                if ($Row[6] == $row[9]) {
                    if ($Row[7] == $row[10]) {
                        $fp = $Row[8] * $addition_overhead;
                        $Row[8] = $fp;
                        $result_unit_price = $Row;
                        //  print_r($Row);  
                    }
                }
            }

            // echo "</pre>";
        }

        return $result_unit_price;
    }

    public function addtocart(Request $request) {
        $number = rand(1, 100);
        $data = array();
        $user_id = auth()->user()->id;
        if ($request->result['article_number']) {

            dd($request->result['number_of_pump']);
        } else {
            $articlenumber = 'Ar-00' . $number . $request->result['number_of_pump'];

            $input = $request->result;
            $input['article_number'] = $articlenumber;
            $input['user_id'] = $user_id;
            dd($input);
            $lastid = Controlpanel::create($input);
            dd($lastid);
        }
        //return redirect()->route('cp.controlpanel');
    }

}
