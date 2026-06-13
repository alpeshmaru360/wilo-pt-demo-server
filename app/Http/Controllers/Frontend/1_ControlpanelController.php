<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Voltage;
use App\AmbientTemp;
use App\Application;
use App\Component;
use App\NumberOfPump;
use App\IpRating;
use App\StarterType;
use App\Enclousre;
use App\ComunicationProtocol;
use App\Power;
use App\Range;
use App\ControlPanel;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Tax;
use App\Traits\ControlPanelModelIdGet;
use App\Helpers\AdderHelper;
use App\User;
use App\ControlPanelCart;
use App\Models\BoosterCart;

class ControlpanelController extends Controller{

    use ControlPanelModelIdGet;

    public function index() {
        $numberOfPumps = NumberOfPump::select('id', 'value')->get();
        $electricalLists = DB::table('main_electrical_list')->get();
        return view('frontend.controlpanel.index', compact('numberOfPumps', 'electricalLists'));
    }

    public function ajaxFilter(Request $request) {
        $idberOfPumps = NumberOfPump::select('id', 'value')->get();
       //DB::enableQueryLog(); // Enable query log
        $controlPanelData = new ControlPanel();
        if(isset($request->no_of_pump) && !empty($request->no_of_pump)){
            if (empty($request->no_of_pump)) {
                $controlPanelData = $controlPanelData->where('no_of_pump_id', '>=', $request->no_of_pump)
                        ->groupBy('power_id');
            } else {
                $controlPanelData = $controlPanelData->where('no_of_pump_id', '=', $request->no_of_pump)
                        ->groupBy('power_id');
            }
        }

        if (isset($request->power_rating) && !empty($request->power_rating)) {
            if (empty($request->power_rating)) {
                $controlPanelData = $controlPanelData->where('power_id', '>=', $request->power_rating)
                        ->groupBy('voltage_id');
            } else {
                $controlPanelData = $controlPanelData->where('power_id', $request->power_rating)
                        ->groupBy('voltage_id');
            }
        }

        if(isset($request->voltage) && !empty($request->voltage)){
            if(empty($request->voltage)){
                $controlPanelData = $controlPanelData->where('voltage_id', '>=', $request->voltage)
                        ->groupBy('application_id');
            }else{
                $controlPanelData = $controlPanelData->where('voltage_id', $request->voltage)
                        ->groupBy('application_id');
            }
        }

        if (isset($request->application) && !empty($request->application)) {
            $controlPanelData = $controlPanelData->where('application_id', $request->application)
                    ->groupBy('ambient_temp_id');
        }
        if (isset($request->ambient_temp) && !empty($request->ambient_temp)) {
            $controlPanelData = $controlPanelData->where('ambient_temp_id', $request->ambient_temp)
                    ->groupBy('stater_type_id');
        }
        if (isset($request->stater_type) && !empty($request->stater_type)) {
            $controlPanelData = $controlPanelData->where('stater_type_id', $request->stater_type)
                    ->groupBy('communication_protocol_id');
        }
        if (isset($request->communication_protocol) && !empty($request->communication_protocol)) {
            $controlPanelData = $controlPanelData->where('communication_protocol_id', $request->communication_protocol)
                    ->groupBy('ip_rating_id');
        }
        if (isset($request->ip_rating) && !empty($request->ip_rating)) {
            $controlPanelData = $controlPanelData->where('ip_rating_id', $request->ip_rating)
                    ->groupBy('components_id');
        }
        if (isset($request->component) && !empty($request->component)) {
            $controlPanelData = $controlPanelData->where('components_id', $request->component)
                    ->groupBy('enclosure_id');
        }
        if (isset($request->enclosure) && !empty($request->enclosure)) {
            $controlPanelData = $controlPanelData->where('enclosure_id', $request->enclosure)
                    ->groupBy('range');
        }
        if (isset($request->range) && !empty($request->range)) {
            $controlPanelData = $controlPanelData->where('range', $request->range);
        }
        $controlPanelData = $controlPanelData
        ->select('id','no_of_pump_id','power_id','voltage_id','application_id','ambient_temp_id','stater_type_id','communication_protocol_id','ip_rating_id','components_id','enclosure_id','range','folder_name','table_name')
                ->with('noofpumps')
                ->with('powers')
                ->with('voltages')
                ->with('applications')
                ->with('ambienttemps')
                ->with('startertypes')
                ->with('ranges')
                ->with('enclousres')
                ->with('comunicationprotocols')
                ->with('ipratings')
                ->get();
        $powers = [];
        $voltages = [];
        $applications = [];
        $ambienttemps = [];
        $startertypes = [];
        $components = [];
        $enclousres = [];
        $comunicationprotocols = [];
        $ipratings = [];
        $ranges = [];
        foreach($controlPanelData as $row){
            $powers[] = array('id' => $row->powers->id, 'value' => $row->powers->value);
            $voltages[] = array('id' => $row->voltages->id, 'value' => $row->voltages->value);
            $applications[] = array('id' => $row->applications->id, 'value' => $row->applications->value);
            $ambienttemps[] = array('id' => $row->ambienttemps->id, 'value' => $row->ambienttemps->value);
            $startertypes[] = array('id' => $row->startertypes->id, 'value' => $row->startertypes->value);
            $components[] = array('id' => $row->components->id, 'value' => $row->components->value);
            $enclousres[] = array('id' => $row->enclousres->id, 'value' => $row->enclousres->value);
            $comunicationprotocols[] = array('id' => $row->comunicationprotocols->id, 'value' => $row->comunicationprotocols->value);
            $ipratings[] = array('id' => $row->ipratings->id, 'value' => $row->ipratings->value);
            $ranges[] = array('id' => $row->ranges->id, 'value' => $row->ranges->value);
        }
        $powers = array_unique($powers, SORT_REGULAR);
        $voltages = array_unique($voltages, SORT_REGULAR);
        $applications = array_unique($applications, SORT_REGULAR);
        $ambienttemps = array_unique($ambienttemps, SORT_REGULAR);
        $startertypes = array_unique($startertypes, SORT_REGULAR);
        $components = array_unique($components, SORT_REGULAR);
        $enclousres = array_unique($enclousres, SORT_REGULAR);
        $comunicationprotocols = array_unique($comunicationprotocols, SORT_REGULAR);
        $ipratings = array_unique($ipratings, SORT_REGULAR);
        $ranges = array_unique($ranges, SORT_REGULAR);
        $data = [];
        $data['controlPanel'] = $controlPanelData;
        $data['powers'] = array_values($powers);
        $data['voltages'] = array_values($voltages);
        $data['applications'] = array_values($applications);
        $data['ambienttemps'] = array_values($ambienttemps);
        $data['startertypes'] = array_values($startertypes);
        $data['components'] = array_values($components);
        $data['enclousres'] = array_values($enclousres);
        $data['comunicationprotocols'] = array_values($comunicationprotocols);
        $data['ipratings'] = array_values($ipratings);
        $data['ranges'] = array_values($ranges);
        if(isset($request->enclosure) && !empty($request->enclosure)){
            if(isset($data['controlPanel'][0]) && !empty($data['controlPanel'][0])){
                $idberOfPump = $data['controlPanel'][0]->noofpumps['value'];
                if(ControlPanel::isIntegerColumn($data['controlPanel'][0]->powers['value'])){ 
                    // Integer Column
                    $power = $data['controlPanel'][0]->powers['value'];
                    $voltage = $data['controlPanel'][0]->voltages['value'];
                    $columnName = $idberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
                }else{ //Float column
                    $power = str_replace(".", '__', $data['controlPanel'][0]->powers['value']);
                    $voltage = $data['controlPanel'][0]->voltages['value'];
                    $columnName = $idberOfPump . 'x' . $power . "kwx" . $voltage . 'v';
                }
                $startertypes = $data['controlPanel'][0]->startertypes['value'];
                $starterCode = $data['controlPanel'][0]->starter_code;
                $range = $data['controlPanel'][0]->ranges['value'];
                $tableName = $data['controlPanel'][0]->table_name;
                $cpRecordsData = [];
                $returnHTML = '';
                $price = 0.00;
                if(Schema::hasTable($tableName)){
                    if(Schema::hasColumn($tableName, $columnName)){
                        $cpRecords = DB::table($tableName)->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
                        $cpRecords1 = DB::table($tableName)
                                    ->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                                    ->whereNotNull($columnName)
                                    ->where($columnName, '!=', 0)
                                    ->where('function_code','=','1')
                                    ->count();


                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        if($arrayResult){
                            $trim_height = trim($arrayResult[0]['item_description'], 'Enclosure ');
                            $height_exist = str_contains($trim_height, 'H');
                            if ($height_exist){
                                $cp_height = substr($trim_height, 0, strpos($trim_height, "H"));
                                $trim_width = trim($trim_height, $cp_height);
                                $cp_width = substr($trim_width, 0, strpos($trim_width, "W"));
                                $cp_width = trim($cp_width, 'H x ');
                            } else{
                                $cp_height = 0;
                                $cp_width = 0;
                            }
                            //here ajax calculate
                            $enclourse_exist = $this->calculatePriceInItem($arrayResult[0], $request, $columnName,$cpRecords1);
                            if($enclourse_exist == 0.0)
                            {
                                $data['enclourse_exist'] = null;
                                $price = 0.0;
                            }
                            else
                            {
                                foreach($arrayResult as $key => $val) {
                                    $price += $this->calculatePriceInItem($val, $request, $columnName,$cpRecords1);
                                }
                                $price1 = $price;
                                $data['control_panel_price_for_booster'] = $price1;
                                if($request->code_price && $request->code_price != '') {
                                        $price = $price + $request->code_price;
                                    }
                                    $tax = Tax::where('id', 1)->get()[0]->amount;
                                    $data['cp_price_booster'] = number_format($price, 2);
                                    $price = ($price * ControlPanel::controlpanel_over_head()) / User::ic_margin_control_panel();
                                $data['cp_price'] = number_format($price, 2);
                                $cpRecordsData[$key + 1]['price'] = number_format($price, 2);
                                $cpRecordsData[$key + 1]['range_id'] = $range;
                                $cpRecordsData[$key + 1]['tax'] = $tax;
                                $controlPanelId = $data['controlPanel'][0]->id;
                                $starter = $data['controlPanel'][0]->startertypes['value'];
                                $application = $data['controlPanel'][0]->applications['value'];
                                $noOfPump = $data['controlPanel'][0]->noofpumps['value'];
                                $power = $data['controlPanel'][0]->powers['value'];
                                $returnHTML = view('frontend.controlpanel.table')->with('cpRecordsData', $controlPanelId)
                                        ->with('tax', $tax)
                                        ->with('price', $price)
                                        ->with('starter', $starter)
                                        ->with('application', $application)
                                        ->with('noOfPump', $noOfPump)
                                        ->with('power', $power)
                                        ->with('starterCode', $starterCode)
                                        ->render();
                                $data['cp_records_html'] = $returnHTML;
                                $data['cp_id'] = $data['controlPanel'][0]->id;
                                $data['table_name'] = $tableName;
                                $data['column_name'] = $columnName;
                                $data['total_price'] = $price;
                                $data['cp_height'] = $cp_height;
                                $data['cp_width'] = $cp_width;
                                $data['enclourse_exist'] = 1;
                                $data['control_panel_price_for_booster'] = $price1;
                            }
                        }
                    }else {
                        $data['cp_records_html'] = '404 error May be Column is not found!';
                    }
                }else{
                    $data['cp_records_html'] = 'No Record Found!';
                    $data['cp_price'] = number_format($price, 2);
                }
                return response()->json(array('success' => true, 'data' => $data));
            }else{
                $data['cp_records_html'] = 'No Record Found!';
                $data['cp_price'] = 0.00;
                return response()->json(array('success' => true, 'data' => $data));
            }
        }else{
            $data['cp_records_html'] = 'No Record Found!';
            $data['cp_price'] = 0.00;
            return response()->json(array('success' => true, 'data' => $data));
        }
    }

    public function searchAjaxFilter(Request $request) {
        $idberOfPumps = NumberOfPump::select('id', 'value')->get();
        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
			if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $BoosterCartData = $BoosterCartData->first();
        if($BoosterCartData)
        {
            $cp_id = ControlPanel::where('id','=',$BoosterCartData->cp_id)->first();
            $request->no_of_pump = $cp_id->no_of_pump_id;
            $request->power_rating = $cp_id->power_id;
            $request->voltage = $cp_id->voltage_id;
            $request->application = $cp_id->application_id;
            $request->ambient_temp = $cp_id->ambient_temp_id;
            $request->stater_type = $cp_id->stater_type_id;
            $request->communication_protocol = $cp_id->communication_protocol_id;
            $request->ip_rating = $cp_id->ip_rating_id;
            $request->component = $cp_id->components_id;
            $request->enclosure = $cp_id->enclosure_id;
            $request->range = $cp_id->range;
            $pump_model = $BoosterCartData->model_no;
            $full_article_number=$request->full_article_number;
            if($cp_id)
            {
                DB::enableQueryLog(); // Enable query log
                $controlPanelData = new ControlPanel();
                if (isset($request->no_of_pump) && !empty($request->no_of_pump)) {
                    if (empty($request->no_of_pump)) {
                        $controlPanelData = $controlPanelData->where('no_of_pump_id', '>=', $request->no_of_pump)
                                ->groupBy('power_id');
                    } else {
                        $controlPanelData = $controlPanelData->where('no_of_pump_id', '=', $request->no_of_pump)
                                ->groupBy('power_id');
                    }
                }

                if (isset($request->power_rating) && !empty($request->power_rating)) {
                    if (empty($request->power_rating)) {
                        $controlPanelData = $controlPanelData->where('power_id', '>=', $request->power_rating)
                                ->groupBy('voltage_id');
                    } else {
                        $controlPanelData = $controlPanelData->where('power_id', $request->power_rating)
                                ->groupBy('voltage_id');
                    }
                }

                if (isset($request->voltage) && !empty($request->voltage)) {
                    if (empty($request->voltage)) {

                        $controlPanelData = $controlPanelData->where('voltage_id', '>=', $request->voltage)
                                ->groupBy('application_id');
                    } else {

                        $controlPanelData = $controlPanelData->where('voltage_id', $request->voltage)
                                ->groupBy('application_id');
                    }
                }

                if (isset($request->application) && !empty($request->application)) {
                    $controlPanelData = $controlPanelData->where('application_id', $request->application)
                            ->groupBy('ambient_temp_id');
                }

                if (isset($request->ambient_temp) && !empty($request->ambient_temp)) {
                    $controlPanelData = $controlPanelData->where('ambient_temp_id', $request->ambient_temp)
                            ->groupBy('stater_type_id');
                }

                if (isset($request->stater_type) && !empty($request->stater_type)) {

                    $controlPanelData = $controlPanelData->where('stater_type_id', $request->stater_type)
                            ->groupBy('communication_protocol_id');
                }

                if (isset($request->communication_protocol) && !empty($request->communication_protocol)) {
                    $controlPanelData = $controlPanelData->where('communication_protocol_id', $request->communication_protocol)
                            ->groupBy('ip_rating_id');
                }

                if (isset($request->ip_rating) && !empty($request->ip_rating)) {
                    $controlPanelData = $controlPanelData->where('ip_rating_id', $request->ip_rating)
                            ->groupBy('components_id');
                }

                if (isset($request->component) && !empty($request->component)) {
                    $controlPanelData = $controlPanelData->where('components_id', $request->component)
                            ->groupBy('enclosure_id');
                }

                if (isset($request->enclosure) && !empty($request->enclosure)) {
                    $controlPanelData = $controlPanelData->where('enclosure_id', $request->enclosure)
                            ->groupBy('range');
                }

                if (isset($request->range) && !empty($request->range)) {
                    $controlPanelData = $controlPanelData->where('range', $request->range);
                }

                $controlPanelData = $controlPanelData->with('noofpumps')
                        ->with('powers')
                        ->with('voltages')
                        ->with('applications')
                        ->with('ambienttemps')
                        ->with('startertypes')
                        ->with('startertypes')
                        ->with('ranges')
                        ->with('enclousres')
                        ->with('comunicationprotocols')
                        ->with('ipratings')
                        ->get();
                        
                $powers = [];
                $voltages = [];
                $applications = [];
                $ambienttemps = [];
                $startertypes = [];
                $components = [];
                $enclousres = [];
                $comunicationprotocols = [];
                $ipratings = [];
                $ranges = [];

                foreach ($controlPanelData as $row) {
                    $powers[] = array('id' => $row->powers->id, 'value' => $row->powers->value);
                    $voltages[] = array('id' => $row->voltages->id, 'value' => $row->voltages->value);
                    $applications[] = array('id' => $row->applications->id, 'value' => $row->applications->value);
                    $ambienttemps[] = array('id' => $row->ambienttemps->id, 'value' => $row->ambienttemps->value);
                    $startertypes[] = array('id' => $row->startertypes->id, 'value' => $row->startertypes->value);
                    $components[] = array('id' => $row->components->id, 'value' => $row->components->value);
                    $enclousres[] = array('id' => $row->enclousres->id, 'value' => $row->enclousres->value);
                    $comunicationprotocols[] = array('id' => $row->comunicationprotocols->id, 'value' => $row->comunicationprotocols->value);
                    $ipratings[] = array('id' => $row->ipratings->id, 'value' => $row->ipratings->value);
                    $ranges[] = array('id' => $row->ranges->id, 'value' => $row->ranges->value);
                }

                $powers = array_unique($powers, SORT_REGULAR);
                $voltages = array_unique($voltages, SORT_REGULAR);
                $applications = array_unique($applications, SORT_REGULAR);
                $ambienttemps = array_unique($ambienttemps, SORT_REGULAR);
                $startertypes = array_unique($startertypes, SORT_REGULAR);
                $components = array_unique($components, SORT_REGULAR);
                $enclousres = array_unique($enclousres, SORT_REGULAR);
                $comunicationprotocols = array_unique($comunicationprotocols, SORT_REGULAR);
                $ipratings = array_unique($ipratings, SORT_REGULAR);
                $ranges = array_unique($ranges, SORT_REGULAR);

                $data = [];
                $data['controlPanel'] = $controlPanelData;
                $data['powers'] = array_values($powers);
                $data['voltages'] = array_values($voltages);
                $data['applications'] = array_values($applications);
                $data['ambienttemps'] = array_values($ambienttemps);
                $data['startertypes'] = array_values($startertypes);
                $data['components'] = array_values($components);
                $data['enclousres'] = array_values($enclousres);
                $data['comunicationprotocols'] = array_values($comunicationprotocols);
                $data['ipratings'] = array_values($ipratings);
                $data['ranges'] = array_values($ranges);
                $data['pump_model'] = $pump_model;
                $data['full_article_number']= $full_article_number;

                if(isset($request->enclosure) && !empty($request->enclosure)){
                    if (isset($data['controlPanel'][0]) && !empty($data['controlPanel'][0])) {
                        $idberOfPump = $data['controlPanel'][0]->noofpumps['value'];
                        if (ControlPanel::isIntegerColumn($data['controlPanel'][0]->powers['value'])) { // Integer Column
                            $power = $data['controlPanel'][0]->powers['value'];
                            $voltage = $data['controlPanel'][0]->voltages['value'];
                            $columnName = $idberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
                        } else { //Float column
                            $power = str_replace(".", '__', $data['controlPanel'][0]->powers['value']);
                            $voltage = $data['controlPanel'][0]->voltages['value'];
                            $columnName = $idberOfPump . 'x' . $power . "kwx" . $voltage . 'v';
                        }
                        $startertypes = $data['controlPanel'][0]->startertypes['value'];
                        $starterCode = $data['controlPanel'][0]->starter_code;
                        $range = $data['controlPanel'][0]->ranges['value'];
                        $tableName = $data['controlPanel'][0]->table_name;
                        $cpRecordsData = [];
                        $returnHTML = '';
                        $price = 0.00;
                        if(Schema::hasTable($tableName)){
                            if (Schema::hasColumn($tableName, $columnName)) {

                                $cpRecords = DB::table($tableName)->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->get();

                                $cpRecords1 = DB::table($tableName)->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->where('function_code','=','1')->count();

                                $arrayResult = json_decode(json_encode($cpRecords), true);

                                //add starts
                    $component = $data['components'][0]['id'];
                    $enclousreAdderItemData = null;
                    if ($BoosterCartData->adder_ids && $BoosterCartData->adder_ids != '') {
                        $addersData = $this->calculateAddersSearchByArticle($BoosterCartData->cp_id, $BoosterCartData->adder_ids, $idberOfPump, $data['controlPanel'][0]->powers['value'], $voltage, $tableName, $columnName,$component); //Code ids
                        $enclousreAdderItemData = $addersData['enclousreItem'];
                        if ($addersData['code_price'] && $addersData['code_price'] != '') {
                            $codePrice = $addersData['code_price'];
                        }
                    }
                    //add ends
                            if($arrayResult){
                                    $trim_height = trim($arrayResult[0]['item_description'], 'Enclosure ');
                                    $height_exist = str_contains($trim_height, 'H');
                                    if ($height_exist){
                                        $cp_height = substr($trim_height, 0, strpos($trim_height, "H"));
                                        $trim_width = trim($trim_height, $cp_height);
                                        $cp_width = substr($trim_width, 0, strpos($trim_width, "W"));
                                        $cp_width = trim($cp_width, 'H x ');
                                    } else{
                                        $cp_height = 0;
                                        $cp_width = 0;
                                    }
                                    //here search ajax calculate
                                    $enclourse_exist = $this->searchByArticleCalculatePriceInItem($arrayResult[0], $data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName,$cpRecords1);
                                    if($enclourse_exist == 0.0)
                                    {
                                        $data['enclourse_exist'] = null;
                                        $price = 0.0;
                                    }
                                    else
                                    {
                                        foreach($arrayResult as $key => $val) {
                                            $price += $this->searchByArticleCalculatePriceInItem($val, $data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName,$cpRecords1);
                                        }
                                        $price1 = $price;
                                        $data['control_panel_price_for_booster'] = $price1;
                                        if($request->code_price && $request->code_price != '') {
                                                $price = $price + $request->code_price;
                                            }
                                            $tax = Tax::where('id', 1)->get()[0]->amount;
                                            $data['cp_price_booster'] = number_format($price, 2);
                                            $price = ($price * ControlPanel::controlpanel_over_head()) / User::ic_margin_control_panel();

                                        $data['cp_price'] = number_format($price, 2);
                                        $cpRecordsData[$key + 1]['price'] = number_format($price, 2);
                                        $cpRecordsData[$key + 1]['range_id'] = $range;
                                        $cpRecordsData[$key + 1]['tax'] = $tax;
                                        $controlPanelId = $data['controlPanel'][0]->id;
                                        $starter = $data['controlPanel'][0]->startertypes['value'];
                                        $application = $data['controlPanel'][0]->applications['value'];
                                        $noOfPump = $data['controlPanel'][0]->noofpumps['value'];
                                        $power = $data['controlPanel'][0]->powers['value'];
                                        $returnHTML = view('frontend.controlpanel.table')->with('cpRecordsData', $controlPanelId)
                                                ->with('tax', $tax)
                                                ->with('price', $price)
                                                ->with('starter', $starter)
                                                ->with('application', $application)
                                                ->with('noOfPump', $noOfPump)
                                                ->with('power', $power)
                                                ->with('starterCode', $starterCode)
                                                ->render();
                                        $data['cp_records_html'] = $returnHTML;
                                        $data['cp_id'] = $data['controlPanel'][0]->id;
                                        $data['table_name'] = $tableName;
                                        $data['column_name'] = $columnName;
                                        $data['total_price'] = $price;
                                        $data['cp_height'] = $cp_height;
                                        $data['cp_width'] = $cp_width;
                                        $data['enclourse_exist'] = 1;
                                        $data['control_panel_price_for_booster'] = $price1;
                                        if ($enclousreAdderItemData) {
                                            $data['enclousreItem'] = $enclousreAdderItemData;
                                        }
                                    }
                                }
                            } else {
                                $data['cp_records_html'] = '404 error May be Column is not found!';
                            }
                        } else {
                            $data['cp_records_html'] = 'No Record Found!';
                            $data['cp_price'] = number_format($price, 2);
                        }
                        return response()->json(array('success' => true, 'data' => $data));
                    } else {
                        $data['cp_records_html'] = 'No Record Found!';
                        $data['cp_price'] = 0.00;
                        return response()->json(array('success' => true, 'data' => $data));
                    }
                } else {
                    $data['cp_records_html'] = 'No Record Found!';
                    $data['cp_price'] = 0.00;
                    return response()->json(array('success' => true, 'data' => $data));
                }
            }
            else {
                $data['cp_records_html_error'] = 'Cotrol panel data not available..!!';
                return response()->json(array('success' => true, 'data' => $data));
            }
        }
        else {
            $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';
            return response()->json(array('success' => true, 'data' => $data));
        }
    }

    public function getMasterSheetPriceData($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if(isset($masterData[0]->price)) {
            return (float) $masterData[0]->price;
        }
        return 0;
    }
       
    public function getMasterSheetWeight($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('weight')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
               if (isset($masterData[0]->weight)) {
            return $masterData[0]->weight;
        }
        return 0;
    }

    public function getMasterSheetHeight($brand_code, $function_code, $range) {

        $masterData = DB::table('master_price_sheet_electrical_components')->select('height')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->height)) {
            return $masterData[0]->height;
        }

        return 0;
    }

    public function getMasterSheetWidth($brand_code, $function_code, $range) {

        $masterData = DB::table('master_price_sheet_electrical_components')->select('width')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->width)) {
            return $masterData[0]->width;
        }

        return 0;
    }

    public function getMasterSheetHeightMultiplyByWidth($brand_code, $function_code, $range) {

        $height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $width = $this->getMasterSheetWidth($brand_code, $function_code, $range);
        if ($height && $width) {
            return $height * $width;
        }

        return 0;
    }

    public function getMasterSheetDepth($brand_code, $function_code, $range) {

        $masterData = DB::table('master_price_sheet_electrical_components')->select('depth')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->depth)) {
            return $masterData[0]->depth;
        }

        return 0;
    }

    public function calculatePriceInItem($val = [], $request, $columnName,$enclosure_count) {      
        $price = 0.00;
        $enclousreItem = json_decode($request->enclousreItem, true);
        if ($request->enclousreItem && !empty($request->enclousreItem) && $request->enclosure == 2 ) {
            //yes
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) 
            {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
            }
        }

        if($request->enclousreItem && !empty($request->enclousreItem) && $request->enclosure == 4)  
        {
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                 //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
            }
        }

        if($request->enclousreItem && !empty($request->enclousreItem) && $request->enclosure == 3)  
        {
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
            }
        }

        if ($request->component == 2 && $val['brand_code'] == 1){ 
            // component 2 =  Economic
            if($this->getMasterSheetPriceData(2, $val['function_code'], $val['range'])){
            $price = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']) * $val[$columnName];
             //Qty * price // 2 parameter is equal to brand code
            }
            else {
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            }
        } else if ($request->enclosure == 3 && $val['brand_code'] == 5 && $val['function_code'] == 1) { 
            //3 equal GRP
            if ($this->getMasterSheetPriceData(31, 63, $val['range'])) {
                $price = $this->getMasterSheetPriceData(31, 63, $val['range']) * $val[$columnName]; //Qty * price
                }
                else {
                $price = 0.00;
            }
        } 

        else if($request->enclosure == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1) {
             //4 equal Stainless
             if ($this->getMasterSheetPriceData(5, 64, $val['range'])){
                 $price = $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName];
                 //Qty * price
                }
                else{
                $price = 0.00;
            }
            }
        else if($request->enclosure == 2  && $val['brand_code'] == 8 && $request->stater_type == 1 ) {
            //2 equal METAL; stater_type =1 = XTREME
            if ($this->getMasterSheetPriceData(32, $val['function_code'], $val['range'])) {
                        $price = $this->getMasterSheetPriceData(32, $val['function_code'], $val['range']) * $val[$columnName];
                         //Qty * price
                    }
            else {
                $price = 0.00;
            }
        } else {
            $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
             //Qty * price
        }
        return $price;
    }

    public function ajaxOptionalModal(Request $request) {
        $electricalLists = DB::table('main_electrical_list')->get();
        $electricalListsData = [];
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
        // array:5 [
            //   "id" => 186352
            //   "range" => 3
            //   "starter_code" => "BECC"
            //   "voltage_id" => 3
            //   "stater_type_id" => 6
            // ]
        foreach ($electricalLists as $electricalList) {
                //dump($electricalList);
            if (($electricalList->code >= 1 && $electricalList->code <= 13 && $rangeAndCode['starter_code'] != 'Xtreme' ) || 
                ($electricalList->code >= 16 && $electricalList->code <= 18 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code >= 37 && $electricalList->code <= 40 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                (($electricalList->code == 46 || $electricalList->code == 48 || $electricalList->code == 50) && $rangeAndCode['starter_code'] != 'Xtreme' && $rangeAndCode['voltage_id'] != 1 ) || 
                ($electricalList->code == 51 && $rangeAndCode['starter_code'] != 'Xtreme' )) 
            {
                //here
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code == 31 || $electricalList->code == 32 || $electricalList->code == 33 || $electricalList->code == 45 || $electricalList->code == 47 || $electricalList->code == 49) && $rangeAndCode['voltage_id'] == 1 && $rangeAndCode['stater_type_id'] == 2) { //voltage_id = 230 V, Only starter constant speed Dol
                //no
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code >= 14 && $electricalList->code <= 15 && $rangeAndCode['range'] == 3) || ($electricalList->code == 29 && $rangeAndCode['range'] == 3) || ($electricalList->code >= 41 && $electricalList->code <= 43 && $rangeAndCode['range'] == 3) || ($electricalList->code == 52 && $rangeAndCode['range'] == 3)) { // 3 = Premium
                //here
                $electricalListsData[] = $electricalList;
            }

            // Code no – 19,20 – Basic version - 01 Basic version\04 Single pump configuration Multi VFD and Multi VFD+bypass

            if ($electricalList->code >= 19 && $electricalList->code <= 20 && ($rangeAndCode['starter_code'] == 'VFD' || $rangeAndCode['starter_code'] == 'VFD+Bypass')) {
                //no
                $electricalListsData[] = $electricalList;
            }

            if ($electricalList->code >= 21 && $electricalList->code <= 24 && $rangeAndCode['starter_code'] == 'Xtreme') {
                //no
                $electricalListsData[] = $electricalList;
            }

            //if(($electricalList->code >= 27 && $electricalList->code <= 28 && $rangeAndCode['starter_code'] != 'Xtreme') || 
            // ($electricalList->code == 30 || ($electricalList->code == 34 && $electricalList->code <= 36) &&
            // $rangeAndCode['starter_code'] != 'Xtreme')) {

            if (($electricalList->code >= 27 && $electricalList->code <= 28 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code == 30 &&
                $rangeAndCode['starter_code'] != 'Xtreme')) {
                $electricalListsData[] = $electricalList;
            }
        }
        $data = view('frontend.controlpanel.modal_optional')->with('electricalListsData', $electricalListsData)->render();
        return response()->json(array('success' => true, 'data' => $data));
    }
    
    public function ajaxOptionalSelectedAdderData(Request $request){
        $noOfPump = $request->no_of_pump;
        $motorPower = $request->power_rating;
        $voltage = $request->voltage;
        $ids = explode(",", $request->adder_ids);

        //Code ids
        $component = $request->component;
        $price = 0.00;
        $encloureArea = 0.00;
        if ($ids){
            foreach($ids as $id){
                switch($id){
                    case($id >= 1 && $id <= 26): //electrical_common_adder code
                        $electricalCommonAdders = DB::table('electrical_common_adder')->select('id', 'item_description','material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $id)->whereNotNull($id)->where($id, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if($arrayResult){
                            foreach ($arrayResult as $key => $val){
                                if ($component == 2 && $val['brand_code'] == 1){
                                $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                if($exist)
                                {
                                    $val['brand_code'] = 2;
                                }
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                }
                               else{
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]

                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                    
                                }
                            }
                        }
                        break;
                    

                    case($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code
                    $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'a';
                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                                            ->whereNotNull($column)
                                                            ->where($column, '!=', 0.00)
                                                            ->get();


                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                }
                               else
                               {
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                               }
                            }
                        }
                        break;
                    

                    case($id >= 37 && $id <= 44):  //electrical_adder_per_pump code

                        $column = $id . 'x1';
                        
                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                            // echo $val[$column] * $noOfPump;
                                if($component == 2 && $val['brand_code'] == "1")
                                {
                                $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                if($exist)
                                {
                                    $val['brand_code'] = 2;
                                }
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]* $noOfPump;
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                }
                                else
                                {
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                }
                            }
                        }
                        break;
                    case($id >= 45 && $id <= 52): 
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage,$noOfPump);
                        if ($id >= 45 && $id <= 52) {
                            $column = $id . 'x' . $nearestColumn . 'ax1';
                        } else {
                            $column = $id . 'x' . $nearestColumn . 'ax2';
                        }
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($id >= 45 && $id <= 52) {
                                    if ($request->component == 2 && $val['brand_code'] == "1")
                                    {
                                        //$val['brand_code'] = "2";
                                        $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                        if($exist)
                                        {
                                            $val['brand_code'] = 2;
                                        }
                                    }
                                    // echo $val['function_code'];
                                    // echo "<br>";
                                    // if($request->enclosure == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1)
                                    // {
                                    //     $val['brand_code'] = "5";
                                    //     $val['function_code'] ="64";
                                    // }
                                    // echo $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1;
                                    // echo "<br>";
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                    
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                } else {
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                   
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                }
                            }
                        }
                        break;
                    default: //default
                        echo "within no code";
                        break;
                }
            }
        }
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
        if($rangeAndCode['starter_code'] == 'Xtreme'){
            return ['code_price' => $price, 'starter_code' => 'xtreme'];
        }else{
            $enclousreItem = $this->getControlPanelItemEnclousreAreaFormula($request->table_name, $request->column_name, $encloureArea);
            if($enclousreItem){
                return ['code_price' => $price, 'enclousreItem' => $enclousreItem, 'starter_code' => 'other'];
            }else{
                return ['enclousreItem' => $enclousreItem];
            }
        }
    }

    public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
        $noOfPump = $this->getValueById('App\NumberOfPump', 'id', $noOfPump);
        $voltage = $this->getValueById('App\Voltage', 'id', $voltage);
        $motorPower = $this->getValueById('App\Power', 'id', $motorPower);
        if($code >= 45 && $code <= 52)
        {
            $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8);    
        }
        else
        {
            $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8) * $noOfPump;
        }
        return AdderHelper::getClosestAmpere($code, $ampere);
    }

    public function getControlPanelRangeAndCode($request){
        $returnRangeAndCode = [];
        $controlPanelData = ControlPanel::where('id', $request->cp_id)->get();
        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code,
            'voltage_id' => $controlPanelData[0]->voltage_id,
            'stater_type_id' => $controlPanelData[0]->stater_type_id
        );
    }

    /**
     * 
     *
     *
     * @param Table Name
     * @param $columnName
     * @return If enclousre is exist calcualte encloure formula
    */

    public function getControlPanelItemEnclousreAreaFormula($tableName, $columnName, $totalEnclousreArea) {
        $enclousreItem = null;
        $nextSize = true;
        if (Schema::hasTable($tableName)) {
            if (Schema::hasColumn($tableName, $columnName)) {
                $cpRecords = DB::table($tableName)->select('id', 'item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                        ->whereNotNull($columnName)->where($columnName, '!=', 0)
                        ->where('item_description', 'like', '%Enclosure%')
                        ->get();
                $arrayResult = json_decode(json_encode($cpRecords), true);
                if ($arrayResult) { 
                    $i = 1;
                    foreach ($arrayResult as $key => $val) {
                        $range = $val['range'];
                        $qty = $val[$columnName];
                        $sizeMeet = AdderHelper::enclosureAreaExist($qty,$range, $totalEnclousreArea);
                        if ($sizeMeet) {
                            if ($i == 1) {
                                $enclousreItem = $val;
                            }
                            $i++;
                        }
                    }
                }
                if (!$enclousreItem) {
                    $cpRecords = DB::table($tableName)->select('id', 'item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                            ->where('item_description', 'like', '%Enclosure%')
                            ->get();

                    $arrayResult = json_decode(json_encode($cpRecords), true);
                    if ($arrayResult) {
                        $i = 1;
                        foreach ($arrayResult as $key => $val) {
                            $range = $val['range'];
                            $qty = $val[$columnName];
                            $sizeMeet = AdderHelper::enclosureAreaExist($qty,$range, $totalEnclousreArea);
                            if ($sizeMeet) {
                                if ($i == 1) {
                                    $enclousreItem = $val;
                                }
                                $i++;
                            }
                        }
                    }
                }
                return $enclousreItem;
            }
        }
    }
    
    //where('user_id', auth()->user()->id)->
    public function searchByArticleNumber(Request $request) {

        $controlPanelCartData = ControlPanelCart::where('full_article_number', $request->article_number)->first();


        //Booster electrical article number either manual or search code starts..!!
        if($controlPanelCartData == null && empty($controlPanelCartData)){
            $controlPanelCartData=BoosterCart::where('electrical_article_number',$request->article_number)->first();
        }
        //Booster electrical article number either manual or search code ends..!!

        if($controlPanelCartData){
            $controlPanelData = ControlPanel::where('id', $controlPanelCartData->control_panel_id)
                    ->with('noofpumps')
                    ->with('powers')
                    ->with('voltages')
                    ->with('applications')
                    ->with('ambienttemps')
                    ->with('startertypes')
                    ->with('startertypes')
                    ->with('ranges')
                    ->with('enclousres')
                    ->with('comunicationprotocols')
                    ->with('ipratings')
                    ->get();

            //Booster electrical article number either manual or search code starts..!!
            if(count($controlPanelData) == 0){
                $controlPanelData = ControlPanel::where('id', $controlPanelCartData->cp_id)
                    ->with('noofpumps')
                    ->with('powers')
                    ->with('voltages')
                    ->with('applications')
                    ->with('ambienttemps')
                    ->with('startertypes')
                    ->with('startertypes')
                    ->with('ranges')
                    ->with('enclousres')
                    ->with('comunicationprotocols')
                    ->with('ipratings')
                    ->get();
            }
            //Booster electrical article number either manual or search code ends..!!
            
            if ($controlPanelData) {
                $powers = [];
                $voltages = [];
                $applications = [];
                $ambienttemps = [];
                $startertypes = [];
                $components = [];
                $enclousres = [];
                $comunicationprotocols = [];
                $ipratings = [];
                $ranges = [];

                foreach ($controlPanelData as $row) {
                    $powers[] = array('id' => $row->powers->id, 'value' => $row->powers->value);
                    $voltages[] = array('id' => $row->voltages->id, 'value' => $row->voltages->value);
                    $applications[] = array('id' => $row->applications->id, 'value' => $row->applications->value);
                    $ambienttemps[] = array('id' => $row->ambienttemps->id, 'value' => $row->ambienttemps->value);
                    $startertypes[] = array('id' => $row->startertypes->id, 'value' => $row->startertypes->value);
                    $components[] = array('id' => $row->components->id, 'value' => $row->components->value);
                    $enclousres[] = array('id' => $row->enclousres->id, 'value' => $row->enclousres->value);
                    $comunicationprotocols[] = array('id' => $row->comunicationprotocols->id, 'value' => $row->comunicationprotocols->value);
                    $ipratings[] = array('id' => $row->ipratings->id, 'value' => $row->ipratings->value);
                    $ranges[] = array('id' => $row->ranges->id, 'value' => $row->ranges->value);
                }
                $powers = array_unique($powers, SORT_REGULAR);
                $voltages = array_unique($voltages, SORT_REGULAR);
                $applications = array_unique($applications, SORT_REGULAR);
                $ambienttemps = array_unique($ambienttemps, SORT_REGULAR);
                $startertypes = array_unique($startertypes, SORT_REGULAR);
                $components = array_unique($components, SORT_REGULAR);
                $enclousres = array_unique($enclousres, SORT_REGULAR);
                $comunicationprotocols = array_unique($comunicationprotocols, SORT_REGULAR);
                $ipratings = array_unique($ipratings, SORT_REGULAR);
                $ranges = array_unique($ranges, SORT_REGULAR);

                $data = [];
                $data['controlPanel'] = $controlPanelData;
                $data['powers'] = array_values($powers);
                $data['voltages'] = array_values($voltages);
                $data['applications'] = array_values($applications);
                $data['ambienttemps'] = array_values($ambienttemps);
                $data['startertypes'] = array_values($startertypes);
                $data['components'] = array_values($components);
                $data['enclousres'] = array_values($enclousres);
                $data['comunicationprotocols'] = array_values($comunicationprotocols);
                $data['ipratings'] = array_values($ipratings);
                $data['ranges'] = array_values($ranges);
                if (isset($data['controlPanel'][0]) && !empty($data['controlPanel'][0])) {
                    $idberOfPump = $data['controlPanel'][0]->noofpumps['value'];
                    if (ControlPanel::isIntegerColumn($data['controlPanel'][0]->powers['value'])) { // Integer Column
                        $power = $data['controlPanel'][0]->powers['value'];
                        $voltage = $data['controlPanel'][0]->voltages['value'];
                        $columnName = $idberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
                    } else { //Float column
                        $power = str_replace(".", '__', $data['controlPanel'][0]->powers['value']);
                        $voltage = $data['controlPanel'][0]->voltages['value'];
                        $columnName = $idberOfPump . 'x' . $power . "kwx" . $voltage . 'v';
                    }

                    $startertypes = $data['controlPanel'][0]->startertypes['value'];
                    $starterCode = $data['controlPanel'][0]->starter_code;
                    $range = $data['controlPanel'][0]->ranges['value'];
                    $tableName = $data['controlPanel'][0]->table_name;
                    $component = $data['components'][0]['id'];

                    $cpRecordsData = [];
                    $price = 0.00;
                    $codePrice = 0.00;
                    $cpRecords = DB::table($tableName)->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                                    ->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
                    $cpRecords1 = DB::table($tableName)
                                ->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                                ->whereNotNull($columnName)
                                ->where($columnName, '!=', 0)
                                ->where('function_code','=','1')
                                ->count();

                    $arrayResult = json_decode(json_encode($cpRecords), true);
                    $enclousreAdderItemData = null;
                    if ($controlPanelCartData->adder_ids && $controlPanelCartData->adder_ids != '') {
                        
                        //Booster electrical article number either manual or search code starts..!!
                        if($controlPanelCartData->control_panel_id == null)
                        {
                            $controlPanelCartData->control_panel_id = $controlPanelCartData->cp_id;
                        }
                        //Booster electrical article number either manual or search code ends..!!

                        $addersData = $this->calculateAddersSearchByArticle($controlPanelCartData->control_panel_id, $controlPanelCartData->adder_ids, $idberOfPump, $data['controlPanel'][0]->powers['value'], $voltage, $tableName, $columnName,$component); //Code ids
                        $enclousreAdderItemData = $addersData['enclousreItem'];
                        if ($addersData['code_price'] && $addersData['code_price'] != '') {
                            $codePrice = $addersData['code_price'];
                        }
                    }
                    foreach ($arrayResult as $key => $val) {
                        $price += $this->searchByArticleCalculatePriceInItem($val, $data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName,$cpRecords1);
                    }
                    if ($codePrice > 0.00) {
                        $price = $price + $codePrice;
                    }
                    
                    $price = ($price * ControlPanel::controlpanel_over_head()) / User::ic_margin_control_panel();
                    $data['cp_price'] = number_format($price, 2);
                    $key = 0;
                    $tax = 0;

                    $cpRecordsData[$key + 1]['price'] = number_format($price, 2);
                    $cpRecordsData[$key + 1]['range_id'] = $range;
                    $cpRecordsData[$key + 1]['tax'] = $tax;
                    $controlPanelId = $data['controlPanel'][0]->id;
                    $starter = $data['controlPanel'][0]->startertypes['value'];
                    $application = $data['controlPanel'][0]->applications['value'];
                    $noOfPump = $data['controlPanel'][0]->noofpumps['value'];
                    $power = $data['controlPanel'][0]->powers['value'];
                    $returnHTML = view('frontend.controlpanel.table')->with('cpRecordsData', $controlPanelId)
                            ->with('tax', $tax)
                            ->with('price', $price)
                            ->with('starter', $starter)
                            ->with('application', $application)
                            ->with('noOfPump', $noOfPump)
                            ->with('power', $power)
                            ->with('starterCode', $starterCode)
                            ->render();

                    $data['cp_records_html'] = $returnHTML;
                    $data['cp_id'] = $data['controlPanel'][0]->id;
                    $data['table_name'] = $tableName;
                    $data['column_name'] = $columnName;
                    $data['total_price'] = $price;
                    $data['code_price'] = $codePrice;
                    $data['adder_ids'] = $controlPanelCartData->adder_ids;
                    $data['no_of_pump'] = $data['controlPanel'][0]->noofpumps['id'];
                    $data['power_rating'] = $data['controlPanel'][0]->powers['id'];
                    $data['voltage'] = $data['controlPanel'][0]->voltages['id'];

                    if ($enclousreAdderItemData) {
                        $data['enclousreItem'] = $enclousreAdderItemData;
                    }
                    return response()->json(array('success' => true, 'data' => $data));
                } else {
                    $data['cp_records_html'] = 'No Record Found!';
                    $data['cp_price'] = 0.00;
                    return response()->json(array('success' => true, 'data' => $data));
                }
            } else {
                $data['cp_records_html'] = 'No Record Found!';
                $data['cp_price'] = 0.00;
                return response()->json(array('success' => true, 'data' => $data));
            }
        } else {
            $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';

            return response()->json(array('success' => true, 'data' => $data));
        }
    }

    public function serachByArticleNoGetControlPanelRangeAndCode($controlPanelId) {
        $returnRangeAndCode = [];
        $controlPanelData = ControlPanel::where('id', $controlPanelId)->get();

        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code,
            'voltage_id' => $controlPanelData[0]->voltage_id,
            'stater_type_id' => $controlPanelData[0]->stater_type_id
        );
    }

    public function calculateAddersSearchByArticle($control_panel_id, $ids, $noOfPump, $motorPower, $voltage, $table_name, $columnName,$component){       
        $noOfPump = $noOfPump;
        $motorPower = $motorPower;
        $voltage = $voltage;
        $ids = explode(",", $ids); //Code ids
        $price = 0.00;
        $component = $component;
        $encloureArea = 0.00;
        if ($ids) {
            foreach ($ids as $id) {
                switch ($id) {
                    case ($id >= 1 && $id <= 26): //electrical_common_adder code
                        //id = $column
                        $electricalCommonAdders = DB::table('electrical_common_adder')->select('id', 'item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $id)
                                        ->whereNotNull($id)->where($id, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if($component == 2 && $val['brand_code'] == 1){
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                // echo $val['brand_code'] . "<br>";
                                $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                            }
                        }

                        break;
                    case ($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code

                        $nearestColumn = $this->searchByArticleCommonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);

                        $column = $id . 'x' . $nearestColumn . 'a';

                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);

                        if ($arrayResult){
                            foreach ($arrayResult as $key => $val) {
                            if($component == 2 && $val['brand_code'] == 1){
                                $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                if($exist)
                                {
                                    $val['brand_code'] = 2;
                                }
                            }
                                $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                            }
                        }
                        break;
                    case ($id >= 37 && $id <= 44):  //electrical_adder_per_pump code
                        $column = $id . 'x1';
                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);
                        
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if($component == 2 && $val['brand_code'] == 1){
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                    //echo $val[$column] * $noOfPump;
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                }
                                }
                                break;
                            case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code

                                $nearestColumn = $this->searchByArticleCommonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                                if ($id >= 45 && $id <= 52) {
                                    $column = $id . 'x' . $nearestColumn . 'ax1';
                        //  echo $column ."</br>";
                        } else {
                            $column = $id . 'x' . $nearestColumn . 'ax2';
                        }
                        
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0.00)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
                        
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($id >= 45 && $id <= 52) {
                                    if($component == 2 && $val['brand_code'] == 1){
                                        $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                        if($exist)
                                        {
                                            $val['brand_code'] = 2;
                                        }
                                    }
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                } else {
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                }
                            }
                        }
                        break;
                    default: //default
                    echo "within no code";
                    break;
                }
            }
        }

        $rangeAndCode = $this->serachByArticleNoGetControlPanelRangeAndCode($control_panel_id);

        if ($rangeAndCode['starter_code'] == 'Xtreme') {
            return ['code_price' => $price, 'starter_code' => 'xtreme'];
        } else {
            $enclousreItem = $this->getControlPanelItemEnclousreAreaFormula($table_name, $columnName, $encloureArea);
            if ($enclousreItem) {
                return ['code_price' => $price, 'enclousreItem' => $enclousreItem, 'starter_code' => 'other'];
            } else {
                return ['enclousreItem' => $enclousreItem];
            }
        }
    }

    public function searchByArticleCommonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump)
    {
        if($code >= 45 && $code <= 52)
        {
            $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8);    
        }
        else
        {
            $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8) * $noOfPump;
        }
        return AdderHelper::getClosestAmpere($code, $ampere);
    }

    public function searchByArticleCalculatePriceInItem($val = [], $component, $enclousre, $starterType, $enclousreItem, $columnName,$enclosure_count) {
        $price = 0.00;
                if($enclousreItem && !empty($enclousreItem) && $enclousre == 2 ) {
                //    $enclousreItem = json_decode($enclousreItem, true);
                    if($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {

                    //$val['range'] = $enclousreItem['range'];
                    // echo "**".$val['range']."***";
                        if($enclosure_count == "1")
                        {
                            $val['range'] = $enclousreItem['range'];
                        }
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                    }
                }

                if($enclousreItem && !empty($enclousreItem) && $enclousre == 4)  
                {
                    // $enclousreItem = json_decode($enclousreItem, true);
                    if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                        //$val['range'] = $enclousreItem['range'];
                        if($enclosure_count == "1")
                        {
                            $val['range'] = $enclousreItem['range'];
                        }
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                         //Qty * price // 2 parameter is equal to brand code
                        $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    }
                }
                
                if($enclousreItem && !empty($enclousreItem) && $enclousre == 3)  
                {
                    // $enclousreItem = json_decode($enclousreItem, true);
                    if($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']){
                        //$val['range'] = $enclousreItem['range'];
                        if($enclosure_count == "1")
                        {
                            $val['range'] = $enclousreItem['range'];
                        }
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                        $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    }
                }

                if ($component == 2 && $val['brand_code'] == 1) { // component 2 =  Economic
                    if ($this->getMasterSheetPriceData(2, $val['function_code'], $val['range'])) {
                        $price = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                        // echo "change" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(2,  $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
                    }
                        //
                    else {
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                        // echo "If condition fail choose brand code 1 **" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
            }
        } else if ($enclousre == 3 && $val['brand_code'] == 5 && $val['function_code'] == 1) { //3 equal GRP
            if ($this->getMasterSheetPriceData(31, 63, $val['range'])) {
                $price = $this->getMasterSheetPriceData(31, 63, $val['range']) * $val[$columnName]; //Qty * price
                    // echo "**".$price;
                    // echo "Encloure  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(31, 63, $val['range']) * $val[$columnName] . "</br>";
                    }
                    //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else if ($enclousre == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1) { //4 equal Stainless
            if ($this->getMasterSheetPriceData(5, 64, $val['range'])) {
                // echo $val['range'];
                $price = $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName]; //Qty * price
                // echo "stainless  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName] . "</br>";
                    }
                //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        }
        //SLIDE 11
        else if ($enclousre == 2 && $starterType == 1 && $val['brand_code'] == 8) {
            //2 equal META; 1 XTREME
            if ($this->getMasterSheetPriceData(32, $val['function_code'], $val['range'])) {
            // echo $val['range'];
                $price = $this->getMasterSheetPriceData(32, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            //  echo "stainless  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName] . "</br>";
                    }
                //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else {
            $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            // echo "Normal  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
        }
        return $price;
    } 
}
