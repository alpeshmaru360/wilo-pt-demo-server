<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\AtmosMasterMotorPrice;
use App\AtmosAssemblyCostPcPk;
use App\AtmosPump;
use App\AtmosPumpType;
use App\AtmosCart;
use App\AtmosItem;
use App\AtmosAdder;
use App\User;
use App\AtmosBOMItems;
use App\AtmosMaterial;
use App\Helpers\CurrencyHelper;

class AtmosGigaController extends Controller {

    public function index() {
        $power = DB::table('atmos_master_motor_prices')->distinct()->pluck('power');
        $voltage = DB::table('atmos_master_motor_prices')->distinct()->pluck('voltage');
        $brand = DB::table('atmos_master_motor_prices')->distinct()->pluck('brand');
        $frequency = DB::table('atmos_master_motor_prices')->distinct()->pluck('frequency');
        $poles = DB::table('atmos_master_motor_prices')->distinct()->pluck('no_of_pole');
        $efficiency = DB::table('atmos_master_motor_prices')->distinct()->pluck('efficiency');
        return view('frontend.atmos_giga.index', compact('power', 'voltage', 'efficiency', 'poles', 'brand', 'frequency'))->with('pump_types', DB::table('atmos_pump_types')->get()
                )->with('atmos_materials', DB::table('atmos_materials')->get()
        );
    }

    public function atmos_giga_maintance_mode() {
        return view('frontend.atmos_giga.maintance');
    }

    public function get_price(Request $request) {
        $impeller_id = DB::table('atmos_materials')->where('id', $request['impeller_id'])->first();
        $pump_model = DB::table('atmos_pump_types')->where('id', $request['pump_model'])->first();
        $get_price = DB::table('atmos_pumps')->where('pump_id', $pump_model->id)
                        ->where('material_id', $impeller_id->id)->get();

        $pump_model_name = (count(AtmosPumpType::where('id', $request->pump_model)->pluck('name')) > 0) ? AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0] : $request->pump_model;
        $material_code = $impeller_id->code;
        $components_price =  "0";
        $balancing_cost =  "0";

        if($material_code == "08"){
            $material_code = "8";
        }

        $atmos_pump_assembly_cost = DB::table('atmos_pump_assembly_cost')
                ->where('model_name','like','%'.$pump_model_name.'%')
                ->where('impeller_material_code','=',$material_code)
                ->first();

        $cost = DB::table('atmos_pump_assembly_cost')
                ->where('model_name','like','%'.$pump_model_name.'%')
                ->where('impeller_material_code','like','%'.$material_code.'%')
                ->first();

        $assembly_cost = $cost->assmebly_cost;
        $testing_cost =  $cost->testing_cost;
        $components_price = $this->insert_components_item($pump_model_name,$material_code);

        $bare_shaft_price = $components_price + $assembly_cost + $testing_cost + $balancing_cost;
        
        $standard_impeller_size = $atmos_pump_assembly_cost->standard_impeller_size;
        $impeller_minimum_size = $atmos_pump_assembly_cost->impeller_minimum_size;
        $impeller_maximum_size = $atmos_pump_assembly_cost->impeller_maximum_size;

        if ($bare_shaft_price >= 1) {
            return response()->json([
                'standard_impeller_size'=>$standard_impeller_size,
                'impeller_minimum_size'=>$impeller_minimum_size,
                'impeller_maximum_size'=>$impeller_maximum_size,
                'pump_model' => $pump_model_name,
                'material_code' => $material_code,
                'get_price' => $bare_shaft_price
            ]);
        } else {
            return "price not found";
        }
    }

    public function get_frame(Request $request) {
        $masterData = AtmosMasterMotorPrice::select('id', 'frame_size')
                ->where('no_of_pole', $request['poles'])
                ->where('brand', $request['motor_brand'])
                ->where('frequency', $request['frequency'])
                ->where('power', $request['motor_power'])
                ->where('efficiency', $request['effieciency'])
                ->where('voltage', $request['power_supply'])
                ->get();
        if (isset($masterData[0])) {
            return $masterData[0];
        }
        return 0;
    }

    public function get_motor_price(Request $request) {
        if($request->val == 2){
            return AtmosMasterMotorPrice::where('id', $request['master_price_id'])->sum(DB::raw('price + insulate_bearing + shipping_cost'));
        } else {
            return AtmosMasterMotorPrice::where('id', $request['master_price_id'])->sum(DB::raw('price + shipping_cost'));
        }
    }

    public function check_for_column($request) {
        $request['frame'] = preg_replace('/(\d+)\s*([a-zA-Z]+)/', '$1$2', $request['frame']);
        $request['frame'] = strtolower($request['frame']);

        $columnName = $request['pump_id'] . 'x' . $request['frame'];
 
        $tbls = DB::getSchemaBuilder()->getColumnListing('atmos_accessories_price');
        $new_col = array();
        foreach ($tbls as $tb) {
            if (strpos($tb, strtolower($request['frame']))) {
                array_push($new_col, $tb);
            }
        }
        
        if (in_array(strtolower($columnName), $new_col)) {
            return $columnName;
        } else {
            
            sort($new_col);
           
            $array_size = sizeof($new_col) - 1;
            $i = 0;
            do{
                
                $i++;
                $col_name = (int)$request['pump_id'] + $i . 'x' . $request['frame'];
                
                $col_name = strtolower($col_name);
                
                if (in_array($col_name,$new_col)) {
                   
                    return $col_name;

                }
                $pmp_present = explode('x',$new_col[$array_size]);
               
            }while($i <= (int)$pmp_present[0]);
        }
    }
	
    public function get_accessories(Request $request) {
        $col_name = $this->check_for_column($request);
        if ($col_name != 0) {
            $col = DB::table("atmos_accessories_price")->where($col_name, '>', 0)->select('unit_price', $col_name,'wilo_article_number')->get();
            $total = 0;
            foreach ($col->toArray() as $c) {
                $atmos_master_pump_price = DB::table('atmos_master_pump_price')->where('china_article_number','=',$c->wilo_article_number)->first();
                $total += (float) $atmos_master_pump_price->unit_price *  $c->$col_name;
            }
            return $total;
        } else {
            return 0;
        }
    }

    public function ajaxCalculate(Request $request) {
        if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
            $pump_model_name = AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0];
            $matirial_code = AtmosMaterial::where('id',$request->impeller_material)->first();
            if($matirial_code){
                $material_code = $matirial_code->code;
            }else{
                $material_code = null; 
            }
            if($material_code == "08"){
                $material_code = "8";
            }
             $cost = DB::table('atmos_pump_assembly_cost')
                    ->where('model_name','like','%'.$pump_model_name.'%')
                    ->where('impeller_material_code',$material_code)
                    ->first();
            $components_price =  "0";
            $assembly_cost = $cost->assmebly_cost;
            $testing_cost =  $cost->testing_cost;
            if(($request->pump_model_impeller_size) == ($request->pump_model_required_size)){
                $balancing_cost = "0";
            }
            else{
                $balancing_cost = $cost->balancing_cost;
            }
            $request->bare_shaft_price = $request->bare_shaft_price + $balancing_cost;
        }
        $getAtmosPumpName = AtmosPumpType::where('id', $request->pump_model)->pluck('name');
        if(count($getAtmosPumpName) < 1){
           $getAtmosPumpName = $request->pump_model; 
        }
        else
        {
            $getAtmosPumpName = $getAtmosPumpName[0];
        }
        $atmosGigaPrice = 0.00;
        $optionalPrice = 0.00; 
        $interCompanyMargin = User::ic_margin_atmos(); 
        $otpMargin = User::otp_margin_atmos(); 
        $shippingPercentage = AtmosPumpType::atmos_shipping_percentage() / 100; 
        $overHead = AtmosPumpType::atmos_over_head(); 

        $ksa_overHead = AtmosPumpType::atmos_ksa_over_head(); 

        $morrocco_overHead = AtmosPumpType::atmos_morrocco_over_head(); 

        $assemblyPrice = 0.00;
        if ($request->code_price && $request->code_price != 'undefined') {
            $optionalPrice = $request->code_price;
        }
        if($request->searchByBarePumpArticleNumber != "1"){
            $getAPPPrice = AtmosAssemblyCostPcPk::where('power', $request->motor_power)->get();
            if ($getAPPPrice) {
                $assemblyPrice = $getAPPPrice[0]->assembly_charge + $getAPPPrice[0]->painting_charge + $getAPPPrice[0]->packing_charge;
            }
        }
        if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
            $components_price = $this->insert_components_item($pump_model_name,$material_code);
            if($request->searchByBarePumpArticleNumber == "1"){
                return $components_price;
            }
            $shippingCost = ($components_price + $request->acessories_price) * $shippingPercentage;
        }
        else{
            $shippingCost = $request->shipping_price_manual;
        }
        if(auth()->user()->country_id == 6){
            $atmosGigaPrice += ((($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $ksa_overHead / $interCompanyMargin;
        }
        elseif(auth()->user()->country_id == 9){
            $atmosGigaPrice += ((($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $morrocco_overHead / $interCompanyMargin;
                    }
        else{
            $atmosGigaPrice += (($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice +   $assemblyPrice + $shippingCost) * $overHead) / $interCompanyMargin;
        }
        $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $getAtmosPumpName)
                ->with('price', $atmosGigaPrice)
                ->with('motor_power', $request->motor_power)
                ->with('motor_brand', $request->motor_brand)
                ->render();
        $data['cp_records_html'] = $returnHTML;
        $data['cp_price'] = number_format($atmosGigaPrice, 2);
        $data['total_price'] = $atmosGigaPrice;
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function ajaxOptionalSelectedAdderData(Request $request) {
        $ids = explode(",", $request->adder_ids); //Code ids
        $price = 0.00;
        if ($ids) {
            foreach ($ids as $id) {

                switch ($id) {

                    case ($id == 1):
                        $price += AtmosMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forwinding')[0];
                        break;

                    case ($id == 2):
                        $price += AtmosMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forbearing')[0];
                        break;

                    case ($id == 3):
                        $price += AtmosMasterMotorPrice::where('id', $request['master_price_id'])->pluck('space_heater')[0];
                        break;

                    case ($id == 4):
                        $price += AtmosPumpType::atmos_adder_code_no_4();
                        break;
                        
                    default: //default
                        $price;
                        break;
                }
            }
        }
        return ['code_price' => $price];
    }

    public function ajaxOptionalModal(Request $request) {
        $atmosAdderData = DB::table('atmos_adders')->get();
        $data = view('frontend.atmos_giga.modal_optional')->with('atmosAdderData', $atmosAdderData)
                ->render();
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function addToCart(Request $request) {
        $supervisor_pump_model_price = "0";
        $components_price = "0";    
        $otpMargin = User::otp_margin_atmos();
        $atmosGigaCart = DB::table('atmos_carts')->where('full_article_number','=',$request->full_article_number);
        if(auth()->user()->country_id == 6){
            $atmosGigaCart = $atmosGigaCart->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $atmosGigaCart = $atmosGigaCart->latest('id')->first();

		if($request->impeller_material == null && $request->application == null && $request->adder_ids == null){
            $request->adder_ids = $atmosGigaCart->adder_ids;
        }
        if($request->motor_power == ""){
            $request->motor_power = $atmosGigaCart->power;
        }
        if($request->pump_model == ""){
            $request->pump_model = $atmosGigaCart->pump_id;
        }
        if($request->impeller_material == ""){
            $request->impeller_material = $atmosGigaCart->material_id;
        }
        if($request->application == ""){
            $request->application = $atmosGigaCart->application;
        }
        if($request->master_price_id == ""){
            $request->master_price_id = $atmosGigaCart->master_id;
        }
        if($request->bare_shaft_price == ""){
            $request->bare_shaft_price = $atmosGigaCart->bare_pump_price;
        }
        if($request->power_supply == ""){
            $request->power_supply = $atmosGigaCart->voltage;
        }
        //will add it
        if($request->frame_size == ""){
            $request->frame_size = $atmosGigaCart->frame_size;
        }
        if($request->frequency == ""){
            $request->frequency = $atmosGigaCart->frequency;
        }
        if($request->efficiency == ""){
            $request->efficiency = $atmosGigaCart->efficiency;
        }
        if($request->poles == ""){
            $request->poles = $atmosGigaCart->no_of_pole;
        }
        if($request->motor_brand == ""){
            $request->motor_brand = $atmosGigaCart->brand;
        }
        if($request->acessories_price == ""){
            $request->acessories_price = $atmosGigaCart->accesories_price;
        }

        if($request->bareshaft_pump_full_pump != "manual"){
            if($request->pump_model_flow == ""){
                $request->pump_model_flow = $atmosGigaCart->flow;
            }
           
            if($request->pump_model_head == ""){
                $request->pump_model_head = $atmosGigaCart->head;
            }
            
            if($request->pump_model_impeller_size == ""){
                $request->pump_model_impeller_size = $atmosGigaCart->impeller_standard_size;
            }
            
            if($request->pump_model_required_size == ""){
                $request->pump_model_required_size = $atmosGigaCart->required_impeller_size;
            }
        }

        if(($request->is_bare_shaft_price_manual == "" || $request->is_bare_shaft_price_manual == "0") && !is_null($request->full_article_number)){
        // if($request->is_bare_shaft_price_manual == "" || $request->is_bare_shaft_price_manual == "0"){
            $request->is_bare_shaft_price_manual = $atmosGigaCart->is_bare_manual;
        }
        if($atmosGigaCart && $request->is_acessories_price_manual == "0"){
            $request->is_acessories_price_manual = $atmosGigaCart->is_accesories_manual;
        }

        $getAssemblyPrice = $this->getCartAssemblyPrices($request->motor_power);
        $interCompanyMargin = User::ic_margin_atmos(); 
        $shippingPercentage = AtmosPumpType::atmos_shipping_percentage() / 100; 
        $overHead = AtmosPumpType::atmos_over_head(); 
        
        if(is_null($request->pump_model_flow) && is_null($request->pump_model_head) && is_null($request->pump_model_impeller_size) && is_null($request->pump_model_required_size)){
                }
                else{
                    if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
                        $pump_model_name = AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0];
                        $matirial_code = AtmosMaterial::where('id',$request->impeller_material)->first();
                        if($matirial_code){
                            $material_code = $matirial_code->code;
                        }else{
                            $material_code = null; 
                        }

                        $pump_model_name1 = str_replace(' ', '', $pump_model_name); // Remove spaces
                        $pump_model_name2 = str_replace('/', '', $pump_model_name); // Remove slashes
                        $cost = DB::table('atmos_pump_assembly_cost')
                                ->where('model_name','like','%'.$pump_model_name.'%')
                                ->orWhere('model_name','like','%'.$pump_model_name2.'%')
                                ->orWhere('model_name','like','%'.$pump_model_name1.'%')
                                ->where('impeller_material_code',$material_code)
                                ->first();
                        $components_price =  "0";
                        $assembly_cost = $cost->assmebly_cost;
                        $testing_cost =  $cost->testing_cost;
                        if(($request->pump_model_impeller_size) == ($request->pump_model_required_size)){
                            $balancing_cost = "0";
                        }
                        else{
                            $balancing_cost = $cost->balancing_cost;
                        }
                        $components_price = $this->insert_components_item($pump_model_name,$material_code);
                        $bare_shaft_price = $components_price + $assembly_cost + $testing_cost + $balancing_cost;
                        $request->bare_shaft_price = $bare_shaft_price;
                    }
                }
        if($request->is_shipping_charge_manual == 1){
            $shipping_cost_price = $request->shipping_price_manual; 
        }else{
            $shipping_cost_price =($components_price + $request->acessories_price) * $shippingPercentage;
        }
        if($request->pump_model == "0"){
            $shipping_cost_price = $atmosGigaCart->shipping_cost_price;
        }
        $bare_shaft_price = round($request->bare_shaft_price, 2);
        $acessories_price = round($request->acessories_price, 2);
        $shipping_cost_price = round($shipping_cost_price, 1);

        //$shipping_cost_price1 = round($atmosGigaCart->shipping_cost_price, 2);
    
        if($request->is_shipping_charge_manual == 1){
            $shipping_cost_price1 = round($request->shipping_price_manual,2); 
        }else{
            $shipping_cost_price1 =round(($components_price + $request->acessories_price) * $shippingPercentage,2);
        }
        //with adder ids
        if($request->adder_ids){
            $atmosCartData = AtmosCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->where('user_id', auth()->user()->id)
                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                    ->where('required_impeller_size', $request->pump_model_required_size)
                    ->where('is_bare_manual', $request->is_bare_shaft_price_manual)
                    ->where('bare_pump_price', $bare_shaft_price)
                    ->where('accesories_price', $acessories_price)
                    // ->where('shipping_cost_price', $shipping_cost_price)
                    ->where(function($query) use ($shipping_cost_price,$shipping_cost_price1){
                        $query->where('shipping_cost_price', $shipping_cost_price)
                                ->orWhere('shipping_cost_price', $shipping_cost_price1);
                    })
                    ->orderBy('id', 'desc')
                    ->first();
            if($atmosCartData == null){
                $atmosCartData1 = AtmosCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                    ->where('required_impeller_size', $request->pump_model_required_size)
                    ->where('is_bare_manual', $request->is_bare_shaft_price_manual)
                    ->where('bare_pump_price', $bare_shaft_price)
                    ->where('accesories_price', $acessories_price)
                    // ->where('shipping_cost_price', $shipping_cost_price)
                    ->where(function($query) use ($shipping_cost_price,$shipping_cost_price1){
                        $query->where('shipping_cost_price', $shipping_cost_price)
                                ->orWhere('shipping_cost_price', $shipping_cost_price1);
                    })
                    ->orderBy('id', 'desc')
                    ->first();
                //query for find article number and full article number ends diff user id..!!
                $atmosCart = new AtmosCart;
				$new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($atmosCartData){
                        if($atmosCartData->full_article_number != "" || $atmosCartData->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $atmosCartData->full_article_number);
								$atmosCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }
                    elseif($atmosCartData1){
						if($atmosCartData1->full_article_number != "" || $atmosCartData1->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $atmosCartData1->full_article_number);
                                $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }
                    else{

                    }
				}
                //BarE sHAFT dATA
                if($atmosCartData1 != null)
                {
                    $atmosCart->article_number = ($atmosCartData1->article_number==null)?null:$atmosCartData1->article_number;
                    $atmosCart->full_article_number = $atmosCartData1->full_article_number; 
                    $atmosCart->bare_shaft_article_number = $atmosCartData1->bare_shaft_article_number;  
                    $request->code_price = $atmosCartData1->total_adders_price;     
                }
                else{
                    $bare_shaft = AtmosCart::where('pump_id', $request->pump_model)
                                    ->where('material_id', $request->impeller_material)
                                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                                    ->where('required_impeller_size', $request->pump_model_required_size)
                                    ->orderBy('id', 'desc')
                                    ->first();  
                    if($bare_shaft){
                        $atmosCart->bare_shaft_article_number = $bare_shaft->bare_shaft_article_number; 
                    }
                }
                //BarE sHAFT dATA
                $atmosCart->pump_id = $request->pump_model;
                $atmosCart->pump_name = (count(AtmosPumpType::where('id', $request->pump_model)->pluck('name')) > 0) ? AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0] : $request->pump_model;
                $atmosCart->material_id = $request->impeller_material;
                
                // Bare Shaft price from Pump assmebly cost add table
                //this 4 condition will be null when user select manual pump model from pop-up +
                if(is_null($request->pump_model_flow) && is_null($request->pump_model_head) && is_null($request->pump_model_impeller_size) && is_null($request->pump_model_required_size)){
                    $atmosCart->bare_pump_price = $request->bare_shaft_price;
                    $atmosCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                }
                else{
                    if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
                        $pump_model_name = AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0];
                        $matirial_code = AtmosMaterial::where('id',$request->impeller_material)->first();
                        if($matirial_code){
                            $material_code = $matirial_code->code;
                        }else{
                            $material_code = null; 
                        }
                        if($material_code == "08"){
                            $material_code = "8";
                        }
                        // dd($pump_model_name,$material_code);
                        $cost = DB::table('atmos_pump_assembly_cost')
                                ->where('model_name','like','%'.$pump_model_name.'%')
                                ->where('impeller_material_code',$material_code)
                                ->first();
                        $components_price =  "0";
                        $assembly_cost = $cost->assmebly_cost;
                        $testing_cost =  $cost->testing_cost;
                        if(($request->pump_model_impeller_size) == ($request->pump_model_required_size)){
                            $balancing_cost = "0";
                        }
                        else{
                            $balancing_cost = $cost->balancing_cost;
                        }
                        $components_price = $this->insert_components_item($pump_model_name,$material_code);
                        $bare_shaft_price = $components_price + $assembly_cost + $testing_cost + $balancing_cost;
                        // $bare_shaft_price = $request->bare_shaft_price;
                        $atmosCart->bare_pump_price = $bare_shaft_price;
                        $request->bare_shaft_price = $bare_shaft_price;
                         $supervisor_pump_model_price = ($bare_shaft_price * $overHead) * $otpMargin;
                    }
                    else{
                        $atmosCart->bare_pump_price =  $request->bare_shaft_price;
                        $supervisor_pump_model_price = ($atmosCart->bare_pump_price * $overHead) * $otpMargin;
                    }
                }

                //Matrer Data
                $atmosCart->power = $request->motor_power;
                $atmosCart->voltage = $request->power_supply;
                $atmosCart->frame_size = $request->frame_size;
                $atmosCart->frequency = $request->frequency;
                $atmosCart->efficiency = $request->efficiency;
                $atmosCart->no_of_pole = $request->poles;
                $atmosCart->brand = $request->motor_brand;
                $atmosCart->master_id = $request->master_price_id;
                //depends upon pump model drop down add flow,over head,Impeller standard size,Required impeller size
                $atmosCart->flow = $request->pump_model_flow;
                $atmosCart->head = $request->pump_model_head;
                $atmosCart->impeller_standard_size = $request->pump_model_impeller_size;
                $atmosCart->required_impeller_size = $request->pump_model_required_size;
                //Asscesories Price
                $atmosCart->accesories_price = $request->acessories_price;
                $atmosCart->is_accesories_manual = $request->is_acessories_price_manual;
                //Assembly Charge Price
                $atmosCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $atmosCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $atmosCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost Price
                if($request->is_shipping_charge_manual == 1){
                    $atmosCart->shipping_cost_price = $request->shipping_price_manual; 
                }else{
                    // $atmosCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                    $atmosCart->shipping_cost_price = ($components_price + $request->acessories_price) * $shippingPercentage;
                    $atmosCart->shipping_cost_percentage = $shippingPercentage;
                }
                $atmosCart->overhead_price = $overHead;
                $atmosCart->inter_company_margin_price = $interCompanyMargin;
                $atmosCart->adder_ids = $request->adder_ids;
                $atmosCart->total_adders_price = $request->code_price;
                $atmosCart->application = $request->application;
                $atmosCart->price = $request->total_price;
                $atmosCart->total_price = $request->total_price;
                $atmosCart->qty = 1;
                $atmosCart->user_id = auth()->user()->id;
                $atmosCart->created_at = date("Y-m-d H:i:s");
                $atmosCart->updated_at = date("Y-m-d H:i:s");
				$atmosCart->country_origin = $request->country;
                $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                $atmosCart->save();
                $atmosCartId = $atmosCart->id;
                // if ($request->is_acessories_price_manual == 0) {
                    if($request->is_acessories_price_manual1 == "0"  && $request->pump_model != "0"){
                        $this->insertItem($atmosCartId, $request);
                        $this->insertAtmosBOM($atmosCartId,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price);
                    }
                // }
            }else{
                if(empty($atmosCartData->quotation_no)){
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {
					$new_ksa_article_number = '';
                        if(auth()->user()->country_id == 6){
                            if($atmosCartData){
								if($atmosCartData->full_article_number != "" || $atmosCartData->full_article_number != null){
                                    // Replace "683" with "339"
                                    if($request->country == "ksa"){
                                        $new_ksa_article_number = str_replace("683", "339", $atmosCartData->full_article_number);
                                    }
								}
							}
						}
                    $atmosCart = $atmosCartData->replicate();
                    //Asscesories Price
                    $atmosCart->accesories_price = $request->acessories_price;
                    $atmosCart->is_accesories_manual = $request->is_acessories_price_manual;

                    $atmosCart->inter_company_margin_price = $interCompanyMargin;
                    $atmosCart->price = $request->total_price;
                    $atmosCart->total_price = $request->total_price;
                    $atmosCart->quotation_no = null;
                    $atmosCart->qty = 1;
					$atmosCart->country_origin = $request->country;
                    $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                    $atmosCart->save();
                    $atmosCartId = $atmosCart->id;
                    if($request->is_acessories_price_manual1 == "0"  && $request->pump_model != "0"){
                        $this->insertItem($atmosCartId, $request);
                        $this->insertAtmosBOM($atmosCartId,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price);
                    }
                }
            }
        }else{
            //with-out adder ids
            $atmosCartData = AtmosCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                    ->where('required_impeller_size', $request->pump_model_required_size)
                    ->where('is_bare_manual', $request->is_bare_shaft_price_manual)
                    ->where('bare_pump_price', $bare_shaft_price)
                    ->where('accesories_price', $acessories_price)
                    ->where(function($query) use ($shipping_cost_price,$shipping_cost_price1){
                        $query->where('shipping_cost_price', $shipping_cost_price)
                                ->orWhere('shipping_cost_price', $shipping_cost_price1);
                    })
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();

            if($atmosCartData == null){
                $atmosCartData1 = AtmosCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                    ->where('required_impeller_size', $request->pump_model_required_size)
                    ->where('is_bare_manual', $request->is_bare_shaft_price_manual)
                    ->where('bare_pump_price', $bare_shaft_price)
                    ->where('accesories_price', $acessories_price)
                    ->where(function($query) use ($shipping_cost_price,$shipping_cost_price1){
                        $query->where('shipping_cost_price', $shipping_cost_price)
                                ->orWhere('shipping_cost_price', $shipping_cost_price1);
                    })
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();
                //query for find article number and full article number ends diff user id..!!
                $atmosCart = new AtmosCart;
				$new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($atmosCartData){
						if($atmosCartData->full_article_number != "" || $atmosCartData->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $atmosCartData->full_article_number);
                                $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
					}
    				elseif($atmosCartData1){
                                if($atmosCartData1->full_article_number != "" || $atmosCartData1->full_article_number != null){
                                    if($request->country == "ksa"){
                                        $new_ksa_article_number = str_replace("683", "339", $atmosCartData1->full_article_number);
                                        $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                                    }
    							}
    				}
    				else{
                    }
                }
					
                //BarE sHAFT dATA
                if($atmosCartData1 != null)
                {
                    $atmosCart->article_number = ($atmosCartData1->article_number==null)?null:$atmosCartData1->article_number;
                    $atmosCart->full_article_number = $atmosCartData1->full_article_number;  
                }
                else{
                    $bare_shaft = AtmosCart::where('pump_id', $request->pump_model)
                                    ->where('material_id', $request->impeller_material)
                                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                                    ->where('required_impeller_size', $request->pump_model_required_size)
                                    ->orderBy('id', 'desc')
                                    ->first();  
                    if($bare_shaft){
                        $atmosCart->bare_shaft_article_number = $bare_shaft->bare_shaft_article_number; 
                    }
                }
                $atmosCart->pump_id = $request->pump_model;
                $atmosCart->pump_name = (count(AtmosPumpType::where('id', $request->pump_model)->pluck('name')) > 0) ? AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0] : $request->pump_model;
                $atmosCart->material_id = $request->impeller_material;
                //this 4 condition will be null when user select manual pump model from pop-up +
                if(is_null($request->pump_model_flow) && is_null($request->pump_model_head) && is_null($request->pump_model_impeller_size) && is_null($request->pump_model_required_size)){
                    $atmosCart->bare_pump_price = $request->bare_shaft_price;
                    $atmosCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                }
                else{
                    if($request->is_acessories_price_manual1 == "0"  && $request->pump_model != "0"){
                        $pump_model_name = AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0];
                        $matirial_code = AtmosMaterial::where('id',$request->impeller_material)->first();
                        if($matirial_code){
                            $material_code = $matirial_code->code;
                        }else{
                            $material_code = null; 
                        }
                        $pump_model_name1 = str_replace(' ', '', $pump_model_name); // Remove spaces
                        $pump_model_name2 = str_replace('/', '', $pump_model_name); // Remove slashes
                        $cost = DB::table('atmos_pump_assembly_cost')
                                ->where('model_name','like','%'.$pump_model_name.'%')
                                ->orWhere('model_name','like','%'.$pump_model_name1.'%')
                                ->orWhere('model_name','like','%'.$pump_model_name2.'%')
                                ->where('impeller_material_code',$material_code)
                                ->first();
                        $components_price =  "0";
                        $assembly_cost = $cost->assmebly_cost;
                        $testing_cost =  $cost->testing_cost;
                        if(($request->pump_model_impeller_size) == ($request->pump_model_required_size)){
                            $balancing_cost = "0";
                        }
                        else{
                            $balancing_cost = $cost->balancing_cost;
                        }
                        $components_price = $this->insert_components_item($pump_model_name,$material_code);
                        $bare_shaft_price = $components_price + $assembly_cost + $testing_cost + $balancing_cost;
                        $atmosCart->bare_pump_price = $bare_shaft_price;
                        $request->bare_shaft_price = $bare_shaft_price;
                        $supervisor_pump_model_price = ($bare_shaft_price * $overHead) * $otpMargin;
                    }
                    else{
                         $atmosCart->bare_pump_price = $request->bare_shaft_price;
                         $supervisor_pump_model_price = ($atmosCart->bare_pump_price * $overHead) * $otpMargin;
                    }
                }
                $atmosCart->power = $request->motor_power;
                $atmosCart->voltage = $request->power_supply;
                $atmosCart->frame_size = $request->frame_size;
                $atmosCart->frequency = $request->frequency;
                $atmosCart->efficiency = $request->efficiency;
                $atmosCart->no_of_pole = $request->poles;
                $atmosCart->brand = $request->motor_brand;
                $atmosCart->master_id = $request->master_price_id;
                //depends upon pump model drop down add flow,over head,Impeller standard size,Required impeller size
                $atmosCart->flow = $request->pump_model_flow;
                $atmosCart->head = $request->pump_model_head;
                $atmosCart->impeller_standard_size = $request->pump_model_impeller_size;
                $atmosCart->required_impeller_size = $request->pump_model_required_size;
                //Asscesories
                $atmosCart->accesories_price = $request->acessories_price ?? 0;
                $atmosCart->is_accesories_manual = $request->is_acessories_price_manual;

                //Assembly Charge
                $atmosCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $atmosCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $atmosCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost
                if($request->is_shipping_charge_manual == 1){
                    $atmosCart->shipping_cost_price = $request->shipping_price_manual; 
                }else{
                    $atmosCart->shipping_cost_price =($components_price + $request->acessories_price) * $shippingPercentage;
                    $atmosCart->shipping_cost_percentage = $shippingPercentage;
                }
                $atmosCart->overhead_price = $overHead;
                $atmosCart->inter_company_margin_price = $interCompanyMargin;
                $atmosCart->application = $request->application;
                $atmosCart->price = $request->total_price;
                $atmosCart->total_price = $request->total_price;
                $atmosCart->qty = 1;
                $atmosCart->is_bareshaft_selection = "0";
                $atmosCart->user_id = auth()->user()->id;
                $atmosCart->created_at = date("Y-m-d H:i:s");
                $atmosCart->updated_at = date("Y-m-d H:i:s");
				$atmosCart->country_origin = $request->country;
                $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                $atmosCart->save();
                $atmosCartId = $atmosCart->id;
                if($request->is_acessories_price_manual1 == "0"  && $request->pump_model != "0"){
                    $this->insertItem($atmosCartId, $request);
                    $this->insertAtmosBOM($atmosCartId,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price);
                }
            } else {
                if (empty($atmosCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {
                    $pump_model_name1 = str_replace(' ', '', $pump_model_name); // Remove spaces
                    $pump_model_name2 = str_replace('/', '', $pump_model_name); // Remove slashes
					$atmosCartData = AtmosCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                    ->where('required_impeller_size', $request->pump_model_required_size)
                    ->where('is_bare_manual', $request->is_bare_shaft_price_manual)
                    ->where('bare_pump_price', $bare_shaft_price)
                    ->where('accesories_price', $acessories_price)
                     ->where(function($query) use ($shipping_cost_price,$shipping_cost_price1){
                        $query->where('shipping_cost_price', $shipping_cost_price)
                                ->orWhere('shipping_cost_price', $shipping_cost_price1);
                    })
                    ->orderBy('id', 'desc')
                    ->first();
					$new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($atmosCartData){
                            if($atmosCartData->full_article_number != "" || $atmosCartData->full_article_number != null){
                                // Replace "683" with "339"
                                if($request->country == "ksa"){
									$new_ksa_article_number = str_replace("683", "339", $atmosCartData->full_article_number);
                                }
                            }
                        }
                    }
                    $atmosCartData['price'] = round($request->total_price,2);
				    $atmosCartData['total_price'] = round($request->total_price,2);
                    $atmosCart = $atmosCartData->replicate();
                    //Asscesories Price
                    $atmosCart->accesories_price = $request->acessories_price;
                    $atmosCart->is_accesories_manual = $request->is_acessories_price_manual;
                    $atmosCart->inter_company_margin_price = $interCompanyMargin;
                    $atmosCart->user_id = auth()->user()->id;
                    $atmosCart->quotation_no = null;
                    $atmosCart->qty = 1;
					$atmosCart->country_origin = $request->country;
                    $atmosCart->is_bareshaft_selection = "0";
                    $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                    $atmosCart->save();
                    $atmosCartId = $atmosCart->id;
                    // if ($request->is_acessories_price_manual == 0) {
                        if($request->is_acessories_price_manual1 == "0"  && $request->pump_model != "0"){
                            $this->insertItem($atmosCartId, $request);
                            $this->insertAtmosBOM($atmosCartId,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price);
                        }
                    // }
                }
            }
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }

    public function insertAtmosBOM($atmosCartId,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price){
        $atmosCart = AtmosCart::where('id',$atmosCartId)->first();
        $pump_model_name = $atmosCart->pump_name;
        $material_code = DB::table('atmos_materials')->where('id',$atmosCart->material_id)->first();
        $material_code = $material_code->code;
        if($material_code == "08"){
            $material_code = "8";
        }
        $components_price = '0';
        $pump_columns = strtolower($pump_model_name);
        $pump_columns = str_replace([' ', '-','/'], '_', $pump_columns);
        $pump_columns = str_replace('n', 'n_', $pump_columns);
        $pump_columns = $pump_columns.'_d_c10x'.$material_code.'x';
        if($pump_columns != 0){
            $columns = DB::select("SHOW COLUMNS FROM atmos_bom LIKE '{$pump_columns}%'");
            $columns = $columns[0]->Field;
            $bom_records  = DB::table('atmos_bom')->where($columns,'>','0')->select('id','descriptionxx','china_article_numberxx','wme_article_numberxx',$columns)->get();
            foreach($bom_records  as $records){
                $atmos_master_pump_price = DB::table('atmos_master_pump_price')->where('china_article_number','=',$records->china_article_numberxx)->first();
                if($atmos_master_pump_price){
                    $atmosItem = new AtmosBOMItems;
                    $atmosItem->atmos_cart_id = $atmosCartId;
                    $atmosItem->item_description = $records->descriptionxx;
                    $atmosItem->wilo_artilce_no = $records->china_article_numberxx;
                    $atmosItem->qty = $records->$columns;
                    $atmosItem->is_role = "0";
                    $atmosItem->unit_price = $atmos_master_pump_price->unit_price;
                    $atmosItem->total_price = $atmosItem->qty * $atmos_master_pump_price->unit_price;
                    $atmosItem->save();
                }
            }
            
                $atmosItem = new AtmosBOMItems;
                $atmosItem->atmos_cart_id = $atmosCartId;
                $atmosItem->item_description = "Assembly Cost";
                $atmosItem->wilo_artilce_no = "";
                $atmosItem->qty = "1";
                 $atmosItem->is_role = "0";
                $atmosItem->unit_price = $assembly_cost;
                $atmosItem->total_price = $assembly_cost;
                $atmosItem->save();

                $atmosItem = new AtmosBOMItems;
                $atmosItem->atmos_cart_id = $atmosCartId;
                $atmosItem->item_description = "Testing Cost";
                $atmosItem->wilo_artilce_no = "";
                $atmosItem->qty = "1";
                 $atmosItem->is_role = "0";
                $atmosItem->unit_price = $testing_cost;
                $atmosItem->total_price = $testing_cost;
                $atmosItem->save();

                $atmosItem = new AtmosBOMItems;
                $atmosItem->atmos_cart_id = $atmosCartId;
                $atmosItem->item_description = "Balancing Cost";
                $atmosItem->wilo_artilce_no = "";
                $atmosItem->qty = "1";
                 $atmosItem->is_role = "0";
                $atmosItem->unit_price = $balancing_cost;
                $atmosItem->total_price = $balancing_cost;
                $atmosItem->save();

                $atmosItem = new AtmosBOMItems;
                $atmosItem->atmos_cart_id = $atmosCartId;
                $atmosItem->item_description = $pump_model_name;
                $atmosItem->wilo_artilce_no = "";
                $atmosItem->qty = "1";
                $atmosItem->is_role = "3";
                $atmosItem->unit_price = $supervisor_pump_model_price;
                $atmosItem->total_price = $supervisor_pump_model_price;
                $atmosItem->save();
        }
    }

    public function insert_components_item($pump_model_name,$material_code){
        $components_price = '0';
        $pump_columns = strtolower($pump_model_name);
        $pump_columns = str_replace([' ', '-','/'], '_', $pump_columns);
        $pump_columns = str_replace('n', 'n_', $pump_columns);
        if($material_code == "08"){
            $material_code = "8";
        }
        $pump_columns = $pump_columns.'_d_c10x'.$material_code.'x';
        if($pump_columns != 0){
            $columns = DB::select("SHOW COLUMNS FROM atmos_bom LIKE '{$pump_columns}%'");
            $columns = $columns[0]->Field;
            $bom_records  = DB::table('atmos_bom')->where($columns,'>','0')->select('id','descriptionxx','china_article_numberxx','wme_article_numberxx',$columns)->get();
            foreach($bom_records  as $records){
                $atmos_master_pump_price = DB::table('atmos_master_pump_price')->where('china_article_number','=',$records->china_article_numberxx)->first();
                if($atmos_master_pump_price){
                    $components_price += $atmos_master_pump_price->unit_price * $records->$columns;
                }
            }
        }
        return $components_price;
    }

    public function insertItem($atmosCartId, $request) {
        $col_name = $this->check_for_column_insert_item($request);
        $col_name = strtolower($col_name); 
        $atmosItem = new AtmosItem;
        if($col_name != 0){
            $col = DB::table("atmos_accessories_price")->where($col_name, '>', 0)->select('description', 'unit_price', 'wilo_article_number', $col_name)->get();
            foreach($col->toArray() as $c){
                 $atmos_master_pump_price = DB::table('atmos_master_pump_price')->where('china_article_number','=',$c->wilo_article_number)->first();
                $atmosItem = new AtmosItem;
                $atmosItem->atmos_cart_id = $atmosCartId;
                $atmosItem->item_description = $c->description;
                $atmosItem->wilo_artilce_no = $c->wilo_article_number;
                $atmosItem->qty = $c->$col_name;
                $atmosItem->unit_price = $atmos_master_pump_price->unit_price;
                $atmosItem->total_price = (float) $c->unit_price * (float) $atmosItem->unit_price;
                $atmosItem->save();
            }
        }
    }

    public function check_for_column_insert_item($request) {
        //Code added for search functionality
        $atmosGigaCart = DB::table('atmos_carts')->where('full_article_number','=',$request->full_article_number);
            if(auth()->user()->country_id == 6){
                $atmosGigaCart = $atmosGigaCart->orWhere('ksa_full_article_number','=',$request->full_article_number);
            }
        
        if($request->bare_shaft_selection == "1"){
            $atmosGigaCart = DB::table('atmos_carts')->where('bare_shaft_article_number','=',$request->bare_shaft_article_number);
        }
        $atmosGigaCart = $atmosGigaCart->latest('id')->first();
        if(!empty($atmosGigaCart)){
            if($request->pump_model == ""){
                $request->pump_model = $atmosGigaCart->pump_id;
            }
            $request['pump_model'] = $atmosGigaCart->pump_id;
            if($request->frame_size == ""){
                $request->frame_size = $atmosGigaCart->frame_size;
            }
                $request['frame_size'] = $atmosGigaCart->frame_size;
        }
        $columnName = $request['pump_model'] . 'x' . $request['frame_size'];
        $tbls = DB::getSchemaBuilder()->getColumnListing('atmos_accessories_price');
        $new_col = array();
        foreach($tbls as $tb){
            if (strpos($tb, strtolower($request['frame_size']))) {
                array_push($new_col, $tb);
            }
        }
        if(in_array(strtolower($columnName), $new_col)){
            return $columnName;
        }else{
            sort($new_col);
            $array_size = sizeof($new_col) - 1;
            $i = 0;
            do{
                $i++;
                $col_name = (int)$request['pump_model'] + $i . 'x' . $request['frame_size'];
                $col_name = strtolower($col_name);
                if (in_array($col_name,$new_col)) {
                    return $col_name;
                }
            $pmp_present = explode('x',$new_col[$array_size]);

            }while($i <= (int)$pmp_present[0]);
            }
    }

    public function getCartAssemblyPrices($motorPower){
        $getAPPPrice = AtmosAssemblyCostPcPk::where('power', $motorPower)->get();
        $data = [];
        if($getAPPPrice){
            $data['assembly_charge'] = $getAPPPrice[0]->assembly_charge;
            $data['painting_charge'] = $getAPPPrice[0]->painting_charge;
            $data['packing_charge'] = $getAPPPrice[0]->packing_charge;
        }
        return $data;
    }

    public function ajaxQtyUpdate(Request $request) {
        $qty = $request->qty;
        $atmosCartId = $request->atmos_cart_id;
        $atmosUpdate = AtmosCart::find($atmosCartId);
        $atmosUpdate->qty = $qty;
        if($atmosUpdate->is_bareshaft_selection != "1"){
            $atmosUpdate->total_price = $atmosUpdate->qty * $atmosUpdate->price;
        }
        else{
            $atmosUpdate->total_price = $atmosUpdate->qty * $atmosUpdate->bare_pump_price;
        }
        $atmosUpdate->save();
        $data['id'] = $atmosCartId;
        if($atmosUpdate->is_bareshaft_selection != "1"){
            $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $atmosUpdate->price);
        }
        else{
            $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $atmosUpdate->bare_pump_price);         
        }
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function removeCart($id) {
        $deleteAtmosCart = AtmosCart::where('id', $id)->delete();
        $deleteItem = AtmosItem::where('atmos_cart_id', $id)->delete();
        $deleteBOMItem = AtmosBOMItems::where('atmos_cart_id', $id)->delete();
    }

    public function cartItems($cartId, $returnDataOnly = false) {
        $adderData = [];
        $items = AtmosItem::where('atmos_cart_id', $cartId)->with('atmosCart')->get();
        $atmosBOMitems = AtmosBOMItems::where('atmos_cart_id', $cartId)->where('is_role','=','0')->get();
        $atmosBOMitemsSupervisor = AtmosBOMItems::where('atmos_cart_id', $cartId)->where('is_role','=','3')->first();
        $atmosCart = AtmosCart::where('id',$cartId)->first();
        $otpMargin = User::otp_margin_atmos();
        if((isset($items[0]->atmosCart->adder_ids) && $items[0]->atmosCart->adder_ids != null))
        {   
            $is_manual = 0;
            $ids = explode(",", $items[0]->atmosCart->adder_ids); 
            if ($ids) {
                foreach ($ids as $id) {
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = AtmosPumpType::atmos_adder_code_no_4();
                            $adderData[$id]['name'] = AtmosAdder::where('id', 4)->get()[0]->adder_list;
                            break;
                            default: //default
                            null;
                            break;
                    }
                }
            }
        }
        else{
            if(count($items) == 0){
                $items = atmosCart::where('id', $cartId)->get();
            }
            $is_manual = 1;
            $ids = explode(",", $items[0]->adder_ids);
            if ($ids && $items[0]->adder_ids != null) {
                foreach ($ids as $id) {
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = AtmosAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = AtmosPumpType::atmos_adder_code_no_4();
                            $adderData[$id]['name'] = AtmosAdder::where('id', 4)->get()[0]->adder_list;
                            break;
                            default: //default
                            null;
                            break;
                    }
                }
            }
        }
        
        if($returnDataOnly) {
            return [
                'items' => $items,
                'adderData' => $adderData,
                'cartId' => $cartId,
                'is_manual' => $is_manual ?? 0,
                'atmosBOMitems' => $atmosBOMitems,
                'atmosCart' => $atmosCart,
                'otpMargin' => $otpMargin,
                'atmosBOMitemsSupervisor' => $atmosBOMitemsSupervisor,
            ];
        }
        return view('frontend.atmos_giga.items', compact('items', 'adderData','cartId','is_manual','atmosBOMitems','atmosCart','otpMargin','atmosBOMitemsSupervisor'));
    }
    
    public function ajaxDetailModalAtmos(Request $request) {
        $adderData = [];
        $atmos_id = $request->atmos_id;
        $atmosData = atmosCart::where('id', $atmos_id)->get()[0];
        $items = AtmosItem::where('atmos_cart_id', $atmos_id)->with('atmosCart')->get();
        $getMaterial = AtmosMaterial::where('id', $atmosData->material_id)->pluck('name')[0];
        if(!empty($atmosData->adder_ids) && $atmosData->adder_ids != null){
            $adderIds = explode(",", $atmosData->adder_ids);
            if($adderIds){
                foreach ($adderIds as $id) {
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('forwinding')[0];
                            }
                            $adderData[$id]['name'] = AtmosAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('forbearing')[0];
                            }
                            $adderData[$id]['name'] = AtmosAdder::where('id', 2)->get()[0]->adder_list;

                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = AtmosMasterMotorPrice::where('id', $items[0]->atmosCart->master_id)->pluck('space_heater')[0];
                            }
                            $adderData[$id]['name'] = AtmosAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = AtmosPumpType::atmos_adder_code_no_4();
                            $adderData[$id]['name'] = ucfirst(AtmosAdder::where('id', 4)->get()[0]->adder_list);
                        break;
                        default: //default
                        null;
                        break;
                    }
                }
            }
            // $addersData = DB::table('main_electrical_list')->select('adder_list')
           //                 ->whereIn('id', $adderIds)->get();
        }
        $atmosData["power"] = $atmosData["power"] . " Kw";
        $atmosData["frequency"] = $atmosData["frequency"] . " Hz";
        $returnHTML = view('frontend.cart.atmos_detail_modal')->with('atmos_data', $atmosData)
                ->with('adderData', $adderData)
                ->with('impeller', $getMaterial)
                ->render();
        $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }

    //Function for find all details with full article number..!!
    public function searchByArticleNumber(Request $request){
        $otpMargin = User::otp_margin_atmos();
        $ksa_overHead = AtmosPumpType::atmos_ksa_over_head(); //This $overHead can be editable by admin
        $morrocco_overHead = AtmosPumpType::atmos_morrocco_over_head(); //This $overHead can be editable by admin
		$atmosCartData = AtmosCart::where('full_article_number', $request->full_article_number);
        if(auth()->user()->country_id == 6){
            $atmosCartData = $atmosCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $bareshaft_pump_article_number = $request->bareshaft_pump_article_number;
        if(!is_null($bareshaft_pump_article_number)){
            $atmosCartData = $atmosCartData->orWhere('bare_shaft_article_number','=',$bareshaft_pump_article_number);
        }
        $components_price = "0";
        $atmosCartData = $atmosCartData->latest('id')->first();
        // $request->searchByArticleNumber = "1";
        if($atmosCartData)
        {
            $getAPPPrice = AtmosAssemblyCostPcPk::where('power', $atmosCartData->power)->get();
            $request->is_acessories_price_manual1 = "0";
            $request->motor_power = $atmosCartData->power;
            $request->pump_model = $atmosCartData->pump_id;
            $request->acessories_price = $atmosCartData->accesories_price;
            $request->impeller_material = $atmosCartData->material_id;

            if($atmosCartData->pump_id != "0" && $request->searchByBarePumpArticleNumber == "1"){
                $components_price = $this->ajaxCalculate($request);
            }

            $interCompanyMargin = User::ic_margin_atmos(); // This is temporary 
            $atmosGigaPrice = 0.00;
            $optionalPrice = 0.00; //Adders Code.
            $motor_price = DB::table('atmos_master_motor_prices')
            ->where('power','=',$atmosCartData->power)
            ->where('no_of_pole','=',$atmosCartData->no_of_pole)
            ->where('voltage','=',$atmosCartData->voltage)
            ->where('frequency','=',$atmosCartData->frequency)
            ->where('efficiency','=',$atmosCartData->efficiency)
            ->where('frame_size','=',$atmosCartData->frame_size)
            // ->where('no_of_phase','=',$atmosCartData->no_of_phase)
            ->first();

            if($motor_price)
            {
                $setup_field = DB::table('setup_fields')->where('name','=','atmos_adder_code_4')->first();
                $enclousreAdderItemData = null;
                if($atmosCartData->adder_ids && $atmosCartData->adder_ids != '') {
                    $explode_ids = explode(",",$atmosCartData->adder_ids);
                    $total_adders_price = 0.00;
                    $adder_id_one_price = 0.00;
                    $adder_id_two_price = 0.00;
                    $adder_id_three_price = 0.00;
                    $adder_id_four_price = 0.00;

                    foreach($explode_ids as $key=>$value)
                    {
                            if($value == "1")
                            {
                                $adder_id_one_price = $motor_price->forwinding;
                            }
                            if($value == "2")
                            {
                                $adder_id_two_price = $motor_price->forbearing;
                            }
                            if($value == "3")
                            {
                                $adder_id_three_price = $motor_price->space_heater;
                            }
                            if($value == "4")
                            {
                                $adder_id_four_price = $setup_field->value;
                            }
                    }
                    $total_adders_price = $adder_id_one_price + $adder_id_two_price + $adder_id_three_price + $adder_id_four_price;
                }
                else{
                    $total_adders_price = 0.00;
                }
                $optionalPrice =  $total_adders_price;
                $overHead = AtmosPumpType::atmos_over_head(); //This $overHead can be editable by admin
                $assemblyPrice = $atmosCartData->assembly_charge + $atmosCartData->painting_charge + $atmosCartData->packing_charge;
                $shippingPercentage = AtmosPumpType::atmos_shipping_percentage() / 100; //This percentage can be

                if($atmosCartData->pump_id != "0"){
                    $shippingCost =($components_price + $atmosCartData->accesories_price) * $shippingPercentage;
                }
                else{
                    $shippingCost = $atmosCartData->shipping_cost_price;
                }
                // application 1 = costant && application 2 = variable
                if($atmosCartData->application == "1")
                {
                    $motor_price = $motor_price->price + $motor_price->shipping_cost;
                }
                elseif($atmosCartData->application == "2"){
                    $motor_price = $motor_price->price + $motor_price->insulate_bearing + $motor_price->shipping_cost;
                }
                else{
                    $motor_price = $motor_price->price;
                }
                
                if(auth()->user()->country_id == 6){
                $atmosGigaPrice += ((($atmosCartData->bare_pump_price + $atmosCartData->accesories_price + $motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $ksa_overHead / $interCompanyMargin;
                }
                elseif(auth()->user()->country_id == 9){
                $atmosGigaPrice += ((($atmosCartData->bare_pump_price + $atmosCartData->accesories_price + $motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $morrocco_overHead / $interCompanyMargin;
                }
                else{
                $atmosGigaPrice += (($atmosCartData->bare_pump_price + $atmosCartData->accesories_price + $motor_price + $optionalPrice + $assemblyPrice + $shippingCost) * $overHead) / $interCompanyMargin;
                }
                if(!is_null($bareshaft_pump_article_number)){
                    $getMaterial = AtmosMaterial::where('id', $atmosCartData->material_id)->pluck('name')[0];
                    $data['cp_records_html'] = "getting";
                    $data['pump_name'] = $atmosCartData->pump_name;
                    $data['impeller_material'] = $getMaterial;
                    $data['flow'] = $atmosCartData->flow;
                    $data['head'] = $atmosCartData->head;
                    $data['impeller_standard_size'] = $atmosCartData->impeller_standard_size;
                    $data['required_impeller_size'] = $atmosCartData->required_impeller_size;
                    return response()->json(array('success' => true, 'data' => $data));
                }
                else{
                    $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $atmosCartData->pump_name)
                    ->with('price', $atmosGigaPrice)
                    ->with('motor_power', $atmosCartData->power)
                    ->with('motor_brand', $atmosCartData->brand)
                    ->render();
                    $data['cp_records_html'] = $returnHTML;
                    $data['motor_power'] = $atmosCartData->power;
                    $data['pump_model'] = $atmosCartData->pump_model;
                    $data['motor_power'] = $atmosCartData->power;
                    $data['cp_price'] = number_format($atmosGigaPrice, 2);
                    $data['total_price'] = $atmosGigaPrice;
                    return response()->json(array('success' => true, 'data' => $data));
                }
            }
            else{
                if($request->searchByBarePumpArticleNumber == "1")
                {
                    $atmosCart = $atmosCartData->replicate();
                    $atmosCart->save();
                    $getMaterial = AtmosMaterial::where('id', $atmosCartData->material_id)->pluck('name')[0];
                    $data['cp_records_html'] = "getting";
                    $data['pump_name'] = $atmosCartData->pump_name;
                    $data['impeller_material'] = $getMaterial;
                    $data['flow'] = $atmosCartData->flow;
                    $data['head'] = $atmosCartData->head;
                    $data['impeller_standard_size'] = $atmosCartData->impeller_standard_size;
                    $data['required_impeller_size'] = $atmosCartData->required_impeller_size;
                    return response()->json(array('success' => true, 'data' => $data));
                }
            }
        }
     else {
        $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';
        return response()->json(array('success' => true, 'data' => $data));
        }
    }

    public function ajaxCalculateBareshaft(Request $request){
        $interCompanyMargin = User::ic_margin_atmos();
        $overHead = AtmosPumpType::atmos_over_head();
        $bare_shaft_method = $request->bare_shaft_method;
        if($bare_shaft_method == "manual"){
            $pump_model = $request->pump_model;
            $impeller_material = $request->impeller_material;
            $pump_model_impeller_size = $request->pump_model_impeller_size;
            $pump_model_required_size = $request->pump_model_required_size;
            $pump_model_name = AtmosPumpType::where('id', $pump_model)->pluck('name')[0];
            $material_code = AtmosMaterial::where('id',$impeller_material)->pluck('code')[0];
            $bare_shaft_price = $this->get_bare_shaft_price($pump_model_name,$material_code,$pump_model_impeller_size,$pump_model_required_size);
        }
        else{
            $bare_shaft_article_number = $request->bare_shaft_article_number;
            $atmosCart = AtmosCart::where('bare_shaft_article_number', $bare_shaft_article_number)->latest('id')->first();
            $pump_model_name = $atmosCart->pump_name;
            $bare_shaft_price = $atmosCart->bare_pump_price;
        }
        $bare_shaft_price = ($bare_shaft_price * $overHead) / $interCompanyMargin;
            $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $pump_model_name)
                    ->with('price', $bare_shaft_price)
                    ->with('motor_power', '')
                    ->with('motor_brand', '')
                    ->render();
                    $data['bare_shaft_price'] = number_format($bare_shaft_price,2);
                    $data['cp_records_html'] = $returnHTML;
               return response()->json(array('success'=>true,'data'=>$data));
    }

    public function get_bare_shaft_price($pump_model_name,$material_code,$pump_model_impeller_size,$pump_model_required_size){
        $components_price =  "0";
        $components_price = $this->insert_components_item($pump_model_name,$material_code);
        if($material_code == "08"){
            $material_code = "8";
        }
        $cost = DB::table('atmos_pump_assembly_cost')
                    ->where('model_name','like','%'.$pump_model_name.'%')
                    ->where('impeller_material_code',$material_code)
                    ->first();
            $assembly_cost = $cost->assmebly_cost;
            $testing_cost =  $cost->testing_cost;
            if(($pump_model_impeller_size) == ($pump_model_required_size)){
                $balancing_cost = "0";
            }
            else{
                $balancing_cost = $cost->balancing_cost;
            }
            $bare_shaft_price = $assembly_cost + $testing_cost + $components_price + $balancing_cost;
            return $bare_shaft_price;
    }

    public function addToCartBareshaft(Request $request){
        $overHead = AtmosPumpType::atmos_over_head(); 
        $otpMargin = User::otp_margin_atmos();
        $interCompanyMargin = User::ic_margin_atmos();
        if($request->is_bare_shaft_article_number_method == "manual"){
            $atmosCartData = AtmosCart::where('pump_id', $request->pump_model)
                        ->where('material_id', $request->impeller_material)
                        ->where('impeller_standard_size', $request->pump_model_impeller_size)
                        ->where('required_impeller_size', $request->pump_model_required_size)
                        ->where('flow', $request->pump_model_flow)
                        ->where('head', $request->pump_model_head)
                        ->where('is_bareshaft_selection','1')
                        ->where('user_id', auth()->user()->id)
                        ->orderBy('id', 'desc')
                        ->first();
                 $pump_model_name = AtmosPumpType::where('id', $request->pump_model)->pluck('name')[0];
                 $material_code = AtmosMaterial::where('id',$request->impeller_material)->pluck('code')[0];
                 $atmosCart = new AtmosCart;
                 $atmosCart->pump_id = $request->pump_model;
                 $atmosCart->pump_name = $pump_model_name;
                 $atmosCart->material_id = $request->impeller_material;
                 // $atmosCart->bare_pump_price = number_format($request->bare_shaft_price,2);
                 $atmosCart->flow = $request->pump_model_flow;
                 $atmosCart->head = $request->pump_model_head;
                 $atmosCart->is_bareshaft_selection = "1";
                 $atmosCart->user_id = auth()->user()->id;
                 $atmosCart->quotation_no = null;
                 $atmosCart->impeller_standard_size = $request->pump_model_impeller_size;
                 $atmosCart->required_impeller_size = $request->pump_model_required_size;
                 $atmosCart->qty = "1";
                 $bare_shaft = AtmosCart::where('pump_id', $request->pump_model)
                                    ->where('material_id', $request->impeller_material)
                                    ->where('impeller_standard_size', $request->pump_model_impeller_size)
                                    ->where('required_impeller_size', $request->pump_model_required_size)
                                    ->where('is_bareshaft_selection','1')
                                    ->orderBy('id', 'desc')
                                    ->first();  
                if($bare_shaft){
                    $atmosCart->bare_shaft_article_number = $bare_shaft->bare_shaft_article_number; 
                }
                 $bare_pump_price = $this->get_bare_shaft_price($pump_model_name,$material_code,$request->pump_model_impeller_size,$request->pump_model_required_size);
                 $bare_pump_price = ($bare_pump_price * $overHead) / $interCompanyMargin;
                 $atmosCart->bare_pump_price = $bare_pump_price;
                 $pump_model_impeller_size = $request->pump_model_impeller_size;
                 $pump_model_required_size = $request->pump_model_required_size;
                 $request['bare_shaft_selection'] = "1";
            if($atmosCartData == null){
                 $atmosCart->save();
            }
            else{
                if (empty($atmosCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                }
                else{
                    $atmosCart->save();
                }
            }
        }
        else{
            $atmosCartData = AtmosCart::where('bare_shaft_article_number', $request->bareshaft_pump_article_number)->first();
            $atmosCart = $atmosCartData->replicate();
            $atmosCart->user_id = auth()->user()->id;
            $atmosCart->is_bareshaft_selection = "1";
            $atmosCart->qty = "1";
            $atmosCart->quotation_no = null;
            $atmosCartData = AtmosCart::where('pump_id', $atmosCart->pump_id)
                        ->where('bare_shaft_article_number', $atmosCart->bare_shaft_article_number)
                        ->where('material_id', $atmosCart->material_id)
                        ->where('impeller_standard_size', $atmosCart->impeller_standard_size)
                        ->where('required_impeller_size', $atmosCart->required_impeller_size)
                        ->where('flow', $atmosCart->flow)
                        ->where('is_bareshaft_selection','1')
                        ->where('head', $atmosCart->head)
                        ->where('user_id', auth()->user()->id)
                        ->orderBy('id', 'desc')
                        ->first();
                        // dd($atmosCartData);
            if($atmosCartData == null){
                 $atmosCart->save();
            }
            else{
                if (empty($atmosCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                }
                else{
                    $atmosCart->save();
                }
            }
            // $atmosCart->save();
            $request['bare_shaft_article_number'] =  $atmosCart->bare_shaft_article_number;
            $request['pump_model'] = $atmosCart->pump_id;
            $request['frame_size'] = $atmosCart->frame_size;
            $request['bare_shaft_selection'] = "1";
            $bare_pump_price = $atmosCart->bare_pump_price;
            $pump_model_impeller_size = $atmosCart->pump_model_impeller_size;
            $pump_model_required_size = $atmosCart->pump_model_required_size;
            $pump_model_name = $atmosCart->pump_name;
        }
            $material_id = $atmosCart->material_id;
            $atmosCart_id = $atmosCart->id;
            $pump_id = $atmosCart->pump_model;
            $supervisor_pump_model_price = ($bare_pump_price * $overHead) * $otpMargin;
            $assembly_cost = $this->getAssemblyCost($pump_model_name,$material_id);
            $testing_cost = $this->getTestingCost($pump_model_name,$material_id);
            $balancing_cost = $this->getBalancingCost($pump_model_name,$material_id,$pump_model_impeller_size,$pump_model_required_size);
            if($pump_id != "0"){
                $this->insertAtmosBOM($atmosCart_id,$assembly_cost,$testing_cost,$balancing_cost,$pump_model_name,$supervisor_pump_model_price);
            }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }

    public function getAssemblyCost($pump_model_name,$material_id){
        $material_code = AtmosMaterial::where('id',$material_id)->pluck('code')[0];
        if($material_code == "08"){
            $material_code = "8";
        }
        $assembly_cost = DB::table('atmos_pump_assembly_cost')
                    ->where('model_name','like','%'.$pump_model_name.'%')
                    ->where('impeller_material_code',$material_code)
                    ->first();
        $assembly_cost = $assembly_cost->assmebly_cost;
        return $assembly_cost;
    }

    public function getTestingCost($pump_model_name,$material_id){
        $material_code = AtmosMaterial::where('id',$material_id)->pluck('code')[0];
        if($material_code == "08"){
            $material_code = "8";
        }
        $testing_cost = DB::table('atmos_pump_assembly_cost')
                    ->where('model_name','like','%'.$pump_model_name.'%')
                    ->where('impeller_material_code',$material_code)
                    ->first();
        $testing_cost = $testing_cost->testing_cost;
        return $testing_cost;
    }

    public function getBalancingCost($pump_model_name,$material_id,$pump_model_impeller_size,$pump_model_required_size){
        $material_code = AtmosMaterial::where('id',$material_id)->pluck('code')[0];
        if($material_code == "08"){
            $material_code = "8";
        }
        $balancing_cost = DB::table('atmos_pump_assembly_cost')
                    ->where('model_name','like','%'.$pump_model_name.'%')
                    ->where('impeller_material_code',$material_code)
                    ->first();
        if(($pump_model_impeller_size) == ($pump_model_required_size)){
            $balancing_cost = "0";
        }
        else{
            $balancing_cost = $balancing_cost->balancing_cost;
        }
        return $balancing_cost;
    }
}
