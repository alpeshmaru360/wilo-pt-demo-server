<?php

namespace App\Http\Controllers\Frontend;

use App\AtmosPump;
use App\Helpers\AdderHelper;
use App\Http\Controllers\Controller;
use App\Models\BaseFrameCalculation;
use App\Models\BoosterBareshaftMotorPrice;
use App\Models\BoosterBareshaftPumpPrice;
use App\Models\BoosterCableSelection;
use App\Models\BoosterFullPumpPrice;
use App\Models\BoosterMasterSheetMechanicalComponent;
use App\Models\PN16MechanicalComponent;
use App\Models\PTPDistanceMechanicalComponent;
use App\NumberOfPump;
use App\Tax;
use App\Traits\ControlPanelModelIdGet;
use App\User;
use App\ControlPanel;
use App\Models\BoosterCart;
use App\Models\BoosterCpItems;
use App\Models\BoosterItems;
use App\Helpers\CurrencyHelper;
use Validator;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Auth;

class BoosterSetController extends Controller {

    use ControlPanelModelIdGet;
    
    public function index($qoutation=null) {

        $numberOfPumps = NumberOfPump::select('id', 'value')->get();
        $mechanicalLists = DB::table('main_electrical_list')->get();
        return view('frontend.booster.index', compact('numberOfPumps','qoutation'));
    }

    public function getPumpDetailByType($BoosterCartData = null) {
        if($BoosterCartData){
            $pumpType = $BoosterCartData->pump_type;
            $article_number = $BoosterCartData->booster_article_number;
        }
        else{
            $pumpType = \request()->get('pumpType');
            $article_number = \request()->get('article_number');
        }
        if ($pumpType == 'full_pump') {
            $data = BoosterFullPumpPrice::where('pump_article_no_helix_pump', $article_number)->orderBy('unit_price')->first();
            if (!empty($data) && $data->unit_price > 0) {
                $pump_detail = array();
                $pump_detail['model_number'] = $data['model_no'];
                $pump_detail['motor_power'] = $data['power'];
                $pump_detail['supply_voltage'] = $data['voltage'];
                $pump_detail['frequency'] = $data['frequency'];
                $pump_detail['price'] = $data['unit_price'];
                $pump_detail['pump_height'] = $data['pump_height'];
                if($BoosterCartData){
                    return $data['unit_price']; 
                }
                else{
                    return response()->json(array('success' => true, 'data' => $pump_detail));
                }
            } else {
                return response()->json(array('success' => false, 'data' => null));
            }
        } elseif ($pumpType == 'bareshaft_pump') {
            if (\request()->get('motor_brand') == null) {
                $data['required'] = 'Motor Brand is required.';
                return response()->json(array('success' => true, 'data' => $data));
            }
            $data = BoosterBareshaftPumpPrice::where('bareshaft_article_no_helix_pump', $article_number)->orderBy('unit_price')->first();

            if (!empty($data) && $data->unit_price > 0) {
                $motor_data = BoosterBareshaftMotorPrice::where('power', $data->actual_power)
                                ->where('voltage', $data->voltage)
                                ->where('frequency', $data->frequency)
                                ->where('brand', \request()->get('motor_brand'))
                                ->orderBy('price')->first();
                $pump_detail = array();
                $pump_detail['model_number'] = $data['model_no'];
                $pump_detail['motor_power'] = $data['actual_power'];
                $pump_detail['supply_voltage'] = $data['voltage'];
                $pump_detail['frequency'] = $data['frequency'];
                $pump_detail['motor_brand'] = $motor_data['brand'];
                $pump_detail['efficiency'] = $motor_data['efficiency'];
                $pump_detail['price'] = $data['unit_price'] + $motor_data['price'];
                $pump_detail['pump_height'] = $data['pump_height'] + $motor_data['motor_height'];
                return response()->json(array('success' => true, 'data' => $pump_detail));
            } else {
                return response()->json(array('success' => false, 'data' => null));
            }
        }
    }

    public function getPumpAllModelNo() {
        $data = BoosterFullPumpPrice::select('model_no', 'pump_height')->get();
        foreach ($data as $key => $val) {
            $data1[$key]['height'] = $val['pump_height'];
            $data1[$key]['model_no'] = $val['model_no'];
        }
        return response()->json(array('success' => true, 'data' => $data1));
    }

    //here mechanical price avaliable
    public function calculateMechanicalComponent(Request $request) {
        $article_number = \request()->get('article_number');
        $pump_model = \request()->get('pump_model');
        $no_of_pumps = (int)\request()->get('no_of_pumps');
        $pump_height = (float)\request()->get('pump_height');
        $panel_height = (float)\request()->get('panel_height');
        $panel_width = (float)\request()->get('panel_width');
        $system_pressure = \request()->get('system_pressure');
        $manifold = \request()->get('manifold');
        
        // $cp_price = (float)\request()->get('cp_price');
        
        $cp_price_raw = \request()->get('cp_price');
        $cp_price = (float)str_replace(',', '', $cp_price_raw);

        $code_price = (float)\request()->get('code_price'); ////Electrical adder code price
        $mechanical_code_price = (float)\request()->get('mechanical_code_price'); //Mechanical adder code price
        $pump_unit_price = (float)\request()->get('pump_unit_price');
        
        $starter_type = \request()->get('starter_type');
        $range = \request()->get('range');
        $motor_power = (float)\request()->get('power');
        $voltage = (float)\request()->get('voltage');


        $validator = Validator::make(\request()->all(), [
                    'pump_model' => 'required',
                    'no_of_pumps' => 'required',
                    'pump_height' => 'required',
                    'panel_height' => 'required',
                    'panel_width' => 'required',
                    'system_pressure' => 'required',
                    'manifold' => 'required',
                    'cp_price' => 'required',
                    'code_price' => 'required',
                    'mechanical_code_price' => 'required',
                    'pump_unit_price' => 'required',
                    'starter_type' => 'required',
                    'range' => 'required',
                    'power' => 'required',
                    'voltage' => 'required',
                        ]
        );
        if ($validator->fails()) {
            $data['validation_messages'] = $validator->messages();
            // The given data did not pass validation
            return response()->json(array('success' => true, 'data' => $data));
        }

        $bill_of_material = array();
        $i = 0;
        
        //get constants values
        $interCompanyMargin = User::ic_margin_booster();
         // This is temporary
        $cable_size_ampere_constant = DB::table('setup_fields')->where('name', 'cable_size_ampere_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $cable_length_constant = DB::table('setup_fields')->where('name', 'cable_length_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $spare_length = DB::table('setup_fields')->where('name', 'spare_length')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        // if (empty($pump_unit_price) || empty($panel_height) || empty($manifold) || empty($panel_width) || empty($pump_model) || empty($no_of_pumps) || empty($system_pressure) || empty($pump_height)) {
        //     // print_r('data missing');
        //     $data['html'] = 'Fields are missing';
        //     return response()->json(array('success' => true, 'data' => $data));
        // }

        $slashPos = strpos($pump_model, ' ');
        $fpump_model = explode(' ', $pump_model);
        // print_r( $fpump_model);
        $model = end($fpump_model);
        if (str_starts_with($model, 'C')) {
            $model = str_replace('.', '', $model);
        
            // $model =  str_replace('-','.',$model);
        }
        else {
            if (str_contains($model, '/')) {
                $slashPos = strpos($model, '/');
                $model = substr($model, 0, $slashPos);
                if(str_starts_with($model,'MV')){
                    $slashPos = strpos($model, 'I');
                    $model = substr($model, $slashPos);
                }
            }
            if (str_ends_with($model, '-')) {
                $model = substr($model, 0, -2);
            }
            if (str_ends_with($model, '/')) {
                $model = substr($model, 0, -2);
            }
            if (str_ends_with($model, '/^[a-zA-Z]+$/')) {
                $model = substr($model, 0, -2);
            }
            if (str_contains($model, '-')) {
                $slashPos = strpos($model, '-');
                $model = substr($model, 0, $slashPos);
            }
        }
    
        $base_frame_size_constant = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0];
        $ptp_data = PTPDistanceMechanicalComponent::
        where('pump_model_range1', 'LIKE', '%' . substr($model, 0, -2) . '%')
            ->orWhere('pump_model_range2', 'LIKE', '%' . substr($model, 0, -2) . '%')->get();
        if (count($ptp_data) == 0) {
            $data['error_html'] = 'No PTP Data Record Found';
            return response()->json(array('success' => true, 'data' => $data));
        }
        if (str_starts_with($model, 'V')) {
            $offset = 1;
        } else if (str_starts_with($model, 'CV1.L') || str_starts_with($model, 'CH1-L')) {
            $offset = 5;
        } else if (str_starts_with($model, 'MHIL')) {
            $offset = 4;
        } else if (str_starts_with($model, 'CV1-L.')) {
            $offset = 6;
        } else {
            $offset = 0;
        }
        $check = (int) substr($model, $offset);
        foreach ($ptp_data as $key => $p) {
            $start = (int) substr(trim($p->pump_model_range1), $offset);
            $end = (int) substr(trim($p->pump_model_range2), $offset);
         //   echo $start.' '.$end.' '.$check.'<br>';
            if ($check >= $start && $check <= $end) {
                $ptp = $p->ptp;
                $ptd_distance_id = $p->id;
                break;
            } else {
                $ptp = 0;
            }
            // else{
            //     $data['html'] = 'No PTP Data record found';
            //     return response()->json(array('success' => true, 'data' => $data));
            // }
        }
        if ($ptp == 0) {
            $data['error_html'] = 'No PTP Data record found';
            return response()->json(array('success' => true, 'data' => $data));
        }
        $panel_stand_price = 0;
        if($panel_height == 400){
            $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1521', '400'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            $panel_stand_height = 1521;
            $base_frame_length_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
                // ->where(function($query) use ($model){
                //     $query->where('pump_model_range1', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                //     $query->orWhere('pump_model_range2', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                // })
                ->get();
            foreach ($base_frame_length_data as $key => $p) {
                $start = (int) substr(trim($p->pump_model_range1), $offset);
                $end = (int) substr(trim($p->pump_model_range2), $offset);
                // echo $start.' '.$end.' '.$check.'<br>';
                if ($check >= $start && $check <= $end) {
                    $base_frame_length = $p;
                    break;
                } else {
                    $base_frame_length = null;
                }
                // else{
                //     $data['html'] = 'No PTP Data record found';
                //     return response()->json(array('success' => true, 'data' => $data));
                // }
            }

            if(empty($base_frame_length)){
                $data['error_html'] = 'No  Base Frame record found for the selected model';
                return response()->json(array('success' => true, 'data' => $data));
            }
            
            $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            if(!empty($base_frame_length_price)){
                $val_price = 0;
                foreach ($base_frame_length_price as $key => $value){
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                    $val_price+=$value->price;
                }
                $base_frame_size_price =  $val_price;
            }
        }
        elseif ($panel_height > 400 && $panel_height < 1000){
            $base_frame_size = $pump_height + $panel_height + $base_frame_size_constant; //from admin configuration setting
            //panel height =600.0
            //base_frame_size = 2043.0
            if($base_frame_size < 1775) {
                //fails
                $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1775', '8040'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                $panel_stand_height = 1775;
            
            } else if ($base_frame_size > 1775){
                //pass
                if($ptp == 300)
                {
                    $no_of_pumps = $no_of_pumps + 2;
                }
                elseif($ptp == 500)
                {
                 $no_of_pumps = $no_of_pumps + 1;
                }
                $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1775', '8040'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                $panel_stand_height = 1775;
            }
            $base_frame_length_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
            ->get();
            $no_of_pumps = (int)\request()->get('no_of_pumps');
            // ->where(function($query) use ($model){
                //     $query->where('pump_model_range1', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                //     $query->orWhere('pump_model_range2', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                // })
            foreach ($base_frame_length_data as $key => $p) {
                $start = (int) substr(trim($p->pump_model_range1), $offset);
                $end = (int) substr(trim($p->pump_model_range2), $offset);
                // echo $start.' '.$end.' '.$check.'<br>';
                if ($check >= $start && $check <= $end) {
                    $base_frame_length = $p;
                    break;
                } else {
                    $base_frame_length = null;
                }
                // else{
                //     $data['html'] = 'No PTP Data record found';
                //     return response()->json(array('success' => true, 'data' => $data));
                // }
            }
            //$panel_height = 600
            if(empty($base_frame_length)){
                $data['error_html'] = 'No Base Frame record found for selected model.';
                return response()->json(array('success' => true, 'data' => $data));
            }
            $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            if(!empty($base_frame_length_price)){
                $val_price = 0;
                foreach ($base_frame_length_price as $key => $value){
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                    $val_price+=$value->price;
                }
                $base_frame_size_price =  $val_price;
                //$base_frame_size_price 286.49;
            }
        } else if ($panel_height >= 1000) {
            //panel stand > 1000
            //get from BASE FRAME CALCULATION SHEET
            // $panel_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)->where('pump_model_range1', 'LIKE', '%' . substr($model, 0, -1) . '%')->orWhere('pump_model_range2', 'LIKE', '%' . substr($model, 0, -1) . '%')->first();
            $baseframe_panel_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
                ->get();
            foreach ($baseframe_panel_data as $key => $p) {
                $start = (int) substr(trim($p->pump_model_range1), $offset);
                $end = (int) substr(trim($p->pump_model_range2), $offset);
            //   echo $start.' '.$end.' '.$check.'<br>';
                if ($check >= $start && $check <= $end) {
                    $base_frame_length = $p;
                    break;
                } else {
                    $base_frame_length = null;
                }
                // else{
                //     $data['html'] = 'No PTP Data record found';
                //     return response()->json(array('success' => true, 'data' => $data));
                // }
            }

            if(empty($base_frame_length)){
                $data['error_html'] = 'No  Base Frame record found for the selected model';
                return response()->json(array('success' => true, 'data' => $data));
            }
             $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            if(!empty($base_frame_length_price)){
                $val_price = 0;
                foreach ($base_frame_length_price as $key => $value){
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                    $val_price+=$value->price;
                }
                $base_frame_size_price =  $val_price;
            }
            // $panel_stand_price = array_sum($master_sheet);
        } else {
            // print_r('$panel_stand_price');
            $data['error_html'] = 'No  Panel Height record found for panel height '.$panel_height;
            return response()->json(array('success' => true, 'data' => $data));
        }
        if (!empty($master_sheet)) {
            foreach ($master_sheet as $key => $value) {
                $bill_of_material[$i]['range'] = $value->range;
                $bill_of_material[$i]['brand_code'] = $value->brand_code;
                $bill_of_material[$i]['function_code'] = $value->function_code;
                $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                $bill_of_material[$i]['item_description'] = $value->description;
                $bill_of_material[$i]['price'] = $value->price;
                $panel_stand_price += $value->price;
                $bill_of_material[$i]['qty'] = 1;
                $i++;
            }
        }
        //calculate power monitor flag
        $power_monitor_flag_price = 0;
        if ($range == 1 && (($starter_type == 1) || ($starter_type == 2) || ($starter_type == 6) || ($starter_type == 5))) {
            if ($system_pressure == 'PN16') {
                $power_monitor_flag = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 91)->where('range', 140)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            } elseif ($system_pressure == 'PN25') {
                $power_monitor_flag = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 91)->where('range', 280)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            }
            if (!empty($power_monitor_flag)) {
                foreach ($power_monitor_flag as $key => $value) {
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $power_monitor_flag_price += $value->price;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                }
            }
        } else if (($range == 2 || $range == 3 || $range == 1) && ($starter_type > 1)) {
            //pass
            $power_monitor_flag2 = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 93)->where('range', 40)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            if (!empty($power_monitor_flag2)) {
                foreach ($power_monitor_flag2 as $key => $value) {
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $bill_of_material[$i]['price'] = $value->price;
                    $power_monitor_flag_price += $value->price;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                }
            }
            
            if ($system_pressure == 'PN16') {
                $power_monitor_flag1 = BoosterMasterSheetMechanicalComponent::where('brand_code', 8)->where('function_code', 92)->where('range', 160)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            } elseif ($system_pressure == 'PN25') {
                $power_monitor_flag1 = BoosterMasterSheetMechanicalComponent::where('brand_code', 8)->where('function_code', 92)->where('range', 250)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            }
            
            if (!empty($power_monitor_flag1)) {
                foreach ($power_monitor_flag1 as $key => $value) {
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $power_monitor_flag_price += $value->price;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                }
            }
        } else {
            //here
            $data['error_html'] = 'No record found against selected starter type.';
            return response()->json(array('success' => true, 'data' => $data));
        }

        //CABLE SIZE
        if ($starter_type == 1 || $starter_type == 2 || $starter_type == 5) {
            $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
            $Cable_Ampere = $Ampere_per_pump * 1.25;
            $rangeData = BoosterCableSelection::where('brand_code', 12)->where('function_code', 111)->orderBy('range')->get();
        } else if ($starter_type == 6) {
            $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
            $rangeData = BoosterCableSelection::where('brand_code', 12)->where('function_code', 111)->orderBy('range')->get();
            $Cable_Ampere = ($Ampere_per_pump * $cable_size_ampere_constant) * 1.25;
        } else if ($starter_type == 3) {
            $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
            $rangeData = BoosterCableSelection::where('brand_code', 14)->where('function_code', 113)->orderBy('range')->get();
            $Cable_Ampere = $Ampere_per_pump * 1.25;
        } else if ($starter_type == 4 || $starter_type == 7) {
            $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
            $Cable_Ampere = ($Ampere_per_pump * $cable_size_ampere_constant) * 1.25;
            $rangeData = BoosterCableSelection::where('brand_code', 14)->where('function_code', 113)->orderBy('range')->get();
        } else {
            // print_r('CableSelection');
            $data['error_html'] = 'No record found against starter type';
            return response()->json(array('success' => true, 'data' => $data));
        }
        $range = 0;
        foreach($rangeData as $r) {
            if ($r->range >= $Cable_Ampere) {
                $range = $r->range;
                break;
            }
        }
        if($range == 0) {
            // print_r('range not found');
            $data['error_html'] = 'No Range record found for booster calcualtion.';
            return response()->json(array('success' => true, 'data' => $data));
        }
        
        if($starter_type == 1 || $starter_type == 2 || $starter_type == 5 || $starter_type == 6) 
        {
            $cable_unit_data = BoosterMasterSheetMechanicalComponent::where('brand_code', 12)->where('function_code', 111)->where('range', $range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
        }
        else
        {
            $cable_unit_data = BoosterMasterSheetMechanicalComponent::where('brand_code', 14)->where('function_code', 113)->where('range', $range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
        }

        $cable_unit_price = 0;

        //Cable Length Calculation
        $start1_arr = array(1, 2, 3, 5);
        $start2_arr = array(4, 6, 7);
        if ($panel_height < 1000) {
            if (in_array($starter_type, $start1_arr)) {
                $Cablelength = ($panel_stand_height + $base_frame_length->base_frame_length) * $no_of_pumps;
            }
            if (in_array($starter_type, $start2_arr)) {
                $Cablelength = (($panel_stand_height + $base_frame_length->base_frame_length) * 2) * $no_of_pumps;
            }
        } else {
            if (in_array($starter_type, $start1_arr)) {
                $Cablelength = ($pump_height + ($cable_length_constant * $panel_height) + $panel_width + $base_frame_length->base_frame_length + $spare_length) * $no_of_pumps;
            }
            if (in_array($starter_type, $start2_arr)) {
                $Cablelength = (($pump_height + ($cable_length_constant * $panel_height) + $panel_width + $base_frame_length->base_frame_length + $spare_length) * 2) * $no_of_pumps;
            }
        }
        if (!empty($cable_unit_data)) {
            foreach ($cable_unit_data as $key => $value) {
                $bill_of_material[$i]['range'] = $value->range;
                $bill_of_material[$i]['brand_code'] = $value->brand_code;
                $bill_of_material[$i]['function_code'] = $value->function_code;
                $bill_of_material[$i]['item_description'] = $value->description;
                $bill_of_material[$i]['price'] = $value->price;
                $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                $bill_of_material[$i]['qty'] = $Cablelength/1000;
                $cable_unit_price += $value->price;
                $i++;
            }
        }
        //manual 
        $cablePrice = $cable_unit_price * ($Cablelength/1000); //CABLE PRICE
        $overhead = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0]; //get from admin
        $intercompany_margin = $interCompanyMargin; 
        $standard_component_price = $this->calcualtePriceInBOM($no_of_pumps, $ptd_distance_id, $system_pressure, $manifold);
        $mechanical_system_price = $standard_component_price + $base_frame_size_price + $power_monitor_flag_price + $mechanical_code_price + $panel_stand_price;  
        $booster_price = ((($pump_unit_price * $no_of_pumps) + $cp_price + $code_price + ($mechanical_system_price + $cablePrice)) * $overhead ) / $intercompany_margin;

        $mechanical_price = ((($pump_unit_price * $no_of_pumps) + ($mechanical_system_price + $cablePrice)) * $overhead ) / $intercompany_margin;

        $data['booster_price'] = $booster_price;
        $data['standard_component_price'] = $standard_component_price;
        $data['mechanical_system_price'] = $mechanical_system_price;
        $data['cablePrice'] = $cablePrice;
        $data['pump_unit_price'] = $pump_unit_price;
        $data['panel_stand_price'] = $panel_stand_price;
        $data['no_of_pumps'] = $no_of_pumps;
        $data['starter_type'] = $starter_type;
        $data['power'] = $motor_power;
        $data['code_price'] = $code_price;
        $data['mechanical_code_price'] = $mechanical_code_price;
        $data['pump_model'] = $pump_model;
        $data['manifold'] = $manifold;
        $data['ptp_distance_id'] = $ptd_distance_id;
        $data['voltage'] = $voltage;
        $data['pressure'] = $system_pressure;
        $data['cp_price'] = $cp_price;
        $data['base_frame_size'] = $base_frame_size_price;
        $data['power_monitor_flag_price'] = $power_monitor_flag_price;
        $data['cable_size'] = $cable_size_ampere_constant;
        $data['Cablelength'] = $Cablelength;

        $data['mechanical_items_price'] = $mechanical_price;
        $data['electrical_items_price'] =  ($cp_price * $overhead) / $intercompany_margin + ($code_price * $overhead) / $intercompany_margin;
        
        // dd($bill_of_material);

        $data['bill_of_material_booster'] = json_encode($bill_of_material);
        $returnHTML = view('frontend.booster.table')->with('boosterData', $data)->render();
        $data['html'] = $returnHTML;
        // dd($data['mechanical_items_price'],$data['electrical_items_price'],$data['booster_price'],$data['code_price'],$data['mechanical_code_price'],$overhead,$intercompany_margin);
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function calcualtePriceInBOM($noOfPump, $ptpDistanceId, $systemPressure, $mainfold) {
        $price = 0.00;
        if ($systemPressure == 'PN16') {
            $tableName = 'booster_pn16_mechanical_component';
        } else {
            $tableName = 'booster_pn25_mechanical_component';
        }

        $columnName = $noOfPump . 'x' . $ptpDistanceId;
        if (Schema::hasColumn($tableName, $columnName)) {
            // task no 39
            $cpRecords = DB::table($tableName)->select('item_description', 'brand_code', 'function_code', 'range', 'unit_price','wilo_article_no', $columnName)
                            ->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
            $arrayResult = json_decode(json_encode($cpRecords), true);
            foreach ($arrayResult as $key => $val) {
                $price += $this->calculatePriceInItem($columnName, $mainfold, $systemPressure ,$val);
            }
        }
        return $price;
    }

    public function calculatePriceInItem($columnName, $mainfold, $systemPressure,$val = []) {
        // add here
        // dd($val);
        $price = 0.00;
        if ($systemPressure == 'PN16') {
            if ($mainfold == 'SS316' && $val['function_code'] == 65) { // Sunction
                $price = $this->getMasterSheetPriceData($val['brand_code'], 69, $val['range']) *$val[$columnName]; //Qty * price // 2 parameter is equal to brand code
            //
            } else if ($mainfold == 'SS316' && $val['function_code'] == 67) { //Discharge
                if ($this->getMasterSheetPriceData($val['brand_code'], 71, $val['range'])) {
                    $price = $this->getMasterSheetPriceData($val['brand_code'], 71, $val['range'])*$val[$columnName] ; //Qty * price
                } else {
                    $price = 0.00;
                   
                }
            } else {
               
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range'])* $val[$columnName]; //Qty * price
              
            }
        } else { // Pn25
                if ($mainfold == 'SS316' && $val['function_code'] == 66) { // Sunction
                $price = $this->getMasterSheetPriceData($val['brand_code'], 70, $val['range'])* $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
            //
            } else if ($mainfold == 'SS316' && $val['function_code'] == 68) { //Discharge
                if ($this->getMasterSheetPriceData($val['brand_code'], 72, $val['range'])) {
                    $price = $this->getMasterSheetPriceData($val['brand_code'], 72, $val['range'])* $val[$columnName]; //Qty * price
                } else {
                    
                    $price = 0.00;
                

                }
            } else {
                if (is_array($val) && isset($val['brand_code'], $val['function_code'], $val['range'], $val[$columnName]))
                    {
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                    }
               
            }
        }
        return $price;
    }

    public function getMasterSheetPriceData($brand_code, $function_code, $range) {
        $masterData = DB::table('booster_master_sheet_mechanical_component')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->price)){
            return (float) $masterData[0]->price;
        }
        return 0;
    }

    public function getMasterSheetPriceCPData($brand_code, $function_code, $range) {

        $masterData = DB::table('master_price_sheet_electrical_components')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->price)) {
            return (float) $masterData[0]->price;
        }
        return 0;
    }

    public function ajaxOptionalHtml(Request $request) {
        $mechanicalLists = DB::table('main_mechanical_adder_lists')->get();
        $mechanicalListsData = [];
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
        foreach ($mechanicalLists as $mechanicalList) {
            if (($mechanicalList->code >= 53 && $mechanicalList->code <= 57) || ($mechanicalList->code >= 60 && $mechanicalList->code <= 61 )) {
                $mechanicalListsData[] = $mechanicalList;
            }

            if (($mechanicalList->code >= 65 && $mechanicalList->code <= 68)) {
                $mechanicalListsData[] = $mechanicalList;
            }


            if (($mechanicalList->code >= 58 && $mechanicalList->code <= 59 && $rangeAndCode['range'] == 2) || ($mechanicalList->code == 58 && $mechanicalList->code <= 59 && $rangeAndCode['range'] == 3)) { // Range 2= standard , 3 = Premium
                $mechanicalListsData[] = $mechanicalList;
            }

            if (($mechanicalList->code >= 62 && $mechanicalList->code <= 64) || ($request->no_of_pumps > 1 && $request->ptp_distance_id > 0)) {
                $mechanicalListsData[] = $mechanicalList;
            }
        }
        $data = view('frontend.booster.mechanical_optional')->with('mechanicalListsData', $mechanicalListsData)
                ->render();
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function getControlPanelRangeAndCode($request) {
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

    public function ajaxOptionalSelectedAdderCalulate(Request $request) {
        $noOfPump = $request->no_of_pump;
        $ptpDistanceId = $request->ptp_distance_id;
        $systemPressure = $request->system_pressure;
        $mainfold = $request->manifold;
            // $codex = 62;
            $mechanicalLists = DB::table('main_mechanical_adder_lists')->get();
            $codes = explode(",", $request->mechanical_adder_ids); //Code ids
            $price = 0.00;
            $code60 = $request->code60;
            $code61 = $request->code61;
            $manifoldMaterialPi = $request->manifold_material_pi;
            
        if ($codes) {
            foreach ($codes as $code) {
                switch ($code) {
                    case ($code >= 53 && $code <= 57): //electrical_common_adder code
                        //id = $column
                        $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                        ->whereNotNull($code)->where($code, '!=', 0)->first();
                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        break;
                    
                    case (($code >= 58 && $code <= 59)) : // Range 2= standard , 3 = Premium
                        $cpRecords = DB::table('mechanical_adder_common')->select('id','brand_code', 'function_code', 'range')->whereNotNull($code)->where($code, '!=', 0)->first();
                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //code 58 price 25.29
                        //code 59 price 30.54
                        break;
                    case ($code == 60):  //electrical_common_adder_based_on_ampere code

                        if ($code60 && !empty($code60)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')->whereNotNull($code)->where($code, '!=', 0)->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        }
                        break;
                    case ($code == 61):  //electrical_common_adder_based_on_ampere code
                        if ($code61 && !empty($code61)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                            ->whereNotNull($code)->where($code, '!=', 0)->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']) * $code61;
                        }
                        break;

                    case ($code == 62):  //electrical_common_adder_based_on_ampere code
                        
                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Strainer" . " " . $variable;
                       
                        $get_vals = DB::table('mechanical_adder_common_strainer')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        //dd($desc,$get_vals);Strainer MANIFOLD-S-3P-21/2"
                        //Illuminate\Support\Collection {#2020 #items: []  }
                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;

                    case ($code == 63):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);

                        $desc = "Flexible connector" . " " . $variable;
                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();

                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;

                    case ($code == 64):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Flexible connector" . " " . $variable;

                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;
                    case ($code == 65 ):  //electrical_common_adder_based_on_ampere code
                        if ($request->code65 && !empty($request->code65)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code65)
                                    ->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        }
                        break;
                    case ($code == 66 ):  //electrical_common_adder_based_on_ampere code

                        if ($request->code66 && !empty($request->code66)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code66)
                                    ->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        }
                        break;

                    case ($code == 67 ):  //electrical_common_adder_based_on_ampere code

                        if ($request->code67 && !empty($request->code67)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code67)
                                    ->first();
                            
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        }
                        break;

                    case ($code == 68 ):  //electrical_common_adder_based_on_ampere code
                        $price += DB::table('setup_fields')->where('name', 'booster_adder_code_no_68')->pluck('value')[0];
                        break;
                    default: //default
                    break;
                }
            }
        }
        return ['mechanical_adder_price' => $price];
    }

    public function getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code) {
        $itemDescription = '';
        if ($systemPressure == 'PN16') {
            $tableName = 'booster_pn16_mechanical_component';
        } else {
            $tableName = 'booster_pn25_mechanical_component';
        }

        $columnName = $noOfPump . 'x' . $ptpDistanceId;
        if (Schema::hasColumn($tableName, $columnName)) {
            $cpRecords = DB::table($tableName)->select('item_description', $columnName, 'function_code', 'brand_code', 'range', 'unit_price')
                            ->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
            $arrayResult = json_decode(json_encode($cpRecords), true);
            foreach ($arrayResult as $key => $val) {
                if ($systemPressure == 'PN16') {
                    if ($mainfold == 'SS316' && $val['function_code'] == 65 && ($code == 62 || $code = 63)) { // Sunction
                        $itemDescription = $this->getStrainerQty($val['brand_code'], 69, $val['range']);
                    //
                    } elseif ($val['function_code'] == 65 && ($code == 62 || $code = 63)) {
                        $itemDescription = $this->getStrainerQty($val['brand_code'], $val['function_code'], $val['range']);
                        
                    }

                    if ($mainfold == 'SS316' && $val['function_code'] == 65 && $code == 64) { // Sunction
                        $itemDescription = $this->getStrainerQty($val['brand_code'], 71, $val['range']);
                    } elseif ($val['function_code'] == 65 && $code == 64) {
                        $itemDescription = $this->getStrainerQty($val['brand_code'], $val['function_code'], $val['range']); //Discharge
                    }
                } else { // Pn25
                    if ($mainfold == 'SS316' && $val['function_code'] == 66 && ($code == 62 || $code = 63)) { // Sunction
                        $itemDescription = $this->getStrainerQty($val['brand_code'], 70, $val['range']);
                    } else if ($val['function_code'] == 66 && ($code == 62 || $code = 63)) {
                        
                        $itemDescription = $this->getStrainerQty($val['brand_code'], $val['function_code'], $val['range']);
                    }

                    if ($mainfold == 'SS316' && $val['function_code'] == 68 && $code = 64) {
                        $itemDescription = $this->getStrainerQty($val['brand_code'], 72, $val['range']);

                    } else if ($val['function_code'] == 68 && $code = 64) {//Discharge
                        $itemDescription = $this->getStrainerQty($val['brand_code'], $val['function_code'], $val['range']);
                    }
                }
            }
        }
        // dd($itemDescription);
        return $itemDescription;
    }

    public function getStrainerQty($brand_code, $function_code, $range) {
        $masterData = DB::table('booster_master_sheet_mechanical_component')->select('description')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
                $desc = $masterData[0]->description;
                $first_break = explode('"', $desc)[0];
                $last = explode(" ", $first_break);
        return end($last);
    }

    //here mechanical price avaliable
    public function addToCart(Request $request) {
        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
			if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
			$BoosterCartData = $BoosterCartData->latest('id')->first();
        if(!empty($request->full_article_number) && $request->full_article_number != null)
        {
            $mechanical_article_number = $BoosterCartData->mechanical_article_number;
            $electrical_article_number = $BoosterCartData->electrical_article_number;
            $article_number = $BoosterCartData->article_number;
            $cp_id = DB::table('control_panels')->where('id','=',$BoosterCartData->cp_id)->first();
            $request->no_of_pump = $cp_id->no_of_pump_id;
            $request->pump_type = $BoosterCartData->pump_type;
            $request->booster_article_number = $BoosterCartData->booster_article_number;
            $request->article_number = $BoosterCartData->booster_article_number;
            $request->pump_model = $BoosterCartData->model_no;
            $request->power = $BoosterCartData->motor_power;
            $request->voltage = $BoosterCartData->supply_voltage;
            $request->manifold = $BoosterCartData->manifold;
            $request->system_pressure = $BoosterCartData->system_pressure;
            // $request->pump_unit_price =  $BoosterCartData->pump_price;
            $request->pump_unit_price = $this->getPumpDetailByType($BoosterCartData);

            if($request->pump_unit_price == null){
                $request->pump_unit_price = $BoosterCartData->pump_price;
            }
            $request->cp_id =  $BoosterCartData->cp_id;
            $request->adder_ids = $BoosterCartData->adder_ids;
            $request->mechanical_adder_ids = $BoosterCartData->mechanical_adder_ids;
        }
        $interCompanyMargin = User::ic_margin_booster(); // This is temporary
        $shippingPercentage = 10 / 100; //This percentage can be editable by admin
        $overHead = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $base_frame_size_constant = DB::table('setup_fields')->where('name', 'base_frame_size')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $cable_size_ampere_constant = DB::table('setup_fields')->where('name', 'cable_size_ampere_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $cable_length_constant = DB::table('setup_fields')->where('name', 'cable_length_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $spare_length = DB::table('setup_fields')->where('name', 'spare_length')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin

        $bill_of_mechanical = json_decode($request->mechanicalcomponent_billofmaterial, true);
        $adderIds = '';
        $mechanicalAdderIds = '';
        if(empty($request->full_article_number) && $request->full_article_number == null)
        {
            if ($request->adder_ids) {
                $adderIds = implode(",", $request->adder_ids);
            }
            if ($request->mechanical_adder_ids) {
                $mechanicalAdderIds = implode(",", $request->mechanical_adder_ids);
            }
        }
        else{
            if ($request->adder_ids) {
                $adderIds = $request->adder_ids;
            }
            if ($request->mechanical_adder_ids) {
                $mechanicalAdderIds = $request->mechanical_adder_ids;
            } 
        }

        if($request->adder_ids || $request->mechanical_adder_ids){
            $boosterCartData = BoosterCart::where('pump_type', $request->pump_type)
                    ->where('model_no', $request->pump_model)
                    ->where('motor_power', $request->power)
                    ->where('supply_voltage', $request->voltage)
                    ->where('manifold', $request->manifold)
                    ->where('system_pressure', $request->system_pressure)
                    ->where('pump_price', $request->pump_unit_price)
                    ->where('cp_id', $request->cp_id)
                    ->where('adder_ids', $adderIds)
                    ->where('mechanical_adder_ids', $mechanicalAdderIds)
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc');

            if($request->pump_type == "manually"){
                $boosterCartData = $boosterCartData->where('pump_price',$request->pump_unit_price);
            }

            $boosterCartData = $boosterCartData->first();
            if($boosterCartData == null || $request->qoutation_value != null){
                $boosterCart = new BoosterCart;

                $boosterCartData1 =  BoosterCart::where('pump_type', $request->pump_type)
                    ->where('model_no', $request->pump_model)
                    ->where('motor_power', $request->power)
                    ->where('supply_voltage', $request->voltage)
                    ->where('manifold', $request->manifold)
                    ->where('system_pressure', $request->system_pressure)
                    //->where('pump_price', $request->pump_unit_price)
                    ->where('cp_id', $request->cp_id)
                    ->where('adder_ids', $adderIds)
                    ->where('mechanical_adder_ids', $mechanicalAdderIds)
                    //->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc');

                    if($request->pump_type == "manually"){
                        $boosterCartData1 = $boosterCartData1->where('pump_price',$request->pump_unit_price);
                    }

                    $boosterCartData1 = $boosterCartData1->first();
					$new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($boosterCartData1){
					if($boosterCartData1->full_article_number != "" || $boosterCartData1->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $boosterCartData1->full_article_number);
								$boosterCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
					}
                }

                    //this if condition is for manual calculation...!!!

                    if($boosterCartData1 && empty($request->full_article_number) && $request->full_article_number == null)
                    {
                        $boosterCart->full_article_number = $boosterCartData1->full_article_number;
                        $boosterCart->article_number = $boosterCartData1->article_number;
                        $boosterCart->mechanical_article_number = $boosterCartData1->mechanical_article_number;
                        $boosterCart->electrical_article_number = $boosterCartData1->electrical_article_number;
                    }
                    //this if condition is for search calculation...!!!

                    if(!empty($request->full_article_number) && $request->full_article_number != null)
                    {
                        // $boosterCart->full_article_number = $request->full_article_number;
                        $boosterCart->full_article_number = $boosterCartData1->full_article_number;
                        $boosterCart->article_number = $article_number;
                        $boosterCart->mechanical_article_number = $mechanical_article_number;
                        $boosterCart->electrical_article_number = $electrical_article_number;
                    }

                $boosterCart->pump_type = $request->pump_type;
                $boosterCart->model_no = $request->pump_model;
                $boosterCart->booster_article_number = $request->article_number;
                //$boosterCart->article_number = $request->article_number;
                $boosterCart->motor_power = $request->power;
                $boosterCart->supply_voltage = $request->voltage;
                $boosterCart->manifold = $request->manifold;
                $boosterCart->system_pressure = $request->system_pressure;
                $boosterCart->pump_price = $request->pump_unit_price;
                $boosterCart->cp_id = $request->cp_id;
                $boosterCart->booster_overhead = $overHead;
                $boosterCart->inter_company_margin = $interCompanyMargin;
                $boosterCart->booster_price = $request->total_price;
                $boosterCart->standard_component_price = $request->standard_component_price;
                $boosterCart->mechanical_system_price = $request->mechanical_system_price;
                $boosterCart->cablePrice = $request->cablePrice;
                $boosterCart->ptp_distance_id = $request->ptp_distance_id;
                $boosterCart->base_frame_size_constant = $base_frame_size_constant;
                $boosterCart->cable_size_ampere_constant = $cable_size_ampere_constant;
                $boosterCart->cable_length_constant = $cable_length_constant;
                $boosterCart->spare_length = $spare_length;

                if($request->cp_price == null){
                    $request->cp_price = $BoosterCartData->cp_price;
                }
                $boosterCart->cp_price = $request->cp_price;
                $boosterCart->adder_ids = $adderIds;
                $boosterCart->total_adders_price = $request->code_price;
                $boosterCart->mechanical_adder_ids = $mechanicalAdderIds;
                $boosterCart->mechanical_total_adders_price = $request->mechanical_code_price;
                $boosterCart->price = $request->total_price;
                $boosterCart->total_price = $request->total_price;
                $boosterCart->qty = 1;
                $boosterCart->user_id = auth()->user()->id;
                $boosterCart->created_at = date("Y-m-d H:i:s");
                $boosterCart->updated_at = date("Y-m-d H:i:s");
				$boosterCart->country_origin = $request->country;
                $boosterCart->ksa_full_article_number = $new_ksa_article_number;
                $boosterCart->quotation_no = $request->qoutation_value;

                $boosterCart->mechanical_items_price = $request->mechanical_items_price + ($request->mechanical_code_price * $overHead) / $interCompanyMargin;
                $boosterCart->electrical_items_price = ($request->control_panel_price_for_booster * $overHead) / $interCompanyMargin + ($request->code_price * $overHead) / $interCompanyMargin;
                $boosterCart->save();
                $boosterCartId = $boosterCart->id;
            
                if (!empty($bill_of_mechanical)) {
                    foreach ($bill_of_mechanical as $data) {
                        $this->insertBoosterItem($boosterCartId, $data['brand_code'], $data['function_code'], $data['range'], $data['qty'], $data['price'], $data);
                    }
                }
                $this->insertItem($boosterCartId, $request->no_of_pump, $request->ptp_distance_id, $request->system_pressure, $request->manifold);

                //here123
                $this->getControlPanelDataItemSave($request->cp_id, $request, $boosterCartId);

                if ($request->mechanical_adder_ids) {
                    $this->boosterAddersData($boosterCartId, $request);
                }
            } else {
                if (empty($boosterCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {
                    if(!empty($request->full_article_number) && $request->full_article_number != null)
                    {
						$new_ksa_article_number = '';
                        if(auth()->user()->country_id == 6){
                            if($boosterCartData){
                                if($boosterCartData->full_article_number != "" || $boosterCartData->full_article_number != null){
									// Replace "683" with "339"
                                    if($request->country == "ksa"){
                                        $new_ksa_article_number = str_replace("683", "339", $boosterCartData->full_article_number);
                                    }
                                }
                            }
                        }
                        $boosterCart = $boosterCartData->replicate();
                        if(!empty($request->full_article_number) && $request->full_article_number != null)
                        {
                            $boosterCart->full_article_number = $request->full_article_number;
                            $boosterCart->article_number = $article_number;
                        }
                        $boosterCart->mechanical_total_adders_price  = $request->mechanical_code_price;
                        $boosterCart->price = $request->total_price;
                        $boosterCart->total_price = $request->total_price;
                        $boosterCart->qty = 1;
                        $boosterCart->quotation_no = $request->qoutation_value;

                        $boosterCart->mechanical_items_price = $request->mechanical_items_price;
                        $boosterCart->electrical_items_price = $request->electrical_items_price;

                        $boosterCart->save();
                    }
                    else{
                        //here1
                        $boosterCart = $boosterCartData->replicate();
                        // $boosterCart->quotation_no = null;
                        $boosterCart->quotation_no = $request->qoutation_value;
						$boosterCart->country_origin = $request->country;
                        $boosterCart->ksa_full_article_number = $new_ksa_article_number;
                        $boosterCart->qty = 1;
                        $boosterCart->save();
                    }
                    //here base frame
                    $boosterCartId = $boosterCart->id;
                    if (!empty($bill_of_mechanical)) {
                        foreach ($bill_of_mechanical as $data){
                            $this->insertBoosterItem($boosterCartId,  $data['brand_code'], $data['function_code'], $data['range'],  $data['qty'], $data['price'] , $data);
                        }
                    }

                    $this->insertItem($boosterCartId, $request->no_of_pump, $request->ptp_distance_id, $request->system_pressure, $request->manifold);
                    $this->getControlPanelDataItemSave($request->cp_id, $request, $boosterCartId);
                    if ($request->mechanical_adder_ids) {
                        $this->boosterAddersData($boosterCartId, $request);
                    }
                }
            }
        } else{
                $boosterCartData = BoosterCart::where('pump_type', $request->pump_type) 
                    ->where('model_no', $request->pump_model)
                    ->where('motor_power', $request->power)
                    ->where('supply_voltage', $request->voltage)
                    ->where('manifold', $request->manifold)
                    ->where('system_pressure', $request->system_pressure)
                    //->where('pump_price', $request->pump_unit_price)
                    ->where('cp_id', $request->cp_id)
                    ->whereNull('adder_ids')
                    ->whereNull('mechanical_adder_ids')
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc');
                    
                    if($boosterCartData != null && $request->pump_type == "manually"){
                        $boosterCartData = $boosterCartData->where('pump_price',$request->pump_unit_price);
                    }
                    
                    $boosterCartData = $boosterCartData->first();

                if($boosterCartData == null || $request->qoutation_value != null){
                    $boosterCart = new BoosterCart;
					$new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($boosterCartData){
								if($boosterCartData->full_article_number != "" || $boosterCartData->full_article_number != null){
                                if($request->country == "ksa"){
									$new_ksa_article_number = str_replace("683", "339", $boosterCartData->full_article_number);
                                    $boosterCart->ksa_full_article_number = $new_ksa_article_number;
								}
                            }
                        }
                    }
                    $boosterCartData1 = BoosterCart::where('pump_type', $request->pump_type) 
                    ->where('model_no', $request->pump_model)
                    ->where('motor_power', $request->power)
                    ->where('supply_voltage', $request->voltage)
                    ->where('manifold', $request->manifold)
                    ->where('system_pressure', $request->system_pressure)
                    //->where('pump_price', $request->pump_unit_price)
                    ->where('cp_id', $request->cp_id)
                    ->whereNull('adder_ids')
                    ->whereNull('mechanical_adder_ids')
                    ->orderBy('id', 'desc');

                     if($request->pump_type == "manually"){
                        $boosterCartData1 = $boosterCartData1->where('pump_price',$request->pump_unit_price);
                    }
                  
                    $boosterCartData1 = $boosterCartData1->first();

                    //this if condition is for manual calculation...!!!
                    if($boosterCartData1 && empty($request->full_article_number) && $request->full_article_number == null)
                    {
                        $boosterCart->full_article_number = $boosterCartData1->full_article_number;
                        $boosterCart->article_number = $boosterCartData1->article_number;
                        $boosterCart->mechanical_article_number = $boosterCartData1->mechanical_article_number;
                        $boosterCart->electrical_article_number = $boosterCartData1->electrical_article_number;
                    }
                    //this if condition is for search functionality...!!!
                    if(!empty($request->full_article_number) && $request->full_article_number != null)
                    {
                        $boosterCart->full_article_number = $request->full_article_number;
                        $boosterCart->article_number = $article_number;
                        $boosterCart->mechanical_article_number = $mechanical_article_number;
                        $boosterCart->electrical_article_number = $electrical_article_number;
                    }

                    //Bare SHAFT dATA
                    $boosterCart->pump_type = $request->pump_type;
                    $boosterCart->model_no = $request->pump_model;
                    $boosterCart->booster_article_number = $request->article_number;
                    // $boosterCart->article_number = $request->article_number;
                    $boosterCart->motor_power = $request->power;
                    $boosterCart->supply_voltage = $request->voltage;
                    $boosterCart->manifold = $request->manifold;
                    $boosterCart->system_pressure = $request->system_pressure;
                    $boosterCart->pump_price = $request->pump_unit_price;
                    $boosterCart->cp_id = $request->cp_id;
                    $boosterCart->booster_overhead = $overHead;
                    $boosterCart->inter_company_margin = $interCompanyMargin;
                    $boosterCart->booster_price = $request->total_price;
                    $boosterCart->standard_component_price = $request->standard_component_price;
                    $boosterCart->mechanical_system_price = $request->mechanical_system_price;
                    $boosterCart->cablePrice = $request->cablePrice;
                    $boosterCart->ptp_distance_id = $request->ptp_distance_id;
                    $boosterCart->base_frame_size_constant = $base_frame_size_constant;
                    $boosterCart->cable_size_ampere_constant = $cable_size_ampere_constant;
                    $boosterCart->cable_length_constant = $cable_length_constant;
                    $boosterCart->spare_length = $spare_length;
                    $boosterCart->quotation_no = $request->qoutation_value;
                    $boosterCart->price = $request->total_price;
                    $boosterCart->total_price = $request->total_price;
                    $boosterCart->qty = 1;
                    $boosterCart->user_id = auth()->user()->id;
                    $boosterCart->created_at = date("Y-m-d H:i:s");
                    $boosterCart->updated_at = date("Y-m-d H:i:s");
					$boosterCart->country_origin = $request->country;
                    $boosterCart->ksa_full_article_number = $new_ksa_article_number;

                    $boosterCart->mechanical_items_price = $request->mechanical_items_price;
                    $boosterCart->electrical_items_price = $request->electrical_items_price;

                    $boosterCart->save();
                    $boosterCartId = $boosterCart->id;
                    if (!empty($bill_of_mechanical)) {
                        foreach ($bill_of_mechanical as $data) {
                            $this->insertBoosterItem($boosterCartId, $data['brand_code'], $data['function_code'], $data['range'],  $data['qty'], $data['price'], $data);
                        }
                    }
                $this->insertItem($boosterCartId, $request->no_of_pump, $request->ptp_distance_id, $request->system_pressure, $request->manifold);
                $this->getControlPanelDataItemSave($request->cp_id, $request, $boosterCartId);
            } else {
                if(empty($boosterCartData->quotation_no)){
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                }else{
					$new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($boosterCartData){
						if($boosterCartData->full_article_number != "" || $boosterCartData->full_article_number != null){
                                // Replace "683" with "339"
                                if($request->country == "ksa"){
								$new_ksa_article_number = str_replace("683", "339", $boosterCartData->full_article_number);
                                }
                            }
                        }
                    }
                    $boosterCart = $boosterCartData->replicate();
                   
                    if($request->pump_type == "manually"){
                        $boosterCart->pump_price = $request->pump_unit_price;
                    }

                    // here
                    if(!empty($request->full_article_number) && $request->full_article_number != null)
                    {
                        // $boosterCart->full_article_number = $request->full_article_number;
                        $boosterCart->full_article_number = $boosterCartData->full_article_number;
                        $boosterCart->article_number = $article_number;
                    }

                    // $boosterCart->quotation_no = null;
                    $boosterCart->quotation_no = $request->qoutation_value;
                    $boosterCart->qty = 1;
					$boosterCart->country_origin = $request->country;
                    $boosterCart->ksa_full_article_number = $new_ksa_article_number;
                    $boosterCart->save();
                    $boosterCartId = $boosterCart->id;
                    //here
                    if (!empty($bill_of_mechanical)) {
                        foreach ($bill_of_mechanical as $data) {
                            $this->insertBoosterItem($boosterCartId,$data['brand_code'],$data['function_code'],$data['range'],$data['qty'],$data['price'],$data);
                        }
                    }
                    // $data = $this->getBoosterDataItemSave($request, $boosterCartId);
                    $this->insertItem($boosterCartId, $request->no_of_pump, $request->ptp_distance_id, $request->system_pressure, $request->manifold);
                    $this->getControlPanelDataItemSave($request->cp_id, $request, $boosterCartId);
                }
            }
        }
        if($request->qoutation_value != null || !empty($request->qoutation_value)){
            app('App\Http\Controllers\Frontend\QuotationController')->AddQuotationWithBooster($request->qoutation_value,$request->total_price,$boosterCart->id);
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }

    public function insertItem($booster_cart_id, $noOfPump, $ptpDistanceId, $systemPressure, $mainfold) {
        $price = 0.00;
        if ($systemPressure == 'PN16') {
            $tableName = 'booster_pn16_mechanical_component';
        } else {
            $tableName = 'booster_pn25_mechanical_component';
        }
        $columnName = $noOfPump . 'x' . $ptpDistanceId;
        if (Schema::hasColumn($tableName, $columnName)) {
            
            // $cpRecords = DB::table($tableName)->select('item_description', 'brand_code', 'function_code', 'range', 'unit_price','wilo_article_no', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->get();

            //
            $cpRecords = DB::table($tableName)->select('item_description', 'brand_code', 'function_code', 'range', 'unit_price','wilo_article_no', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
            //

            $arrayResult = json_decode(json_encode($cpRecords), true);

            foreach ($arrayResult as $key => $val) {
                if ($systemPressure == 'PN16') {
                    if ($mainfold == 'SS316' && $val['function_code'] == 65) { // Sunction
                        
                        $price = $this->getMasterSheetPriceData($val['brand_code'], 69, $val['range']); //Qty * price // 2 parameter is equal to brand code
                        $this->insertBoosterItem($booster_cart_id, $val['brand_code'], 69, $val['range'], $val['range'], $val[$columnName], $price, $val);
                    } else if ($mainfold == 'SS316' && $val['function_code'] == 67) { //Discharge
                        
                        $price = $this->getMasterSheetPriceData($val['brand_code'], 71, $val['range']); //Qty * price
                        $this->insertBoosterItem($booster_cart_id, $val['brand_code'], 71, $val['range'], $val[$columnName], $price, $val);
                    } else {
                        
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']); //Qty * price
                        $this->insertBoosterItem($booster_cart_id, $val['brand_code'], $val['function_code'], $val['range'], $val[$columnName], $price,  $val);
                    }
                } else { // Pn25
                    if ($mainfold == 'SS316' && $val['function_code'] == 66) { // Sunction
                        
                        $price = $this->getMasterSheetPriceData($val['brand_code'], 70, $val['range']); //Qty * price // 2 parameter is equal to brand code
                        $this->insertBoosterItem($booster_cart_id, $val['brand_code'], 70, $val['range'], $val[$columnName], $price, $val);
                    } else if ($mainfold == 'SS316' && $val['function_code'] == 68) { //Discharge
                        if ($this->getMasterSheetPriceData($val['brand_code'], 72, $val['range'])) {
                            
                            $price = $this->getMasterSheetPriceData($val['brand_code'], 72, $val['range']); //Qty * price
                            $this->insertBoosterItem($booster_cart_id,$val['brand_code'], 72, $val['range'], $val[$columnName], $price, $val);
                        } else {

                            $price = 0.00;
                        }
                    } else {
                       //change:- Riddhi Patva
                       //Date :- 24-3-2022
                       //old code is in comment
                        // $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                        
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];  //Qty * price
                        $this->insertBoosterItem($booster_cart_id, $val['brand_code'], $val['function_code'], $val['range'], $val[$columnName], $price, $val);
            // echo "Normal  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
                    }
                }
            }
        }
        return $price;
    }

    public function insertBoosterItem($booster_cart_id,$brand_code, $function_code, $range, $columnName, $price, $val = []) {
        $boosterItem = new BoosterItems;
        $boosterItem->booster_cart_id = $booster_cart_id;

        //        if($brand_code == null && $function_code == null && $range == null){
        //            $boosterItem->item_description = $val['item_description'];
        //            $boosterItem->wilo_artilce_no = $val['wilo_artilce_no'];
        //
        //        }
        //        else{
            $boosterItem->item_description = $this->getMasterSheetBoosterItemDescription($brand_code, $function_code, $range);
            // $boosterItem->wilo_artilce_no = $val['wilo_article_no'] ?? '';

            $boosterItem->wilo_artilce_no = $this->getMasterSheetBoosterItemWiloArticleNo($brand_code, $function_code, $range);
        //        }

        $boosterItem->material_number = '';
        $boosterItem->weight = '';
        $boosterItem->height = '';
        $boosterItem->width = '';
        $boosterItem->depth = '';
        $boosterItem->brand_code = $brand_code;
        $boosterItem->function_code = $function_code;
        $boosterItem->ranges = $range;
        $boosterItem->qty = $columnName;

        // $boosterItem->price = $price;

        $boosterItem->price = $this->getMasterSheetBoosterItemPrice($brand_code, $function_code, $range);

        $boosterItem->total_price = $boosterItem->price * $columnName;
        $boosterItem->save();
    }

    public function ajaxQtyUpdate(Request $request) {
        $qty = $request->qty;
        $boosterCartId = $request->booster_cart_id;
        $boosterUpdate = BoosterCart::find($boosterCartId);
        $boosterUpdate->qty = $qty;
        $boosterUpdate->total_price = $boosterUpdate->qty * $boosterUpdate->price;
        $boosterUpdate->save();
        $data['id'] = $boosterCartId;
        $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $boosterUpdate->price);
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function removeCart($id){
        $deleteBoosterCart = BoosterCart::where('id', $id)->delete();
        $deleteItem = BoosterItems::where('booster_cart_id', $id)->delete();
    }

    public function cartItems($cartId, $returnDataOnly = false) { //$val is itemData
        $adderData = [];
        $boosterCartData = BoosterCart::with('boosterCpData')->where('id',$cartId)->first();

        $items = BoosterItems::where('booster_cart_id', $cartId)->with('boosterCart')->get();

        $cpBoosterItems = BoosterCpItems::where('booster_cart_id', $cartId)->with('boosterCart')->orderBy('adder_code')->get();

        if($returnDataOnly) {
            return [
                'adderData' => $adderData,
                'boosterCartData' => $boosterCartData,
                'items' => $items,
                'cpBoosterItems' => $cpBoosterItems,
                'cartId' => $cartId,
            ];
        }
        
        return view('frontend.booster.items', compact('items', 'cpBoosterItems','boosterCartData'));
    }

    public function getControlPanelDataItemSave($cpId, $request, $boosterCartId) {
        $controlPanelData = new ControlPanel();
        $controlPanelData = ControlPanel::where('id', '=', $cpId)
                ->with('noofpumps')
                ->with('powers')
                ->with('voltages')
                ->with('applications')
                ->with('ambienttemps')
                ->with('startertypes')
                ->with('components')
                ->with('ranges')
                ->with('enclousres')
                ->with('comunicationprotocols')
                ->with('ipratings')
                ->get();
        $numberOfPump = $controlPanelData[0]->noofpumps['value'];
        if (ControlPanel::isIntegerColumn($controlPanelData[0]->powers['value'])) {
            $power = $controlPanelData[0]->powers['value'];
            $voltage = $controlPanelData[0]->voltages['value'];
            $columnName = $numberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
        } else {
            $power = str_replace(".", '__', $controlPanelData[0]->powers['value']);
            $voltage = $controlPanelData[0]->voltages['value'];
            $columnName = $numberOfPump . 'x' . $power . "kwx" . $voltage . 'v';
        }

        $tableName = $controlPanelData[0]['table_name'];
        $starterCode = $controlPanelData[0]['starter_code'];
        $cpRecordsData = [];
        $returnHTML = '';
        $price = 0.00;

        if (Schema::hasTable($tableName)) {
        //   DB::enableQueryLog();
            if (Schema::hasColumn($tableName, $columnName)) {
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
                if ($request->adder_ids) {
                    $price = $this->addersData($request, $boosterCartId);
                    $enclousreAdderItemData = $request->enclousreItem;
                }
                if ($arrayResult) {
                    $i= 1;
                    if($request->enclosure == null)
                    {
                        $request->enclosure = $controlPanelData[0]->enclosure_id;
                    }
                    foreach ($arrayResult as $key => $val) {
                        $price = $this->calculateControlPanelBomInItem($request, $columnName, $boosterCartId, $enclousreAdderItemData,$i,$cpRecords1,$val);
                        $i++;

                        //Before go the price calculation, Brand code “1” should be replaced by “2” and get the relevant the description and article number, weight and price from Master sheet.
                    }

                    $tax = Tax::where('id', 1)->get()[0]->amount;
                    return ['price' => $price, 'starter_code' => $starterCode, 'range' => $controlPanelData[0]->range];
                }
            }
        }
        return;
    }

    public function calculateControlPanelBomInItem($request, $columnName, $boosterCartId, $enclousreAdderItemData,$i,$enclosure_count,$val = []) {
        $price = 0.00;
        if ($enclousreAdderItemData && !empty($enclousreAdderItemData) && $request->enclosure != 3 && $request->enclosure != 4) {
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                if($i==1){
                    $val['range'] = $enclousreItem['range'];
                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
                }
            }
        }
            if($enclousreAdderItemData && !empty($enclousreAdderItemData && $request->enclosure == 3))  
            {
                $enclousreItem = json_decode($request->enclousreItem, true);
                if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                    if($enclosure_count == "1")
                    {
                        $val['range'] = $enclousreItem['range'];
                    }
                    $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                    $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                    if($i==1){
                        // $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
                            }
                }
            }
            
            if($enclousreAdderItemData && !empty($enclousreAdderItemData && $request->enclosure == 4))  
            {
                $enclousreItem = json_decode($request->enclousreItem, true);
                if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                    if($enclosure_count == "1")
                    {
                        $val['range'] = $enclousreItem['range'];
                    }
                    $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                    $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                    if($i==1){
                        // $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
                            }
                }
            }

        if ($request->component == 2 && $val['brand_code'] == 1) { // component 2 =  Economic
            if ($this->getMasterSheetPriceCPData(2, $val['function_code'], $val['range'])) {
                $price = $this->getMasterSheetPriceCPData(2, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceCPData(2, $val['function_code'], $val['range']);
                $this->itemSave(2, $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
                    //                echo "change" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceCPData(2, $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
                    }
                    
            else {
                $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);

            //                echo "If condition fail choose brand code 1 **" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
            }
        } else if ($request->enclosure == 3 && $val['brand_code'] == 5 && $val['function_code'] == 1) {
            if ($this->getMasterSheetPriceCPData(31, 63, $val['range'])) {
                $price = $this->getMasterSheetPriceCPData(31, 63, $val['range']) * $val[$columnName]; //Qty * price
                $unitPrice = $this->getMasterSheetPriceCPData(31, 63, $val['range']);
                $this->itemSave(31, 63, $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
        //                echo "Encloure  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceCPData(31, 63, $val['range']) * $val[$columnName] . "</br>";
            }
        //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else if ($request->enclosure == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1) {
         //4 equal Stainless
            if ($this->getMasterSheetPriceCPData(5, 64, $val['range'])) {
            //                echo $val['range'];
                $price = $this->getMasterSheetPriceCPData(5, 64, $val['range']) * $val[$columnName]; //Qty * price
                $unitPrice = $this->getMasterSheetPriceCPData(5, 64, $val['range']);
                $this->itemSave(5, 64, $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
            //                echo "stainless  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceCPData(5, 64, $val['range']) * $val[$columnName] . "</br>";
            }
            //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        }
        //SLIDE 11
        else if ($request->enclosure == 2 && $request->stater_type == 1 && $val['brand_code'] == 8) {
            //2 equal META; 1 XTREME
            if ($this->getMasterSheetPriceCPData(32, $val['function_code'], $val['range'])) {
            //                echo $val['range'];
                $price = $this->getMasterSheetPriceCPData(32, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                $unitPrice = $this->getMasterSheetPriceCPData(32, $val['function_code'], $val['range']);
                $this->itemSave(32, $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
            //                echo "stainless  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceCPData(5, 64, $val['range']) * $val[$columnName] . "</br>";
            }
            //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else {
            if ($enclousreAdderItemData && !empty($enclousreAdderItemData)) {
                $enclousreItem = json_decode($request->enclousreItem, true);
                if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code'] && $val['range'] == $enclousreItem['range']) {

                } else {
                    $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                    $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
                }
            } else {
                $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice,$val);
            }
        //            $price = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
        //            $unitPrice = $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']);
        //            $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $boosterCartId, $price, $unitPrice);
                    // echo "Normal  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceCPData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
        }
        return $price;
    }

    public function itemSave($brand_code, $function_code, $range, $columnName, $boosterCartId, $totalPrice, $unitPrice,$val, $adderCode = null, $noOfPump = null) {
        $item = new BoosterCpItems; 
        if ($this->cpitemDescription($brand_code, $function_code, $range) && $this->cpitemDescription($brand_code, $function_code, $range) != '') {
            $item->item_description = $this->cpitemDescription($brand_code, $function_code, $range);
        } else {
            $item->item_description = $val['item_description'];
        }
        $item->booster_cart_id = $boosterCartId;
        $item->material_number = $this->getMasterSheetMaterialNumber($brand_code, $function_code, $range);
        $item->wilo_artilce_no = $this->getArticleNumberBySheetControlPanel($brand_code, $function_code, $range);
        $item->weight = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $item->brand_code = $brand_code;
        $item->function_code = $function_code;
        $item->ranges = $range;
        $item->price = $unitPrice;
        $item->total_price = $totalPrice;
        $item->margin = str_replace('_', '', $val['margin']);
        //
        $item->height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $item->width = $this->getMasterSheetWidth($brand_code, $function_code, $range);
        $item->depth = $this->getMasterSheetDepth($brand_code, $function_code, $range);
        if ($adderCode) {
            $item->adder_code = $adderCode;
            if ($noOfPump) {               
                $item->qty = $columnName * $noOfPump;
            } else {
                $item->qty = $columnName;
            }
        } else {
            $item->qty = $val[$columnName];
        }
        $item->save();
        // echo '<pre>';print_r($item);echo '</pre>';
    }

    public function addersData(Request $request, $boosterCartId) {
        $noOfPump = $request->no_of_pump;
        $motorPower = $request->power;
        $voltage = $request->voltage;
        if(is_array($request->adder_ids)){
            $ids = explode(",", implode(",", $request->adder_ids)); //Code ids
        }
        else{
            $ids = explode(",",$request->adder_ids);
        }
        $price = 0.00;
        $encloureArea = 0.00;
        $component = $request->component;
        if ($ids) {
            foreach ($ids as $id) {
                switch ($id) {
                    case ($id >= 1 && $id <= 26): //electrical_common_adder code
                        //id = $column
                        $electricalCommonAdders = DB::table('electrical_common_adder')->select('id', 'item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $id)
                                        ->whereNotNull($id)->where($id, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                            // $val['brand_code'] = "1"
                            // $val['function_code'] = "6"
                            // $val['range'] = "6"
                            // $val = array:11 [
                            //   "id" => 4
                            //   "item_description" => "S203M-C 6   Mini Circuit Breaker"
                            //   "material_number" => "2CDS273001R0064"
                            //   "wilo_article_number" => ""
                            //   "brand_code" => "1"
                            //   "function_code" => "6"
                            //   "range" => "6"
                            //   "weight" => ""
                            //   "height" => ""
                            //   "margin" => ""
                            //   1 => 1
                            // ]
                            // $val[$id] = 1
                            // $boosterCartId = 5566
                            // $price = 17.711171662125
                            // $unitPrice = 17.711171662125
                            // $id = "1"
                                // $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$id], $boosterCartId, $price, $unitPrice, $id);
                                $this->itemSave($val['brand_code'],$val['function_code'] , $val['range'], $val[$id], $boosterCartId, $price, $unitPrice, $val, $id);
                            }
                        }

                        break;
                    case ($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code

                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'a';

                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,$column);
                            }
                        }
                        break;
                    case ($id >= 37 && $id <= 44):  //electrical_adder_per_pump code

                        $column = $id . 'x1';

                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                    $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice, $val, $column, $noOfPump);
                            }
                        }
                        break;
                    case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        if ($id >= 45 && $id <= 52) {
                            $column = $id . 'x' . $nearestColumn . 'ax1';
                        } else {
                            $column = $id . 'x' . $nearestColumn . 'ax2';
                        }
                        
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($id >= 45 && $id <= 52) 
                                {
                                    if ($component == 2 && $val['brand_code'] == 1)
                                    {
                                        $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                        if($exist)
                                        {
                                            $val['brand_code'] = 2;
                                        }
                                            $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                            $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                            $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                            $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,  $column, $noOfPump);
                                    }
                                    else
                                    {
                                        $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                        $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);

                                        $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];

                                        $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,  $column, $noOfPump);
                                    }
                                } 
                             
                             else {
                                    $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                    $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,  $column, $noOfPump);
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
        return $price;
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

    public function getMasterSheetMaterialNumber($brand_code, $function_code, $range) {

        $masterData = DB::table('master_price_sheet_electrical_components')->select('material_number')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->material_number)) {
            return $masterData[0]->material_number;
        }

        return 0;
    }

    public function cpitemDescription($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('description')
            ->where('brand_code', $brand_code)
            ->where('function_code', $function_code)
            ->where('range', $range)
            ->get();

        if (isset($masterData[0]->description)) {
            return $masterData[0]->description;
        }

        return '';
    }
    
    public function getMasterSheetHeightMultiplyByWidth($brand_code, $function_code, $range) {

        $height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $width = $this->getMasterSheetWidth($brand_code, $function_code, $range);

        if ($height && $width) {
            return $height * $width;
        }

        return 0;
    }

    public function getMasterSheetBoosterItemDescription($brand_code, $function_code, $range) {

        $masterData = DB::table('booster_master_sheet_mechanical_component')->select('description')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->description)) {
            return $masterData[0]->description;
        }
        return 0;
    }

    public function getMasterSheetBoosterItemWiloArticleNo($brand_code, $function_code, $range) {

        $masterData = DB::table('booster_master_sheet_mechanical_component')->select('wilo_article_no')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->wilo_article_no)) {
            return $masterData[0]->wilo_article_no;
        }

        return 0;
    }

    public function getMasterSheetBoosterItemPrice($brand_code, $function_code, $range) {

        $masterData = DB::table('booster_master_sheet_mechanical_component')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->price)) {
            return $masterData[0]->price;
        }

        return 0;
    }

    public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
        DB::enableQueryLog(); // Enable query log
        $voltage_value = $voltage;
        $motor_power_value = $motorPower;
        $noOfPump = $this->getValueById('App\NumberOfPump', 'id', $noOfPump);
        $voltage = $this->getValueById('App\Voltage', 'id', $voltage);
        if($voltage == false)
        {
            $voltage = $voltage_value;
        }
        $motorPower = $this->getValueById('App\Power', 'id', $motorPower);
        if($motorPower == false)
        {
            $motorPower = $motor_power_value;
        }
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

    public function getMasterSheetElectricalPriceData($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->price)) {
            return (float) $masterData[0]->price;
        }

        return 0;
    }

    public function boosterAddersData($booster_cart_id, $request) {
        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
            if(auth()->user()->country_id == 6){
			$BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
            }
            $BoosterCartData = $BoosterCartData->first();
        if(!empty($request->full_article_number) && $request->full_article_number != null)
        {
            $cp_id = DB::table('control_panels')->where('id','=',$BoosterCartData->cp_id)->first();
            $request->ptp_distance_id = $BoosterCartData->ptp_distance_id;
            $request->no_of_pump = $cp_id->no_of_pump_id;
            $request->pump_type = $BoosterCartData->pump_type;
            $request->article_number = $BoosterCartData->article_number;
            $request->pump_model = $BoosterCartData->model_no;
            $request->power = $BoosterCartData->motor_power;
            $request->voltage = $BoosterCartData->supply_voltage;
            $request->manifold = $BoosterCartData->manifold;
            $request->system_pressure = $BoosterCartData->system_pressure;
            $request->pump_unit_price =  $BoosterCartData->pump_price;
            $request->cp_id =  $BoosterCartData->cp_id;
            $request->adder_ids = $BoosterCartData->adder_ids; //electrical adder ids
            $request->mechanical_adder_ids = $BoosterCartData->mechanical_adder_ids;

            $code60 = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','60')
                            ->value('qty');

            $code61 = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','61')
                            ->value('qty');
            $code65_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','65')
                            ->value('item_description');

            $code66_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','66')
                            ->value('item_description');

            $code67_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','67')
                            ->value('item_description');

            $code65 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '65')
                                ->whereNotNull('65')
                                ->where('65','!=','')
                                ->where('65','!=','0')
                                ->where('item_description','=',$code65_desc)
                                ->value('id');

            $code66 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '66')
                                ->whereNotNull('66')
                                ->where('66','!=','')
                                ->where('66','!=','0')
                                ->where('item_description','=',$code66_desc)
                                ->value('id');

            $code67 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '67')
                                ->whereNotNull('67')
                                ->where('67','!=','')
                                ->where('67','!=','0')
                                ->where('item_description','=',$code67_desc)
                                ->value('id');

            $request->code65 = $code65;
            $request->code66 = $code66;
            $request->code67 = $code67;
            
        }
        $noOfPump = $request->no_of_pump;
        $ptpDistanceId = $request->ptp_distance_id;
        $systemPressure = $request->system_pressure;
        $mainfold = $request->manifold;
        $codes = '';
        if(empty($request->full_article_number) && $request->full_article_number == null)
        {
            if ($request->mechanical_adder_ids) {
                $codes = explode(",", implode(",", $request->mechanical_adder_ids));
            }
        }
        else{
            if ($request->mechanical_adder_ids) {
                $codes = explode(",",$request->mechanical_adder_ids);
            } 
        }
        $price = 0.00;
        if($request->full_article_number == null){
            $code60 = $request->code60;
            $code61 = $request->code61;
        }
        if($codes){
            foreach($codes as $code){
                switch ($code) {
                    case ($code >= 53 && $code <= 57): //electrical_common_adder code
                        //id = $column
                        $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                        ->whereNotNull($code)->where($code, '!=', 0)->first();

                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        break;


                    case (($code >= 58 && $code <= 59)) : // Range 2= standard , 3 = Premium
                        $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                        ->whereNotNull($code)->where($code, '!=', 0)->first();

                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        break;

                    case ($code == 60):  //electrical_common_adder_based_on_ampere code
                        if($request->full_article_number != null){
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                            ->whereNotNull($code)->where($code, '!=', 0)->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code, $code60);
                        }
                        else{
                            if($code60 && !empty($code60)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                            ->whereNotNull($code)->where($code, '!=', 0)->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code, $code60);
                        }
   
                        }
                        break;

                    case ($code == 61):  //electrical_common_adder_based_on_ampere code
                        if ($code61 && !empty($code61)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                            ->whereNotNull($code)->where($code, '!=', 0)->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change remove * $code61 from below price formula
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code, $code61);
                        }
                        break;

                    case ($code == 62):  //electrical_common_adder_based_on_ampere code
                        // $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                        //                 ->whereNotNull($code)->where($code, '!=', 0)->first();
                        // $arrayResult = json_decode(json_encode($cpRecords), true);
                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Strainer" . " " . $variable;

                        $get_vals = DB::table('mechanical_adder_common_strainer')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        dd($desc,$get_vals);
                        $price = $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;

                    case ($code == 63):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);

                        $desc = "Flexible connector" . " " . $variable;
                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();

                        $price = $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;

                    case ($code == 64):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Flexible connector" . " " . $variable;

                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        $price = $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;
                    
                    case ($code == 65 ):  //mechanical_adder_common_pressure_vessel code
                        //if ($request->code65 && !empty($request->code65)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code65)
                                    ->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change:- remove * $code61 from below price formula
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        //}
                        break;
                    case ($code == 66 ):  //mechanical_adder_common_pressure_vessel code

                        if ($request->code66 && !empty($request->code66)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code66)
                                    ->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                             //date : 8-4-2022 change remove * $code61 from below price formula
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        }
                        break;

                    case ($code == 67 ):  //electrical_common_adder_based_on_ampere code
                        if ($request->code67 && !empty($request->code67)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code67)
                                    ->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change remove * $code61 from below price formula
                            $price = $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        }
                        break;

                    case ($code == 68): 
                        //in admin
                        $price = DB::table('setup_fields')->where('name', 'booster_adder_code_no_68')->pluck('value')[0];
                        $this->insertAdderBoosterItem($booster_cart_id, 0, 0, 0, $price, $code);
                        break;
                    default: //default
                    break;
                }
            }
        }
        return ['mechanical_adder_price' => $price];
    }

    public function insertAdderBoosterItem($booster_cart_id, $brand_code, $function_code, $range, $price, $code, $qty = 1) {
        $boosterItem = new BoosterItems;
        $boosterItem->booster_cart_id = $booster_cart_id;

        $boosterItem->item_description = $this->getMasterSheetBoosterItemDescription($brand_code, $function_code, $range);

        $boosterItem->material_number = '';
        $boosterItem->wilo_artilce_no = '';
        $boosterItem->weight = '';
        $boosterItem->height = '';
        $boosterItem->width = '';
        $boosterItem->depth = '';
        $boosterItem->brand_code = $brand_code;
        $boosterItem->function_code = $function_code;
        $boosterItem->ranges = $range;
        $boosterItem->adder_code = $code;
        $boosterItem->qty = $qty;
        $boosterItem->price = $price;
        $boosterItem->total_price = $price * $qty;

        $boosterItem->save();
    }

    public function getArticleNumberBySheetControlPanel($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('wilo_artilce_no')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();

        if (isset($masterData[0]->wilo_artilce_no)) {
            return $masterData[0]->wilo_artilce_no;
        }
        return '';
    }

    public function ajaxDetailModalBooster(Request $request) {
        $addersData = [];
        $mechanical_addersData=[];
        $cpId = $request->booster_id;
        $response = array();

        $controlPanelData = BoosterCart::where('id', $cpId)->with(['BoosterCPdata' => function ($query) {
                                $query->with('noofpumps')
                                ->with('powers')
                                ->with('voltages')
                                ->with('applications')
                                ->with('ambienttemps')
                                ->with('startertypes')
                                ->with('components')
                                ->with('ranges')
                                ->with('enclousres')
                                ->with('comunicationprotocols')
                                ->with('ipratings');
                            }])
                        ->get()[0];
        if(!empty($controlPanelData->adder_ids) && $controlPanelData->adder_ids != null) {
            $adderIds = explode(",", $controlPanelData->adder_ids);
            
            $addersData = DB::table('main_electrical_list')->select('id','adder_list')
            ->whereIn('id', $adderIds)->get();
        }
        if(!empty($controlPanelData->mechanical_adder_ids) && $controlPanelData->mechanical_adder_ids != null){
            $adderIds = explode(",", $controlPanelData->mechanical_adder_ids);

            $mechanical_addersData = DB::table('main_mechanical_adder_lists')
                                    ->select('adder_list','code')
                                    ->whereIn('code', $adderIds)->get();

            foreach($mechanical_addersData as $val)
            {
                $data = array();
                $data['adder_list'] = $val->adder_list;
                $data['code'] = $val->code;

                $mechanical_Data = DB::table('booster_items')
                ->select('booster_cart_id','item_description','adder_code','qty')
                ->where('booster_cart_id',$cpId)
                ->whereIn('adder_code',  $adderIds)->get();

                    foreach($mechanical_Data as $value)
                    {   
                        if($val->code == $value->adder_code)
                        {
                            $data['adder_code'] = $value->adder_code;
                            $data['item_description'] = $value->item_description;
                            $data['qty'] = $value->qty;
                        }
                    }
                array_push($response,$data);
            }
        }
        $controlPanelData->BoosterCPdata[0]->powers->value = $controlPanelData->BoosterCPdata[0]->powers->value . " Kw";
        $controlPanelData->BoosterCPdata[0]->voltages->value = $controlPanelData->BoosterCPdata[0]->voltages->value . " V";
        $controlPanelData->BoosterCPdata[0]->ambienttemps->value = $controlPanelData->BoosterCPdata[0]->ambienttemps->value . " °C";
        $returnHTML = view('frontend.cart.detail_modal_booster')->with('controlPanelData', $controlPanelData)
                ->with('addersData', $addersData)
                // ->with('mechanical_addersData', $mechanical_addersData)
                ->with('response', $response)
                ->render();
        $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }

    //Added for search ensloure area formula starts..!!
    public function getControlPanelItemEnclousreAreaFormula($tableName, $columnName, $totalEnclousreArea) {
        $enclousreItem = null;
        // $totalEnclousreArea = 70000;
        $nextSize = true;
        if (Schema::hasTable($tableName)) {
            if (Schema::hasColumn($tableName, $columnName)) {
                $cpRecords = DB::table($tableName)->select('id', 'item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                        ->whereNotNull($columnName)->where($columnName, '!=', 0)
                        ->where('item_description', 'like', '%Enclosure%')
                        ->get();

                $arrayResult = json_decode(json_encode($cpRecords), true);


                if ($arrayResult) {  //  $enclousre area  drop down item check with qty
                    $i = 1;
                    foreach ($arrayResult as $key => $val) {

                        $range = $val['range'];
                        $sizeMeet = AdderHelper::enclosureAreaExist($range, $totalEnclousreArea);

                        if ($sizeMeet) {

                            if ($i == 1) {

                                $enclousreItem = $val;
                            }
                            $i++;
                        }
                    }
                }

                if (!$enclousreItem) { // if $enclousre area does not meet in drop down item check without qty (blank)
                    $cpRecords = DB::table($tableName)->select('id', 'item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                            ->where('item_description', 'like', '%Enclosure%')
                            ->get();

                    $arrayResult = json_decode(json_encode($cpRecords), true);
                    if ($arrayResult) {
                        $i = 1;
                        foreach ($arrayResult as $key => $val) {

                            $range = $val['range'];
                            $sizeMeet = AdderHelper::enclosureAreaExist($range, $totalEnclousreArea);

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

    //Added for search ensloure area formula ends..!!
    //search factor
    public function searchCalculateMechanicalComponent(Request $request) {
        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
        if(auth()->user()->country_id == 6){
		$BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $BoosterCartData = $BoosterCartData->latest('id')->first();
        $pump_unit_price = $this->getPumpDetailByType($BoosterCartData);

        if($pump_unit_price == NULL){
            $pump_unit_price = $BoosterCartData->pump_price;
        }

        $pump_height = DB::table('booster_full_pump_price')
        ->select('pump_height')
        ->where('pump_article_no_helix_pump','=',$BoosterCartData->booster_article_number)
        ->value('pump_height');
        
        $search = "Enclosure ";
        $booster_cart_id = DB::table('booster_cp_items')
        ->where('booster_cart_id','=',$BoosterCartData->id)
        ->where('item_description','LIKE','%'.$search.'%')
        ->value('item_description');
        // ->first();
        if($booster_cart_id)
        {
            $item_description_height = substr($booster_cart_id, 0, strpos($booster_cart_id, 'Hx'));
            $enclosure_height = explode('Enclosure ', $item_description_height);
            $panel_height = $enclosure_height[1];
            $item_description_width = substr($booster_cart_id, 0, strpos($booster_cart_id, 'Wx'));
            $enclosure_width = explode('Hx', $item_description_width);
            $panel_width = $enclosure_width[1];
        }
        if($booster_cart_id == null)
        {
            $booster_cart_id = DB::table('booster_cp_items')
                        ->where('booster_cart_id','=',$BoosterCartData->id)
                        ->where('item_description','LIKE','%'.'Enclosure'.'%')
                        ->value('item_description');
            $item_description_height = substr($booster_cart_id, 0, strpos($booster_cart_id, 'Hx'));
            $enclosure_height = explode('Enclosure', $item_description_height);
            $panel_height = $enclosure_height[1];
            $item_description_width = substr($booster_cart_id, 0, strpos($booster_cart_id, 'Wx'));
            $enclosure_width = explode('Hx', $item_description_width);
            $panel_width = $enclosure_width[1];
        }
        $full_article_number = $request->full_article_number;
        $manifold = $BoosterCartData->manifold;
        $pump_model = $request->pump_model;
        $article_number = $BoosterCartData->article_number;
        $no_of_pumps = $request->no_of_pumps;
        $system_pressure = $BoosterCartData->system_pressure;
        $cp_price = $request->cp_price;
        // $code_price = $BoosterCartData->total_adders_price;
        //$pump_unit_price = $BoosterCartData->pump_price;
        $starter_type = $request->starter_type;
        $range = $request->range;
        $motor_power = $request->power;
        $voltage = $request->voltage;
        $code_price = 0.00;
        if($BoosterCartData->adder_ids != null && !empty($BoosterCartData->adder_ids)){
            $code_price = $this->searchBoosterElectricalAdderData($request,$BoosterCartData)['electrical_adder_price'];
        }
        else{
            $code_price = 0.00;
        }

        $mechanical_code_price = 0.00;
        if($BoosterCartData->mechanical_adder_ids != null && !empty($BoosterCartData->mechanical_adder_ids)){
            $mechanical_code_price = $this->searchBoosterAddersData($request,$request)['mechanical_adder_price'];
        }
        else{
            $mechanical_code_price = 0.00;
        }
        $bill_of_material = array();
        $i = 0;
        //get constants values
        $interCompanyMargin = User::ic_margin_booster();
         // This is temporary
        $cable_size_ampere_constant = DB::table('setup_fields')->where('name', 'cable_size_ampere_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $cable_length_constant = DB::table('setup_fields')->where('name', 'cable_length_constant')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $spare_length = DB::table('setup_fields')->where('name', 'spare_length')->pluck('value')[0]; //get from admin; //This $overHead can be editable by admin
        $slashPos = strpos($pump_model, ' ');
        $fpump_model = explode(' ', $pump_model);
        $model = end($fpump_model);
        if (str_starts_with($model, 'C')) {
            $model = str_replace('.', '', $model);
        }
        else {
            if (str_contains($model, '/')) {
                $slashPos = strpos($model, '/');
                $model = substr($model, 0, $slashPos);
                if(str_starts_with($model,'MV')){
                    $slashPos = strpos($model, 'I');
                    $model = substr($model, $slashPos);
                }
            }
            if (str_ends_with($model, '-')) {
                $model = substr($model, 0, -2);
            }
            if (str_ends_with($model, '/')) {
                $model = substr($model, 0, -2);
            }
            if (str_ends_with($model, '/^[a-zA-Z]+$/')) {
                $model = substr($model, 0, -2);
            }
            if (str_contains($model, '-')) {
                $slashPos = strpos($model, '-');
                $model = substr($model, 0, $slashPos);
            }
        }
            $base_frame_size_constant = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0];
            $ptp_data = PTPDistanceMechanicalComponent::
            where('pump_model_range1', 'LIKE', '%' . substr($model, 0, -2) . '%')
                ->orWhere('pump_model_range2', 'LIKE', '%' . substr($model, 0, -2) . '%')->get();
            if (count($ptp_data) == 0) {
                $data['error_html'] = 'No PTP Data Record Found';
                return response()->json(array('success' => true, 'data' => $data));
            }
            if (str_starts_with($model, 'V')) {
                $offset = 1;
            } else if (str_starts_with($model, 'CV1.L') || str_starts_with($model, 'CH1-L')) {
                $offset = 5;
            } else if (str_starts_with($model, 'MHIL')) {
                $offset = 4;
            } else if (str_starts_with($model, 'CV1-L.')) {
                $offset = 6;
            } else {
                $offset = 0;
            }
            $check = (int) substr($model, $offset);
            foreach ($ptp_data as $key => $p) {
                $start = (int) substr(trim($p->pump_model_range1), $offset);
                $end = (int) substr(trim($p->pump_model_range2), $offset);
                // echo $start.' '.$end.' '.$check.'<br>';
                if ($check >= $start && $check <= $end) {
                    $ptp = $p->ptp;
                    $ptd_distance_id = $p->id;
                    break;
                } else {
                    $ptp = 0;
                }
            }
            if ($ptp == 0) {
                $data['error_html'] = 'No PTP Data record found';
                return response()->json(array('success' => true, 'data' => $data));
            }
            $panel_stand_price = 0;
            $panel_height = $request->panel_height;
            if($panel_height == 400){
                $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1521', '400'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                $panel_stand_height = 1521;
                $base_frame_length_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
                ->get();
                foreach ($base_frame_length_data as $key => $p) {
                    $start = (int) substr(trim($p->pump_model_range1), $offset);
                    $end = (int) substr(trim($p->pump_model_range2), $offset);
                    // echo $start.' '.$end.' '.$check.'<br>';
                    if ($check >= $start && $check <= $end) {
                        $base_frame_length = $p;
                        break;
                    } else {
                        $base_frame_length = null;
                    }
                }

                if(empty($base_frame_length)){
                    $data['error_html'] = 'No  Base Frame record found for the selected model';
                    return response()->json(array('success' => true, 'data' => $data));
                }
                
                $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                if(!empty($base_frame_length_price)){
                    $val_price = 0;
                    foreach ($base_frame_length_price as $key => $value){
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['price'] = $value->price;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                        $val_price+=$value->price;
                    }
                    $base_frame_size_price =  $val_price;
                }
            }            
            elseif ($panel_height > 400 && $panel_height < 1000){
                $base_frame_size = $pump_height + $panel_height + $base_frame_size_constant; //from admin configuration setting
               if($base_frame_size < 1775) {
                    //fails
                    $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1775', '8040'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                    $panel_stand_height = 1775;
                
                } else if ($base_frame_size > 1775){
                    //pass
                    if($ptp == 300)
                    {
                        $no_of_pumps = $no_of_pumps + 2;
                    }
                    elseif($ptp == 500)
                    {
                     $no_of_pumps = $no_of_pumps + 1;
                    }
                    $master_sheet = BoosterMasterSheetMechanicalComponent::where('brand_code', 5)->where('function_code', 110)->whereIn('range', ['1775', '8040'])->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                    $panel_stand_height = 1775;
                }
                $base_frame_length_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
                ->get();
                $no_of_pumps = (int)\request()->get('no_of_pumps');
                // ->where(function($query) use ($model){
                    //     $query->where('pump_model_range1', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                    //     $query->orWhere('pump_model_range2', 'LIKE', '%' . trim(substr($model, 0, -1)) . '%');
                    // })
                foreach ($base_frame_length_data as $key => $p) {
                    $start = (int) substr(trim($p->pump_model_range1), $offset);
                    $end = (int) substr(trim($p->pump_model_range2), $offset);
                    // echo $start.' '.$end.' '.$check.'<br>';
                    if ($check >= $start && $check <= $end) {
                        $base_frame_length = $p;
                        break;
                    } else {
                        $base_frame_length = null;
                    }
                    // else{
                    //     $data['html'] = 'No PTP Data record found';
                    //     return response()->json(array('success' => true, 'data' => $data));
                    // }
                }
                //$panel_height = 600
                if(empty($base_frame_length)){
                    $data['error_html'] = 'No Base Frame record found for selected model.';
                    return response()->json(array('success' => true, 'data' => $data));
                }
                $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                if(!empty($base_frame_length_price)){
                    $val_price = 0;
                    foreach ($base_frame_length_price as $key => $value){
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['price'] = $value->price;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                        $val_price+=$value->price;
                    }
                    $base_frame_size_price =  $val_price;
                    //$base_frame_size_price 286.49;
                }
            } else if ($panel_height >= 1000) {
                //panel stand > 1000
                //get from BASE FRAME CALCULATION SHEET
                // $panel_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)->where('pump_model_range1', 'LIKE', '%' . substr($model, 0, -1) . '%')->orWhere('pump_model_range2', 'LIKE', '%' . substr($model, 0, -1) . '%')->first();
                $baseframe_panel_data = BaseFrameCalculation::where('no_of_pumps', $no_of_pumps)->where('ptp', $ptp)
                    ->get();
                foreach ($baseframe_panel_data as $key => $p) {
                    $start = (int) substr(trim($p->pump_model_range1), $offset);
                    $end = (int) substr(trim($p->pump_model_range2), $offset);
                    //   echo $start.' '.$end.' '.$check.'<br>';
                    if ($check >= $start && $check <= $end) {
                        $base_frame_length = $p;
                        break;
                    } else {
                        $base_frame_length = null;
                    }
                    // else{
                    //     $data['html'] = 'No PTP Data record found';
                    //     return response()->json(array('success' => true, 'data' => $data));
                    // }
                }

                if(empty($base_frame_length)){
                    $data['error_html'] = 'No  Base Frame record found for the selected model';
                    return response()->json(array('success' => true, 'data' => $data));
                }
                 $base_frame_length_price = BoosterMasterSheetMechanicalComponent::where('brand_code', $base_frame_length->brand_code)->where('function_code', $base_frame_length->function_code)->where('range', $base_frame_length->range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                if(!empty($base_frame_length_price)){
                    $val_price = 0;
                    foreach ($base_frame_length_price as $key => $value){
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['price'] = $value->price;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                        $val_price+=$value->price;
                    }
                    $base_frame_size_price =  $val_price;
                }
                // $panel_stand_price = array_sum($master_sheet);
            } else {
                // print_r('$panel_stand_price');
                $data['error_html'] = 'No  Panel Height record found for panel height '.$panel_height;
                return response()->json(array('success' => true, 'data' => $data));
            }
            if (!empty($master_sheet)) {
                foreach ($master_sheet as $key => $value) {
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $panel_stand_price += $value->price;
                    $bill_of_material[$i]['qty'] = 1;
                    $i++;
                }
            }
            //calculate power monitor flag
            $power_monitor_flag_price = 0;
            
            if ($range == 1 && (($starter_type == 1) || ($starter_type == 2) || ($starter_type == 6) || ($starter_type == 5))) {
                if ($system_pressure == 'PN16') {
                    $power_monitor_flag = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 91)->where('range', 140)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                } elseif ($system_pressure == 'PN25') {
                    $power_monitor_flag = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 91)->where('range', 280)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                }
                if (!empty($power_monitor_flag)) {
                    foreach ($power_monitor_flag as $key => $value) {
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['price'] = $value->price;
                        $power_monitor_flag_price += $value->price;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                    }
                }
            } else if (($range == 2 || $range == 3 || $range == 1) && ($starter_type > 1)) {
                //pass
                $power_monitor_flag2 = BoosterMasterSheetMechanicalComponent::where('brand_code', 20)->where('function_code', 93)->where('range', 40)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                if (!empty($power_monitor_flag2)) {
                    foreach ($power_monitor_flag2 as $key => $value) {
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['price'] = $value->price;
                        $power_monitor_flag_price += $value->price;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                    }
                }
                
                if ($system_pressure == 'PN16') {
                    $power_monitor_flag1 = BoosterMasterSheetMechanicalComponent::where('brand_code', 8)->where('function_code', 92)->where('range', 160)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                } elseif ($system_pressure == 'PN25') {
                    $power_monitor_flag1 = BoosterMasterSheetMechanicalComponent::where('brand_code', 8)->where('function_code', 92)->where('range', 250)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
                }
                
                if (!empty($power_monitor_flag1)) {
                    foreach ($power_monitor_flag1 as $key => $value) {
                        $bill_of_material[$i]['range'] = $value->range;
                        $bill_of_material[$i]['brand_code'] = $value->brand_code;
                        $bill_of_material[$i]['function_code'] = $value->function_code;
                        $bill_of_material[$i]['item_description'] = $value->description;
                        $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                        $bill_of_material[$i]['price'] = $value->price;
                        $power_monitor_flag_price += $value->price;
                        $bill_of_material[$i]['qty'] = 1;
                        $i++;
                    }
                }
            } else {
                //here
                $data['error_html'] = 'No record found against selected starter type.';
                return response()->json(array('success' => true, 'data' => $data));
            }

            //CABLE SIZE
            if ($starter_type == 1 || $starter_type == 2 || $starter_type == 5) {
                $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
                $Cable_Ampere = $Ampere_per_pump * 1.25;
                $rangeData = BoosterCableSelection::where('brand_code', 12)->where('function_code', 111)->orderBy('range')->get();
            } else if ($starter_type == 6) {
                $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
                $rangeData = BoosterCableSelection::where('brand_code', 12)->where('function_code', 111)->orderBy('range')->get();
                $Cable_Ampere = ($Ampere_per_pump * $cable_size_ampere_constant) * 1.25;
            } else if ($starter_type == 3) {
                $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
                $rangeData = BoosterCableSelection::where('brand_code', 14)->where('function_code', 113)->orderBy('range')->get();
                $Cable_Ampere = $Ampere_per_pump * 1.25;
            } else if ($starter_type == 4 || $starter_type == 7) {
                $Ampere_per_pump = (($motor_power * 1000) / (1.732 * $voltage * 0.8));
                $Cable_Ampere = ($Ampere_per_pump * $cable_size_ampere_constant) * 1.25;
                $rangeData = BoosterCableSelection::where('brand_code', 14)->where('function_code', 113)->orderBy('range')->get();
            } else {
                // print_r('CableSelection');
                $data['error_html'] = 'No record found against starter type';
                return response()->json(array('success' => true, 'data' => $data));
            }
            $range = 0;
            foreach($rangeData as $r) {
                if ($r->range >= $Cable_Ampere) {
                    $range = $r->range;
                    break;
                }
            }
            if($range == 0) {
                // print_r('range not found');
                $data['error_html'] = 'No Range record found for booster calcualtion.';
                return response()->json(array('success' => true, 'data' => $data));
            }
            
            if($starter_type == 1 || $starter_type == 2 || $starter_type == 5 || $starter_type == 6)
            {
                $cable_unit_data = BoosterMasterSheetMechanicalComponent::where('brand_code', 12)->where('function_code', 111)->where('range', $range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            }
            else{
                $cable_unit_data = BoosterMasterSheetMechanicalComponent::where('brand_code', 14)->where('function_code', 113)->where('range', $range)->select('price', 'description', 'range', 'brand_code', 'function_code','wilo_article_no')->get();
            }

            $cable_unit_price = 0;
            //Cable Length Calculation
            $start1_arr = array(1, 2, 3, 5);
            $start2_arr = array(4, 6, 7);
            if ($panel_height < 1000) {
                if (in_array($starter_type, $start1_arr)) {
                    $Cablelength = ($panel_stand_height + $base_frame_length->base_frame_length) * $no_of_pumps;
                }
                if (in_array($starter_type, $start2_arr)) {
                    $Cablelength = (($panel_stand_height + $base_frame_length->base_frame_length) * 2) * $no_of_pumps;
                }
            } else {
                if (in_array($starter_type, $start1_arr)) {
                    $Cablelength = ($pump_height + ($cable_length_constant * $panel_height) + $panel_width + $base_frame_length->base_frame_length + $spare_length) * $no_of_pumps;
                }
                if (in_array($starter_type, $start2_arr)) {
                    $Cablelength = (($pump_height + ($cable_length_constant * $panel_height) + $panel_width + $base_frame_length->base_frame_length + $spare_length) * 2) * $no_of_pumps;
                }
            }
            if(!empty($cable_unit_data)) {
                foreach ($cable_unit_data as $key => $value) {
                    $bill_of_material[$i]['range'] = $value->range;
                    $bill_of_material[$i]['brand_code'] = $value->brand_code;
                    $bill_of_material[$i]['function_code'] = $value->function_code;
                    $bill_of_material[$i]['item_description'] = $value->description;
                    $bill_of_material[$i]['wilo_article_no'] = $value->wilo_article_no;
                    $bill_of_material[$i]['price'] = $value->price;
                    $bill_of_material[$i]['qty'] = $Cablelength/1000;
                    $cable_unit_price += $value->price;
                    $i++;
                }
            }
            
            //search
            $cablePrice = $cable_unit_price * ($Cablelength/1000); //CABLE PRICE
            $overhead = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0]; //get from admin
            $intercompany_margin = $interCompanyMargin; 
            $standard_component_price = $this->calcualtePriceInBOM($no_of_pumps, $ptd_distance_id, $system_pressure, $manifold);
            $mechanical_system_price = $standard_component_price + $base_frame_size_price + $power_monitor_flag_price + $mechanical_code_price + $panel_stand_price;  
            
            $booster_price = ((($pump_unit_price * $no_of_pumps) + $cp_price + $code_price + ($mechanical_system_price + $cablePrice)) * $overhead ) / $intercompany_margin;

            $mechanical_price = ((($pump_unit_price * $no_of_pumps) + ($mechanical_system_price + $cablePrice)) * $overhead ) / $intercompany_margin;

            $data['full_article_number'] = $request->full_article_number;
            $data['booster_price'] = $booster_price;
            $data['standard_component_price'] = $standard_component_price;
            $data['mechanical_system_price'] = $mechanical_system_price;
            $data['cablePrice'] = $cablePrice;
            $data['pump_unit_price'] = $pump_unit_price;
            $data['panel_stand_price'] = $panel_stand_price;
            $data['no_of_pumps'] = $no_of_pumps;
            $data['starter_type'] = $starter_type;
            $data['power'] = $motor_power;
            $data['code_price'] = $code_price;
            $data['mechanical_code_price'] = $mechanical_code_price;
            $data['pump_model'] = $pump_model;
            $data['manifold'] = $manifold;
            $data['ptp_distance_id'] = $ptd_distance_id;
            $data['voltage'] = $voltage;
            $data['pressure'] = $system_pressure;
            $data['cp_price'] = $cp_price;
            $data['base_frame_size'] = $base_frame_size_price;
            $data['power_monitor_flag_price'] = $power_monitor_flag_price;
            $data['cable_size'] = $cable_size_ampere_constant;
            $data['Cablelength'] = $Cablelength;
            $data['bill_of_material_booster'] = json_encode($bill_of_material);

            $data['mechanical_items_price'] = $mechanical_price;
            $data['electrical_items_price'] = ($cp_price * $overhead) / $intercompany_margin;

            $returnHTML = view('frontend.booster.table')->with('boosterData', $data)->render();
            $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }

    //where('user_id', auth()->user()->id)
    public function searchByArticleNumber(Request $request) {
        //$BoosterCartData = BoosterCart::where('full_article_number', $request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
		if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
		$BoosterCartData = $BoosterCartData->first();
            if($BoosterCartData){
                $controlPanelData = ControlPanel::where('id', $BoosterCartData->cp_id)
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
                if($controlPanelData){
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
                if (isset($data['controlPanel'][0]) && !empty($data['controlPanel'][0])) {
                    $idberOfPump = $data['controlPanel'][0]->noofpumps['value'];
                    if (ControlPanel::isIntegerColumn($data['controlPanel'][0]->powers['value'])) { // Integer Column
                        $power = $data['controlPanel'][0]->powers['value'];
                        $voltage = $data['controlPanel'][0]->voltages['value'];
                        $columnName = $idberOfPump . 'x' . $power . "__0kwx" . $voltage . 'v';
                    } else { // Float column
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
                    $cpRecords = DB::table($tableName)->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)->whereNotNull($columnName)->where($columnName, '!=', 0)->get();
                    $arrayResult = json_decode(json_encode($cpRecords), true);
                    $enclousreAdderItemData = null;
                    // Up to here..
                    if ($BoosterCartData->adder_ids && $BoosterCartData->adder_ids != '') {
                        $addersData = $this->calculateAddersSearchByArticle($BoosterCartData->cp_id, $BoosterCartData->adder_ids, $idberOfPump, $data['controlPanel'][0]->powers['value'], $voltage, $tableName, $columnName,$component); //Code ids
                        $enclousreAdderItemData = $addersData['enclousreItem'];
                        if ($addersData['code_price'] && $addersData['code_price'] != '') {
                            $codePrice = $addersData['code_price'];
                        }
                    }
                    foreach ($arrayResult as $key => $val) {
                        $price += $this->searchByArticleCalculatePriceInItem($val, $data['controlPanel'][0]->components['id'], $data['controlPanel'][0]->enclousres['id'], $data['controlPanel'][0]->startertypes['id'], $enclousreAdderItemData, $columnName);
                    }
                    if ($codePrice > 0.00) {
                        $price = $price + $codePrice;
                    }
                    $interCompanyMargin = User::ic_margin_booster();

                    $overhead  = DB::table('setup_fields')->where('name', 'booster_overhead')->pluck('value')[0];
                    $price = ($price * $overhead) / $interCompanyMargin;
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
                    $data['adder_ids'] = $BoosterCartData->adder_ids;
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
    
    //this function done for electrical adder ids.
    public function calculateAddersSearchByArticle($control_panel_id, $ids, $noOfPump, $motorPower, $voltage, $table_name, $columnName,$component) {       
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
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                // echo $val['brand_code'] . "<br>";
                                $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
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
                                $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                if($exist)
                                {
                                    $val['brand_code'] = 2;
                                }
                            }
                                $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
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
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                    //echo $val[$column] * $noOfPump;
                                    $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
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
                                        $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                        if($exist)
                                        {
                                            $val['brand_code'] = 2;
                                        }
                                    }
                                    $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                } else {
                                    $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
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
    
    public function searchByArticleCommonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump){
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

    public function searchByArticleCalculatePriceInItem($component, $enclousre, $starterType, $enclousreItem, $columnName,$val = []){
        $price = 0.00;
                if($enclousreItem && !empty($enclousreItem) && $enclousre == 2 ) {
                //    $enclousreItem = json_decode($enclousreItem, true);

                    if($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                    $val['range'] = $enclousreItem['range'];
                    // echo "**".$val['range']."***";
                    }
                }
                if($enclousreItem && !empty($enclousreItem) && $enclousre == 4)  
                {
                    // $enclousreItem = json_decode($enclousreItem, true);
                    if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                        $val['range'] = $enclousreItem['range'];
                        $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                         //Qty * price // 2 parameter is equal to brand code
                        $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    }
                }
                // getMasterSheetElectricalPriceData
                if($enclousreItem && !empty($enclousreItem) && $enclousre == 3)  
                {
                    // $enclousreItem = json_decode($enclousreItem, true);
                    if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                        $val['range'] = $enclousreItem['range'];
                        $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                        $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    }
                }

                if ($component == 2 && $val['brand_code'] == 1) { // component 2 =  Economic
                    if ($this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range'])) {
                        $price = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                        // echo "change" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(2,  $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
                    }
                        //
                    else {
                        $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
                        // echo "If condition fail choose brand code 1 **" . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
            }
        } else if ($enclousre == 3 && $val['brand_code'] == 5 && $val['function_code'] == 1) { //3 equal GRP
            if ($this->getMasterSheetElectricalPriceData(31, 63, $val['range'])) {
                $price = $this->getMasterSheetElectricalPriceData(31, 63, $val['range']) * $val[$columnName]; //Qty * price
                    // echo "**".$price;
                    // echo "Encloure  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(31, 63, $val['range']) * $val[$columnName] . "</br>";
                    }
                    //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else if ($enclousre == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1) { //4 equal Stainless
            if ($this->getMasterSheetElectricalPriceData(5, 64, $val['range'])) {
                // echo $val['range'];
                $price = $this->getMasterSheetElectricalPriceData(5, 64, $val['range']) * $val[$columnName]; //Qty * price
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
            if ($this->getMasterSheetElectricalPriceData(32, $val['function_code'], $val['range'])) {
            // echo $val['range'];
                $price = $this->getMasterSheetElectricalPriceData(32, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            //  echo "stainless  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName] . "</br>";
                    }
                //
            else {
                //echo "Not ";
                $price = 0.00;
            }
        } else {
            $price = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            // echo "Normal  " . $val['brand_code'] . "  price == " . $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName] . "</br>";
        }
        return $price;
    }

    //Function for search by full article number functionality of booster mechanical adder ids.
    //here by search
    public function searchBoosterAddersData($booster_cart_id, $request) {  
        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
			$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
			if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
			$BoosterCartData = $BoosterCartData->first();
        if(!empty($request->full_article_number) && $request->full_article_number != null)
        {
            $cp_id = DB::table('control_panels')->where('id','=',$BoosterCartData->cp_id)->first();
            $request->ptp_distance_id = $BoosterCartData->ptp_distance_id;
            $request->no_of_pump = $cp_id->no_of_pump_id;
            $request->pump_type = $BoosterCartData->pump_type;
            $request->article_number = $BoosterCartData->article_number;
            $request->pump_model = $BoosterCartData->model_no;
            $request->power = $BoosterCartData->motor_power;
            $request->voltage = $BoosterCartData->supply_voltage;
            $request->manifold = $BoosterCartData->manifold;
            $request->system_pressure = $BoosterCartData->system_pressure;
            $request->pump_unit_price =  $BoosterCartData->pump_price;
            $request->cp_id =  $BoosterCartData->cp_id;
            $request->adder_ids = $BoosterCartData->adder_ids; //electrical adder ids
            $request->mechanical_adder_ids = $BoosterCartData->mechanical_adder_ids;
        }
        $noOfPump = $request->no_of_pump;
        $ptpDistanceId = $request->ptp_distance_id;
        $systemPressure = $request->system_pressure;
        $mainfold = $request->manifold;
        $codes = '';
        if(empty($request->full_article_number) && $request->full_article_number == null)
        {
            if ($request->mechanical_adder_ids) {
                $codes = explode(",", implode(",", $request->mechanical_adder_ids));
            }
        }
        else{
            if ($request->mechanical_adder_ids) {
                $codes = explode(",",$request->mechanical_adder_ids);
            } 
        }

        if($BoosterCartData)
        {
            $code60 = DB::table('booster_items')
                            ->select('qty')
                            ->where('booster_cart_id','=',$BoosterCartData->id)
                            ->where('adder_code', '60')
                            ->value('qty');
            $code61 = DB::table('booster_items')
                            ->select('qty')
                            ->where('booster_cart_id','=',$BoosterCartData->id)
                            ->where('adder_code', '61')
                            ->value('qty');

            $code65_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','65')
                            ->value('item_description');

            $code66_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','66')
                            ->value('item_description');

            $code67_desc = DB::table('booster_items')
                            ->where('booster_cart_id',$BoosterCartData->id)
                            ->where('adder_code','67')
                            ->value('item_description');

            $code65 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '65')
                                ->whereNotNull('65')
                                ->where('65','!=','')
                                ->where('65','!=','0')
                                ->where('item_description','=',$code65_desc)
                                ->value('id');

            $code66 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '66')
                                ->whereNotNull('66')
                                ->where('66','!=','')
                                ->where('66','!=','0')
                                ->where('item_description','=',$code66_desc)
                                ->value('id');

            $code67 = DB::table('mechanical_adder_common_pressure_vessel')
                                ->select('id', 'item_description', '67')
                                ->whereNotNull('67')
                                ->where('67','!=','')
                                ->where('67','!=','0')
                                ->where('item_description','=',$code67_desc)
                                ->value('id');

            $request->code65 = $code65;
            $request->code66 = $code66;
            $request->code67 = $code67;
        }
        if($codes) {
            $price = 0.00;
            foreach ($codes as $code) {
                switch ($code) {
                    case ($code >= 53 && $code <= 57): //electrical_common_adder code
                        //id = $column
                        $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                        ->whereNotNull($code)->where($code, '!=', 0)->first();

                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        break;
                    case (($code >= 58 && $code <= 59)) : // Range 2= standard , 3 = Premium
                        $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                        ->whereNotNull($code)->where($code, '!=', 0)->first();

                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        
                        $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        break;
                    case ($code == 60):  //electrical_common_adder_based_on_ampere code
                        if ($code60 && !empty($code60)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')->whereNotNull($code)->where($code, '!=', 0)->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code, $code60);  
                        }
                        break;
                    case ($code == 61):  //electrical_common_adder_based_on_ampere code
                        if ($code61 && !empty($code61)) {
                            $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                                            ->whereNotNull($code)->where($code, '!=', 0)->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change remove * $code61 from below price formula
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']) * $code61;
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code, $code61);
                        }
                        break;

                    case ($code == 62):  //electrical_common_adder_based_on_ampere code
                        // $cpRecords = DB::table('mechanical_adder_common')->select('brand_code', 'function_code', 'range')
                        //                 ->whereNotNull($code)->where($code, '!=', 0)->first();
                        // $arrayResult = json_decode(json_encode($cpRecords), true);
                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Strainer" . " " . $variable;

                        $get_vals = DB::table('mechanical_adder_common_strainer')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        break;

                    case ($code == 63):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);

                        $desc = "Flexible connector" . " " . $variable;
                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();

                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);
                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;

                    case ($code == 64):  //electrical_common_adder_based_on_ampere code

                        $variable = $this->getCommonStainer($noOfPump, $ptpDistanceId, $systemPressure, $mainfold, $code);
                        $desc = "Flexible connector" . " " . $variable;

                        $get_vals = DB::table('mechanical_adder_common_flexible')->where('item_description', 'LIKE', "%" . $desc . "%")->get();
                        $price += $this->getMasterSheetPriceData($get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range);

                        $this->insertAdderBoosterItem($booster_cart_id, $get_vals[0]->brand_code, $get_vals[0]->function_code, $get_vals[0]->range, $price, $code);
                        // $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                        //$price += $this->getMasterSheetPriceData($arrayResult['bran  d_code'], $arrayResult['function_code'], $arrayResult['range']) * $code60;
                        break;
                    case ($code == 65 ):  //mechanical_adder_common_pressure_vessel code
                        //if ($request->code65 && !empty($request->code65)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')
                                        ->select('id','brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code65)
                                    ->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change:- remove * $code61 from below price formula
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        //}
                        break;
                    case ($code == 66 ):  //mechanical_adder_common_pressure_vessel code
                        //if ($request->code66 && !empty($request->code66)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code66)
                                    ->first();
                            $arrayResult = json_decode(json_encode($cpRecords), true);
                             //date : 8-4-2022 change remove * $code61 from below price formula
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                           $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        //}
                        break;

                    case ($code == 67):  //electrical_common_adder_based_on_ampere code
                        //if ($request->code67 && !empty($request->code67)) {
                            $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('brand_code', 'function_code', 'range')
                                    ->whereNotNull($code)->where($code, '!=', 0)
                                    ->where('id', $request->code67)
                                    ->first();

                            $arrayResult = json_decode(json_encode($cpRecords), true);
                            //date : 8-4-2022 change remove * $code61 from below price formula
                            $price += $this->getMasterSheetPriceData($arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range']);
                            $this->insertAdderBoosterItem($booster_cart_id, $arrayResult['brand_code'], $arrayResult['function_code'], $arrayResult['range'], $price, $code);
                        //}
                        break;

                    case ($code == 68): 
                        //in admin
                        $price += DB::table('setup_fields')->where('name', 'booster_adder_code_no_68')->pluck('value')[0];
                        $this->insertAdderBoosterItem($booster_cart_id, 0, 0, 0, $price, $code);
                        break;
                    default: //default
                    break;
                }
            }
        }
        return ['mechanical_adder_price' => $price];
    }

    //Function for search by full article number functionality of booster electrical adder ids.
    public function searchBoosterElectricalAdderData(Request $request, $boosterCartId) {

        //$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number)->first();
		$BoosterCartData = BoosterCart::where('full_article_number','=',$request->full_article_number);
		if(auth()->user()->country_id == 6){
            $BoosterCartData = $BoosterCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
		$BoosterCartData = $BoosterCartData->first();
        $cp_id = ControlPanel::where('id','=',$BoosterCartData->cp_id)->first();
        if($cp_id)
        {
            $component = $cp_id->components_id;
        }
        else
        {
            $component = 1;
        }
        $noOfPump = $request->no_of_pumps;
        $motorPower = $request->power;
        $voltage = $request->voltage;
        if(is_array($BoosterCartData->adder_ids)){
            $ids = explode(",", implode(",", $BoosterCartData->adder_ids)); //Code ids
        }
        else{
            $ids = explode(",",$BoosterCartData->adder_ids);
        }
        $price = 0.00;
        $encloureArea = 0.00;
        $component = $request->component;
        if ($ids) {
            foreach ($ids as $id) {
                switch ($id) {
                    case ($id >= 1 && $id <= 26): //electrical_common_adder code
                        //id = $column
                        $electricalCommonAdders = DB::table('electrical_common_adder')->select('id', 'item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $id)
                                        ->whereNotNull($id)->where($id, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$id], $boosterCartId, $price, $unitPrice,$val,  $id);
                            }
                        }
                        break;
                    case ($id >= 27 && $id <= 36): 
                        
                        //electrical_common_adder_based_on_ampere code
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $column = $id . 'x' . $nearestColumn . 'a';

                        $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,$column);
                            }
                        }
                        break;
                    case ($id >= 37 && $id <= 44):  //electrical_adder_per_pump code

                        $column = $id . 'x1';

                        $electricalAdderPerPump = DB::table('electrical_adder_per_pump')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($component == 2 && $val['brand_code'] == 1)
                                {
                                    $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = 2;
                                    }
                                }
                                    $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,  $column, $noOfPump);
                            }
                        }
                        break;
                    case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code

                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        if ($id >= 45 && $id <= 52) {
                            $column = $id . 'x' . $nearestColumn . 'ax1';
                        } else {
                            $column = $id . 'x' . $nearestColumn . 'ax2';
                        }
                        
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                                        ->whereNotNull($column)->where($column, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($id >= 45 && $id <= 52) 
                                {
                                    if ($component == 2 && $val['brand_code'] == 1)
                                    {
                                        $exist = $this->getMasterSheetElectricalPriceData(2, $val['function_code'], $val['range']);
                                        if($exist)
                                        {
                                            $val['brand_code'] = 2;
                                        }
                                            $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                            $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                            $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                            $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,$column, $noOfPump);
                                    }
                                    else
                                    {
                                        $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                        $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);

                                        $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];

                                        $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,$column, $noOfPump);
                                    }
                                } 
                             
                             else {
                                    $price += $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                    $unitPrice = $this->getMasterSheetElectricalPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $this->itemSave($val['brand_code'], $val['function_code'], $val['range'], $val[$column], $boosterCartId, $price, $unitPrice,$val,$column, $noOfPump);
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
        return ['electrical_adder_price'=>$price];
    }
}