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
use App\Models\BoosterCartCpDetail; // A Code: 22-06-2026

class ControlpanelController extends Controller{

    use ControlPanelModelIdGet;

    public function index() {
        $numberOfPumps = NumberOfPump::select('id', 'value')->get();
        $electricalLists = DB::table('main_electrical_list')->get();
        return view('frontend.controlpanel.index', compact('numberOfPumps', 'electricalLists'));
    }   

    // A Code: 27-03-2026 Start    
    public function ajaxFilter(Request $request)
    {
        $controlPanelData = ControlPanelsMaster::query();

        // A Code: 30-06-2026 Start

        // CSV Fields (without spaces)
        $csvFields = [
            'no_of_pump'   => 'no_of_pumps',
            'power_rating' => 'power_rating',
            'voltage'      => 'power_supply',
            'ambient_temp' => 'min_of_ambient_temp',
        ];

        foreach ($csvFields as $requestKey => $column) {
            if (!empty($request->$requestKey)) {
                $controlPanelData->whereRaw("FIND_IN_SET(?, {$column})", [$request->$requestKey]);
            }
        }

        // CSV Fields (values contain spaces)
        $csvFieldsWithSpaces = [
            'application'            => 'applications',
            'component'              => 'components',
            'communication_protocol' => 'communication_protocol',
        ];

        foreach ($csvFieldsWithSpaces as $requestKey => $column) {
            if (!empty($request->$requestKey)) {
                $controlPanelData->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE({$column}, ' ', ''))", [$request->$requestKey]);
            }
        }

        // Normal Fields
        $normalFields = [
            'stater_type' => 'starter_type',
            'ip_rating'   => 'ip_rating',
            'range'       => 'range',
        ];

        foreach ($normalFields as $requestKey => $column) {
            if (!empty($request->$requestKey)) {
                $controlPanelData->where($column, $request->$requestKey);
            }
        }

        // Enclosure (Multiple Selection)
        if (!empty($request->enclosure)) {

            $enclosures = array_map('trim', explode(',', $request->enclosure));

            $controlPanelData->where(function ($query) use ($enclosures) {
                foreach ($enclosures as $enclosure) {
                    $query->orWhereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(enclosure, ' ', ''))",[$enclosure]);
                }
            });
        }        
        // A Code: 30-06-2026 End

        $controlPanelData = $controlPanelData->get();      

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

            // A Code: 01-07-2026 Start
            $starter = $panel->starter_type ?? '';
            $starterCode = $panel->code ?? '';
            $range = $panel->range ?? '';
            // A Code: 01-07-2026 Start

            $cpRecordsData = [];
            $returnHTML = '';

            // A Code: 27-03-2026 End

            $tableName = $panel->table_name;
            $price = 0;

            // A Code: 27-03-2026 Start
            if(Schema::hasTable($tableName)){
                if(Schema::hasColumn($tableName, $columnName)){      
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
                                    ->where('function_code', '=', '1')
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
                            $rangeId = self::getIdByValue('ranges', $range) ?? 0;
                            $cpRecordsData[$key + 1]['range_id'] = $rangeId;// A Code: 01-07-2026
                            $cpRecordsData[$key + 1]['tax'] = $tax;
                            $controlPanelId = $panel->id; // A Code: 01-07-2026                          

                            // A Code: 01-07-2026 Start
                            $application = $request->application; // "Circulation"                          
                            $noOfPump = $request->no_of_pump; // "3"
                            $power = $request->power_rating; // "0.75", "4.00", "1.10"
                            //$power = (float) $request->power_rating; // "0.75", "4", "1.1"
                            // A Code: 01-07-2026 End

                            $returnHTML = view('frontend.controlpanel.table')
                                            ->with('cpRecordsData', $controlPanelId)
                                            //->with('cpRecordsData', $cpRecordsData) // A Code: 01-07-2026                                           
                                            ->with('tax', $tax)
                                            ->with('price', $price)
                                            ->with('starter', $starter)
                                            ->with('application', $application)
                                            ->with('noOfPump', $noOfPump)
                                            ->with('power', $power)
                                            ->with('starterCode', $starterCode)
                                            ->render();

                            $data['cp_records_html'] = $returnHTML;
                            $data['cp_id'] = $controlPanelId; // A Code: 01-07-2026
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
    
    // A Code: 22-06-2026 Start
    public function searchAjaxFilter(Request $request) 
    {     
        $idberOfPumps = NumberOfPump::select('id', 'value')->get();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
		if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $BoosterCartData = $BoosterCartData->first(); 

        if($BoosterCartData)
        {
            // A Code: 22-06-2026 Start
            $boosterCartCpDetail = BoosterCartCpDetail::where('booster_cart_id', $BoosterCartData->id)->latest('id')->first();
            if ($boosterCartCpDetail) { 

                $request->no_of_pump = self::getIdByValue('number_of_pumps', $boosterCartCpDetail->no_of_pump);
                $request->power_rating = self::getIdByValue('powers', $boosterCartCpDetail->power);
                $request->voltage = self::getIdByValue('voltages', $boosterCartCpDetail->voltage);
                $request->application = self::getIdByValue('applications', $boosterCartCpDetail->application);
                $request->ambient_temp = self::getIdByValue('ambient_temps', $boosterCartCpDetail->ambient_temp);
                $request->stater_type = self::getIdByValue('starter_types', $boosterCartCpDetail->stater_type);
                $request->communication_protocol = self::getIdByValue('comunication_protocols', $boosterCartCpDetail->communication_protocol);
                $request->ip_rating = self::getIdByValue('ip_ratings', $boosterCartCpDetail->ip_rating);
                $request->component = self::getIdByValue('components', $boosterCartCpDetail->component);
                $request->enclosure = self::getIdByValue('enclousres', $boosterCartCpDetail->enclosure);
                $request->range = self::getIdByValue('ranges', $boosterCartCpDetail->range);

            }else{

                // Create New Request Data From Old Control Panels Table Due to Not exit
                $request_data = ControlPanel::where('id','=',$BoosterCartData->cp_id)->first();                      
                
                $request->no_of_pump = optional($request_data)->no_of_pump_id;
                $request->power_rating = optional($request_data)->power_id;
                $request->voltage = optional($request_data)->voltage_id;
                $request->application = optional($request_data)->application_id;
                $request->ambient_temp = optional($request_data)->ambient_temp_id;
                $request->stater_type = optional($request_data)->stater_type_id;
                $request->communication_protocol = optional($request_data)->communication_protocol_id;
                $request->ip_rating = optional($request_data)->ip_rating_id;
                $request->component = optional($request_data)->components_id;
                $request->enclosure = optional($request_data)->enclosure_id;
                $request->range = optional($request_data)->range;

            }

            // Find New Control Panel Data on base of Request Date
            $cpNo_of_pump = self::getValue('number_of_pumps', $request->no_of_pump);
            $cpPower = self::getValue('powers', $request->power_rating);
            $cpVoltage = self::getValue('voltages', $request->voltage);
            $cpApplication = self::getValue('applications', $request->application);
            $cpAmbient_temp = self::getValue('ambient_temps', $request->ambient_temp);
            $cpStater_type = self::getValue('starter_types', $request->stater_type);
            $cpCommunication_protocol = self::getValue('comunication_protocols', $request->communication_protocol);
            $cpIp_rating = self::getValue('ip_ratings', $request->ip_rating);
            $cpComponent = self::getValue('components', $request->component);
            $cpEnclosure = self::getValue('enclousres', $request->enclosure);
            $cpRange = self::getValue('ranges', $request->range);             
            
            $power1 = number_format($cpPower, 2, '.', ''); // A Code: 02-03-2026   
            // A Code: 30-06-2026 Start
            $NewcontrolPanelData = DB::table('control_panels_master')
                ->whereRaw("FIND_IN_SET(?, no_of_pumps)", [$cpNo_of_pump])
                ->whereRaw("FIND_IN_SET(?, power_rating)", [$power1])
                ->whereRaw("FIND_IN_SET(?, power_supply)", [$cpVoltage])
                ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(applications, ' ', ''))", [$cpApplication])
                ->whereRaw("FIND_IN_SET(?, min_of_ambient_temp)", [$cpAmbient_temp])
                ->where('starter_type', $cpStater_type)
                ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(communication_protocol, ' ', ''))", [$cpCommunication_protocol])
                ->where('ip_rating', $cpIp_rating)
                ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(components, ' ', ''))", [$cpComponent])
                ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(enclosure, ' ', ''))", [$cpEnclosure])
                ->where('range', $cpRange)
                ->first();
            // A Code: 30-06-2026 End

            $booster_cart_cp_id = optional($NewcontrolPanelData)->id;
            // A Code: 22-06-2026 End           

            $pump_model = $BoosterCartData->model_no;
            $full_article_number=$request->full_article_number;
            if($booster_cart_cp_id)
            {
                DB::enableQueryLog(); // Enable query log 
                        
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

                $powers[] = array('id' => $request->power_rating, 'value' => $cpPower);
                $voltages[] = array('id' => $request->voltage, 'value' => $cpVoltage);
                $applications[] = array('id' => $request->application, 'value' => $cpApplication);
                $ambienttemps[] = array('id' => $request->ambient_temp, 'value' => $cpAmbient_temp);
                $startertypes[] = array('id' => $request->stater_type, 'value' => $cpStater_type);
                $components[] = array('id' => $request->component, 'value' => $cpComponent);
                $enclousres[] = array('id' => $request->enclosure, 'value' => $cpEnclosure);
                $comunicationprotocols[] = array('id' => $request->communication_protocol, 'value' => $cpCommunication_protocol);
                $ipratings[] = array('id' => $request->ip_rating, 'value' => $cpIp_rating);
                $ranges[] = array('id' => $request->range, 'value' => $cpRange);                   

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
                $data['controlPanel'] = $NewcontrolPanelData;
                $data['no_of_pump'] = $cpNo_of_pump;
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

                    if (isset($data['controlPanel']->id) && !empty($data['controlPanel']->id)) 
                    {                        
                        $idberOfPump = $cpNo_of_pump;                       

                        if (ControlPanelsMaster::isIntegerColumn($data['powers'][0]['value'])) { // Integer Column
                            $power = $data['powers'][0]['value'];
                            $voltage = $data['voltages'][0]['value'];
                            $columnName = $idberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
                        } else { //Float column
                            $power = str_replace(".", '__', $data['powers'][0]['value']);
                            $voltage = $data['voltages'][0]['value'];
                            $columnName = $idberOfPump . 'x' . $power . "kwx" . $voltage . 'v';
                        }

                        $startertypes = $data['startertypes'][0]['value'];
                        $starterCode = $data['controlPanel']->code;
                        $range = $data['ranges'][0]['value'];
                        $tableName = $data['controlPanel']->table_name;

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

                                    $addersData = $this->calculateAddersSearchByArticle(
                                                        $booster_cart_cp_id, // A Code: 25-06-2026
                                                        $BoosterCartData->adder_ids, 
                                                        $idberOfPump, 
                                                        $data['powers'][0]['value'], 
                                                        $voltage, $tableName, $columnName,$component); //Code ids
                                    
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

                                    //here search ajax calculate
                                    $enclourse_exist = $this->searchByArticleCalculatePriceInItem(
                                                                $data['components'][0]['id'],
                                                                $data['enclousres'][0]['id'], 
                                                                $data['startertypes'][0]['id'], 
                                                                $enclousreAdderItemData, $columnName,$cpRecords1,$arrayResult[0]);                                    
                                    if($enclourse_exist == 0.0)
                                    {
                                        $data['enclourse_exist'] = null;
                                        $price = 0.0;
                                    }
                                    else
                                    {
                                        foreach($arrayResult as $key => $val) {
                                            $price += $this->searchByArticleCalculatePriceInItem(
                                                                $data['components'][0]['id'],
                                                                $data['enclousres'][0]['id'], 
                                                                $data['startertypes'][0]['id'], 
                                                                $enclousreAdderItemData, $columnName,$cpRecords1,$val);
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
                                        
                                        $controlPanelId = $data['controlPanel']->id;   
                                        $starter = $data['startertypes'][0]['value'];
                                        $application = $data['applications'][0]['value'];
                                        $noOfPump = $cpNo_of_pump;
                                        $power = $data['powers'][0]['value'];

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
                                        $data['cp_id'] = $data['controlPanel']->id;
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
    // A Code: 22-06-2026 Start 

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

        // STEP 3: Special Logic
        $specialUnitPrice = $this->getSpecialUnitPrice(
            $brand, $function, $range, $enclosure, $component, $starter
        );

        if ($specialUnitPrice !== null) {
            return $specialUnitPrice * $qty;
        }

        // STEP 4: Default
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

    public function ajaxOptionalModal(Request $request) 
    {
        $electricalLists = DB::table('main_electrical_list')->get();
        $electricalListsData = [];
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
        foreach ($electricalLists as $electricalList) {
            if (($electricalList->code >= 1 && $electricalList->code <= 13 && $rangeAndCode['starter_code'] != 'Xtreme' ) || 
                ($electricalList->code >= 16 && $electricalList->code <= 18 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code >= 37 && $electricalList->code <= 40 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                (($electricalList->code == 46 || $electricalList->code == 48 || $electricalList->code == 50) 
                && $rangeAndCode['starter_code'] != 'Xtreme' && $rangeAndCode['voltage_id'] != 1 ) || 
                ($electricalList->code == 51 && $rangeAndCode['starter_code'] != 'Xtreme' )) 
            {
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code == 31 || $electricalList->code == 32 || $electricalList->code == 33 || $electricalList->code == 45 
            || $electricalList->code == 47 || $electricalList->code == 49) && $rangeAndCode['voltage_id'] == 1 
            && $rangeAndCode['stater_type_id'] == 2) { //voltage_id = 230 V, Only starter constant speed Dol
                //no
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code >= 14 && $electricalList->code <= 15 && $rangeAndCode['range'] == 3) 
                || ($electricalList->code == 29 && $rangeAndCode['range'] == 3) || ($electricalList->code >= 41 
            && $electricalList->code <= 43 && $rangeAndCode['range'] == 3) || ($electricalList->code == 52 
            && $rangeAndCode['range'] == 3)) { // 3 = Premium
                $electricalListsData[] = $electricalList;
            }
            // Code no – 19,20 – Basic version - 01 Basic version\04 Single pump configuration Multi VFD and Multi VFD+bypass
            if ($electricalList->code >= 19 && $electricalList->code <= 20 && ($rangeAndCode['starter_code'] == 'VFD' 
            || $rangeAndCode['starter_code'] == 'VFD+Bypass')) {
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

    /*
    public function ajaxOptionalSelectedAdderData(Request $request)
    {        
        $noOfPump = $request->no_of_pump;
        $motorPower = $request->power_rating;
        $voltage = $request->voltage;
        $ids = explode(",", $request->adder_ids);

        //Code ids
        $component = self::getIdByValue('components', $request->component) ?? 0; // A Code: 30-06-2026
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
                                 
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                    
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                } else {
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                   
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                }
                            }
                        }
                        break;
                    default:
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
    */    
    
    // A Code: 02-07-2026 Start    
    public function ajaxOptionalSelectedAdderData(Request $request)
    {       
        $noOfPump = $request->no_of_pump;
        $motorPower = $request->power_rating;
        $voltage = $request->voltage;

        $ids = array_filter(array_map('trim', explode(',', $request->adder_ids)));

        //Code ids
        $component = self::getIdByValue('components', $request->component) ?? 0; // A Code: 30-06-2026

        $price = 0.00;
        $encloureArea = 0.00;

        if ($ids){
            foreach($ids as $id){
                switch(true){
                    case($id >= 1 && $id <= 26): //electrical_common_adder code  
                        $electricalCommonAdders = DB::table('electrical_common_adder')
                                                        ->select(
                                                            'id',
                                                            'item_description',
                                                            'material_number',
                                                            'wilo_article_number',
                                                            'brand_code',
                                                            'function_code',
                                                            'range',
                                                            $id
                                                        )
                                                        ->whereNotNull($id)
                                                        ->where($id, '!=', 0.00)
                                                        ->get();
                     
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if($arrayResult){

                            foreach ($arrayResult as $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );                          
                                
                                $qty = $val[$id] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty;
                                $encloureArea += $unitArea * $qty;                                
                            }
                        }
                        break;                    

                    case($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'a';
                        
                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')
                                                                    ->select(
                                                                        'item_description',
                                                                        'material_number',
                                                                        'wilo_article_number',
                                                                        'brand_code',
                                                                        'function_code',
                                                                        'range',
                                                                        $column
                                                                    )
                                                                    ->whereNotNull($column)
                                                                    ->where($column, '!=', 0.00)
                                                                    ->get();

                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $val) {                                

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );

                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $area = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty;
                                $encloureArea += $area * $qty;
                            }
                        }
                        break;                    

                    case($id >= 37 && $id <= 44):  //electrical_adder_per_pump code

                        $column = $id . 'x1';
                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')
                                                        ->select(
                                                            'item_description',
                                                            'material_number',
                                                            'wilo_article_number',
                                                            'brand_code',
                                                            'function_code',
                                                            'range',
                                                            $column
                                                        )
                                                        ->whereNotNull($column)
                                                        ->where($column, '!=', 0.00)
                                                        ->get();

                        
                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);
                        if ($arrayResult) {

                            foreach ($arrayResult as $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );

                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty * $noOfPump;
                                $encloureArea += $unitArea * $qty;                                 
                            }                           
                        }

                        break;
                    case($id >= 45 && $id <= 52): 
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'ax1';

                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')
                                                                    ->select(
                                                                        'item_description',
                                                                        'material_number',
                                                                        'wilo_article_number',
                                                                        'brand_code',
                                                                        'function_code',
                                                                        'range',
                                                                        $column
                                                                    )
                                                                    ->whereNotNull($column)
                                                                    ->where($column, '!=', 0.00)
                                                                    ->get();

                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );         
                                
                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty * $noOfPump;
                                $encloureArea += $unitArea * $qty * $noOfPump; 
                              
                            }
                        }
                        break;

                    default:
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
    // A Code: 02-07-2026 End

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

    // A Code: 23-04-2026 Start
    public function getControlPanelRangeAndCode($request)
    {     
        $voltageId = self::getIdByValue('voltages', $request->voltage);
        $starterTypeId = self::getIdByValue('starter_types', $request->stater_type);       

        $controlPanel = ControlPanelsMaster::find($request->cp_id);

        if (!$controlPanel) {
            return null;
        }        

        $rangeId = self::getIdByValue('ranges', $controlPanel->range);

        return [
            'id'              => $controlPanel->id,
            'range'           => $rangeId,
            'starter_code'    => $controlPanel->code,
            'voltage_id'      => $voltageId,
            'stater_type_id'  => $starterTypeId,
        ];
    }
    // A Code: 23-04-2026 End

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
        $noOfPump = self::getValue('number_of_pumps', $controlPanelCartData->no_of_pump_id);
        $power = self::getValue('powers', $controlPanelCartData->power_id);
        $voltage = self::getValue('voltages', $controlPanelCartData->voltage_id);
        $application = self::getValue('applications', $controlPanelCartData->application_id);

        // A Code: 22-06-2026 Start
        $ambient_temp = self::getValue('ambient_temps', $controlPanelCartData->ambient_temp_id);
        $starter_type = self::getValue('starter_types', $controlPanelCartData->stater_type_id);
        $communication_protocol = self::getValue('comunication_protocols', $controlPanelCartData->communication_protocol_id);
        $ip_rating = self::getValue('ip_ratings', $controlPanelCartData->ip_rating_id);
        $component = self::getValue('components', $controlPanelCartData->components_id);
        $enclosure = self::getValue('enclousres', $controlPanelCartData->enclosure_id);
        $range = self::getValue('ranges', $controlPanelCartData->range);
        // A Code: 22-06-2026 End

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
            
        // A Code: 30-06-2026 Start
        $controlPanelData = DB::table('control_panels_master')
            ->whereRaw("FIND_IN_SET(?, no_of_pumps)", [$noOfPump])
            ->whereRaw("FIND_IN_SET(?, power_rating)", [$power1])
            ->whereRaw("FIND_IN_SET(?, power_supply)", [$voltage])
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(applications, ' ', ''))", [$application])
            ->whereRaw("FIND_IN_SET(?, min_of_ambient_temp)", [$ambient_temp])
            ->where('starter_type', $starter_type)
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(communication_protocol, ' ', ''))", [$communication_protocol])
            ->where('ip_rating', $ip_rating)
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(components, ' ', ''))", [$component])
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(enclosure, ' ', ''))", [$enclosure])
            ->where('range', $range)
            ->first();
        // A Code: 30-06-2026 End

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

        if (!empty($controlPanelCartData->adder_ids)) 
        {
            $controlPanelCartData->control_panel_id = $controlPanelData->id; // A Code: 23-06-2026

            $addersData = $this->calculateAddersSearchByArticle(
                $controlPanelCartData->control_panel_id,
                $controlPanelCartData->adder_ids,
                $noOfPump,
                $power,
                $voltage,
                $tableName,
                $columnName,
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
                'enclousreItem'   => $enclousreAdderItemData,
                'no_of_pump_id'   => $controlPanelCartData->no_of_pump_id // A Code:19-06-2026
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
        $noOfPump = self::getValue('number_of_pumps', $controlPanelCartData->no_of_pump_id);
        $power = self::getValue('powers', $controlPanelCartData->power_id);
        $voltage = self::getValue('voltages', $controlPanelCartData->voltage_id);
        $application = self::getValue('applications', $controlPanelCartData->application_id);
        $ambient_temp = self::getValue('ambient_temps', $controlPanelCartData->ambient_temp_id);
        $starter_type = self::getValue('starter_types', $controlPanelCartData->stater_type_id);
        $communication_protocol = self::getValue('comunication_protocols', $controlPanelCartData->communication_protocol_id);
        $ip_rating = self::getValue('ip_ratings', $controlPanelCartData->ip_rating_id);
        $component = self::getValue('components', $controlPanelCartData->components_id);
        $enclosure = self::getValue('enclousres', $controlPanelCartData->enclosure_id);
        $range = self::getValue('ranges', $controlPanelCartData->range);

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

        // A Code: 30-06-2026 Start
        $controlPanelData = DB::table('control_panels_master')
            ->whereRaw("FIND_IN_SET(?, no_of_pumps)", [$noOfPump])
            ->whereRaw("FIND_IN_SET(?, power_rating)", [$power1])
            ->whereRaw("FIND_IN_SET(?, power_supply)", [$voltage])
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(applications, ' ', ''))", [$application])
            ->whereRaw("FIND_IN_SET(?, min_of_ambient_temp)", [$ambient_temp])
            ->where('starter_type', $starter_type)
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(communication_protocol, ' ', ''))", [$communication_protocol])
            ->where('ip_rating', $ip_rating)
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(components, ' ', ''))", [$component])
            ->whereRaw("FIND_IN_SET(REPLACE(?, ' ', ''), REPLACE(enclosure, ' ', ''))", [$enclosure])
            ->where('range', $range)
            ->first();
        // A Code: 30-06-2026 End

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

            $controlPanelCartData->control_panel_id = $controlPanelData->id; // A Code: 23-06-2026

            $addersData = $this->calculateAddersSearchByArticle(
                $controlPanelCartData->control_panel_id,
                $controlPanelCartData->adder_ids,
                $noOfPump,
                $power,
                $voltage,
                $tableName,
                $columnName,
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

    // A Code: 23-06-2026 Start
    public function serachByArticleNoGetControlPanelRangeAndCode($controlPanelId)
    {
        $controlPanel = ControlPanelsMaster::find($controlPanelId);

        if (!$controlPanel) {
            return null;
        }

        return [
            'id' => $controlPanel->id,
            'starter_code' => $controlPanel->code,
        ];
    }
    // A Code: 23-06-2026 End

    /*
    public function calculateAddersSearchByArticle($control_panel_id, $ids, $noOfPump, $motorPower, $voltage, $table_name, $columnName, $component)
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
                                    $price += $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                            }
                        }
                        break;
                    case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code

                        $nearestColumn = $this->searchByArticleCommonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
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
                    default:
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
    */
    
    // A Code: 02-07-2026 Start
    public function calculateAddersSearchByArticle($control_panel_id, $ids, $noOfPump, $motorPower, $voltage, $table_name, $columnName, $component)
    {  
        $ids = array_filter(array_map('trim', explode(',', $ids)));

        $price = 0.00;        
        $encloureArea = 0.00;

        if ($ids) {
            foreach ($ids as $id) {
                switch (true) {
                    case ($id >= 1 && $id <= 26): //electrical_common_adder code
                        $electricalCommonAdders = DB::table('electrical_common_adder')
                                                        ->select(
                                                            'id', 
                                                            'item_description', 
                                                            'material_number', 
                                                            'wilo_article_number', 
                                                            'brand_code', 
                                                            'function_code', 
                                                            'range', 
                                                            $id
                                                        )
                                                        ->whereNotNull($id)
                                                        ->where($id, '!=', 0.00)
                                                        ->get();
                        
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                
                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );                          
                                
                                $qty = $val[$id] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty;
                                $encloureArea += $unitArea * $qty;                               
                               
                            }
                        }
                        break;
                    case ($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code

                        $nearestColumn = $this->searchByArticleCommonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'a';

                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')
                                                                ->select(
                                                                    'item_description', 
                                                                    'material_number', 
                                                                    'wilo_article_number', 
                                                                    'brand_code', 
                                                                    'function_code', 
                                                                    'range', 
                                                                    $column
                                                                )
                                                                ->whereNotNull($column)
                                                                ->where($column, '!=', 0.00)
                                                                ->get();

                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);

                        if ($arrayResult){
                            foreach ($arrayResult as $key => $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );

                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $area = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty;
                                $encloureArea += $area * $qty;                                
                                
                            }
                        }
                        break;
                    case ($id >= 37 && $id <= 44):  //electrical_adder_per_pump code
                        $column = $id . 'x1';
                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')
                                                    ->select(
                                                        'item_description', 
                                                        'material_number', 
                                                        'wilo_article_number', 
                                                        'brand_code', 
                                                        'function_code', 
                                                        'range', 
                                                        $column
                                                    )
                                                    ->whereNotNull($column)
                                                    ->where($column, '!=', 0.00)
                                                    ->get();

                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);
                        
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );

                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty * $noOfPump;
                                $encloureArea += $unitArea * $qty;                                  
                         
                            }
                        }
                        break;
                    case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code

                        $nearestColumn = $this->searchByArticleCommonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'ax1';
                        
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')
                                                                ->select(
                                                                    'item_description', 
                                                                    'material_number', 
                                                                    'wilo_article_number', 
                                                                    'brand_code', 
                                                                    'function_code', 
                                                                    'range', 
                                                                    $column
                                                                )
                                                                ->whereNotNull($column)
                                                                ->where($column, '!=', 0.00)
                                                                ->get();
                                                                
                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
                        
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {

                                // Component Logic (Economic / Schneider / Lovato)
                                $val['brand_code'] = $this->getFinalBrandCode(
                                    $val['brand_code'],
                                    $component,
                                    $val['function_code'],
                                    $val['range']
                                );         
                                
                                $qty = $val[$column] ?? 0;
                                $unitPrice = $this->getMasterSheetPriceData(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $unitArea = $this->getMasterSheetHeightMultiplyByWidth(
                                    $val['brand_code'],
                                    $val['function_code'],
                                    $val['range']
                                );
                                $price += $unitPrice * $qty * $noOfPump; // Column qty × No. of Pumps × Pump Qty (1)
                                $encloureArea += $unitArea * $qty * $noOfPump;                           
                               
                            }
                        }
                        break;
                    default:
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

    private function getFinalBrandCode($brandCode, $component, $functionCode, $range)
    {
        $brandCode = $this->getEffectiveBrandAdders(
            $brandCode,
            $component,
            $functionCode,
            $range
        );
        if (
            $component == 2 &&
            $brandCode == 1 &&
            $this->getMasterSheetPriceData(2, $functionCode, $range)
        ) {
            $brandCode = 2;
        }

        return $brandCode;
    }
    // A Code: 02-07-2026 End

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

        // STEP 3: Special Logic
        $specialUnitPrice = $this->getSpecialUnitPrice(
            $brand, $function, $range, $enclosure, $component, $starter
        );

        if ($specialUnitPrice !== null) {
            return $specialUnitPrice * $qty;
        }

        // STEP 4: Default
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

    // A Code: 30-06-2026 Start
    public static function getValue($table, $id)
    {
        return !empty($id)
            ? DB::table($table)->where('id', $id)->value('value')
            : null;
    }

    public static function getIdByValue($table, $value)
    {
        return !empty($value)
            ? DB::table($table)->where('value', $value)->value('id')
            : null;
    }
    // A Code: 30-06-2026 End


}