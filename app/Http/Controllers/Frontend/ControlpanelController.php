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
use App\WarehousePumpDetails; // A Code: 13-04-2026
use App\Models\BoosterCart;
use App\Models\ControlPanelsMaster; // A Code: 27-03-2026

class ControlpanelController extends Controller{

    use ControlPanelModelIdGet;

    public function index() {
        $numberOfPumps = NumberOfPump::select('id', 'value')->get();
        $electricalLists = DB::table('main_electrical_list')->get();
        return view('frontend.controlpanel.index', compact('numberOfPumps', 'electricalLists'));
    }

    // short code change 1 comment this ajaxFilter starts
    // short code change 1 comment this ajaxFilter ends

    // short code change 2 add this ajaxFilter starts
    // short code change 2 add this ajaxFilter ends

    // A Code: 27-03-2026 Start
    public function ajaxFilter(Request $request)
    {
        $controlPanelData = ControlPanelsMaster::query();

        // Filters (CSV safe with space fix)
        if ($request->no_of_pump) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(no_of_pumps, ' ', ''))", [$request->no_of_pump]);
        }

        if ($request->power_rating) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(power_rating, ' ', ''))", [$request->power_rating]);
        }

        if ($request->voltage) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(power_supply, ' ', ''))", [$request->voltage]);
        }

        if ($request->application) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(applications, ' ', ''))", [$request->application]);
        }

        if ($request->ambient_temp) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(min_of_ambient_temp, ' ', ''))", [$request->ambient_temp]);
        }

        if ($request->stater_type) {
            $controlPanelData->where('starter_type', $request->stater_type);
        }

        if ($request->communication_protocol) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(communication_protocol, ' ', ''))", [$request->communication_protocol]);
        }

        if ($request->ip_rating) {
            $controlPanelData->where('ip_rating', $request->ip_rating);
        }

        if ($request->component) {
            $controlPanelData->whereRaw("FIND_IN_SET(?, REPLACE(components, ' ', ''))", [$request->component]);
        }

        // A Code: 02-04-2026 Start (enclosure CSV fix)
        if ($request->enclosure) {
            // Remove extra spaces
            $enclosures = array_map('trim', explode(',', $request->enclosure));

            // A Code: 06-04-2026 Start
            $controlPanelData->where(function ($query) use ($enclosures) {
                foreach ($enclosures as $enclosure) {
                    $query->orWhereRaw(
                        "FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(enclosure, ' ', ''))",
                        [$enclosure]
                    );
                }
            });   
            // A Code: 06-04-2026 End   
        }
        // A Code: 02-04-2026 End (enclosure CSV fix)

        if ($request->range) {
            $controlPanelData->where('range', $request->range);
        }

        $controlPanelData = $controlPanelData->get();

        // A Code: 27-03-2026 (Helper function for CSV)
        $getFirstCSV = function($value){
            return explode(',', $value)[0] ?? '';
        };
        // A Code: 27-03-2026 End

        // Build dropdown arrays
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

            foreach(explode(',', $row->power_rating) as $val){
                $powers[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            foreach(explode(',', $row->power_supply) as $val){
                $voltages[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            foreach(explode(',', $row->applications) as $val){
                $applications[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            foreach(explode(',', $row->min_of_ambient_temp) as $val){
                $ambienttemps[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            $startertypes[] = ['id'=>$row->starter_type,'value'=>$row->starter_type];

            foreach(explode(',', $row->communication_protocol) as $val){
                $comunicationprotocols[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            foreach(explode(',', $row->components) as $val){
                $components[] = ['id'=>trim($val),'value'=>trim($val)];
            }

            $ipratings[] = ['id'=>$row->ip_rating,'value'=>$row->ip_rating];
            //$enclousres[] = ['id'=>$row->enclosure,'value'=>$row->enclosure];

            // A Code: 02-04-2026 Start (enclosure CSV fix)
            foreach(explode(',', $row->enclosure) as $val){
                $enclousres[] = ['id'=>trim($val),'value'=>trim($val)];
            }
            // A Code: 02-04-2026 End (enclosure CSV fix)

            $ranges[] = ['id'=>$row->range,'value'=>$row->range];
        }  

        $data = [];
        $data['controlPanel'] = $controlPanelData;
        $data['powers'] = array_values(array_unique($powers,SORT_REGULAR));
        $data['voltages'] = array_values(array_unique($voltages,SORT_REGULAR));
        $data['applications'] = array_values(array_unique($applications,SORT_REGULAR));
        $data['ambienttemps'] = array_values(array_unique($ambienttemps,SORT_REGULAR));
        $data['startertypes'] = array_values(array_unique($startertypes,SORT_REGULAR));
        $data['components'] = array_values(array_unique($components,SORT_REGULAR));
        $data['enclousres'] = array_values(array_unique($enclousres,SORT_REGULAR));
        $data['comunicationprotocols'] = array_values(array_unique($comunicationprotocols,SORT_REGULAR));
        $data['ipratings'] = array_values(array_unique($ipratings,SORT_REGULAR));
        $data['ranges'] = array_values(array_unique($ranges,SORT_REGULAR));

        // FINAL CALCULATION
        if($request->enclosure && isset($controlPanelData[0])){

            $panel = $controlPanelData[0];

            // A Code: 27-03-2026 Start (space and dot issue in column name)         
         
            $idberOfPump = trim($request->no_of_pump);
            $power = trim($request->power_rating);
            $voltage = trim($request->voltage);

            // Remove ALL spaces
            $idberOfPump = str_replace(' ', '', $idberOfPump);
            $power = str_replace(' ', '', $power);
            $voltage = str_replace(' ', '', $voltage);

            // Convert power correctly
            $power = (float)$power;

            // Format as per DB structure
            if (floor($power) == $power) {
                $powerFormatted = intval($power) . '__0';
            } else {
                $powerFormatted = str_replace('.', '__', $power);
            }

            // Final column name (NO spaces anywhere)
            $columnName = $idberOfPump . 'x' . $powerFormatted . 'kwx' . $voltage . 'v';
            
            // $startertypes = $panel->startertypes['value'];
            // $starterCode = $panel->starter_code;
            // $range = $panel->ranges['value'];

            // A Code: 23-05-2026 Start
            $startertypes = $panel->startertypes['value'] ?? '';
            $starterCode = $panel->starter_code ?? '';
            $range = $panel->ranges['value'] ?? '';
            // A Code: 23-05-2026 End

            $cpRecordsData = [];
            $returnHTML = '';
            //$price = 0.00;

            // A Code: 27-03-2026 End

            $tableName = $panel->table_name;
            $price = 0;

            // A Code: 27-03-2026 Start
            if(Schema::hasTable($tableName)){
                if(Schema::hasColumn($tableName, $columnName)){
                    $cpRecords = DB::table($tableName)
                                ->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                                ->whereNotNull($columnName)->where($columnName, '!=', 0)
                                ->get();

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
                        $enclourse_exist = $this->calculatePriceInItem($request, $columnName,$cpRecords1,$arrayResult[0]);
                        
                        if($enclourse_exist == 0.0)
                        {
                            $data['enclourse_exist'] = null;
                            $price = 0.0;
                        }
                        else
                        {
                            foreach($arrayResult as $key => $val) {
                                
                                // $price += $this->calculatePriceInItem($val, $request, $columnName,$cpRecords1);  13-12-2024 price issue
                                $unitPrice = $this->calculatePriceInItem($request, $columnName, $cpRecords1,$val);
                                $price += $unitPrice;                               
                            }                                                

                            $price1 = $price;
                                
                            $data['control_panel_price_for_booster'] = $price1;
                            if($request->code_price && $request->code_price != '') {
                                $price = $price + $request->code_price;
                            }
                            $tax = Tax::where('id', 1)->get()[0]->amount;
                            $data['cp_price_booster'] = number_format($price, 2);
                            // A Code: 10-04-2026 (Removed Overhead)
                            $price = ($price * ControlPanelsMaster::controlpanel_over_head()) / User::ic_margin_control_panel();
                                                        
                            $data['cp_price'] = number_format($price, 2);
                            $cpRecordsData[$key + 1]['price'] = number_format($price, 2);
                            $cpRecordsData[$key + 1]['range_id'] = $range;
                            $cpRecordsData[$key + 1]['tax'] = $tax;
                            $controlPanelId = $data['controlPanel'][0]->id;

                            // A Code: 27-03-2026 Start (CSV access fix)                        
                            
                            $controlPanel = $data['controlPanel'][0];
                            $starter = $controlPanel->starter_type ?? '';

                            //$application = $getFirstCSV($controlPanel->applications); // "Booster, Chiller, Circulation, Lifting"                            
                            //$noOfPump = $getFirstCSV($controlPanel->no_of_pumps); // "2,3,4,5,6,7,8"
                            //$power = $getFirstCSV($controlPanel->power_rating); // "0.37,0.55,0.75,1.10,1.50,2.20,3.00"  

                            // A Commented Above Code due to coming Selected data in comma separated and Used $request Data instead

                            $application = $getFirstCSV($request->application); // "Circulation"                          
                            $noOfPump = $getFirstCSV($request->no_of_pump); // "3"
                            $power = $getFirstCSV($request->power_rating); // "0.75"
                            // A Code: 27-03-2026 End

                            $returnHTML = view('frontend.controlpanel.table')
                                    ->with('cpRecordsData', $controlPanelId)
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
            // A Code: 27-03-2026 End

        }

        return response()->json(['success'=>true,'data'=>$data]);
    }
    // A Code: 27-03-2026 End

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
            //$cp_id = ControlPanel::where('id','=',$BoosterCartData->cp_id)->first();
            $cp_id = ControlPanelsMaster::where('id','=',$BoosterCartData->cp_id)->first();

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
                        if (ControlPanelsMaster::isIntegerColumn($data['controlPanel'][0]->powers['value'])) { // Integer Column
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
                            $s = trim($cp_width, 'H x ');
                        } else{
                            $cp_height = 0;
                            $cp_width = 0;
                        }
                        // 
                        //here search ajax calculate
                        $enclourse_exist = $this->searchByArticleCalculatePriceInItem($data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName,$cpRecords1,$arrayResult[0]);
                        if($enclourse_exist == 0.0)
                        {
                            $data['enclourse_exist'] = null;
                            $price = 0.0;
                        }
                        else
                        {
                            foreach($arrayResult as $key => $val) {
                                $price += $this->searchByArticleCalculatePriceInItem($data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName,$cpRecords1,$val);
                            }
                            $price1 = $price;
                            $data['control_panel_price_for_booster'] = $price1;
                            if($request->code_price && $request->code_price != '') {
                                $price = $price + $request->code_price;
                            }
                            $tax = Tax::where('id', 1)->get()[0]->amount;
                            $data['cp_price_booster'] = number_format($price, 2);
                            $price = ($price * ControlPanelsMaster::controlpanel_over_head()) / User::ic_margin_control_panel();

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

    // A Code: 17-04-2026 Start
    public function calculatePriceInItem($request, $columnName, $enclosure_count, $val = [])
    {
        $qty = (int)($val[$columnName] ?? 0);

        if ($qty <= 0) {
            return 0.00;
        }

        // ====================== NORMALIZATION ======================
        $enclosure = strtolower(trim($request->enclosure ?? ''));
        $component = strtolower(trim($request->component ?? ''));
        $starter   = strtolower(trim($request->stater_type ?? ''));

        $enclousreItem = !empty($request->enclousreItem)
            ? json_decode($request->enclousreItem, true)
            : [];

        $brand    = (int)$val['brand_code'];
        $function = (int)$val['function_code'];
        $range    = $val['range'];

        // Component Logic (Economic / Schneider / Lovato)
        $brand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-06-2026

        // ====================== LOVATO / SCHNEIDER ======================
        if (in_array($component, ['lovato', 'schneider'])) {

            // STEP 1: Enclosure Override
            if (!empty($enclousreItem) &&
                in_array($enclosure, ['metal', 'grp', 'stainless steel'])) {

                if (isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                    $brand === (int)$enclousreItem['brand_code'] &&
                    $function === (int)$enclousreItem['function_code']) {

                    if ($enclosure_count == 1) {
                        $range = $enclousreItem['range'] ?? $range;
                    }

                    $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                    return $unitPrice * $qty;
                }
            }

            // STEP 2: Special Logic
            $specialUnitPrice = $this->getSpecialUnitPrice(
                $brand, $function, $range, $enclosure, $component, $starter
            );

            if ($specialUnitPrice !== null) {
                return $specialUnitPrice * $qty;
            }

            // STEP 3: Default
            $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
            return $unitPrice * $qty;
        }

        // ====================== DEFAULT FLOW ======================

        // STEP 1: Enclosure Override
        if (!empty($enclousreItem) &&
            in_array($enclosure, ['metal', 'grp', 'stainless steel'])) {

            if (isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                $brand === (int)$enclousreItem['brand_code'] &&
                $function === (int)$enclousreItem['function_code']) {

                if ($enclosure_count == 1) {
                    $range = $enclousreItem['range'] ?? $range;
                }

                $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                return $unitPrice * $qty;
            }
        }

        // STEP 2: Effective Brand (Economic / Schneider / Lovato)
        // $effectiveBrand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-06-2026 Comment

        // STEP 3: Special Logic
        $specialUnitPrice = $this->getSpecialUnitPrice(
            $brand, $function, $range, $enclosure, $component, $starter
        );

        if ($specialUnitPrice !== null) {
            return $specialUnitPrice * $qty;
        }

        // STEP 4: Default
        //$unitPrice = $this->getMasterSheetPriceData($effectiveBrand, $function, $range); // A Code: 01-06-2026 Comment
        $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range); // A Code: 01-06-2026

        return $unitPrice * $qty;
    }

    private function getEffectiveBrand(int $brand, string $component, int $function, $range): int
    {
        $component = strtolower(trim($component ?? ''));
        if ($component === 'economic' && $brand === 1) {
            return $this->getMasterSheetPriceData(2, $function, $range) ? 2 : $brand;
        }

        if ($component === 'schneider' && $brand === 1) {
            return $this->getMasterSheetPriceData(34, $function, $range) ? 34 : $brand;
        }

        if ($component === 'lovato' && $brand === 1) {
            return $this->getMasterSheetPriceData(35, $function, $range) ? 35 : $brand;
        }

        return $brand;
    }
    // A Code: 02-06-2026 Start
    private function getEffectiveBrandAdders(int $brand, string $component, int $function, $range): int
    {
        $component = strtolower(trim($component ?? ''));
        if ($component === 'economic' || $component === '2' && $brand === 1) {
            return $this->getMasterSheetPriceData(2, $function, $range) ? 2 : $brand;
        }

        if ($component === 'schneider' || $component === '3' && $brand === 1) {
            return $this->getMasterSheetPriceData(34, $function, $range) ? 34 : $brand;
        }

        if ($component === 'lovato' || $component === '4' && $brand === 1) {
            return $this->getMasterSheetPriceData(35, $function, $range) ? 35 : $brand;
        }

        return $brand;
    }
    // A Code: 02-06-2026 End

    private function getSpecialUnitPrice(int $brand, int $function, $range, string $enclosure, string $component, string $starter)
    {
        // GRP
        if ($enclosure === 'grp' && $brand === 5 && $function === 1) {
            return $this->getMasterSheetPriceData(31, 63, $range) ?: null;
        }

        // Stainless Steel
        if ($enclosure === 'stainless steel' && $brand === 5 && $function === 1) {
            return $this->getMasterSheetPriceData(5, 64, $range) ?: null;
        }

        // Metal + Starter
        if ($enclosure === 'metal' && $brand === 8) {
            //if (in_array($starter, ['multi vfd + bypass', 'xtreme'])) {
            if (in_array($starter, ['xtreme'])) {
                return $this->getMasterSheetPriceData(32, $function, $range) ?: null;
            }
        }

        return null;
    }
    // A Code: 17-04-2026 End

    public function ajaxOptionalModal(Request $request) {
        $electricalLists = DB::table('main_electrical_list')->get();
        $electricalListsData = [];
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
        foreach ($electricalLists as $electricalList) {
            if (($electricalList->code >= 1 && $electricalList->code <= 13 && $rangeAndCode['starter_code'] != 'Xtreme' ) || 
                ($electricalList->code >= 16 && $electricalList->code <= 18 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code >= 37 && $electricalList->code <= 40 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                (($electricalList->code == 46 || $electricalList->code == 48 || $electricalList->code == 50) && $rangeAndCode['starter_code'] != 'Xtreme' && $rangeAndCode['voltage_id'] != 1 ) || 
                ($electricalList->code == 51 && $rangeAndCode['starter_code'] != 'Xtreme' )) 
            {
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code == 31 || $electricalList->code == 32 || $electricalList->code == 33 || $electricalList->code == 45 || $electricalList->code == 47 || $electricalList->code == 49) && $rangeAndCode['voltage_id'] == 1 && $rangeAndCode['stater_type_id'] == 2) { //voltage_id = 230 V, Only starter constant speed Dol
                //no
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code >= 14 && $electricalList->code <= 15 && $rangeAndCode['range'] == 3) || ($electricalList->code == 29 && $rangeAndCode['range'] == 3) || ($electricalList->code >= 41 && $electricalList->code <= 43 && $rangeAndCode['range'] == 3) || ($electricalList->code == 52 && $rangeAndCode['range'] == 3)) { // 3 = Premium
                $electricalListsData[] = $electricalList;
            }
            // Code no – 19,20 – Basic version - 01 Basic version\04 Single pump configuration Multi VFD and Multi VFD+bypass
            if ($electricalList->code >= 19 && $electricalList->code <= 20 && ($rangeAndCode['starter_code'] == 'VFD' || $rangeAndCode['starter_code'] == 'VFD+Bypass')) {
                $electricalListsData[] = $electricalList;
            }

            if ($electricalList->code >= 21 && $electricalList->code <= 24 && $rangeAndCode['starter_code'] == 'Xtreme') {
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code >= 27 && $electricalList->code <= 28 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code == 30 &&
                $rangeAndCode['starter_code'] != 'Xtreme')) {
                $electricalListsData[] = $electricalList;
            }
        }
        $data = view('frontend.controlpanel.modal_optional')->with('electricalListsData', $electricalListsData)->render();
        return response()->json(array('success' => true, 'data' => $data));
    }
    
    public function ajaxOptionalSelectedAdderData(Request $request)
    {        
        $noOfPump = $request->no_of_pump;
        $motorPower = $request->power_rating;
        $voltage = $request->voltage;
        $ids = explode(",", $request->adder_ids);

        //Code ids
        //$component = $request->component;
        $component = DB::table('components')->where('value', $request->component)->value('id') ?? 0; // A Code: 17-04-2026
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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrand($val['brand_code'], $request->component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrand($val['brand_code'], $request->component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrand($val['brand_code'], $request->component, $val['function_code'], $val['range']); // A Code: 02-06-2026
                                                              
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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrand($val['brand_code'], $request->component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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
        //dd($price);
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

    // A Code: 10-04-2026 Start
    public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
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
    // A Code: 10-04-2026 End

    // A Code: 10-04-2026 Start
    public function getControlPanelRangeAndCode($request){
        $returnRangeAndCode = [];
        //$controlPanelData = ControlPanel::where('id', $request->cp_id)->get();
        $controlPanelData = ControlPanelsMaster::where('id', $request->cp_id)->get();        
        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code,
            'voltage_id' => $controlPanelData[0]->voltage_id,
            'stater_type_id' => $controlPanelData[0]->stater_type_id
        );
    }
    // A Code: 10-04-2026 End

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
    // A Code: 01-04-2026 Start
    public function searchByArticleNumber(Request $request)
    {
        $articleNumber = $request->article_number;

        // =========================
        // STEP 1: Find Cart Data
        // =========================
        $controlPanelCartData = ControlPanelCart::where('full_article_number', $articleNumber)->first();

        if (!$controlPanelCartData) {
            $controlPanelCartData = BoosterCart::where('electrical_article_number', $articleNumber)->first();

            if ($controlPanelCartData) {
                $controlPanelCartData->control_panel_id = $controlPanelCartData->cp_id;
            }
        }        

        if (!$controlPanelCartData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html_error' => 'This article number does not exist.'
                ]
            ]);
        }
                
        // =========================
        // STEP 2: Resolve Values
        // =========================
        $noOfPump   = DB::table('number_of_pumps')->where('id', $controlPanelCartData->no_of_pump_id)->value('value');
        $power      = DB::table('powers')->where('id', $controlPanelCartData->power_id)->value('value');
        $voltage    = DB::table('voltages')->where('id', $controlPanelCartData->voltage_id)->value('value');
        $application= DB::table('applications')->where('id', $controlPanelCartData->application_id)->value('value');

        if (!$noOfPump || !$power || !$voltage || !$application) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'Invalid configuration!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 3: Master Table
        // =========================
        $power1 = number_format($power, 2, '.', ''); // A Code: 02-03-2026
        $controlPanelData = DB::table('control_panels_master')
            ->where('id', $controlPanelCartData->control_panel_id)
            ->whereRaw("FIND_IN_SET(?, REPLACE(no_of_pumps, ' ', ''))", [$noOfPump])
            ->whereRaw("FIND_IN_SET(?, REPLACE(power_rating, ' ', ''))", [$power1])
            ->whereRaw("FIND_IN_SET(?, REPLACE(power_supply, ' ', ''))", [$voltage])
            ->whereRaw("FIND_IN_SET(?, REPLACE(applications, ' ', ''))", [$application])
            ->first(); 

        if (!$controlPanelData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'No Record Found!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 4: Column Name
        // =========================
        if (ControlPanelsMaster::isIntegerColumn($power)) {
            $columnName = $noOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
        } else {
            $columnName = $noOfPump . 'x' . str_replace(".", '__', $power) . "kwx" . $voltage . 'v';
        }

        $tableName = $controlPanelData->table_name;

        if (!$tableName || !Schema::hasColumn($tableName, $columnName)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'Invalid column mapping!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 5: Fetch Items
        // =========================
        $cpRecords = DB::table($tableName)
            ->select(
                'item_description',
                'material_number',
                'wilo_article_number',
                'weight',
                'brand_code',
                'function_code',
                'range',
                'unit_price',
                'margin',
                $columnName
            )
            ->whereNotNull($columnName)
            ->where($columnName, '!=', 0)
            ->get();

        $cpRecords1 = DB::table($tableName)
            ->whereNotNull($columnName)
            ->where($columnName, '!=', 0)
            ->where('function_code', 1)
            ->count();

        // =========================
        // STEP 6: Adders Logic
        // =========================
        $codePrice = 0;
        $enclousreAdderItemData = null;

        if (!empty($controlPanelCartData->adder_ids)) {

            if (!$controlPanelCartData->control_panel_id) {
                $controlPanelCartData->control_panel_id = $controlPanelCartData->cp_id;
            }

            $addersData = $this->calculateAddersSearchByArticle(
                $controlPanelCartData->control_panel_id,
                $controlPanelCartData->adder_ids,
                $noOfPump,
                $power,
                $voltage,
                $tableName,
                $columnName,
                //$controlPanelData->components,
                $controlPanelCartData->components_id // A Code: 02-06-2026
            );

            $enclousreAdderItemData = $addersData['enclousreItem'] ?? null;
            $codePrice = $addersData['code_price'] ?? 0;
        }

        // =========================
        // STEP 7: Price Calculation
        // =========================
        $price = 0; 

        foreach ($cpRecords as $val) {
            $price += $this->searchByArticleCalculatePriceInItem(
                //$controlPanelData->components,
                //$controlPanelData->enclosure,
                //$controlPanelData->starter_type,
                $controlPanelCartData->components_id,
                $controlPanelCartData->enclosure_id,
                $controlPanelCartData->stater_type_id,
                $enclousreAdderItemData,
                $columnName,
                $cpRecords1,
                (array)$val
            );
        }

        if ($codePrice > 0) {
            $price += $codePrice;
        }

        // =========================
        // STEP 8: Final Price
        // =========================
        // A Code: 10-04-2026 (Removed Overhead)
        $price = ($price * ControlPanelsMaster::controlpanel_over_head()) / User::ic_margin_control_panel();

        // =========================
        // STEP 9: Generate HTML
        // =========================
        $tax = 0;
        $returnHTML = view('frontend.controlpanel.table')
            ->with('cpRecordsData', $cpRecords) // pass actual records
            ->with('tax', $tax)
            ->with('price', $price)
            ->with('starter', $controlPanelData->starter_type)
            ->with('application', $application)
            ->with('noOfPump', $noOfPump)
            ->with('power', $power)
            //->with('starterCode', $controlPanelData->starter_code ?? null)
            ->with('starterCode', $controlPanelData->code ?? null)  // A Code: 02-03-2026
            ->render();       

        // =========================
        // STEP 10: Response
        // =========================
        return response()->json([
            'success' => true,
            'data' => [
                'cp_price'        => number_format($price, 2),
                'cp_records_html' => $returnHTML,
                'cp_id'           => $controlPanelData->id,             
                'table_name'      => $tableName,
                'column_name'     => $columnName,
                'total_price'     => $price,
                'code_price'      => $codePrice,
                'adder_ids'       => $controlPanelCartData->adder_ids ?? null,
                'enclousreItem'   => $enclousreAdderItemData
            ]
        ]);
    }
    // A Code: 01-04-2026 End

    // A Code: 22-04-2026 Start
    public function searchByFullArticleNumberForStock(Request $request)
    {
        $articleNumber = $request->full_article_number_for_stock;

        // =========================
        // STEP 1: Find Cart Data
        // =========================
        $controlPanelCartData = ControlPanelCart::where('full_article_number', $articleNumber)->first();

        if (!$controlPanelCartData) {
            $controlPanelCartData = BoosterCart::where('electrical_article_number', $articleNumber)->first();

            if ($controlPanelCartData) {
                $controlPanelCartData->control_panel_id = $controlPanelCartData->cp_id;
            }
        }        

        if (!$controlPanelCartData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html_error' => 'This article number does not exist.'
                ]
            ]);
        }

        // =========================
        // STEP 2: Resolve Values
        // =========================
        $noOfPump   = DB::table('number_of_pumps')->where('id', $controlPanelCartData->no_of_pump_id)->value('value');
        $power      = DB::table('powers')->where('id', $controlPanelCartData->power_id)->value('value');
        $voltage    = DB::table('voltages')->where('id', $controlPanelCartData->voltage_id)->value('value');
        $application= DB::table('applications')->where('id', $controlPanelCartData->application_id)->value('value');

        if (!$noOfPump || !$power || !$voltage || !$application) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'Invalid configuration!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 3: Master Table
        // =========================
        $power1 = number_format($power, 2, '.', ''); // A Code: 02-03-2026
        $controlPanelData = DB::table('control_panels_master')
            ->where('id', $controlPanelCartData->control_panel_id)
            ->whereRaw("FIND_IN_SET(?, REPLACE(no_of_pumps, ' ', ''))", [$noOfPump])
            ->whereRaw("FIND_IN_SET(?, REPLACE(power_rating, ' ', ''))", [$power1])
            ->whereRaw("FIND_IN_SET(?, REPLACE(power_supply, ' ', ''))", [$voltage])
            ->whereRaw("FIND_IN_SET(?, REPLACE(applications, ' ', ''))", [$application])
            ->first(); 

        if (!$controlPanelData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'No Record Found!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 4: Column Name
        // =========================
        if (ControlPanelsMaster::isIntegerColumn($power)) {
            $columnName = $noOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
        } else {
            $columnName = $noOfPump . 'x' . str_replace(".", '__', $power) . "kwx" . $voltage . 'v';
        }

        $tableName = $controlPanelData->table_name;

        if (!$tableName || !Schema::hasColumn($tableName, $columnName)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cp_records_html' => 'Invalid column mapping!',
                    'cp_price' => 0
                ]
            ]);
        }

        // =========================
        // STEP 5: Fetch Items
        // =========================
        $cpRecords = DB::table($tableName)
            ->select(
                'item_description',
                'material_number',
                'wilo_article_number',
                'weight',
                'brand_code',
                'function_code',
                'range',
                'unit_price',
                'margin',
                $columnName
            )
            ->whereNotNull($columnName)
            ->where($columnName, '!=', 0)
            ->get();

        $cpRecords1 = DB::table($tableName)
            ->whereNotNull($columnName)
            ->where($columnName, '!=', 0)
            ->where('function_code', 1)
            ->count();

        // =========================
        // STEP 6: Adders Logic
        // =========================
        $codePrice = 0;
        $enclousreAdderItemData = null;

        if (!empty($controlPanelCartData->adder_ids)) {

            if (!$controlPanelCartData->control_panel_id) {
                $controlPanelCartData->control_panel_id = $controlPanelCartData->cp_id;
            }

            $addersData = $this->calculateAddersSearchByArticle(
                $controlPanelCartData->control_panel_id,
                $controlPanelCartData->adder_ids,
                $noOfPump,
                $power,
                $voltage,
                $tableName,
                $columnName,
                //$controlPanelData->components,
                $controlPanelCartData->components_id // A Code: 02-06-2026
            );

            $enclousreAdderItemData = $addersData['enclousreItem'] ?? null;
            $codePrice = $addersData['code_price'] ?? 0;
        }

        // =========================
        // STEP 7: Price Calculation
        // =========================
        $price = 0; 

        // A Code: 22-04-2026 Start (Check item is exist in stock or not)
        $exists_item = false;
        foreach ($cpRecords as $val) {
            $data = $this->getArticleNumberByNewSheet(
                $val->brand_code,
                $val->function_code,
                $val->range
            );
            if ($data) {
                $exists_item = true;
                break;
            }
        }
        DB::table('control_panel_carts')
            ->where('full_article_number', $request->full_article_number_for_stock)
            ->update([
                'full_article_number_for_stock' => $exists_item ? $request->full_article_number_for_stock : null,
                'stock_check' => $exists_item ? 1 : 0
            ]);
        // A Code: 22-04-2026 End

        foreach ($cpRecords as $val) {
            $price += $this->searchByArticleCalculatePriceInItem(
                $controlPanelCartData->components_id,
                $controlPanelCartData->enclosure_id,
                $controlPanelCartData->stater_type_id,
                $enclousreAdderItemData,
                $columnName,
                $cpRecords1,
                (array)$val
            );
        }

        if ($codePrice > 0) {
            $price += $codePrice;
        }

        // =========================
        // STEP 8: Final Price
        // =========================
        // A Code: 10-04-2026 (Removed Overhead)
        $price = ($price * ControlPanelsMaster::controlpanel_over_head()) / User::ic_margin_control_panel();

        // =========================
        // STEP 9: Generate HTML
        // =========================
        $tax = 0;
        $returnHTML = view('frontend.controlpanel.table')
            ->with('cpRecordsData', $cpRecords) // pass actual records
            ->with('tax', $tax)
            ->with('price', $price)
            ->with('starter', $controlPanelData->starter_type)
            ->with('application', $application)
            ->with('noOfPump', $noOfPump)
            ->with('power', $power)
            //->with('starterCode', $controlPanelData->starter_code ?? null)
            ->with('starterCode', $controlPanelData->code ?? null)  // A Code: 02-03-2026
            ->render();       

        // =========================
        // STEP 10: Response
        // =========================
        return response()->json([
            'success' => true,
            'data' => [
                'cp_price'        => number_format($price, 2),
                'cp_records_html' => $returnHTML,
                'cp_id'           => $controlPanelData->id,             
                'table_name'      => $tableName,
                'column_name'     => $columnName,
                'total_price'     => $price,
                'code_price'      => $codePrice,
                'adder_ids'       => $controlPanelCartData->adder_ids ?? null,
                'enclousreItem'   => $enclousreAdderItemData
            ]
        ]);
    }
    // A Code: 22-04-2026 End

    // A Code: 10-04-2026 Start
    public function serachByArticleNoGetControlPanelRangeAndCode($controlPanelId) {
        $returnRangeAndCode = [];
        //$controlPanelData = ControlPanel::where('id', $controlPanelId)->get();
        $controlPanelData = ControlPanelsMaster::where('id', $controlPanelId)->get();        

        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code,
            'voltage_id' => $controlPanelData[0]->voltage_id,
            'stater_type_id' => $controlPanelData[0]->stater_type_id
        );
    }
    // A Code: 10-04-2026 End

    public function calculateAddersSearchByArticle($control_panel_id, $ids, $noOfPump, $motorPower, $voltage, $table_name, $columnName,$component)
    {       
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
                                
                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrandAdders($val['brand_code'], $component, $val['function_code'], $val['range']); // A Code: 02-06-2026
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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrandAdders($val['brand_code'], $component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrandAdders($val['brand_code'], $component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getEffectiveBrandAdders($val['brand_code'], $component, $val['function_code'], $val['range']); // A Code: 02-06-2026

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

    // A Code: 17-04-2026 Start
    public function searchByArticleCalculatePriceInItem(
        $component,
        $enclousre,
        $starterType,
        $enclousreItem,
        $columnName,
        $enclosure_count,
        $val = []
    ) {
        $qty = (int)($val[$columnName] ?? 0);

        if ($qty <= 0) {
            return 0.00;
        }

        // ====================== NORMALIZATION ======================
        // Convert numeric codes → string (match Controller 2 logic)
        $enclosureMap = [
            2 => 'metal',
            3 => 'grp',
            4 => 'stainless steel',
        ];

        $componentMap = [
            1 => 'standard',
            2 => 'economic',
            3 => 'schneider',
            4 => 'lovato',
        ];

        $starterMap = [
            1 => 'xtreme',
            //2 => 'multi vfd + bypass',
        ];

        $enclosure = $enclosureMap[$enclousre] ?? '';
        $component = $componentMap[$component] ?? '';
        $starter   = $starterMap[$starterType] ?? '';

        $enclousreItem = !empty($enclousreItem) ? $enclousreItem : [];

        $brand    = (int)$val['brand_code'];
        $function = (int)$val['function_code'];
        $range    = $val['range'];      
        
        // Component Logic (Economic / Schneider / Lovato)
        $brand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-01-2026

        // ====================== LOVATO / SCHNEIDER ======================
        if (in_array($component, ['lovato', 'schneider'])) {

            // STEP 1: Enclosure Override
            if (!empty($enclousreItem) &&
                in_array($enclosure, ['metal', 'grp', 'stainless steel'])) {

                if (isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                    $brand === (int)$enclousreItem['brand_code'] &&
                    $function === (int)$enclousreItem['function_code']) {

                    if ($enclosure_count == 1) {
                        $range = $enclousreItem['range'] ?? $range;
                    }

                    $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                    return $unitPrice * $qty;
                }
            }

            // STEP 2: Special Logic
            $specialUnitPrice = $this->getSpecialUnitPrice(
                $brand, $function, $range, $enclosure, $component, $starter
            );

            if ($specialUnitPrice !== null) {
                return $specialUnitPrice * $qty;
            }

            // STEP 3: Default
            $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
            return $unitPrice * $qty;
        }

        // ====================== DEFAULT FLOW ======================

        // STEP 1: Enclosure Override
        if (!empty($enclousreItem) &&
            in_array($enclosure, ['metal', 'grp', 'stainless steel'])) {

            if (isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                $brand === (int)$enclousreItem['brand_code'] &&
                $function === (int)$enclousreItem['function_code']) {

                if ($enclosure_count == 1) {
                    $range = $enclousreItem['range'] ?? $range;
                }

                $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                return $unitPrice * $qty;
            }
        }

        // STEP 2: Effective Brand
        // $effectiveBrand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-01-2026 Comment

        // STEP 3: Special Logic
        $specialUnitPrice = $this->getSpecialUnitPrice(
            $brand, $function, $range, $enclosure, $component, $starter
        );

        if ($specialUnitPrice !== null) {
            return $specialUnitPrice * $qty;
        }

        // STEP 4: Default
        //$unitPrice = $this->getMasterSheetPriceData($effectiveBrand, $function, $range); // A Code: 01-01-2026 Comment
        $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range); // A Code: 01-01-2026

        return $unitPrice * $qty;
    }
    // A Code: 17-04-2026 End

    // A Code: 22-04-2026 Start
    public function getArticleNumberByNewSheet($brand_code, $function_code, $range)
    {
        $masterData = DB::table('master_price_sheet_electrical_components')
            ->where('brand_code', $brand_code)
            ->where('function_code', $function_code)
            ->where('range', $range)
            ->value('wilo_artilce_no');

        if (!$masterData) {
            return null;
        }

        return WarehousePumpDetails::where('art_no', $masterData)
            ->select('art_no', 'product_name', 'total_qty')
            ->first();
    }
    // A Code: 22-04-2026 End


}