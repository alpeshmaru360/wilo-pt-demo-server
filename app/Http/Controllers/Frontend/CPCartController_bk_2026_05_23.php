<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BoosterCart;
use Illuminate\Http\Request;
use App\Traits\ControlPanelModelIdGet;
use App\ControlPanelCart;
use App\ControlPanel;
use App\Tax;
use App\Item;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;
use App\Helpers\AdderHelper;
use App\AtmosCart;
use App\ScpCart;
use App\ScpvCart;
use App\User;
use App\Helpers\CurrencyHelper;
use App\Models\FireFighting\FireFightingCarts;

class CPCartController extends Controller {
// control_panel_price_for_booster
    use ControlPanelModelIdGet;

    public function index($id){
        $items = Item::where('cp_cart_id', $id)->get();
        $atmosCartData = AtmosCart::cartData();
        $scpCartData = ScpCart::cartData();
        $scpvCartData = ScpvCart::cartData();
        $boosterCartData = BoosterCart::cartData();
        $controlPanelCartData = ControlPanelCart::where('user_id', auth()->user()->id)
                ->whereNull('quotation_no')
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
		$fireFightingCartData = FireFightingCarts::where('user_id', auth()->user()->id)->whereNull('quotation_no')->get();

		return view('frontend.cart.index', compact('items', 'controlPanelCartData', 'atmosCartData', 'scpCartData', 'scpvCartData', 'boosterCartData', 'fireFightingCartData'));
        
    }

    public function addToCart(Request $request) 
    {
        if($request->adder_ids){
            $controlPanelCartData = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                    ->where('user_id', auth()->user()->id)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();


            if($controlPanelCartData == null){
                $controlPanelCartData1 = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();
            
                $controlPanelCart = new ControlPanelCart;
                if($controlPanelCartData1)
                {
                    $request->no_of_pump = $controlPanelCartData1->no_of_pump_id;
                    $request->power_rating = $controlPanelCartData1->power_id;
                    $request->voltage = $controlPanelCartData1->voltage_id;
                    $request->application = $controlPanelCartData1->application_id;
                    $request->ambient_temp = $controlPanelCartData1->ambient_temp_id;
                    $request->stater_type = $controlPanelCartData1->stater_type_id;
                    $request->communication_protocol = $controlPanelCartData1->components_id;
                    $request->ip_rating = $controlPanelCartData1->ip_rating_id;
                    $request->component = $controlPanelCartData1->components_id;
                    $request->enclosure = $controlPanelCartData1->enclosure_id;
                    $request->communication_protocol = $controlPanelCartData1->components_id;
                    $controlPanelCart->article_number = $controlPanelCartData1->article_number;
                    $controlPanelCart->full_article_number = $controlPanelCartData1->full_article_number;
                    
                    //Booster electrical article number either manual or search code starts..!!
                    if($controlPanelCartData1->full_article_number == null || $controlPanelCartData1->full_article_number == "0"){
                        $test = BoosterCart::where('cp_id',$controlPanelCartData1->control_panel_id)->first();
                        if($test)
                        {
                            $controlPanelCart->full_article_number = $test->electrical_article_number;
                        }
                    }
                    //Booster electrical article number either manual or search code ends..!!
                }
                else{
                    $controlPanelCartData1 = BoosterCart::where('cp_id', $request->control_panel_id)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();
                    $article_number = $controlPanelCartData1->article_number;
                    $full_article_number = $controlPanelCartData1->full_article_number;
                    // dd($controlPanelCartData1);
                    if($controlPanelCartData1)
                    {
                        $controlPanelCartData1 = ControlPanel::where('id', $controlPanelCartData1->cp_id)->first();
                    }
                    if($controlPanelCartData1){
                        $request->no_of_pump = $controlPanelCartData1->no_of_pump_id;
                        $request->power_rating = $controlPanelCartData1->power_id;
                        $request->voltage = $controlPanelCartData1->voltage_id;
                        $request->application = $controlPanelCartData1->application_id;
                        $request->ambient_temp = $controlPanelCartData1->ambient_temp_id;
                        $request->stater_type = $controlPanelCartData1->stater_type_id;
                        $request->communication_protocol = $controlPanelCartData1->components_id;
                        $request->ip_rating = $controlPanelCartData1->ip_rating_id;
                        $request->component = $controlPanelCartData1->components_id;
                        $request->enclosure = $controlPanelCartData1->enclosure_id;
                        $request->communication_protocol = $controlPanelCartData1->components_id;
                        // $controlPanelCart->article_number = $controlPanelCartData1->article_number;

                        // $controlPanelCart->full_article_number = $controlPanelCartData1->full_article_number;
                        //Booster electrical article number either manual or search code starts..!!
                        // if($controlPanelCartData1->full_article_number == null || $controlPanelCartData1->full_article_number == "0"){
                        //     $test = BoosterCart::where('cp_id',$controlPanelCartData1->id)->first();
                        //     dd($test);
                        //     if($test)
                        //     {
                        //         $controlPanelCart->full_article_number = $test->electrical_article_number;
                        //         $controlPanelCart->article_number = $test->article_number;
                        //     }
                        //     else{
                        //         $test = BoosterCart::where('cp_id',$controlPanelCartData1->id)->first();
                        //         if($test)
                        //         {
                        //         }
                        //     }
                        // }
                        //Booster electrical article number either manual or search code ends..!!
                    }
                    $controlPanelCart->full_article_number = $full_article_number;
                    $controlPanelCart->article_number = $article_number;
                }

                $controlPanelCart->control_panel_id = $request->control_panel_id;
                $controlPanelCart->no_of_pump_id = $request->no_of_pump;
                $controlPanelCart->power_id = $request->power_rating;
                $controlPanelCart->voltage_id = $request->voltage;
                $controlPanelCart->application_id = $request->application;
                $controlPanelCart->ambient_temp_id = $request->ambient_temp;
                $controlPanelCart->stater_type_id = $request->stater_type;
                $controlPanelCart->communication_protocol_id = $request->communication_protocol;
                $controlPanelCart->ip_rating_id = $request->ip_rating;
                $controlPanelCart->components_id = $request->component;
                $controlPanelCart->enclosure_id = $request->enclosure;
                $controlPanelCart->range = $this->getIdByValue('App\Range', 'value', $request->range);
                $controlPanelCart->folder_name = '';
                $controlPanelCart->file_name_under_folder = '';
                $controlPanelCart->price = $request->total_price;
                $controlPanelCart->qty = 1;
                $controlPanelCart->tax = Tax::where('id', 1)->get()[0]->amount;
                $controlPanelCart->total_price = $request->total_price;
                $controlPanelCart->user_id = auth()->user()->id;
                $controlPanelCart->adder_ids = $request->adder_ids;
                $controlPanelCart->overhead = ControlPanel::controlpanel_over_head();
                $controlPanelCart->intercompany_margin = User::ic_margin_control_panel();
                $controlPanelCart->created_at = date("Y-m-d H:i:s");
                $controlPanelCart->updated_at = date("Y-m-d H:i:s");
                if($request->enclosure == null)
                {
                    $request->enclosure = $controlPanelCartData1->enclosure_id;
                }

                $controlPanelCart->save();
                $cpId = $controlPanelCart->id;
                //below
                $data = $this->getControlPanelDataItemSave($request, $request->enclosure, $cpId, $request->control_panel_id);
                $controlPanelUpdate = $controlPanelCart::find($cpId);
                $controlPanelUpdate->starter_code = $data['starter_code'];
                $controlPanelUpdate->range = $data['range'];
                $controlPanelUpdate->save();
            }else{
                if(empty($controlPanelCartData->quotation_no)){
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                }else{
                    $controlPanelCart = $controlPanelCartData->replicate();

                    $controlPanelCart->quotation_no = null;
                    $controlPanelCart->qty = 1;
                    $controlPanelCart->save();
                    $cpId = $controlPanelCart->id;
                    if($request->enclosure == null)
                    {
                        $request->enclosure = $controlPanelCart->enclosure_id;
                    }
                    $data = $this->getControlPanelDataItemSave($request, $request->enclosure, $cpId, $request->control_panel_id);
                    $controlPanelUpdate = $controlPanelCart::find($cpId);
                    $controlPanelUpdate->price = $request->total_price;
                    $controlPanelUpdate->total_price = $request->total_price;
                    $controlPanelUpdate->starter_code = $data['starter_code'];
                    $controlPanelUpdate->range = $data['range'];
                    $controlPanelUpdate->save();
                }
            }
        }else{
            $controlPanelCartData = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                    ->where('user_id', auth()->user()->id)
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();
            if($controlPanelCartData == null){
                $controlPanelCartData1 =ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                        ->whereNull('adder_ids')
                        ->orderBy('id', 'desc')
                        ->first();
                $controlPanelCart = new ControlPanelCart;

                if($controlPanelCartData1)
                {
                    $request->no_of_pump = $controlPanelCartData1->no_of_pump_id;
                    $request->power_rating = $controlPanelCartData1->power_id;
                    $request->voltage = $controlPanelCartData1->voltage_id;
                    $request->application = $controlPanelCartData1->application_id;
                    $request->ambient_temp = $controlPanelCartData1->ambient_temp_id;
                    $request->stater_type = $controlPanelCartData1->stater_type_id;
                    $request->communication_protocol = $controlPanelCartData1->components_id;
                    $request->ip_rating = $controlPanelCartData1->ip_rating_id;
                    $request->component = $controlPanelCartData1->components_id;
                    $request->enclosure = $controlPanelCartData1->enclosure_id;
                    $request->communication_protocol = $controlPanelCartData1->components_id;
                    ;
                    $controlPanelCart->article_number = $controlPanelCartData1->article_number;
                    $controlPanelCart->full_article_number = $controlPanelCartData1->full_article_number;
                    //Booster electrical article number either manual or search code starts..!!
                    if($controlPanelCartData1->full_article_number == null || $controlPanelCartData1->full_article_number == "0"){
                        $test = BoosterCart::where('cp_id',$controlPanelCartData1->control_panel_id)->first();
                        if($test)
                        {
                            $controlPanelCart->full_article_number = $test->electrical_article_number;
                        }
                    }
                    //Booster electrical article number either manual or search code ends..!!
                }

                  else{
                    $controlPanelCartData1 = BoosterCart::where('cp_id', $request->control_panel_id)
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();

                    $booster_data = BoosterCart::where('cp_id', $request->control_panel_id)
                    ->whereNull('adder_ids')
                    ->orderBy('id', 'desc')
                    ->first();

                    if($controlPanelCartData1)
                    {
                        $controlPanelCartData1 = ControlPanel::where('id', $controlPanelCartData1->cp_id)
                        ->first();
                    }
                    if($controlPanelCartData1){
                        $request->no_of_pump = $controlPanelCartData1->no_of_pump_id;
                        $request->power_rating = $controlPanelCartData1->power_id;
                        $request->voltage = $controlPanelCartData1->voltage_id;
                        $request->application = $controlPanelCartData1->application_id;
                        $request->ambient_temp = $controlPanelCartData1->ambient_temp_id;
                        $request->stater_type = $controlPanelCartData1->stater_type_id;
                        $request->communication_protocol = $controlPanelCartData1->components_id;
                        $request->ip_rating = $controlPanelCartData1->ip_rating_id;
                        $request->component = $controlPanelCartData1->components_id;
                        $request->enclosure = $controlPanelCartData1->enclosure_id;
                        $request->communication_protocol = $controlPanelCartData1->components_id;
                        $controlPanelCart->article_number = $booster_data->article_number;
                        $controlPanelCart->full_article_number = $booster_data->full_article_number;                        
                        //Booster electrical article number either manual or search code starts..!!
                        if($booster_data->full_article_number == null || $booster_data->full_article_number == "0"){
                            $test = BoosterCart::where('cp_id',$controlPanelCartData1->control_panel_id)->first();
                            if($test)
                            {
                                $controlPanelCart->full_article_number = $test->electrical_article_number;
                                $controlPanelCart->article_number = $test->article_number;
                            }
                            else{
                                $test = BoosterCart::where('cp_id',$controlPanelCartData1->id)->first();
                                if($test)
                                {
                                    $controlPanelCart->full_article_number = $test->electrical_article_number;
                                    $controlPanelCart->article_number = $test->article_number;
                                }
                            }
                        }
                        //Booster electrical article number either manual or search code ends..!!
                    }
                }

                $controlPanelCart->control_panel_id = $request->control_panel_id;
                $controlPanelCart->no_of_pump_id = $request->no_of_pump;
                $controlPanelCart->power_id = $request->power_rating;
                $controlPanelCart->voltage_id = $request->voltage;
                $controlPanelCart->application_id = $request->application;
                $controlPanelCart->ambient_temp_id = $request->ambient_temp;
                $controlPanelCart->stater_type_id = $request->stater_type;
                $controlPanelCart->communication_protocol_id = $request->communication_protocol;
                $controlPanelCart->ip_rating_id = $request->ip_rating;
                $controlPanelCart->components_id = $request->component;
                $controlPanelCart->enclosure_id = $request->enclosure;
                $controlPanelCart->range = $this->getIdByValue('App\Range', 'value', $request->range);
                $controlPanelCart->folder_name = '';
                $controlPanelCart->file_name_under_folder = '';
                $controlPanelCart->price = $request->total_price;
                $controlPanelCart->qty = 1;
                $controlPanelCart->tax = Tax::where('id', 1)->get()[0]->amount;
                $controlPanelCart->total_price = $request->total_price;
                $controlPanelCart->user_id = auth()->user()->id;
                $controlPanelCart->overhead = ControlPanel::controlpanel_over_head();
                $controlPanelCart->intercompany_margin = User::ic_margin_control_panel();
                $controlPanelCart->created_at = date("Y-m-d H:i:s");
                $controlPanelCart->updated_at = date("Y-m-d H:i:s");
                $controlPanelCart->save();
                $cpId = $controlPanelCart->id;
                if($request->enclosure == null)
                {
                    $request->enclosure = $controlPanelCartData1->enclosure_id;
                }
                $data = $this->getControlPanelDataItemSave($request,$request->enclosure, $cpId, $request->control_panel_id);
                $controlPanelUpdate = $controlPanelCart::find($cpId);
                $controlPanelUpdate->starter_code = $data['starter_code'];
                $controlPanelUpdate->range = $data['range'];
                $controlPanelUpdate->save();
            }else{
                if(empty($controlPanelCartData->quotation_no)){
                    $msg = 'This item already in your cart.';
                    return response()->json(array('success' => true, 'msg' => $msg));
                }else{
                    $controlPanelCart = $controlPanelCartData->replicate();
                    $controlPanelCart->quotation_no = null;
                    $controlPanelCart->qty = 1;
                    $controlPanelCart->save();
                    $cpId = $controlPanelCart->id;
                    if($request->enclosure == null)
                    {
                        $request->enclosure = $controlPanelCart->enclosure_id;
                    }
                    $data = $this->getControlPanelDataItemSave($request,$request->enclosure, $cpId, $request->control_panel_id);
                    $controlPanelUpdate = $controlPanelCart::find($cpId);
                    $controlPanelUpdate->price = $request->total_price;
                    $controlPanelUpdate->total_price = $request->total_price;
                    $controlPanelUpdate->starter_code = $data['starter_code'];
                    $controlPanelUpdate->range = $data['range'];
                    $controlPanelUpdate->save();
                }
            }
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }

    public function getControlPanelDataItemSave($request, $enclousre, $cpId, $controlPanelId) 
    {
        $controlPanelData = ControlPanel::where('id', $controlPanelId)->with('noofpumps')
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
        // dd($columnName,$controlPanelData[0]['table_name']);
        if (Schema::hasTable($tableName)){
            if (Schema::hasColumn($tableName, $columnName)){
                $cpRecords = DB::table($tableName)
                            ->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                            ->whereNotNull($columnName)
                            ->where($columnName, '!=', 0)
                            ->get();

                $cpRecords1 = DB::table($tableName)
                            ->select('item_description', 'material_number', 'wilo_article_number', 'weight', 'brand_code', 'function_code', 'range', 'unit_price', 'margin', $columnName)
                            ->whereNotNull($columnName)
                            ->where($columnName, '!=', 0)
                            ->where('function_code','=','1')
                            ->count();

                $arrayResult = json_decode(json_encode($cpRecords), true);
                $enclousreAdderItemData = null;
                if ($request->adder_ids) {
                    $price = $this->addersData($request, $cpId, $controlPanelData[0]->noofpumps['id'], $controlPanelData[0]->powers['id'], $controlPanelData[0]->voltages['id']);
                    $enclousreAdderItemData = $request->enclousreItem;
                }
                if ($arrayResult) {
                    $i= 1;
                    foreach ($arrayResult as $key => $val) {
                        //dd($val, $request,$enclousre, $columnName, $cpId, $enclousreAdderItemData,$i,$cpRecords1);
                        $price = $this->calculatePriceInItem($val, $request,$enclousre, $columnName, $cpId, $enclousreAdderItemData,$i,$cpRecords1);
                        $i++;
                    }
                    $tax = Tax::where('id', 1)->get()[0]->amount;
                    return ['price' => $price, 'starter_code' => $starterCode, 'range' => $controlPanelData[0]->range];
                }
            }
        }
        return;
    }

    public function getMasterSheetPriceData($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('price')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->price)){
            return (float) $masterData[0]->price;
        }
        return 0;
    }

    public function getMasterSheetPriceData1($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('brand_code')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->brand_code)) {
            return (float) $masterData[0]->brand_code;
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

    public function ajaxQtyUpdate(Request $request) {
        $qty = $request->qty;
        $cpId = $request->cp_id;
        $controlPanelUpdate = ControlPanelCart::find($cpId);
        $controlPanelUpdate->qty = $qty;
        $controlPanelUpdate->total_price = $controlPanelUpdate->qty * $controlPanelUpdate->price;
        $controlPanelUpdate->save();
        $data['id'] = $cpId;
        $data['total_price_update'] = CurrencyHelper::withCurrency($qty * $controlPanelUpdate->price);

        return response()->json(array('success' => true, 'data' => $data));
    }

    public function calculatePriceInItem($val, $request, $enclosure ,$columnName, $cpId, $enclousreAdderItemData,$i,$enclosure_count) 
    {
        $price = 0.00;
        //2 metal 3 GRP 4 stainless
        $enclousreItem = json_decode($request->enclousreItem, true);
        if($enclousreAdderItemData && !empty($enclousreAdderItemData && $request->enclosure != 3  && $request->enclosure != 4))  
        {
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code'])
            {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                //if($i==1){
                    $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
                   //     }
            }
        }
        if($enclousreAdderItemData && !empty($enclousreAdderItemData && $request->enclosure == 3) &&  !empty($enclousreItem) && $enclosure == 3)  
        {
            //check this
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
               if($i==1){
                       }
            }
        }

        if($enclousreAdderItemData && !empty($enclousreAdderItemData && $request->enclosure == 4) &&  !empty($enclousreItem) && $enclosure == 4)  
        {
            $enclousreItem = json_decode($request->enclousreItem, true);
            if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
                if($enclosure_count == "1")
                {
                    $val['range'] = $enclousreItem['range'];
                }
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price // 2 parameter is equal to brand code
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
               if($i==1){
                }
            }
        }
        
        //$request->component = 2 = means Economic
        if($request->component == 2 && $val['brand_code'] == 1) { // component 2 =  Economic
            if ($this->getMasterSheetPriceData(2, $val['function_code'], $val['range'])) {
                $price = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                $this->itemSave($val, 2, $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            else{
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
        }
        
        //$request->component = 3 = means Schneider
        if($request->component == 3 && $val['brand_code'] == 1) { // component 3 =  Schneider
            if ($this->getMasterSheetPriceData(34, $val['function_code'], $val['range'])) {
                $price = $this->getMasterSheetPriceData(34, $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData(34, $val['function_code'], $val['range']);
                $this->itemSave($val, 34, $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            else{
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
        }

        //$request->component = 4 = means Lovato
        if($request->component == 4 && $val['brand_code'] == 1) { // component 4 =  Lovato
            if ($this->getMasterSheetPriceData(35, $val['function_code'], $val['range'])) {
                $price = $this->getMasterSheetPriceData(35, $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData(35, $val['function_code'], $val['range']);
                $this->itemSave($val, 35, $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            else{
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
        }
        
        else if ($request->enclosure == 3 && $val['brand_code'] == 5 && $val['function_code'] == 1 ) { 
            //3 equal GRP
            if($this->getMasterSheetPriceData(31, 63, $val['range']))
            {
                $price = $this->getMasterSheetPriceData(31, 63, $val['range']) * $val[$columnName]; 
                $unitPrice = $this->getMasterSheetPriceData(31, 63, $val['range']);
                $this->itemSave($val, 31, 63, $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            else {
                $price = 0.00;
            }
        } 
        
        else if ($request->enclosure == 4 && $val['brand_code'] == 5 && $val['function_code'] == 1) { //4 equal Stainless
            if($this->getMasterSheetPriceData(5, 64, $val['range'])) {
            $price = $this->getMasterSheetPriceData(5, 64, $val['range']) * $val[$columnName]; //Qty * price
            $unitPrice = $this->getMasterSheetPriceData(5, 64, $val['range']);
            $this->itemSave($val, 5, 64, $val['range'], $columnName, $cpId, $price, $unitPrice);
                }
    
                else {
                    $price = 0.00;
                }
        }

        else if ($request->enclosure == 2 && $request->stater_type == 1 && $val['brand_code'] == 8) {
            //2 equal META; 1 XTREME
            if ($this->getMasterSheetPriceData(32, $val['function_code'], $val['range'])) {
            $price = $this->getMasterSheetPriceData(32, $val['function_code'], $val['range']) * $val[$columnName]; //Qty * price
            $unitPrice = $this->getMasterSheetPriceData(32, $val['function_code'], $val['range']);
            $this->itemSave($val, 32, $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            else {
                $price = 0.00;
            }
        } 

        else
        {
            if ($enclousreAdderItemData && !empty($enclousreAdderItemData)) {
                $enclousreItem = json_decode($request->enclousreItem, true);
                if ($val['brand_code'] == $enclousreItem['brand_code'] && $val['function_code'] == $enclousreItem['function_code']) {
            } else {
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
            } else {
                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$columnName];
                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $columnName, $cpId, $price, $unitPrice);
            }
        }

        return $price;
    }

    public function addersData(Request $request, $cpId, $noofpump, $power, $voltage) {
        $noOfPump = $noofpump;
        $motorPower = $power;
        $voltage = $voltage;
        $ids = explode(",", $request->adder_ids); //Code ids
        $price = 0.00;
        $encloureArea = 0.00;
        if ($ids) {
            foreach ($ids as $id) {
                switch ($id) {
                    case ($id >= 1 && $id <= 26): //electrical_common_adder code
                        $electricalCommonAdders = DB::table('electrical_common_adder')->select('id', 'item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $id)
                                        ->whereNotNull($id)->where($id, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
                        if ($arrayResult) {
                            foreach ($arrayResult as $key => $val) {
                                if ($request->component == 2 && $val['brand_code'] == "1")
                                {
                                    //$val['brand_code'] = "2";
                                    //echo $exist;
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = "2";
                                    }
                                }
                                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$id], $cpId, $price, $unitPrice, $id);
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
                                if ($request->component == 2 && $val['brand_code'] == "1")
                                {
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = "2";
                                    }
                                }
                                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
                                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column);
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
                                if ($request->component == 2 && $val['brand_code'] == "1")
                                {
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if($exist)
                                    {
                                        $val['brand_code'] = "2";
                                    }
                                }
                                $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
                                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
                            }
                        }
                        break;
                    case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code
                        // $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage,'1');

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
                                if ($id >= 45 && $id <= 52) {
                                    if ($request->component == 2 && $val['brand_code'] == "1")
                                    {
                                       $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                       if($exist)
                                       {
                                           $val['brand_code'] = "2";
                                       }
                                    }
                                    $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                                    $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
                                } else {
                                    $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                                    $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                    $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
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

    public function getControlPanelRangeAndCode($request) {
        $returnRangeAndCode = [];
        $controlPanelData = ControlPanel::where('id', $request->cp_id)->get();

        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code
        );
    }

    public function getMasterSheetHeightMultiplyByWidth($brand_code, $function_code, $range) {
        $height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $width = $this->getMasterSheetWidth($brand_code, $function_code, $range);
        if ($height && $width) {
            return $height * $width;
        }
        return 0;
    }

    public function getControlPanelItemEnclousreAreaFormula($tableName, $columnName, $totalEnclousreArea){
        $enclousreItem = null;
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

    public function itemSave($val = [],$brand_code = null, $function_code= null, $range= null, $columnName = null, $cpId= null, $totalPrice= null, $unitPrice= null, $adderCode = null, $noOfPump = null) {
        $item = new Item;
        if ($this->itemDescription($brand_code, $function_code, $range) && $this->itemDescription($brand_code, $function_code, $range) != '') {
            
            $item->item_description = $this->itemDescription($brand_code, $function_code, $range);
        } else {
            $item->item_description = $val['item_description'];
        }
        
        $item->cp_cart_id = $cpId;
        $item->material_number = $this->getMasterSheetMaterialNumber($brand_code, $function_code, $range);
        $item->wilo_artilce_no = $this->getArticleNumberBySheet($brand_code, $function_code, $range);
        $item->weight = $this->getMasterSheetHeight($brand_code, $function_code, $range);
        $item->brand_code = $brand_code;
        $item->function_code = $function_code;
        $item->ranges = $range;
        $item->price = $unitPrice;
        $item->total_price = $totalPrice;
        $item->margin = str_replace('_', '', $val['margin']);
        
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
    }

    //here1
    public function cartItems($cartId, $returnDataOnly = false) {
        $items = Item::where('cp_cart_id', $cartId)->with('contolPanelCart')->orderBy('adder_code')->get();
        if($returnDataOnly){
            $bomSummaryItems = $items;
            $items = Item::where('cp_cart_id', $cartId)->with('contolPanelCart')->orderBy('adder_code')->whereNotIn('brand_code', [16, 17, 18])->get();
            return [
                'items' => $items,
                'bomSummaryItems'=> $bomSummaryItems,
                'cartId' => $cartId,
            ];
        }
        return view('frontend.cart.items', compact('items'));
    }

    public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
        $noOfPump = $this->getValueById('App\NumberOfPump', 'id', $noOfPump);
        DB::enableQueryLog(); // Enable query log

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

    public function removeCart($id) {

        $deleteControlPanel = ControlPanelCart::where('id', $id)->delete();

        $deleteItem = Item::where('cp_cart_id', $id)->delete();
    }

    public function ajaxDetailModalControlPanel(Request $request) {
        $addersData = [];
        $cpId = $request->cp_id;
        
        $controlPanelData = ControlPanelCart::where('id', $cpId)
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
                        ->get()[0];

        if(!empty($controlPanelData->adder_ids) && $controlPanelData->adder_ids != null){
            $adderIds = explode(",", $controlPanelData->adder_ids);
            $addersData = DB::table('main_electrical_list')->select('id','adder_list')
                            ->whereIn('id', $adderIds)->get();
        }
        $controlPanelData->powers->value = $controlPanelData->powers->value . " Kw";
        $controlPanelData->voltages->value = $controlPanelData->voltages->value . " V";
        $controlPanelData->ambienttemps->value = $controlPanelData->ambienttemps->value . " °C";
        $returnHTML = view('frontend.cart.detail_modal')->with('controlPanelData', $controlPanelData)
                ->with('addersData', $addersData)
                ->render();
        $data['html'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function updatedTotalPrice() {

        $atmosCartData = AtmosCart::cartData();
        $scpCartData = ScpCart::cartData();
        $scpvCartData = ScpvCart::cartData(); // A Code: 18-02-2026
        $boosterCartData = BoosterCart::cartData();
        $controlPanelCartData = ControlPanelCart::where('user_id', auth()->user()->id)
                ->whereNull('quotation_no')
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
        $fireFightingCartData = FireFightingCarts::where('user_id', auth()->user()->id)->whereNull('quotation_no')->get();
        $returnHTML = view('frontend.cart.qty_updated_total_price')->with('controlPanelCartData', $controlPanelCartData)
				->with('atmosCartData', $atmosCartData)
				->with('scpCartData', $scpCartData)
                ->with('scpvCartData', $scpvCartData) // A Code: 18-02-2026
				->with('boosterCartData', $boosterCartData)
				->with('fireFightingCartData', $fireFightingCartData)
				->render();
        $data['total_price_updated'] = $returnHTML;
        return response()->json(array('success' => true, 'data' => $data));
    }

    public function itemDescription($brand_code, $function_code, $range) {
        $masterData = DB::table('master_price_sheet_electrical_components')->select('description')
                ->where('brand_code', $brand_code)
                ->where('function_code', $function_code)
                ->where('range', $range)
                ->get();
        if (isset($masterData[0]->description)){
            return $masterData[0]->description;
        }
        return '';
    }

    public function getArticleNumberBySheet($brand_code, $function_code, $range) {
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
}
