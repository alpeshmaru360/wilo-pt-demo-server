<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log; // Test
use DB;
use App\ScpvMasterMotorPrice;
use App\ScpvAdder;
use App\ScpvAssemblyCostPcPk;
use App\ScpvCart;
use App\ScpvItem;
use App\ScpvPumpType;
use App\AtmosPump;
use App\User;
use App\ScpvMaterial;
use App\Helpers\CurrencyHelper;

class ScpvController extends Controller {

    public function index() {
        $power = DB::table('scpv_master_motor_prices')->distinct()->pluck('power');
        $voltage = DB::table('scpv_master_motor_prices')->distinct()->pluck('voltage');
        $brand = DB::table('scpv_master_motor_prices')->distinct()->pluck('brand');
        $frequency = DB::table('scpv_master_motor_prices')->distinct()->pluck('frequency');
        $poles = DB::table('scpv_master_motor_prices')->distinct()->pluck('no_of_pole');
        $efficiency = DB::table('scpv_master_motor_prices')->distinct()->pluck('efficiency');

        return view('frontend.scpv_pump.index', compact('power', 'voltage', 'efficiency', 'poles', 'brand', 'frequency'))
                    ->with('pump_types', DB::table('scpv_pump_types')->get())
                    ->with('atmos_materials', DB::table('scpv_materials')->get());

    }

    public function get_price(Request $request) {
        $impeller_id = DB::table('scpv_materials')->where('id', $request['impeller_id'])->first();
        $pump_model = DB::table('scpv_pump_types')->where('id', $request['pump_model'])->first(); 
        $get_price = DB::table('scpv_pumps')->where('pump_id', $pump_model->id)
                        ->where('material_id', $impeller_id->id)->get();        

        if (count($get_price) >= 1) {
            return $request['sg_pack'] == 1 ? $get_price[0]->gland_packed_price : $get_price[0]->mechanical_seal_price;
        } else {
            return "price not found";
        }
    }
        
    // A Code: 05-03-2026 Start
    public function get_frame(Request $request)
    {
        $masterData = ScpvMasterMotorPrice::select('id', 'frame_size')
            ->where('no_of_pole', $request->poles)
            ->where('brand', $request->motor_brand)
            ->where('frequency', $request->frequency)
            ->where('power', $request->motor_power)
            ->where('efficiency', $request->effieciency)
            ->where('voltage', $request->power_supply)
            ->first();

        return $masterData ?? 0;
    }
    // A Code: 05-03-2026 End

    //table scpv_accessories_price scpv_master_motor_prices

    // A Code: 06-03-2026 Start
    public function get_motor_price(Request $request)
    {
        if ($request->val == 2) {
            return ScpvMasterMotorPrice::where('id', $request->master_price_id)
                ->selectRaw('price + insulate_bearing as total')
                ->value('total') ?? 0;
        } else {
            return ScpvMasterMotorPrice::where('id', $request->master_price_id)
                ->value('price') ?? 0;
        }
    }
    // A Code: 06-03-2026 End

    //here
    // public function check_for_column($request) {
    //     $frame = preg_replace('/[a-zA-Z]+$/', '', $request['frame']);
    //     $columnName = $request['pump_id'] . 'x' . $frame;
    //     $tbls = DB::getSchemaBuilder()->getColumnListing('scpv_accessories_price');
    //     $new_col = array();
    //     foreach ($tbls as $tb) {
    //        if (strpos($tb, strtolower($request['frame']))) {
    //             array_push($new_col, $tb);
    //         }
    //     }

    //     if (in_array(strtolower($columnName), $new_col)) {
    //         return $columnName;
    //     } else {
    //         $i = 0;
    //         foreach ($new_col as $n_c) {
    //             $i++;
    //             $col_name = (int)$request['pump_id'] + $i . 'x' . $request['frame'];
    //             $col_name = strtolower($col_name);
    //             if (in_array($col_name,$new_col)) {
    //                 return $col_name;
    //             } else {
    //                 continue;
    //             }
    //         }
    //     }
    // }

    // R Code: 17-03-2026 Start
    public function check_for_column($request)
    {
        $frame = preg_replace('/[a-zA-Z]+$/', '', $request['frame']);
        $columnName = strtolower($request['pump_id'] . 'x' . $frame);
        $tbls = DB::getSchemaBuilder()->getColumnListing('scpv_accessories_price');
        $matchedColumns = [];
        foreach ($tbls as $tb) {
            $tb_lower = strtolower($tb);
            $tb_clean = preg_replace('/[a-zA-Z]+$/', '', $tb_lower);
            if ($tb_clean == $columnName) {
                $matchedColumns[] = $tb; // store original column name
            }
        }

        if (!empty($matchedColumns)) {
            return $matchedColumns[0]; // you can change logic if needed
        }

        $i = 1;
        foreach ($tbls as $tb) {
            $tb_lower = strtolower($tb);
            $tb_clean = preg_replace('/[a-zA-Z]+$/', '', $tb_lower);
            $newColumn = strtolower(($request['pump_id'] + $i) . 'x' . $frame);
            if ($tb_clean == $newColumn) {
                return $tb;
            }
            $i++;
        }
        return null;
    }
    // R Code: 17-03-2026 End

    // A Code: 06-03-2026 Start
    public function get_accessories(Request $request)
    {    
        $col_name = $this->check_for_column($request);

        if ($col_name == 0) {
            return 0;
        }

        $rows = DB::table('scpv_accessories_price')
            ->where($col_name, '>', 0)
            ->select('unit_price', $col_name)
            ->get();

        $total = $rows->sum(function ($row) use ($col_name) {
            return $row->unit_price * $row->$col_name;
        });

        return $total;
    }
    // A Code: 06-03-2026 End

    // A Code: 06-03-2026 Start
    public function check_for_column_insert_item($request) {
        $SCPVCartData = DB::table('scpv_carts')->where('full_article_number','=',$request->full_article_number);

        if(auth()->user()->country_id == 6){
             $SCPVCartData = $SCPVCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }        
        
        $SCPVCartData = $SCPVCartData->whereNotNull('full_article_number')->where('full_article_number','!=',0)->latest('id')->first();
        // if(!empty($SCPVCartData)){
        if($SCPVCartData){
            if($request->pump_model == ""){
                $request->pump_model = $SCPVCartData->pump_id;
            }

            $request['pump_model'] = $SCPVCartData->pump_id;
            
            if($request->frame_size == ""){
                $request->frame_size = $SCPVCartData->frame_size;
            }
            $request['frame_size'] = $SCPVCartData->frame_size;
        }
		//Code for finding price & add to cart via full article number ends..!!
        $request['frame_size'] = preg_replace('/[^0-9]/', '', $request->frame_size);
        $columnName = $request['pump_model'] . 'x' . $request['frame_size'];

        $tbls = DB::getSchemaBuilder()->getColumnListing('scpv_accessories_price');

        $new_col = array();
        foreach ($tbls as $tb) {
            if (strpos($tb, strtolower($request['frame_size']))) {
                array_push($new_col, $tb);
            }
        }
        if (in_array(strtolower($columnName), $new_col)) {
            return $columnName;
        } else {
            
		  $i = 0;
            foreach ($new_col as $n_c) {
                $i++;
                $col_name = (int)$request['pump_model'] + $i . 'x' . $request['frame_size'];
                $col_name = strtolower($col_name);
                
                if (in_array($col_name,$new_col)) {

                    return $col_name;

                } else {

                    continue;

                }
            }
        }
    }
    // A Code: 06-03-2026 End

    // A Code: 16-03-2026 Start
    public function ajaxCalculate(Request $request) {
        
        // A Code: 16-03-2026 Start
        $getScpvPumpName = ScpvPumpType::where('id', $request->pump_model)->value('name');
        if(!$getScpvPumpName){
            $getScpvPumpName = $request->pump_model;
        }
        // A Code: 16-03-2026 End

        $scpvPrice = 0.00;
        $optionalPrice = 0.00; //Adders Code

        $interCompanyMargin = User::ic_margin_scpv(); // This is temporary 
        $shippingPercentage = ScpvPumpType::scpv_shipping_percentage() / 100; //This percentage can be editable by admin
        $overHead = ScpvPumpType::scpv_over_head(); //This $overHead can be editable by admin        
        $assemblyPrice = 0.00;
        
        // A Code: 16-03-2026 Start
        if (!empty($request->code_price) && $request->code_price != 'undefined') {
            $optionalPrice = $request->code_price;
        }

        $getAPPPrice = ScpvAssemblyCostPcPk::where('power', $request->motor_power)->first();
        if ($getAPPPrice) {
            $assemblyPrice = $getAPPPrice->assembly_charge 
                        + $getAPPPrice->painting_charge 
                        + $getAPPPrice->packing_charge;
        }
        // A Code: 16-03-2026 End

        $shippingCost = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
        $scpvPrice += ((((float)$request->bare_shaft_price 
            + (float)$request->acessories_price 
            + (float)$request->motor_price 
            + $optionalPrice 
            + $assemblyPrice) * $overHead) + $shippingCost) / $interCompanyMargin;
              
        $returnHTML = view('frontend.scpv_pump.table')
            ->with('pumpName', $getScpvPumpName)
            ->with('price', $scpvPrice)
            ->with('motor_power', $request->motor_power)
            ->with('motor_brand', $request->motor_brand)
            ->render();
        
        $data['cp_records_html'] = $returnHTML;
        $data['cp_price'] = number_format($scpvPrice, 2);
        $data['total_price'] = $scpvPrice;

        return response()->json(array('success' => true, 'data' => $data));
    }
    // A Code: 16-03-2026 End

    // A Code: 05-03-2026 Start
    // public function ajaxCalculate(Request $request) {

    //     if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
    //         $pump_model_name = ScpvPumpType::where('id', $request->pump_model)->pluck('name')[0];
    //         $matirial_code = ScpvPumpType::where('id',$request->impeller_material)->first();          	

    //         if($matirial_code){
    //             $material_code = $matirial_code->code;
    //             //$material_code = $matirial_code->name;
    //         }else{
    //             $material_code = null; 
    //         }
    //         if($material_code == "08"){
    //             $material_code = "8";
    //         }
            
    //         $cost = DB::table('scpv_pump_assembly_cost')
    //                 ->where('model_name','like','%'.$pump_model_name.'%')
    //                 ->where('impeller_material_code',$material_code)
    //                 ->first();
    //         //dd($material_code);
    //         $components_price =  "0";
    //         //$assembly_cost = $cost->assmebly_cost;
    //         //$testing_cost =  $cost->testing_cost;
    //         if(($request->pump_model_impeller_size) == ($request->pump_model_required_size)){
    //             $balancing_cost = "0";
    //         }
    //         else{
    //             $balancing_cost = $cost->balancing_cost;
    //         }
    //         $request->bare_shaft_price = $request->bare_shaft_price + $balancing_cost;
    //     }

    //     $getScpvPumpName = ScpvPumpType::where('id', $request->pump_model)->pluck('name');
    //     if(count($getScpvPumpName) < 1){
    //        $getScpvPumpName = $request->pump_model; 
    //     }
    //     else
    //     {
    //         $getScpvPumpName = $getScpvPumpName[0];
    //     }
    //     $scpvPrice = 0.00;
    //     $optionalPrice = 0.00; 
    //     $interCompanyMargin = User::ic_margin_scpv(); 
    //     $otpMargin = User::otp_margin_atmos(); 
    //     $shippingPercentage = ScpvPumpType::scpv_shipping_percentage() / 100; 
    //     $overHead = ScpvPumpType::scpv_over_head();
    //     $ksa_overHead = ScpvPumpType::scpv_ksa_over_head();
        
    //     $morrocco_overHead = ScpvPumpType::scpv_morrocco_over_head();
    //     $assemblyPrice = 0.00;   

    //     if ($request->code_price && $request->code_price != 'undefined') {
    //         $optionalPrice = $request->code_price;
    //     }
    //     if($request->searchByBarePumpArticleNumber != "1"){
    //         $getAPPPrice = ScpvAssemblyCostPcPk::where('power', $request->motor_power)->get();
    //         if ($getAPPPrice) {
    //             $assemblyPrice = $getAPPPrice[0]->assembly_charge + $getAPPPrice[0]->painting_charge + $getAPPPrice[0]->packing_charge;
    //         }
    //     }
    //     if($request->is_acessories_price_manual1 == "0" && $request->pump_model != "0"){
    //         $components_price = $this->insert_components_item($pump_model_name,$material_code);
    //         if($request->searchByBarePumpArticleNumber == "1"){
    //             return $components_price;
    //         }
    //         $shippingCost = ($components_price + $request->acessories_price) * $shippingPercentage;
    //     }
    //     else{
    //         $shippingCost = $request->shipping_price_manual;
    //     }
    //     if(auth()->user()->country_id == 6){
    //         $scpvPrice += ((($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $ksa_overHead / $interCompanyMargin;
    //     }
    //     elseif(auth()->user()->country_id == 9){
    //         $scpvPrice += ((($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice + $shippingCost) * $otpMargin) + $assemblyPrice) * $morrocco_overHead / $interCompanyMargin;
    //                 }
    //     else{
    //         $scpvPrice += (($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice +   $assemblyPrice + $shippingCost) * $overHead) / $interCompanyMargin;
    //     }

    //     $returnHTML = view('frontend.scpv_pump.table')->with('pumpName', $getScpvPumpName)
    //             ->with('price', $scpvPrice)
    //             ->with('motor_power', $request->motor_power)
    //             ->with('motor_brand', $request->motor_brand)
    //             ->render();        
        
    //     $data['cp_records_html'] = $returnHTML;
    //     $data['cp_price'] = number_format($scpvPrice, 2);
    //     $data['total_price'] = $scpvPrice;
    //     return response()->json(array('success' => true, 'data' => $data));
    // }

    // public function insert_components_item($pump_model_name,$material_code){
    //     $components_price = '0';
    //     $pump_columns = strtolower($pump_model_name);
    //     $pump_columns = str_replace([' ', '-','/'], '_', $pump_columns);
    //     $pump_columns = str_replace('n', 'n_', $pump_columns);
    //     if($material_code == "08"){
    //         $material_code = "8";
    //     }
    //     $pump_columns = $pump_columns.'_d_c10x'.$material_code.'x';
    //     if($pump_columns != 0){
    //         $columns = DB::select("SHOW COLUMNS FROM atmos_bom LIKE '{$pump_columns}%'");
    //         $columns = $columns[0]->Field;
    //         $bom_records  = DB::table('atmos_bom')->where($columns,'>','0')->select('id','descriptionxx','china_article_numberxx','wme_article_numberxx',$columns)->get();
    //         foreach($bom_records  as $records){
    //             $atmos_master_pump_price = DB::table('atmos_master_pump_price')->where('china_article_number','=',$records->china_article_numberxx)->first();
    //             if($atmos_master_pump_price){
    //                 $components_price += $atmos_master_pump_price->unit_price * $records->$columns;
    //             }
    //         }
    //     }
    //     return $components_price;
    // }
    // A Code: 05-03-2026 End

    public function ajaxOptionalSelectedAdderData(Request $request) {
        $ids = explode(",", $request->adder_ids); //Code ids
        $price = 0.00;
        if ($ids) {
            foreach ($ids as $id) {

                switch ($id) {

                    case ($id == 1):
                        $price += ScpvMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forwinding')[0];
                        break;
                    case ($id == 2):
                        $price += ScpvMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forbearing')[0];
                        break;
                    case ($id == 3):
                        $price += ScpvMasterMotorPrice::where('id', $request['master_price_id'])->pluck('space_heater')[0];
                        break;
                    case ($id == 4):
                        $price += ScpvPumpType::scpv_adder_code_no_4();
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
        $scpvAdderData = DB::table('scpv_adders')->get();
        $data = view('frontend.scpv_pump.modal_optional')->with('scpvAdderData', $scpvAdderData)
                ->render();
        return response()->json(array('success' => true, 'data' => $data));
    }
    
    // A Code: 09-03-2026 Start
    public function addToCart(Request $request){
		//code starts for search via article number
		$SCPVCartData = DB::table('scpv_carts')->where('full_article_number','=',$request->full_article_number);
        if(auth()->user()->country_id == 6){
            $SCPVCartData = $SCPVCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $SCPVCartData = $SCPVCartData->latest('id')->first();
        if($request->impeller_material == null && $request->application == null && $request->adder_ids == null){
            $request->adder_ids = $SCPVCartData->adder_ids;
        }
        if($request->motor_power == ""){
            $request->motor_power = $SCPVCartData->power;
        }
        if($request->pump_model == ""){
            $request->pump_model = $SCPVCartData->pump_id;
        }
        if($request->impeller_material == ""){
            $request->impeller_material = $SCPVCartData->material_id;
        }
        if($request->application == ""){
            $request->application = $SCPVCartData->application;
        }
        if($request->master_price_id == ""){
            $request->master_price_id = $SCPVCartData->master_id;
        }
        if($request->bare_shaft_price == ""){
            $request->bare_shaft_price = $SCPVCartData->bare_pump_price;
        }
        if($request->power_supply == ""){
            $request->power_supply = $SCPVCartData->voltage;
        }
        if($request->s_g_pack == ""){
            $request->s_g_pack = $SCPVCartData->seal_gland_pack_id;
        }
        // add it because add to cart not working
        if($request->frame_size == ""){
            $request->frame_size = $SCPVCartData->frame_size;
        }
        if($request->frequency == ""){
            $request->frequency = $SCPVCartData->frequency;
        }
        if($request->efficiency == ""){
            $request->efficiency = $SCPVCartData->efficiency;
        }
        if($request->poles == ""){
            $request->poles = $SCPVCartData->no_of_pole;
        }
        if($request->motor_brand == ""){
            $request->motor_brand = $SCPVCartData->brand;
        }
        if($request->acessories_price == ""){
            $request->acessories_price = $SCPVCartData->accesories_price;
        }
        if($SCPVCartData && $request->is_acessories_price_manual == "0"){
            $request->is_acessories_price_manual = $SCPVCartData->is_accesories_manual;
        }
        if($request->code_price == null){
            $request->code_price = "0.00";
        }
        //code ends for search via article number		
        $getAssemblyPrice = $this->getCartAssemblyPrices($request->motor_power);
        $interCompanyMargin = User::ic_margin_scpv(); // This is temporary 
        $shippingPercentage = ScpvPumpType::scpv_shipping_percentage() / 100; //This percentage can be editable by admin
        $overHead = ScpvPumpType::scpv_over_head(); //This $overHead can be editable by admin

        if ($request->adder_ids) {
            
            // $scpvCartData = ScpvCart::where('pump_id', $request->pump_model)
            //     ->where('material_id', $request->impeller_material)
            //     ->where('seal_gland_pack_id', $request->s_g_pack)
            //     ->where('master_id', $request->master_price_id)
            //     ->where('application', $request->application)
            //     ->where('adder_ids', $request->adder_ids)
            //     ->where('user_id', auth()->user()->id)
            //     ->orderBy('id', 'desc')
            //     ->first();

            // A Code: 20-03-2026 Start
            $pumpId = is_numeric($request->pump_model) ? (int)$request->pump_model : null;
            $scpvCartData = ScpvCart::where('pump_id', $pumpId)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();
            // A Code: 20-03-2026 End  

            if($scpvCartData == null){

                // $scpvCartData1 = ScpvCart::where('pump_id', $request->pump_model)
                //     ->where('material_id', $request->impeller_material)
                //     ->where('seal_gland_pack_id', $request->s_g_pack)
                //     ->where('master_id', $request->master_price_id)
                //     ->where('application', $request->application)
                //     ->where('adder_ids', $request->adder_ids)
                //     ->orderBy('id', 'desc')
                //     ->first();

                // A Code: 20-03-2026 Start
                $pumpId = is_numeric($request->pump_model) ? (int)$request->pump_model : null;
                $scpvCartData1 = ScpvCart::where('pump_id', $pumpId)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();
                // A Code: 20-03-2026 End  

                $scpvCart = new ScpvCart;
                $new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($scpvCartData){
                        if($scpvCartData->full_article_number != "" || $scpvCartData->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpvCartData->full_article_number);
                                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }elseif($scpvCartData1){
                        if($scpvCartData1->full_article_number != "" || $scpvCartData1->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpvCartData1->full_article_number);
                                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }else{

                    }
                }
                if($scpvCartData1 != null)
                {
                    $scpvCart->article_number = ($scpvCartData1->article_number==null)?null:$scpvCartData1->article_number;
                    $scpvCart->full_article_number = $scpvCartData1->full_article_number;
                    $request->code_price = $scpvCartData1->total_adders_price;
                }
                //BarE sHAFT dATA
                $scpvCart->pump_id = $request->pump_model;
                //$scpvCart->pump_name = isset(ScpvPumpType::where('id', $request->pump_model)->pluck('name')[0]) ? ScpvPumpType::where('id', $request->pump_model)->pluck('name')[0] : '';
                $scpvCart->pump_name = ScpvPumpType::where('id', $request->pump_model)->value('name') ?? $request->pump_model; // A Code: 19-03-2026
                $scpvCart->material_id = $request->impeller_material;
                $scpvCart->seal_gland_pack_id = $request->s_g_pack;
                $scpvCart->bare_pump_price = $request->bare_shaft_price;
                $scpvCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                //Matrer Data
                $scpvCart->power = $request->motor_power;
                $scpvCart->voltage = $request->power_supply;
                $scpvCart->frame_size = $request->frame_size;
                $scpvCart->frequency = $request->frequency;
                $scpvCart->efficiency = $request->efficiency;
                $scpvCart->no_of_pole = $request->poles;
                $scpvCart->brand = $request->motor_brand;
                $scpvCart->master_id = $request->master_price_id;
                //Asscesories
                $scpvCart->accesories_price = $request->acessories_price;
                $scpvCart->is_accesories_manual = $request->is_acessories_price_manual;

                //Assembly Charge

                $scpvCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $scpvCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $scpvCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost
                $scpvCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                $scpvCart->shipping_cost_percentage = $shippingPercentage;
                $scpvCart->overhead_price = $overHead;
                $scpvCart->inter_company_margin_price = $interCompanyMargin;

                $scpvCart->adder_ids = $request->adder_ids;
                $scpvCart->total_adders_price = $request->code_price;
                $scpvCart->application = $request->application;

                $scpvCart->price = $request->total_price;
                $scpvCart->total_price = $request->total_price;
                $scpvCart->qty = 1;

                $scpvCart->user_id = auth()->user()->id;
                $scpvCart->created_at = date("Y-m-d H:i:s");
                $scpvCart->updated_at = date("Y-m-d H:i:s");
				$scpvCart->country_origin = $request->country;
                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                $scpvCart->save();
                $scpvCartId = $scpvCart->id;
                if ($request->is_acessories_price_manual == 0) {
                    $this->insertItem($scpvCartId, $request);
                }
            } else {
                    if (empty($scpvCartData->quotation_no)) {
                        $msg = 'This item already in your cart.';
                        return response()->json(array('success' => true, 'msg' => $msg));
                    } else {
                        $new_ksa_article_number = '';
                        if(auth()->user()->country_id == 6){
                            if($scpvCartData){
                                    if($scpvCartData->full_article_number != "" || $scpvCartData->full_article_number != null){
                                    // Replace "683" with "339"
                                    if($request->country == "ksa"){
                                        $new_ksa_article_number = str_replace("683", "339", $scpvCartData->full_article_number);
                                    }
                                }
                            }
                        }
                        $scpvCart = $scpvCartData->replicate();
                        $scpvCart->accesories_price = $request->acessories_price;
                        $scpvCart->is_accesories_manual = $request->is_acessories_price_manual;
                        $scpvCart->inter_company_margin_price = $interCompanyMargin;

                        $scpvCart->user_id = auth()->user()->id;
                        $scpvCart->price = $request->total_price;
                        $scpvCart->total_price = $request->total_price;
                        $scpvCart->quotation_no = null;
                        $scpvCart->country_origin = $request->country;
                        $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                        $scpvCart->qty = 1;
                        $scpvCart->save();
                        $scpvCartId = $scpvCart->id;

                        if ($request->is_acessories_price_manual == 0) {
                            $this->insertItem($scpvCartId, $request);
                        }
                    }
            }

        }else{

            // $scpvCartData = ScpvCart::where('pump_id', $request->pump_model)
            //     ->where('material_id', $request->impeller_material)
            //     ->where('seal_gland_pack_id', $request->s_g_pack)
            //     ->where('master_id', $request->master_price_id)
            //     ->where('application', $request->application)
            //     ->whereNull('adder_ids')
            //     ->where('user_id', auth()->user()->id)
            //     ->orderBy('id', 'desc')
            //     ->first();

            // A Code: 20-03-2026 Start
            $pumpId = is_numeric($request->pump_model) ? (int)$request->pump_model : null;
            $scpvCartData = ScpvCart::where('pump_id', $pumpId)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();
            // A Code: 20-03-2026 End

            if ($scpvCartData == null) {

                //query for find article number and full article number starts for diff user id..!!
                // $scpvCartData1 = ScpvCart::where('pump_id', $request->pump_model)
                //     ->where('material_id', $request->impeller_material)
                //     ->where('seal_gland_pack_id', $request->s_g_pack)
                //     ->where('master_id', $request->master_price_id)
                //     ->where('application', $request->application)
                //     ->whereNull('adder_ids')
                //     ->orderBy('id', 'desc')
                //     ->first();

                // A Code: 20-03-2026 Start
                $pumpId = is_numeric($request->pump_model) ? (int)$request->pump_model : null;
                $scpvCartData1 = ScpvCart::where('pump_id', $pumpId)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();
                // A Code: 20-03-2026 End


                //query for find article number and full article number ends diff user id..!!
                $scpvCart = new ScpvCart;
                $new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($scpvCartData){
                        if($scpvCartData->full_article_number != "" || $scpvCartData->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpvCartData->full_article_number);
                                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }
                    elseif($scpvCartData1){
                        if($scpvCartData1->full_article_number != "" || $scpvCartData1->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpvCartData1->full_article_number);
                                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }
                    else{

                    }
                }

                if($scpvCartData1 != null)
                {
                    $scpvCart->article_number = ($scpvCartData1->article_number==null)?null:$scpvCartData1->article_number;
                    $scpvCart->full_article_number = $scpvCartData1->full_article_number;  
                }
                //BarE sHAFT dATA
                $scpvCart->pump_id = $request->pump_model;                
                //$scpvCart->pump_name = isset(ScpvPumpType::where('id', $request->pump_model)->pluck('name')[0]) ? ScpvPumpType::where('id', $request->pump_model)->pluck('name')[0] : '';
                $scpvCart->pump_name = ScpvPumpType::where('id', $request->pump_model)->value('name') ?? $request->pump_model; // A Code: 19-03-2026
                $scpvCart->material_id = $request->impeller_material;
                $scpvCart->seal_gland_pack_id = $request->s_g_pack;
                $scpvCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                $scpvCart->bare_pump_price = $request->bare_shaft_price;

                //Matrer Data
                $scpvCart->power = $request->motor_power;
                $scpvCart->voltage = $request->power_supply;
                $scpvCart->frame_size = $request->frame_size;
                $scpvCart->frequency = $request->frequency;
                $scpvCart->efficiency = $request->efficiency;
                $scpvCart->no_of_pole = $request->poles;
                $scpvCart->brand = $request->motor_brand;
                $scpvCart->master_id = $request->master_price_id;
                //Asscesories
                $scpvCart->accesories_price = $request->acessories_price ?? 0;
                $scpvCart->is_accesories_manual = $request->is_acessories_price_manual;

                //Assembly Charge

                $scpvCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $scpvCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $scpvCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost
                //$scpvCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                $scpvCart->shipping_cost_price = ((float)$request->bare_shaft_price + (float)$request->acessories_price) * $shippingPercentage; // A Code: 06-03-2026

                $scpvCart->shipping_cost_percentage = $shippingPercentage;
                $scpvCart->overhead_price = $overHead;
                $scpvCart->inter_company_margin_price = $interCompanyMargin;
                $scpvCart->application = $request->application;
                $scpvCart->price = $request->total_price;
                $scpvCart->total_price = $request->total_price;
                $scpvCart->qty = 1;

                $scpvCart->user_id = auth()->user()->id;
                $scpvCart->created_at = date("Y-m-d H:i:s");
                $scpvCart->updated_at = date("Y-m-d H:i:s");
                $scpvCart->country_origin = $request->country;
                $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                $scpvCart->save();
                $scpvCartId = $scpvCart->id;
                if ($request->is_acessories_price_manual == 0) {
                    $this->insertItem($scpvCartId, $request);
                }
            } else {
                if (empty($scpvCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';   
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {

                    // $scpvCartData = ScpvCart::where('pump_id', $request->pump_model)
                    //     ->where('material_id', $request->impeller_material)
                    //     ->where('master_id', $request->master_price_id)
                    //     ->where('application', $request->application)
                    //     ->whereNull('adder_ids')
                    //     ->orderBy('id', 'desc')
                    //     ->first();

                    // A Code: 20-03-2026 Start
                    $pumpId = is_numeric($request->pump_model) ? (int)$request->pump_model : null;
                    $scpvCartData = ScpvCart::where('pump_id', $pumpId)
                        ->where('material_id', $request->impeller_material)
                        ->where('master_id', $request->master_price_id)
                        ->where('application', $request->application)
                        ->whereNull('adder_ids')
                        ->orderBy('id', 'desc')
                        ->first();
                    // A Code: 20-03-2026 End  

                    $new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($scpvCartData){
                            if($scpvCartData->full_article_number != "" || $scpvCartData->full_article_number != null){
                                // Replace "683" with "339"
                                if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpvCartData->full_article_number);
                                }
                            }
                        }
                    }
                    $scpvCart = $scpvCartData->replicate();
                    $scpvCart->accesories_price = $request->acessories_price;
                    $scpvCart->is_accesories_manual = $request->is_acessories_price_manual;               
                    $scpvCart->inter_company_margin_price = $interCompanyMargin;
                    $scpvCart->price = $request->total_price;
                    $scpvCart->total_price = $request->total_price;
                    $scpvCart->quotation_no = null;
                    $scpvCart->user_id = auth()->user()->id;
                    $scpvCart->qty = 1;
                    $scpvCart->country_origin = $request->country;
                    $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                    $scpvCart->save();
                    $scpvCartId = $scpvCart->id;
                    if ($request->is_acessories_price_manual == 0) {
                        $this->insertItem($scpvCartId, $request);
                    }
                }
            }
            
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }
    // A Code: 09-03-2026 End

    public function getCartAssemblyPrices($motorPower) {
        $getAPPPrice = ScpvAssemblyCostPcPk::where('power', $motorPower)->get();
        $data = [];

        if ($getAPPPrice) {

            $data['assembly_charge'] = $getAPPPrice[0]->assembly_charge;
            $data['painting_charge'] = $getAPPPrice[0]->painting_charge;
            $data['packing_charge'] = $getAPPPrice[0]->packing_charge;
        }
        return $data;
    }

    public function insertItem($scpvCartId, $request) {
		$col_name = $this->check_for_column_insert_item($request);   

        $scpvItem = new ScpvItem;
        if ($col_name != 0) {

            $col = DB::table("scpv_accessories_price")->where($col_name, '>', 0)->select('description', 'unit_price','wilo_article_number', $col_name)->get();         
            foreach ($col->toArray() as $c) {              

                $scpvItem = new ScpvItem;
                $scpvItem->scpv_cart_id = $scpvCartId;
                $scpvItem->item_description = $c->description;
                $scpvItem->wilo_artilce_no = $c->wilo_article_number;
                $scpvItem->qty = $c->$col_name;
                $scpvItem->unit_price = $c->unit_price;
                $scpvItem->total_price = $c->unit_price * $c->$col_name;
                $scpvItem->save();
            }
        }
    }

    public function ajaxQtyUpdate(Request $request) {
        $qty = $request->qty;
        $scpvCartId = $request->scpv_cart_id;
        $scpvUpdate = ScpvCart::find($scpvCartId);
        $scpvUpdate->qty = $qty;
        $scpvUpdate->total_price = $scpvUpdate->qty * $scpvUpdate->price;
        $scpvUpdate->save();
        $data['id'] = $scpvCartId;
        $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $scpvUpdate->price);
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function removeCart($id) {
        $deleteScpvCart = ScpvCart::where('id', $id)->delete();
        $deleteItem = ScpvItem::where('scpv_cart_id', $id)->delete();
    }

    public function cartItems($cartId,$returnDataOnly = false) { 
        //$val is itemData
        $adderData = [];
        $items = ScpvItem::where('scpv_cart_id', $cartId)->with('scpvCart')->get();

        if(isset($items[0]->scpvCart->adder_ids) && $items[0]->scpvCart->adder_ids != null){
            $is_manual = 0;
            $ids = explode(",", $items[0]->scpvCart->adder_ids); //Code ids
            if($ids){
                foreach($ids as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpvPumpType::scpv_adder_code_no_4();
                            $adderData[$id]['name'] = ScpvAdder::where('id', 4)->get()[0]->adder_list;
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
                $items = ScpvCart::where('id', $cartId)->get();
            }
            $is_manual = 1;
            $ids = explode(",", $items[0]->adder_ids); //Code ids
            if($ids && $items[0]->adder_ids != null){
                foreach($ids as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = ScpvAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpvPumpType::scpv_adder_code_no_4();
                            $adderData[$id]['name'] = ScpvAdder::where('id', 4)->get()[0]->adder_list;
                            break;
                        default: //default
                            null;
                            break;
                    }
                }
            }
        }
        
        if($returnDataOnly) {
            $scpvCart = ScpvCart::where('id',$cartId)->first();
            $article_number = DB::table('scpv_pump_types')->where('id',$scpvCart->pump_id)->pluck('bare_shaft_article_number')->first();
            $motor_price =  DB::table('scpv_master_motor_prices')
                            ->where('brand',$scpvCart->brand)
                            ->where('power',$scpvCart->power)
                            ->where('no_of_pole',$scpvCart->no_of_pole)
                            ->where('frequency',$scpvCart->frequency)
                            ->where('voltage',$scpvCart->voltage)
                            ->get();

            $scpv_master_motor_prices_item_desc = $motor_price[0]->item_desc; // A Code: 20-03-2026
            $scpv_master_motor_prices_article_number = $motor_price[0]->wilo_article_number; // A Code: 20-03-2026

            if($scpvCart->application == 2)             {     
                $motor_price = $motor_price[0]->price + $motor_price[0]->insulate_bearing;
            }else{
                $motor_price = $motor_price[0]->price;
            }
            return [
                'items' => $items,
                'adderData' => $adderData,
                'cartId' => $cartId,
                'is_manual' => $is_manual ?? 0,
                'scpvCart' => $scpvCart,
                'motor_price' => $motor_price,
                'scpv_master_motor_prices_item_desc' => $scpv_master_motor_prices_item_desc, // A Code: 20-03-2026
                'scpv_master_motor_prices_article_number' => $scpv_master_motor_prices_article_number, // A Code: 20-03-2026
                'article_number' =>  $article_number,
            ];
        }
        
        return view('frontend.scpv_pump.items', compact('items', 'adderData','cartId','is_manual'));
    }

    public function ajaxDetailModalScpv(Request $request) {
        $adderData = [];
        $scpv_id = $request->scpv_id;
        $scpvData = ScpvCart::where('id', $scpv_id)->get()[0];
        $items = ScpvItem::where('scpv_cart_id', $scpv_id)->with('scpvCart')->get();
        $getMaterial = ScpvMaterial::where('id', $scpvData->material_id)->pluck('name')[0];
        
        if(!empty($scpvData->adder_ids) && $scpvData->adder_ids != null){
            $adderIds = explode(",", $scpvData->adder_ids);
                if($adderIds){
                foreach($adderIds as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('forwinding')[0];
                            }
                            $adderData[$id]['name'] = ScpvAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('forbearing')[0];
                            }
                            $adderData[$id]['name'] = ScpvAdder::where('id', 2)->get()[0]->adder_list;

                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpvMasterMotorPrice::where('id', $items[0]->scpvCart->master_id)->pluck('space_heater')[0];
                            }
                            $adderData[$id]['name'] = ScpvAdder::where('id', 3)->get()[0]->adder_list;

                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpvPumpType::scpv_adder_code_no_4();
                            $adderData[$id]['name'] = ucfirst(ScpvAdder::where('id', 4)->get()[0]->adder_list);

                            break;
                        default: //default
                            null;
                            break;
                    }
                }
            }
        }
        $scpvData['power'] = $scpvData['power']." Kw";
        $scpvData['frequency'] = $scpvData['frequency']." Hz"; 
        $returnHTML = view('frontend.cart.scpv_detail_modal')->with('scpv_data', $scpvData)
                ->with('adderData', $adderData)
                ->with('impeller', $getMaterial)
                ->render();


        $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }
	
    // public function searchByArticleNumber(Request $request) {
	// 	$scpvCartData = ScpvCart::where('full_article_number', $request->full_article_number);
	// 	if(auth()->user()->country_id == 6){
    //         $scpvCartData = $scpvCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
    //     }
    //     $scpvCartData = $scpvCartData->latest('id')->first();
    //     if($scpvCartData)
    //     {
    //         $interCompanyMargin = User::ic_margin_scpv(); // This is temporary 
    //         $scpvPrice = 0.00;
    //         $optionalPrice = 0.00; //Adders Code.
    //         $motor_price = DB::table('scpv_master_motor_prices')
    //         ->where('power','=',$scpvCartData->power)
    //         ->where('no_of_pole','=',$scpvCartData->no_of_pole)
    //         ->where('voltage','=',$scpvCartData->voltage)
    //         ->where('frequency','=',$scpvCartData->frequency)
    //         ->where('efficiency','=',$scpvCartData->efficiency)
    //         ->where('frame_size','=',$scpvCartData->frame_size)
    //         ->first();
    //         if($motor_price)
    //         {
    //             $setup_field = DB::table('setup_fields')->where('name','=','scpv_adder_code_no_4')->first();
    //             $enclousreAdderItemData = null;
    //             if($scpvCartData->adder_ids && $scpvCartData->adder_ids != '') {
    //             $explode_ids = explode(",",$scpvCartData->adder_ids);
    //             $total_adders_price = 0.00;
    //             $adder_id_one_price = 0.00;
    //             $adder_id_two_price = 0.00;
    //             $adder_id_three_price = 0.00;
    //             $adder_id_four_price = 0.00;

    //             foreach($explode_ids as $key=>$value)
    //             {
    //                     if($value == "1")
    //                     {
    //                         $adder_id_one_price = $motor_price->forwinding;
    //                     }
    //                     if($value == "2")
    //                     {
    //                         $adder_id_two_price = $motor_price->forbearing;
    //                     }
    //                     if($value == "3")
    //                     {
    //                         $adder_id_three_price = $motor_price->space_heater;
    //                     }
    //                     if($value == "4")
    //                     {
    //                         $adder_id_four_price = $setup_field->value;
    //                     }
    //             }
    //             $total_adders_price = $adder_id_one_price + $adder_id_two_price + $adder_id_three_price + $adder_id_four_price;
    //             }
    //             else{
    //                 $total_adders_price = 0.00;
    //             }
    //             $overHead = ScpvPumpType::scpv_over_head(); //This $overHead can be editable by admin
    //             $assemblyPrice = $scpvCartData->assembly_charge + $scpvCartData->painting_charge + $scpvCartData->packing_charge;
    //             $shippingPercentage = ScpvPumpType::scpv_shipping_percentage() / 100; //This percentage can be editable by admin
    //             $shippingCost =($scpvCartData->bare_pump_price + $scpvCartData->accesories_price) * $shippingPercentage;
               
    //             if($scpvCartData->application == "1")
    //             {
    //                 $motor_price = $motor_price->price;
    //             }
    //             elseif($scpvCartData->application == "2"){
    //                 $motor_price = $motor_price->price + $motor_price->insulate_bearing;
    //             }
    //             else{
    //                 $motor_price = $motor_price->price;
    //             }
    //             $scpvPrice = ((($scpvCartData->bare_pump_price + $scpvCartData->accesories_price + $motor_price + $total_adders_price + $assemblyPrice) * $overHead) + $shippingCost ) / $interCompanyMargin;
    //             $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $scpvCartData->pump_name)
    //             ->with('price', $scpvPrice)
    //             ->with('motor_power', $scpvCartData->power)
    //             ->with('motor_brand', $scpvCartData->brand)
    //             ->render();
    //             $data['cp_records_html'] = $returnHTML;
    //             $data['motor_power'] = $scpvCartData->power;
    //             $data['pump_model'] = $scpvCartData->pump_model;
    //             $data['motor_power'] = $scpvCartData->power;

    //             $data['cp_price'] = number_format($scpvPrice, 2);
    //             $data['total_price'] = $scpvPrice;
    //             return response()->json(array('success' => true, 'data' => $data));
    //         }
    //     }
    //     else {
    //     $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';
    //     return response()->json(array('success' => true, 'data' => $data));
    //     }
    // }

    public function searchByArticleNumber(Request $request) {
		$scpvCartData = ScpvCart::where('full_article_number', $request->full_article_number);
		if(auth()->user()->country_id == 6){
            $scpvCartData = $scpvCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $scpvCartData = $scpvCartData->latest('id')->first();
        if($scpvCartData)
        {
            $interCompanyMargin = User::ic_margin_scpv();
            
            $pump_name = ScpvPumpType::where('id',$scpvCartData->pump_id)->value('name');
            $pump_id = ScpvPumpType::where('id',$scpvCartData->pump_id)->value('id');
            
            $scpvPrice = 0.00;
            $optionalPrice = 0.00;

            $motor_price = DB::table('scpv_master_motor_prices')
            ->where('power','=',$scpvCartData->power)
            ->where('no_of_pole','=',$scpvCartData->no_of_pole)
            ->where('voltage','=',$scpvCartData->voltage)
            ->where('frequency','=',$scpvCartData->frequency)
            ->where('efficiency','=',$scpvCartData->efficiency)
            ->where('frame_size','=',$scpvCartData->frame_size)
            ->first();

            if($motor_price)
            {
                $setup_field = DB::table('setup_fields')->where('name','=','scpv_adder_code_no_4')->first();
                $enclousreAdderItemData = null;
                if($scpvCartData->adder_ids && $scpvCartData->adder_ids != '') {
                    $explode_ids = explode(",",$scpvCartData->adder_ids);
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
                $overHead = ScpvPumpType::scpv_over_head();
                $assemblyPrice = $scpvCartData->assembly_charge + $scpvCartData->painting_charge + $scpvCartData->packing_charge;
                $shippingPercentage = ScpvPumpType::scpv_shipping_percentage() / 100;
                $shippingCost =($scpvCartData->bare_pump_price + $scpvCartData->accesories_price) * $shippingPercentage;
               
                if($scpvCartData->application == "1")
                {
                    $motor_price = $motor_price->price;
                }elseif($scpvCartData->application == "2"){
                    $motor_price = $motor_price->price + $motor_price->insulate_bearing;
                }else{
                    $motor_price = $motor_price->price;
                }
                $scpvPrice = ((($scpvCartData->bare_pump_price 
                                + $scpvCartData->accesories_price 
                                + $motor_price 
                                + $total_adders_price 
                                + $assemblyPrice) * $overHead) + $shippingCost ) / $interCompanyMargin;
                $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $scpvCartData->pump_name)
                                ->with('price', $scpvPrice)
                                ->with('motor_power', $scpvCartData->power)
                                ->with('motor_brand', $scpvCartData->brand)
                                ->render();
                $data['cp_records_html'] = $returnHTML;
                $data['motor_power'] = $scpvCartData->power;
                $data['pump_model'] = $scpvCartData->pump_model;
                $data['motor_power'] = $scpvCartData->power;

                $data['cp_price'] = number_format($scpvPrice, 2);
                $data['total_price'] = $scpvPrice;
                return response()->json(array('success' => true, 'data' => $data));
            }
        }
        else {
        $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';
        return response()->json(array('success' => true, 'data' => $data));
        }
    }
    
}