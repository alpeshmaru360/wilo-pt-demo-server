<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\ScpMasterMotorPrice;
use App\ScpAdder;
use App\ScpAssemblyCostPcPk;
use App\ScpCart;
use App\ScpItem;
use App\ScpPumpType;
use App\AtmosPump;
use App\User;
use App\ScpMaterial;
use App\Helpers\CurrencyHelper;

class schController extends Controller {
    public function index_home() {
        $power = DB::table('scp_master_motor_prices')->distinct()->pluck('power');
        $voltage = DB::table('scp_master_motor_prices')->distinct()->pluck('voltage');
        $brand = DB::table('scp_master_motor_prices')->distinct()->pluck('brand');
        $frequency = DB::table('scp_master_motor_prices')->distinct()->pluck('frequency');
        $poles = DB::table('scp_master_motor_prices')->distinct()->pluck('no_of_pole');
        $efficiency = DB::table('scp_master_motor_prices')->distinct()->pluck('efficiency');
        return view('frontend.sch_pump.index',compact('power', 'voltage', 'efficiency', 'poles', 'brand', 'frequency'))->with(
                        'pump_types', DB::table('scp_pump_types')->get()
                )->with(
                        'atmos_materials', DB::table('scp_materials')->get());
    }

    public function get_price(Request $request) {

        $impeller_id = DB::table('scp_materials')->where('id', $request['impeller_id'])->first();
        $pump_model = DB::table('scp_pump_types')->where('id', $request['pump_model'])->first();

        $get_price = DB::table('scp_pumps')->where('pump_id', $pump_model->id)
                        ->where('material_id', $impeller_id->id)->get();


        if (count($get_price) >= 1) {
            return $request['sg_pack'] == 1 ? $get_price[0]->gland_packed_price : $get_price[0]->mechanical_seal_price;
        } else {
            return "price not found";
        }
    }

    public function get_frame(Request $request) {
        //    dd("frame");
        $masterData = ScpMasterMotorPrice::select('id', 'frame_size')
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


        if ($request->val == 2) {

            return ScpMasterMotorPrice::where('id', $request['master_price_id'])->sum(DB::raw('price + insulate_bearing'));
        } else {

            return ScpMasterMotorPrice::where('id', $request['master_price_id'])->pluck('price')[0];
        }
    }

    public function check_for_column($request) {

        $columnName = $request['pump_id'] . 'x' . $request['frame'];

        $tbls = DB::getSchemaBuilder()->getColumnListing('scp_accessories_price');

        $new_col = array();
        foreach ($tbls as $tb) {
            if (strpos($tb, strtolower($request['frame']))) {
                array_push($new_col, $tb);
            }
        }


        if (in_array(strtolower($columnName), $new_col)) {


            return $columnName;
        } else {

            //foreach ($new_col as $nc) {

                //$available_pumps = explode("x", $nc);

                //if ((int) $available_pumps[0] > (int) $request['pump_id']) {

                //    return $nc ?? 0;
              //  }
            //}
			$i = 0;
            foreach ($new_col as $n_c) {
                // dd($n_c);
                // dd((int)$request['pump_id'] + 1);
                $i++;
                $col_name = (int)$request['pump_id'] + $i . 'x' . $request['frame'];
                // dd($col_name);
                $col_name = strtolower($col_name);
                
                if (in_array($col_name,$new_col)) {

                    return $col_name;

                } else {

                    continue;

                }

                // $i++;
            }
        }
    }

    public function get_accessories(Request $request) {

        $col_name = $this->check_for_column($request);

        if ($col_name != 0) {

            $col = DB::table("scp_accessories_price")->where($col_name, '>', 0)->select('unit_price', $col_name)->get();
            $total = 0;
            foreach ($col->toArray() as $c) {
                $total += $c->unit_price * $c->$col_name;
            }

        //            return number_format($total, 2);
            return $total;
        } else {

            return 0;
        }
    }

    public function check_for_column_insert_item($request) {
		//Code for finding price & add to cart via full article number starts..!!
		//$SCPCartData = db::table('scp_carts')->where('full_article_number','=',$request->full_article_number)->first();
        $SCPCartData = DB::table('scp_carts')->where('full_article_number','=',$request->full_article_number);
            if(auth()->user()->country_id == 6){
                $SCPCartData = $SCPCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
            }
            $SCPCartData = $SCPCartData->latest('id')->first();
        if(!empty($SCPCartData)){
            if($request->pump_model == ""){
                $request->pump_model = $SCPCartData->pump_id;
            }

            $request['pump_model'] = $SCPCartData->pump_id;
            
            if($request->frame_size == ""){
                $request->frame_size = $SCPCartData->frame_size;
            }
            $request['frame_size'] = $SCPCartData->frame_size;
        }
		//Code for finding price & add to cart via full article number ends..!!
        $columnName = $request['pump_model'] . 'x' . $request['frame_size'];

        $tbls = DB::getSchemaBuilder()->getColumnListing('scp_accessories_price');

        $new_col = array();
        foreach ($tbls as $tb) {
            if (strpos($tb, strtolower($request['frame_size']))) {
                array_push($new_col, $tb);
            }
        }


        if (in_array(strtolower($columnName), $new_col)) {


            return $columnName;
        } else {

            //foreach ($new_col as $nc) {

        //              $available_pumps = explode("x", $nc);
        //
        //            if ((int) $available_pumps[0] > (int) $request['pump_id']) {

          //              return $nc ?? 0;
            //        }
              //  }
		  $i = 0;
            foreach ($new_col as $n_c) {
                // dd($n_c);
                // dd((int)$request['pump_id'] + 1);
                $i++;
                $col_name = (int)$request['pump_model'] + $i . 'x' . $request['frame_size'];
                // dd($col_name);
                $col_name = strtolower($col_name);
                
                if (in_array($col_name,$new_col)) {

                    return $col_name;

                } else {

                    continue;

                }

                // $i++;
            }
        }
    }

    public function ajaxCalculate(Request $request) {
        $getScpPumpName = ScpPumpType::where('id', $request->pump_model)->pluck('name')[0];
        //dd($request->all());
        $scpPrice = 0.00;
        $optionalPrice = 0.00; //Adders Code
        $interCompanyMargin = User::ic_margin_scp(); // This is temporary 
        $shippingPercentage = ScpPumpType::scp_shipping_percentage() / 100; //This percentage can be editable by admin
        $overHead = ScpPumpType::scp_over_head(); //This $overHead can be editable by admin
        $assemblyPrice = 0.00;

        if ($request->code_price && $request->code_price != 'undefined') {
            $optionalPrice = $request->code_price;
        }

        $getAPPPrice = ScpAssemblyCostPcPk::where('power', $request->motor_power)->get();
        if ($getAPPPrice) {

            $assemblyPrice = $getAPPPrice[0]->assembly_charge + $getAPPPrice[0]->painting_charge + $getAPPPrice[0]->packing_charge;
        }

        $shippingCost = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;

        $scpPrice += ((($request->bare_shaft_price + $request->acessories_price + $request->motor_price + $optionalPrice + $assemblyPrice) * $overHead) + $shippingCost ) / $interCompanyMargin;

        $returnHTML = view('frontend.scp_pump.table')->with('pumpName', $getScpPumpName)
                ->with('price', $scpPrice)
                ->with('motor_power', $request->motor_power)
                ->with('motor_brand', $request->motor_brand)
                ->render();
        //         
        $data['cp_records_html'] = $returnHTML;

        $data['cp_price'] = number_format($scpPrice, 2);
        $data['total_price'] = $scpPrice;
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function ajaxOptionalSelectedAdderData(Request $request) {

        $ids = explode(",", $request->adder_ids); //Code ids
        $price = 0.00;
        if ($ids) {
            foreach ($ids as $id) {

                switch ($id) {

                    case ($id == 1):
                        $price += ScpMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forwinding')[0];

                        break;
                    case ($id == 2):
                        $price += ScpMasterMotorPrice::where('id', $request['master_price_id'])->pluck('forbearing')[0];
                        break;
                    case ($id == 3):

                        $price += ScpMasterMotorPrice::where('id', $request['master_price_id'])->pluck('space_heater')[0];
                        break;
                    case ($id == 4):

                        $price += ScpPumpType::scp_adder_code_no_4();
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
        $scpAdderData = DB::table('scp_adders')->get();
        $data = view('frontend.scp_pump.modal_optional')->with('scpAdderData', $scpAdderData)
                ->render();
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function addToCart(Request $request){
		 //code starts for search via article number
          //$SCPCartData = db::table('scp_carts')->where('full_article_number','=',$request->full_article_number)->latest('id')->first();
		  $SCPCartData = DB::table('scp_carts')->where('full_article_number','=',$request->full_article_number);
if(auth()->user()->country_id == 6){
            $SCPCartData = $SCPCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }

        $SCPCartData = $SCPCartData->latest('id')->first();
          if($request->impeller_material == null && $request->application == null && $request->adder_ids == null){
              $request->adder_ids = $SCPCartData->adder_ids;
          }
          if($request->motor_power == ""){
              $request->motor_power = $SCPCartData->power;
          }
          if($request->pump_model == ""){
              $request->pump_model = $SCPCartData->pump_id;
          }
          if($request->impeller_material == ""){
              $request->impeller_material = $SCPCartData->material_id;
          }
          if($request->application == ""){
              $request->application = $SCPCartData->application;
          }
          if($request->master_price_id == ""){
              $request->master_price_id = $SCPCartData->master_id;
          }
          if($request->bare_shaft_price == ""){
              $request->bare_shaft_price = $SCPCartData->bare_pump_price;
          }
          if($request->power_supply == ""){
              $request->power_supply = $SCPCartData->voltage;
          }

          if($request->s_g_pack == ""){
            $request->s_g_pack = $SCPCartData->seal_gland_pack_id;
          }

          // add it because add to cart not working
        if($request->frame_size == ""){
            $request->frame_size = $SCPCartData->frame_size;
        }

        if($request->frequency == ""){
            $request->frequency = $SCPCartData->frequency;
        }
        if($request->efficiency == ""){
            $request->efficiency = $SCPCartData->efficiency;
        }
        if($request->poles == ""){
            $request->poles = $SCPCartData->no_of_pole;
        }
        if($request->motor_brand == ""){
            $request->motor_brand = $SCPCartData->brand;
        }
        if($request->acessories_price == ""){
            $request->acessories_price = $SCPCartData->accesories_price;
        }
        if($SCPCartData && $request->is_acessories_price_manual == "0"){
            $request->is_acessories_price_manual = $SCPCartData->is_accesories_manual;
        }
        if($request->code_price == null){
            $request->code_price = "0.00";
        }
          //code ends for search via article number
		
        $getAssemblyPrice = $this->getCartAssemblyPrices($request->motor_power);
        $interCompanyMargin = User::ic_margin_scp(); // This is temporary 
        $shippingPercentage = ScpPumpType::scp_shipping_percentage() / 100; //This percentage can be editable by admin
        $overHead = ScpPumpType::scp_over_head(); //This $overHead can be editable by admin

        if ($request->adder_ids) {
            $scpCartData = ScpCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();
            if($scpCartData == null){
                $scpCartData1 = ScpCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();
                $scpCart = new ScpCart;
				$new_ksa_article_number = '';
                if(auth()->user()->country_id == 6){
                    if($scpCartData){
                        if($scpCartData->full_article_number != "" || $scpCartData->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpCartData->full_article_number);
								$scpCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
                    }
                    elseif($scpCartData1){
							if($scpCartData1->full_article_number != "" || $scpCartData1->full_article_number != null){
                            if($request->country == "ksa"){
                                $new_ksa_article_number = str_replace("683", "339", $scpCartData1->full_article_number);
                                $scpCart->ksa_full_article_number = $new_ksa_article_number;
                            }
                        }
}
                    else{

                    }
                }
                if($scpCartData1 != null)
                {
                    $scpCart->article_number = ($scpCartData1->article_number==null)?null:$scpCartData1->article_number;
                    $scpCart->full_article_number = $scpCartData1->full_article_number;
                    $request->code_price = $scpCartData1->total_adders_price;
                }
                //BarE sHAFT dATA
                $scpCart->pump_id = $request->pump_model;
                $scpCart->pump_name = isset(ScpPumpType::where('id', $request->pump_model)->pluck('name')[0]) ? ScpPumpType::where('id', $request->pump_model)->pluck('name')[0] : '';
                $scpCart->material_id = $request->impeller_material;
                $scpCart->seal_gland_pack_id = $request->s_g_pack;
                $scpCart->bare_pump_price = $request->bare_shaft_price;
                $scpCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                //Matrer Data
                $scpCart->power = $request->motor_power;
                $scpCart->voltage = $request->power_supply;
                $scpCart->frame_size = $request->frame_size;
                $scpCart->frequency = $request->frequency;
                $scpCart->efficiency = $request->efficiency;
                $scpCart->no_of_pole = $request->poles;
                $scpCart->brand = $request->motor_brand;
                $scpCart->master_id = $request->master_price_id;
                //Asscesories
                $scpCart->accesories_price = $request->acessories_price;
                $scpCart->is_accesories_manual = $request->is_acessories_price_manual;

                //Assembly Charge

                $scpCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $scpCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $scpCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost
                $scpCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                $scpCart->shipping_cost_percentage = $shippingPercentage;
                $scpCart->overhead_price = $overHead;
                $scpCart->inter_company_margin_price = $interCompanyMargin;

                $scpCart->adder_ids = $request->adder_ids;
                $scpCart->total_adders_price = $request->code_price;
                $scpCart->application = $request->application;

                $scpCart->price = $request->total_price;
                $scpCart->total_price = $request->total_price;
                $scpCart->qty = 1;

                $scpCart->user_id = auth()->user()->id;
                $scpCart->created_at = date("Y-m-d H:i:s");
                $scpCart->updated_at = date("Y-m-d H:i:s");
				$scpCart->country_origin = $request->country;
                $scpCart->ksa_full_article_number = $new_ksa_article_number;
                $scpCart->save();
                $scpCartId = $scpCart->id;
                if ($request->is_acessories_price_manual == 0) {
                    $this->insertItem($scpCartId, $request);
                }
            } else {
                if (empty($scpCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';
                //  dd($msg);
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {
					$new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($scpCartData){
								if($scpCartData->full_article_number != "" || $scpCartData->full_article_number != null){
                                // Replace "683" with "339"
                                if($request->country == "ksa"){
                                    $new_ksa_article_number = str_replace("683", "339", $scpCartData->full_article_number);
                                }
                            }
						}
                    }
                    $scpCart = $scpCartData->replicate();
                   // $scpCart->bare_shaft_price = $request->bare_shaft_price;
                   // $scpCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                   // Asscesories
                   $scpCart->accesories_price = $request->acessories_price;
                   $scpCart->is_accesories_manual = $request->is_acessories_price_manual;
                   // Assembly Charge
                   // $scpCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                   // $scpCart->painting_charge = $getAssemblyPrice['painting_charge'];
                   // $scpCart->packing_charge = $getAssemblyPrice['packing_charge'];
                   // Shiiping Cost
                   // $scpCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                   // $scpCart->shipping_cost_percentage = $shippingPercentage;
                   // $scpCart->overhead_price = $overHead;
                    $scpCart->inter_company_margin_price = $interCompanyMargin;

                   // $scpCart->adder_ids = $request->adder_ids;
                   // $scpCart->total_adders_price = $request->code_price;
                    $scpCart->user_id = auth()->user()->id;
                    $scpCart->price = $request->total_price;
                    $scpCart->total_price = $request->total_price;
                    $scpCart->quotation_no = null;
					$scpCart->country_origin = $request->country;
                    $scpCart->ksa_full_article_number = $new_ksa_article_number;
                    $scpCart->qty = 1;
                    $scpCart->save();
                    $scpCartId = $scpCart->id;

                    if ($request->is_acessories_price_manual == 0) {
                        $this->insertItem($scpCartId, $request);
                    }
                }
            }
        }else{
                //DB::enableQueryLog();
            $scpCartData = ScpCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();
            //dd(DB::getQueryLog());

            if ($scpCartData == null) {
                //query for find article number and full article number starts for diff user id..!!
                $scpCartData1 = ScpCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('seal_gland_pack_id', $request->s_g_pack)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();
                //query for find article number and full article number ends diff user id..!!
                $scpCart = new ScpCart;
				$new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
                        if($scpCartData){
                            if($scpCartData->full_article_number != "" || $scpCartData->full_article_number != null){
                                if($request->country == "ksa"){
                                    $new_ksa_article_number = str_replace("683", "339", $scpCartData->full_article_number);
                                    $scpCart->ksa_full_article_number = $new_ksa_article_number;
                                }
                            }
                        }
                        elseif($scpCartData1){
								if($scpCartData1->full_article_number != "" || $scpCartData1->full_article_number != null){
                                if($request->country == "ksa"){
                                    $new_ksa_article_number = str_replace("683", "339", $scpCartData1->full_article_number);
                                    $scpCart->ksa_full_article_number = $new_ksa_article_number;
                                }
}
                        }
                        else{

                        }
                    }
                if($scpCartData1 != null)
                {
                    $scpCart->article_number = ($scpCartData1->article_number==null)?null:$scpCartData1->article_number;
                    $scpCart->full_article_number = $scpCartData1->full_article_number;  
                }
                //BarE sHAFT dATA
                $scpCart->pump_id = $request->pump_model;
                $scpCart->pump_name = isset(ScpPumpType::where('id', $request->pump_model)->pluck('name')[0]) ? ScpPumpType::where('id', $request->pump_model)->pluck('name')[0] : '';
                $scpCart->material_id = $request->impeller_material;
                $scpCart->seal_gland_pack_id = $request->s_g_pack;
                $scpCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                $scpCart->bare_pump_price = $request->bare_shaft_price;

                //Matrer Data
                $scpCart->power = $request->motor_power;
                $scpCart->voltage = $request->power_supply;
                $scpCart->frame_size = $request->frame_size;
                $scpCart->frequency = $request->frequency;
                $scpCart->efficiency = $request->efficiency;
                $scpCart->no_of_pole = $request->poles;
                $scpCart->brand = $request->motor_brand;
                $scpCart->master_id = $request->master_price_id;
                //Asscesories
                $scpCart->accesories_price = $request->acessories_price ?? 0;
                $scpCart->is_accesories_manual = $request->is_acessories_price_manual;

                //Assembly Charge

                $scpCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                $scpCart->painting_charge = $getAssemblyPrice['painting_charge'];
                $scpCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //Shiiping Cost
                $scpCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                $scpCart->shipping_cost_percentage = $shippingPercentage;
                $scpCart->overhead_price = $overHead;
                $scpCart->inter_company_margin_price = $interCompanyMargin;
                $scpCart->application = $request->application;
                $scpCart->price = $request->total_price;
                $scpCart->total_price = $request->total_price;
                $scpCart->qty = 1;

                $scpCart->user_id = auth()->user()->id;
                $scpCart->created_at = date("Y-m-d H:i:s");
                $scpCart->updated_at = date("Y-m-d H:i:s");
				$scpCart->country_origin = $request->country;
                $scpCart->ksa_full_article_number = $new_ksa_article_number;
                $scpCart->save();
                $scpCartId = $scpCart->id;
                if ($request->is_acessories_price_manual == 0) {
                    $this->insertItem($scpCartId, $request);
                }
            } else {
                if (empty($scpCartData->quotation_no)) {
                    $msg = 'This item already in your cart.';   
                //dd('msg');
                    return response()->json(array('success' => true, 'msg' => $msg));
                } else {
					$scpCartData = ScpCart::where('pump_id', $request->pump_model)
                    ->where('material_id', $request->impeller_material)
                    ->where('master_id', $request->master_price_id)
                    ->where('application', $request->application)
                    ->whereNull('adder_ids')
					->orderBy('id', 'desc')
                    ->first();

                    $new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6){
						if($scpCartData){
                            if($scpCartData->full_article_number != "" || $scpCartData->full_article_number != null){
                                // Replace "683" with "339"
                                if($request->country == "ksa"){
								$new_ksa_article_number = str_replace("683", "339", $scpCartData->full_article_number);
                                }
                            }
                        }
                    }
                    $scpCart = $scpCartData->replicate();
                //                    $scpCart->bare_shaft_price = $request->bare_shaft_price;
                //                    $scpCart->is_bare_manual = $request->is_bare_shaft_price_manual;
                //
                                   //Asscesories
                                   $scpCart->accesories_price = $request->acessories_price;
                                   $scpCart->is_accesories_manual = $request->is_acessories_price_manual;
                //
                //                    //Assembly Charge
                //
                //                    $scpCart->assembly_charge = $getAssemblyPrice['assembly_charge'];
                //                    $scpCart->painting_charge = $getAssemblyPrice['painting_charge'];
                //                    $scpCart->packing_charge = $getAssemblyPrice['packing_charge'];
                //                    //Shiiping Cost
                //                    $scpCart->shipping_cost_price = ($request->bare_shaft_price + $request->acessories_price) * $shippingPercentage;
                //                    $scpCart->shipping_cost_percentage = $shippingPercentage;
                //                    $scpCart->overhead_price = $overHead;
                //                    $scpCart->inter_company_margin_price = $interCompanyMargin;
                    $scpCart->inter_company_margin_price = $interCompanyMargin;
                    $scpCart->price = $request->total_price;
                    $scpCart->total_price = $request->total_price;
                    $scpCart->quotation_no = null;
                    $scpCart->user_id = auth()->user()->id;
                    $scpCart->qty = 1;
					$scpCart->country_origin = $request->country;
                    $scpCart->ksa_full_article_number = $new_ksa_article_number;
                    $scpCart->save();
                    $scpCartId = $scpCart->id;
                    if ($request->is_acessories_price_manual == 0) {
                        $this->insertItem($scpCartId, $request);
                    }
                }
            }
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }

    public function getCartAssemblyPrices($motorPower) {
        $getAPPPrice = ScpAssemblyCostPcPk::where('power', $motorPower)->get();
        $data = [];

        if ($getAPPPrice) {

            $data['assembly_charge'] = $getAPPPrice[0]->assembly_charge;
            $data['painting_charge'] = $getAPPPrice[0]->painting_charge;
            $data['packing_charge'] = $getAPPPrice[0]->packing_charge;
        }
        return $data;
    }

    public function insertItem($scpCartId, $request) {
		$col_name = $this->check_for_column_insert_item($request);
        $scpItem = new ScpItem;
        if ($col_name != 0) {

            $col = DB::table("scp_accessories_price")->where($col_name, '>', 0)->select('description', 'unit_price','wilo_article_number', $col_name)->get();

            foreach ($col->toArray() as $c) {
                $scpItem = new ScpItem;
                $scpItem->scp_cart_id = $scpCartId;
                $scpItem->item_description = $c->description;
                $scpItem->wilo_artilce_no = $c->wilo_article_number;
                $scpItem->qty = $c->$col_name;
                $scpItem->unit_price = $c->unit_price;
                $scpItem->total_price = $c->unit_price * $c->$col_name;
                $scpItem->save();
            }
        }
    }

    public function ajaxQtyUpdate(Request $request) {
        $qty = $request->qty;
        $scpCartId = $request->scp_cart_id;
        $scpUpdate = ScpCart::find($scpCartId);
        $scpUpdate->qty = $qty;
        $scpUpdate->total_price = $scpUpdate->qty * $scpUpdate->price;
        $scpUpdate->save();
        $data['id'] = $scpCartId;
        $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $scpUpdate->price);
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function removeCart($id) {
        $deleteScpCart = ScpCart::where('id', $id)->delete();
        $deleteItem = ScpItem::where('scp_cart_id', $id)->delete();
    }

    public function cartItems($cartId) { 
        //$val is itemData
        $adderData = [];
        $items = ScpItem::where('scp_cart_id', $cartId)->with('scpCart')->get();
        if(isset($items[0]->scpCart->adder_ids) && $items[0]->scpCart->adder_ids != null){
            $is_manual = 0;
            $ids = explode(",", $items[0]->scpCart->adder_ids); //Code ids
            if($ids){
                foreach($ids as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpPumpType::scp_adder_code_no_4();
                            $adderData[$id]['name'] = ScpAdder::where('id', 4)->get()[0]->adder_list;
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
                $items = ScpCart::where('id', $cartId)->get();
            }
            $is_manual = 1;
            $ids = explode(",", $items[0]->adder_ids); //Code ids
            if($ids && $items[0]->adder_ids != null){
                foreach($ids as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forwinding')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->master_id)->pluck('forbearing')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 2)->get()[0]->adder_list;
                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->master_id)->pluck('space_heater')[0];
                            $adderData[$id]['name'] = ScpAdder::where('id', 3)->get()[0]->adder_list;
                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpPumpType::scp_adder_code_no_4();
                            $adderData[$id]['name'] = ScpAdder::where('id', 4)->get()[0]->adder_list;
                            break;
                        default: //default
                            null;
                            break;
                    }
                }
            }
        }
        return view('frontend.scp_pump.items', compact('items', 'adderData','cartId','is_manual'));
    }

    public function ajaxDetailModalScp(Request $request) {
        $adderData = [];
        $scp_id = $request->scp_id;
        $scpData = ScpCart::where('id', $scp_id)->get()[0];
        $items = ScpItem::where('scp_cart_id', $scp_id)->with('scpCart')->get();
        $getMaterial = ScpMaterial::where('id', $scpData->material_id)->pluck('name')[0];
        if(!empty($scpData->adder_ids) && $scpData->adder_ids != null){
            $adderIds = explode(",", $scpData->adder_ids);
                if($adderIds){
                foreach($adderIds as $id){
                    switch ($id) {
                        case ($id == 1):
                            $adderData[$id]['id'] = 1;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('forwinding')[0];
                            }
                            $adderData[$id]['name'] = ScpAdder::where('id', 1)->get()[0]->adder_list;
                            break;
                        case ($id == 2):
                            $adderData[$id]['id'] = 2;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('forbearing')[0];
                            }
                            $adderData[$id]['name'] = ScpAdder::where('id', 2)->get()[0]->adder_list;

                            break;
                        case ($id == 3):
                            $adderData[$id]['id'] = 3;
                            if(count($items) != 0)
                            {
                                $adderData[$id]['price'] = ScpMasterMotorPrice::where('id', $items[0]->scpCart->master_id)->pluck('space_heater')[0];
                            }
                            $adderData[$id]['name'] = ScpAdder::where('id', 3)->get()[0]->adder_list;

                            break;
                        case ($id == 4):
                            $adderData[$id]['id'] = 4;
                            $adderData[$id]['price'] = ScpPumpType::scp_adder_code_no_4();
                            $adderData[$id]['name'] = ucfirst(ScpAdder::where('id', 4)->get()[0]->adder_list);

                            break;
                        default: //default
                            null;
                            break;
                    }
                }
            }

        //            $addersData = DB::table('main_electrical_list')->select('adder_list')
        //                            ->whereIn('id', $adderIds)->get();
        }
        //        dd($adderData);
        $scpData['power'] = $scpData['power']." Kw";
        $scpData['frequency'] = $scpData['frequency']." Hz"; 
        $returnHTML = view('frontend.cart.scp_detail_modal')->with('scp_data', $scpData)
                ->with('adderData', $adderData)
                ->with('impeller', $getMaterial)
                ->render();


        $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }
	
	//Fuction for finding price and add to cart via full article number
    //('user_id', auth()->user()->id)
    public function searchByArticleNumber(Request $request) {
        //$scpCartData = ScpCart::where('full_article_number', $request->full_article_number)->latest('id')->first();
		$scpCartData = ScpCart::where('full_article_number', $request->full_article_number);
		if(auth()->user()->country_id == 6){
            $scpCartData = $scpCartData->orWhere('ksa_full_article_number','=',$request->full_article_number);
        }
        $scpCartData = $scpCartData->latest('id')->first();
        if($scpCartData)
        {
            $interCompanyMargin = User::ic_margin_scp(); // This is temporary 
            $scpPrice = 0.00;
            $optionalPrice = 0.00; //Adders Code.
            $motor_price = DB::table('scp_master_motor_prices')
            ->where('power','=',$scpCartData->power)
            ->where('no_of_pole','=',$scpCartData->no_of_pole)
            ->where('voltage','=',$scpCartData->voltage)
            ->where('frequency','=',$scpCartData->frequency)
            ->where('efficiency','=',$scpCartData->efficiency)
            ->where('frame_size','=',$scpCartData->frame_size)
            ->first();
            if($motor_price)
            {
                $setup_field = DB::table('setup_fields')->where('name','=','scp_adder_code_no_4')->first();
                $enclousreAdderItemData = null;
                if($scpCartData->adder_ids && $scpCartData->adder_ids != '') {
                $explode_ids = explode(",",$scpCartData->adder_ids);
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
				//dd($total_adders_price);
                $overHead = ScpPumpType::scp_over_head(); //This $overHead can be editable by admin
                $assemblyPrice = $scpCartData->assembly_charge + $scpCartData->painting_charge + $scpCartData->packing_charge;
                $shippingPercentage = ScpPumpType::scp_shipping_percentage() / 100; //This percentage can be editable by admin
                $shippingCost =($scpCartData->bare_pump_price + $scpCartData->accesories_price) * $shippingPercentage;
               
                if($scpCartData->application == "1")
                {
                    $motor_price = $motor_price->price;
                }
                elseif($scpCartData->application == "2"){
                    $motor_price = $motor_price->price + $motor_price->insulate_bearing;
                }
                else{
                    $motor_price = $motor_price->price;
                }
                $scpPrice = ((($scpCartData->bare_pump_price + $scpCartData->accesories_price + $motor_price + $total_adders_price + $assemblyPrice) * $overHead) + $shippingCost ) / $interCompanyMargin;
                $returnHTML = view('frontend.atmos_giga.table')->with('pumpName', $scpCartData->pump_name)
                ->with('price', $scpPrice)
                ->with('motor_power', $scpCartData->power)
                ->with('motor_brand', $scpCartData->brand)
                ->render();
                $data['cp_records_html'] = $returnHTML;
                $data['motor_power'] = $scpCartData->power;
                $data['pump_model'] = $scpCartData->pump_model;
                $data['motor_power'] = $scpCartData->power;

                $data['cp_price'] = number_format($scpPrice, 2);
                $data['total_price'] = $scpPrice;
                return response()->json(array('success' => true, 'data' => $data));
            }
        }
     else {
        $data['cp_records_html_error'] = 'This article number does not exits. Please select another article number or manually selects.';
        return response()->json(array('success' => true, 'data' => $data));
    }
}
}
