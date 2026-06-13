<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BoosterCart;
use Illuminate\Http\Request;
use App\Traits\ControlPanelModelIdGet;
use App\ControlPanelCart;
use App\WarehousePumpDetails; // A Code: 13-04-2026
use App\ControlPanel;
use App\Models\ControlPanelsMaster; // A Code: 27-03-2026
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

    // A Code: 03-04-2026 Start
    public function addToCart(Request $request) 
    {
        // A Code: 07-03-2026 Start
        $getIdByValue = function ($table, $value) {
            return DB::table($table)
                ->where('value', trim($value))
                ->value('id');
        };
        $numberOfPumpID = $getIdByValue('number_of_pumps', $request->no_of_pump);
        $powerID        = $getIdByValue('powers', $request->power_rating);
        $voltageID      = $getIdByValue('voltages', $request->voltage);
        $applicationID  = $getIdByValue('applications', $request->application);
        $ambientTempID = $getIdByValue('ambient_temps', $request->ambient_temp);
        $staterTypeID = $getIdByValue('starter_types', $request->stater_type);
        $communicationProtocolID = $getIdByValue('comunication_protocols', $request->communication_protocol);
        $ipRatingID = $getIdByValue('ip_ratings', $request->ip_rating);
        $componentID = $getIdByValue('components', $request->component);
        $enclosureID = $getIdByValue('enclousres', $request->enclosure);
        // A Code: 07-03-2026 End

        if($request->adder_ids){
            //$controlPanelCartData = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
            //         ->where('user_id', auth()->user()->id)
            //         ->where('adder_ids', $request->adder_ids)
            //         ->orderBy('id', 'desc')
            //         ->first();

            // A Code: 07-03-2026 Start 
            $query = ControlPanelCart::where('control_panel_id', $request->cp_id)
                                    ->where('user_id', auth()->user()->id)
                                    ->where('adder_ids', $request->adder_ids)
                                    ->orderBy('id', 'desc');

            if (!empty($request->no_of_pump)) {
                // When number of pumps is provided → detailed matching
                $controlPanelCartData = $query->where([
                    ['no_of_pump_id', '=', $numberOfPumpID],
                    ['power_id', '=', $powerID],
                    ['voltage_id', '=', $voltageID],
                    ['application_id', '=', $applicationID],
                    ['ambient_temp_id', '=', $ambientTempID],
                    ['stater_type_id', '=', $staterTypeID],
                    ['communication_protocol_id', '=', $communicationProtocolID],
                    ['ip_rating_id', '=', $ipRatingID],
                    ['components_id', '=', $componentID],
                    ['enclosure_id', '=', $enclosureID],
                ])->first();

            } elseif (!empty($request->full_article_number)) {
                // When full article number is provided
                $controlPanelCartData = $query->where('full_article_number', $request->full_article_number)
                                            ->first();                
            } 
            // A Code: 13-04-2026 Start
            elseif (!empty($request->full_article_number_for_stock)) {
                // When full article number for stock is provided
                $controlPanelCartData = $query->where('full_article_number', $request->full_article_number_for_stock)
                                            ->first();                
                // $existsArticleNumber = WarehousePumpDetails::where('art_no', $request->full_article_number_for_stock)->exists();

                // // if exist then Update stock_check = 1 Other Wise 0
                // DB::table('control_panel_carts')
                //     ->where('full_article_number', $request->full_article_number_for_stock)
                //     ->update([
                //         'full_article_number_for_stock' => $existsArticleNumber 
                //             ? $request->full_article_number_for_stock 
                //             : null,
                //         'stock_check' => $existsArticleNumber ? 1 : 0
                //     ]);
                
            } 
            // A Code: 13-04-2026 End
            else {
                // Default case
                $controlPanelCartData = $query->first();
            }
            // A Code: 07-03-2026 End             
                       

            if($controlPanelCartData == null){
                //$controlPanelCartData1 = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                $controlPanelCartData1 = ControlPanelCart::where('control_panel_id', $request->cp_id)
                    ->where('adder_ids', $request->adder_ids)
                    ->orderBy('id', 'desc')
                    ->first();                
            
                $controlPanelCart = new ControlPanelCart;
                if($controlPanelCartData1)
                {
                    // A Code: 27-04-2026 Start (Commented code)
                    // $request->no_of_pump = $controlPanelCartData1->no_of_pump_id;
                    // $request->power_rating = $controlPanelCartData1->power_id;
                    // $request->voltage = $controlPanelCartData1->voltage_id;
                    // $request->application = $controlPanelCartData1->application_id;
                    // $request->ambient_temp = $controlPanelCartData1->ambient_temp_id;
                    // $request->stater_type = $controlPanelCartData1->stater_type_id;
                    // $request->communication_protocol = $controlPanelCartData1->components_id;
                    // $request->ip_rating = $controlPanelCartData1->ip_rating_id;
                    // $request->component = $controlPanelCartData1->components_id;
                    // $request->enclosure = $controlPanelCartData1->enclosure_id;   
                    // A Code: 27-04-2026 End (Commented code)                 

                    //$request->communication_protocol = $controlPanelCartData1->components_id;
                    $controlPanelCart->article_number = $controlPanelCartData1->article_number;
                    $controlPanelCart->full_article_number = $controlPanelCartData1->full_article_number ?? 0;
                    
                    //Booster electrical article number either manual or search code starts..!!
                    if($controlPanelCartData1->full_article_number == null || $controlPanelCartData1->full_article_number == "0"){
                        $test = BoosterCart::where('cp_id',$controlPanelCartData1->control_panel_id)->first();
                        if($test)
                        {
                            $controlPanelCart->full_article_number = $test->electrical_article_number ?? 0;
                        }
                    }
                    //Booster electrical article number either manual or search code ends..!!
                }
                else{
                    //$controlPanelCartData1 = BoosterCart::where('cp_id', $request->control_panel_id)
                    $controlPanelCartData1 = BoosterCart::where('cp_id', $request->cp_id)
                        ->where('adder_ids', $request->adder_ids)
                        ->orderBy('id', 'desc')
                        ->first();                       

                    // A Code: 27-03-2026 Start
                    $article_number = $controlPanelCartData1->article_number ?? 0;
                    $full_article_number = $controlPanelCartData1->full_article_number ?? 0;
                    if($controlPanelCartData1)
                    {
                        //$controlPanelCartData1 = ControlPanel::where('id', $controlPanelCartData1->cp_id)->first();
                        $controlPanelCartData1 = ControlPanelsMaster::where('id', $controlPanelCartData1->cp_id)->first(); 
                                        
                    }                    
                    // A Code: 27-03-2026 End

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

                // A Code: 27-03-2026 Start
                // $controlPanelCart->power_id = DB::table('powers')->where('value', $request->power_rating)->value('id') ?? 0;  
                // $controlPanelCart->voltage_id = DB::table('voltages')->where('value', $request->voltage)->value('id') ?? 0;
                // $controlPanelCart->application_id = DB::table('applications')->where('value', $request->application)->value('id') ?? 0;
                // $controlPanelCart->ambient_temp_id = DB::table('ambient_temps')->where('value', $request->ambient_temp)->value('id') ?? 0;
                // $controlPanelCart->stater_type_id = DB::table('starter_types')->where('value', $request->stater_type)->value('id') ?? 0;
                // $controlPanelCart->communication_protocol_id = DB::table('comunication_protocols')->where('value', $request->communication_protocol)->value('id') ?? 0;
                // $controlPanelCart->ip_rating_id = DB::table('ip_ratings')->where('value', $request->ip_rating)->value('id') ?? 0;
                // $controlPanelCart->components_id = DB::table('components')->where('value', $request->component)->value('id') ?? 0;
                // $controlPanelCart->enclosure_id = DB::table('enclousres')->where('value', $request->enclosure)->value('id') ?? 0;                
                // A Code: 27-03-2026 End
                
                // A Code: 27-04-2026 Start
                $controlPanelCart->power_id = $powerID ?? 0; 
                $controlPanelCart->voltage_id = $voltageID ?? 0;
                $controlPanelCart->application_id = $applicationID ?? 0;
                $controlPanelCart->ambient_temp_id = $ambientTempID ?? 0;
                $controlPanelCart->stater_type_id = $staterTypeID ?? 0;
                $controlPanelCart->communication_protocol_id = $communicationProtocolID ?? 0;
                $controlPanelCart->ip_rating_id = $ipRatingID ?? 0;
                $controlPanelCart->components_id = $componentID ?? 0;
                $controlPanelCart->enclosure_id = $enclosureID ?? 0;                 
                // A Code: 27-04-2026 End
                
                $controlPanelCart->range = $this->getIdByValue('App\Range', 'value', $request->range);
                $controlPanelCart->folder_name = '';
                $controlPanelCart->file_name_under_folder = '';
                $controlPanelCart->price = $request->total_price;
                $controlPanelCart->qty = 1;
                $controlPanelCart->tax = Tax::where('id', 1)->get()[0]->amount;
                $controlPanelCart->total_price = $request->total_price;
                $controlPanelCart->user_id = auth()->user()->id;
                $controlPanelCart->adder_ids = $request->adder_ids;
                $controlPanelCart->overhead = ControlPanelsMaster::controlpanel_over_head();
                $controlPanelCart->intercompany_margin = User::ic_margin_control_panel();
                $controlPanelCart->created_at = date("Y-m-d H:i:s");
                $controlPanelCart->updated_at = date("Y-m-d H:i:s");


                if($request->enclosure == null)
                {
                    $request->enclosure = @$controlPanelCartData1->enclosure_id;
                }
                
                $controlPanelCart->save();
                $cpId = $controlPanelCart->id;
                //below
                $data = $this->getControlPanelDataItemSave($request, $request->enclosure, $cpId, $request->control_panel_id);
                //dd($controlPanelCart,$data,$request->all(), $request->enclosure, $cpId, $request->control_panel_id);
                //dd('case 1',$data); // with adder_ids options empty cart data condition result
                $controlPanelUpdate = $controlPanelCart::find($cpId);
                $controlPanelUpdate->starter_code = $data['starter_code'];
                //$controlPanelUpdate->range = $data['range'];
                $controlPanelUpdate->range = $this->getIdByValue('App\Range', 'value', $data['range']); // A Code: 27-03-2026
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
                    //dd('case 2',$data); // with adder_ids options not empty cart data condition result
                    $controlPanelUpdate = $controlPanelCart::find($cpId);
                    $controlPanelUpdate->price = $request->total_price;
                    $controlPanelUpdate->total_price = $request->total_price;
                    $controlPanelUpdate->starter_code = $data['starter_code'];
                    //$controlPanelUpdate->range = $data['range'];
                    $controlPanelUpdate->range = $this->getIdByValue('App\Range', 'value', $data['range']); // A Code: 27-03-2026
                    $controlPanelUpdate->save();
                }
            }
        }else{
            //$controlPanelCartData = ControlPanelCart::where('control_panel_id', $request->control_panel_id)
            //         ->where('user_id', auth()->user()->id)
            //         ->whereNull('adder_ids')
            //         ->orderBy('id', 'desc')
            //         ->first();

            // A Code: 07-03-2026 Start     
            $query = ControlPanelCart::where('control_panel_id', $request->cp_id)
                                    ->where('user_id', auth()->user()->id)
                                    ->whereNull('adder_ids')
                                    ->orderBy('id', 'desc');

            if (!empty($request->no_of_pump)) {
                // Full criteria when number of pumps is provided
                $controlPanelCartData = $query->where([
                    ['no_of_pump_id', $numberOfPumpID],
                    ['power_id', $powerID],
                    ['voltage_id', $voltageID],
                    ['application_id', $applicationID],
                    ['ambient_temp_id', $ambientTempID],
                    ['stater_type_id', $staterTypeID],
                    ['communication_protocol_id', $communicationProtocolID],
                    ['ip_rating_id', $ipRatingID],
                    ['components_id', $componentID],
                    ['enclosure_id', $enclosureID],
                ])->first();

            } elseif (!empty($request->full_article_number)) {
                // When full article number is provided
                $controlPanelCartData = $query->where('full_article_number', $request->full_article_number)
                                            ->first();    

            } 
            // A Code: 13-04-2026 Start
            elseif (!empty($request->full_article_number_for_stock)) {
                // When full article number is provided
                $controlPanelCartData = $query->where('full_article_number', $request->full_article_number_for_stock)
                                            ->first();                
                // $existsArticleNumber = WarehousePumpDetails::where('art_no', $request->full_article_number_for_stock)->exists();

                // // if exist then Update stock_check = 1 Other Wise 0
                // DB::table('control_panel_carts')
                //     ->where('full_article_number', $request->full_article_number_for_stock)
                //     ->update([
                //         'full_article_number_for_stock' => $existsArticleNumber ? $request->full_article_number_for_stock : null,
                //         'stock_check' => $existsArticleNumber ? 1 : 0
                //     ]);
                
            } 
            // A Code: 13-04-2026 End
            
            else {
                // Default case: only control_panel_id + user
                $controlPanelCartData = $query->first();
            }
            // A Code: 07-03-2026 End      
            
            
            
            if($controlPanelCartData == null){
                //$controlPanelCartData1 =ControlPanelCart::where('control_panel_id', $request->control_panel_id)
                //         ->whereNull('adder_ids')
                //         ->orderBy('id', 'desc')
                //         ->first();

                // A Code: 07-03-2026 Start
                if(!empty($request->no_of_pump)){
                    $controlPanelCartData1 = ControlPanelCart::where('control_panel_id', $request->cp_id)
                            ->where('no_of_pump_id', $numberOfPumpID)
                            ->where('power_id', $powerID)
                            ->where('voltage_id', $voltageID)
                            ->where('application_id', $applicationID)
                            ->where('ambient_temp_id', $ambientTempID)
                            ->where('stater_type_id', $staterTypeID)
                            ->where('communication_protocol_id', $communicationProtocolID)
                            ->where('ip_rating_id', $ipRatingID)
                            ->where('components_id', $componentID)
                            ->where('enclosure_id', $enclosureID)
                            ->where('user_id', auth()->user()->id)
                            ->whereNull('adder_ids')
                            ->orderBy('id', 'desc')
                            ->first();
                }else{
                    $controlPanelCartData1 = ControlPanelCart::where('control_panel_id', $request->cp_id)
                            ->whereNull('adder_ids')
                            ->orderBy('id', 'desc')
                            ->first();
                }            
                // A Code: 07-03-2026 End         

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
                        //$controlPanelCartData1 = ControlPanel::where('id', $controlPanelCartData1->cp_id)->first();
                        $controlPanelCartData1 = ControlPanelsMaster::where('id', $controlPanelCartData1->cp_id)->first(); 
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

                // A Code: 27-03-2026 Start
                $controlPanelCart->power_id = DB::table('powers')->where('value', $request->power_rating)->value('id') ?? 0;  
                $controlPanelCart->voltage_id = DB::table('voltages')->where('value', $request->voltage)->value('id') ?? 0;
                $controlPanelCart->application_id = DB::table('applications')->where('value', $request->application)->value('id') ?? 0;
                $controlPanelCart->ambient_temp_id = DB::table('ambient_temps')->where('value', $request->ambient_temp)->value('id') ?? 0;
                $controlPanelCart->stater_type_id = DB::table('starter_types')->where('value', $request->stater_type)->value('id') ?? 0;
                $controlPanelCart->communication_protocol_id = DB::table('comunication_protocols')->where('value', $request->communication_protocol)->value('id') ?? 0;
                $controlPanelCart->ip_rating_id = DB::table('ip_ratings')->where('value', $request->ip_rating)->value('id') ?? 0;
                $controlPanelCart->components_id = DB::table('components')->where('value', $request->component)->value('id') ?? 0;
                $controlPanelCart->enclosure_id = DB::table('enclousres')->where('value', $request->enclosure)->value('id') ?? 0;
                // A Code: 27-03-2026 End  
                
                $controlPanelCart->range = $this->getIdByValue('App\Range', 'value', $request->range);
                $controlPanelCart->folder_name = '';
                $controlPanelCart->file_name_under_folder = '';
                $controlPanelCart->price = $request->total_price;
                $controlPanelCart->qty = 1;
                $controlPanelCart->tax = Tax::where('id', 1)->get()[0]->amount;
                $controlPanelCart->total_price = $request->total_price;
                $controlPanelCart->user_id = auth()->user()->id;
                $controlPanelCart->overhead = ControlPanelsMaster::controlpanel_over_head();
                $controlPanelCart->intercompany_margin = User::ic_margin_control_panel();
                $controlPanelCart->created_at = date("Y-m-d H:i:s");
                $controlPanelCart->updated_at = date("Y-m-d H:i:s");

                // A Code: 13-04-2026 Start
                // $existsArticleNumber = WarehousePumpDetails::where('art_no', $request->full_article_number_for_stock)->exists();
                // // if exist then Update stock_check = 1 Other Wise 0
                // if($existsArticleNumber){     
                //     $controlPanelCart->full_article_number_for_stock = $request->full_article_number_for_stock;
                //     $controlPanelCart->stock_check = 1;
                // }else{
                //     $controlPanelCart->full_article_number_for_stock = null;
                //     $controlPanelCart->stock_check = 0;
                // }
                // A Code: 13-04-2026 End

                $controlPanelCart->save();
                $cpId = $controlPanelCart->id;
                if($request->enclosure == null)
                {
                    $request->enclosure = $controlPanelCartData1->enclosure_id;
                }
                $data = $this->getControlPanelDataItemSave($request,$request->enclosure, $cpId, $request->control_panel_id);
                //dd('case 3',$data); // without adder_ids options empty cart data condition result
                $controlPanelUpdate = $controlPanelCart::find($cpId);
                $controlPanelUpdate->starter_code = $data['starter_code'];
                //$controlPanelUpdate->range = $data['range'];
                $controlPanelUpdate->range = $this->getIdByValue('App\Range', 'value', $data['range']); // A Code: 27-03-2026
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
                        //$request->enclosure = $controlPanelCart->enclosure_id;
                        $request->enclosure = DB::table('enclousres')
                                                ->where('id', $controlPanelCart->enclosure_id)
                                                ->value('value'); 

                    }
                    $data = $this->getControlPanelDataItemSave($request,$request->enclosure, $cpId, $request->control_panel_id);
                    //dd('case 4',$data); // without adder_ids options not empty cart data condition result
                    
                    $controlPanelUpdate = $controlPanelCart::find($cpId);
                    $controlPanelUpdate->price = $request->total_price;
                    $controlPanelUpdate->total_price = $request->total_price;
                    $controlPanelUpdate->starter_code = $data['starter_code'];
                    //$controlPanelUpdate->range = $data['range'];
                    $controlPanelUpdate->range = $this->getIdByValue('App\Range', 'value', $data['range']); // A Code: 27-03-2026  
                    
                    // A Code: 13-04-2026 Start
                    // $existsArticleNumber = WarehousePumpDetails::where('art_no', $request->full_article_number_for_stock)->exists();
                    // // if exist then Update stock_check = 1 Other Wise 0
                    // if($existsArticleNumber){     
                    //     $controlPanelUpdate->full_article_number_for_stock = $request->full_article_number_for_stock;
                    //     $controlPanelUpdate->stock_check = 1;
                    // }else{
                    //     $controlPanelUpdate->full_article_number_for_stock = null;
                    //     $controlPanelUpdate->stock_check = 0;
                    // }
                    // A Code: 13-04-2026 End

                    $controlPanelUpdate->save();
                }
            }
        }
        return response()->json(array('success' => true, 'url' => url('/controlpanel/cart/' . auth()->user()->id)));
    }
    // A Code: 03-04-2026 End

    // public function getControlPanelDataItemSave($request, $enclousre, $cpId, $controlPanelId)
    // {
    //     $controlPanelData = ControlPanelsMaster::where('id', $controlPanelId)->first();

    //     if (!$controlPanelData) {
    //         return;
    //     }

    //     // Convert comma-separated values into arrays
    //     $noOfPumpsArr = array_map('trim', explode(',', $controlPanelData->no_of_pumps));
    //     $powerArr     = array_map('trim', explode(',', $controlPanelData->power_rating));
    //     $voltageArr   = array_map('trim', explode(',', $controlPanelData->power_supply));

    //     // Request values
    //     $numberOfPump = trim($request->no_of_pump);
    //     $power        = trim((string) $request->power_rating);
    //     $voltage      = trim($request->voltage);

    //     // Normalize power (remove trailing zeros like 11.00 → 11, 7.50 → 7.5)
    //     $normalizedPower = rtrim(rtrim($power, '0'), '.');

    //     // Validate existence
    //     if (
    //         !in_array($numberOfPump, $noOfPumpsArr) ||
    //         !in_array($power, $powerArr) ||
    //         !in_array($voltage, $voltageArr)
    //     ) {
    //         // Debug if needed
    //         // dd($numberOfPump, $power, $voltage);
    //         return;
    //     }

    //     // Column Name Logic
    //     if (strpos($normalizedPower, '.') !== false) {
    //         // Decimal case (7.5 → 7__5)
    //         $formattedPower = str_replace('.', '__', $normalizedPower);
    //         $columnName = $numberOfPump . 'x' . $formattedPower . "kwx" . $voltage . 'v';
    //     } else {
    //         // Integer case (11 → 11__0)
    //         $columnName = $numberOfPump . 'x' . $normalizedPower . "__0kwx" . $voltage . 'v';
    //     }

    //     $tableName   = $controlPanelData->table_name;
    //     $starterCode = $controlPanelData->code;
    //     $price       = 0.00;

    //     // Debug logs (optional)
    //     // logger("Table: $tableName | Column: $columnName");

    //     if (!Schema::hasTable($tableName)) {
    //         return;
    //     }

    //     if (!Schema::hasColumn($tableName, $columnName)) {
    //         // logger("Column not found: $columnName");
    //         return;
    //     }

    //     // Fetch records
    //     $cpRecords = DB::table($tableName)
    //         ->select(
    //             'item_description',
    //             'material_number',
    //             'wilo_article_number',
    //             'weight',
    //             'brand_code',
    //             'function_code',
    //             'range',
    //             'unit_price',
    //             'margin',
    //             $columnName
    //         )
    //         ->whereNotNull($columnName)
    //         ->where($columnName, '!=', 0)
    //         ->get();

    //     // Count function_code = 1
    //     $cpRecords1 = DB::table($tableName)
    //         ->whereNotNull($columnName)
    //         ->where($columnName, '!=', 0)
    //         ->where('function_code', '1')
    //         ->count();

    //     $arrayResult = $cpRecords->toArray();

    //     $enclousreAdderItemData = null;

    //     // Adders logic
    //     if (!empty($request->adder_ids)) {
    //         $price = $this->addersData(
    //             $request,
    //             $cpId,
    //             $numberOfPump,
    //             $power,
    //             $voltage
    //         );

    //         $enclousreAdderItemData = $request->enclousreItem ?? null;
    //     }

    //     if (!empty($arrayResult)) {
    //         $i = 1;

    //         foreach ($arrayResult as $val) {
    //             $price = $this->calculatePriceInItem(
    //                 (array) $val,
    //                 $request,
    //                 $enclousre,
    //                 $columnName,
    //                 $cpId,
    //                 $enclousreAdderItemData,
    //                 $i,
    //                 $cpRecords1
    //             );
    //             $i++;
    //         }

    //         $tax = Tax::where('id', 1)->value('amount');

    //         return [
    //             'price' => $price,
    //             'starter_code' => $starterCode,
    //             'range' => $controlPanelData->range
    //         ];
    //     }

    //     return;
    // }

    // A Code: 04-03-2026 Start 
    public function getControlPanelDataItemSave($request, $enclousre, $cpId, $controlPanelId)
    {        
        // 1. Get correct cart
        $controlPanelCartData = ControlPanelCart::find($cpId);

        if (!$controlPanelCartData) {
            return;
        }
    
        $request->enclosure = $request->enclosure ?: DB::table('enclousres')
            ->where('id', $controlPanelCartData->enclosure_id)
            ->value('value');        

        $request->component = $request->component ?: DB::table('components')
            ->where('id', $controlPanelCartData->components_id)
            ->value('value');

        $request->stater_type = $request->stater_type ?: DB::table('starter_types')
            ->where('id', $controlPanelCartData->stater_type_id)
            ->value('value');
    
        // 2. Get control panel data (from cart to ensure consistency)
        $controlPanelData = ControlPanelsMaster::find($controlPanelCartData->control_panel_id);

        if (!$controlPanelData) {
            return;
        }      

        $numberOfPump = !empty($request->no_of_pump)
            ? trim((string)$request->no_of_pump)
            : trim((string) DB::table('number_of_pumps')->where('id', $controlPanelCartData->no_of_pump_id)->value('value'));

        $power = !empty($request->power_rating)
            ? trim((string)$request->power_rating)
            : trim((string) DB::table('powers')->where('id', $controlPanelCartData->power_id)->value('value'));

        $voltage = !empty($request->voltage)
            ? trim((string)$request->voltage)
            : trim((string) DB::table('voltages')->where('id', $controlPanelCartData->voltage_id)->value('value'));

        // Trim values
        $numberOfPump = trim($numberOfPump);
        $power        = trim($power);
        $voltage      = trim($voltage);

        // Safety check
        if (!$numberOfPump || !$power || !$voltage) {
            //\Log::error('Missing CP Inputs', compact('numberOfPump', 'power', 'voltage'));
            return;
        }

        // 4. Normalize power
        $normalizedPower = rtrim(rtrim($power, '0'), '.');

        // 5. Generate column name
        if (strpos($normalizedPower, '.') !== false) {
            $formattedPower = str_replace('.', '__', $normalizedPower);
            $columnName = "{$numberOfPump}x{$formattedPower}kwx{$voltage}v";
        } else {
            $columnName = "{$numberOfPump}x{$normalizedPower}__0kwx{$voltage}v";
        }

        $tableName   = $controlPanelData->table_name;
        $starterCode = $controlPanelData->code;
        $price       = 0.00;

        // 6. Validate table
        if (!Schema::hasTable($tableName)) {
            //\Log::error("Table not found: {$tableName}");
            return;
        }

        // 7. Validate column (100% check)
        if (!Schema::hasColumn($tableName, $columnName)) {

            $columns = Schema::getColumnListing($tableName);         

            return;
        }

        // 8. Fetch records and convert to array (PHP 7.2 safe)
        $cpRecords = DB::table($tableName)
            ->whereNotNull($columnName)
            ->where($columnName, '!=', 0)
            ->get([
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
            ]);        

        $arrayResult = array_map(function ($item) {
            return (array) $item;
        }, $cpRecords->toArray());

        if (empty($arrayResult)) {
            return;
        }

        // 9. Count function_code = 1
        $cpRecords1 = 0;
        foreach ($arrayResult as $row) {
            if ((string)$row['function_code'] === "1") {
                $cpRecords1++;
            }
        }

        $enclousreAdderItemData = null;

        // 10. Adders logic
        if (!empty($controlPanelCartData->adder_ids)) {
            $price = $this->addersData(
                $request,
                $cpId,
                $numberOfPump,
                $power,
                $voltage
            );

            $enclousreAdderItemData = $controlPanelCartData->enclousreItem ?? null;
        }

        // 11. Process items
        $i = 1;
        foreach ($arrayResult as $val) {            

            $price = $this->calculatePriceInItem(
                $val,
                $request,
                $enclousre,
                $columnName,
                $cpId,
                $enclousreAdderItemData,
                $i,
                $cpRecords1
            );
            //var_dump($price);echo "<br>";

            $i++;
        }
        //exit();

        // 12. Return result
        return [
            'price' => $price,
            'starter_code' => $starterCode,
            'range' => $controlPanelData->range
        ];
    }
    // A Code: 04-03-2026 End

    public function getMasterSheetPriceData($brand_code, $function_code, $range) 
    {
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

    // A Code: 14-04-2026 Start  
    // public function searchMasterSheetData($brand_code, $function_code, $range) 
    // {        
    //     $sheetdata = DB::table('master_price_sheet_electrical_components as mp')
    //         ->join('warehouse_pump_details as wp', 'mp.wilo_artilce_no', '=', 'wp.art_no')
    //         ->select('mp.description', 'mp.wilo_artilce_no')
    //         ->get();
    // }
    // A Code: 14-04-2026 End

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

    // A Code: 10-04-2026 Optimized
    public function calculatePriceInItem($val, $request, $enclosure, $columnName, $cpId, $enclousreAdderItemData, $i, $enclosure_count)
    {
        //dd($val, $request->all(), $enclosure, $columnName, $cpId, $enclousreAdderItemData, $i, $enclosure_count);
        // A Code: 17-04-2026 Start    
        $getValue = function ($table, $id) {
            return (string) (DB::table($table)->where('id', $id)->value('value') ?? '');
        };

        if (!empty($request->full_article_number)) {

            $controlPanelCartData = ControlPanelCart::where('control_panel_id', $request->cp_id)
                ->where('full_article_number', $request->full_article_number)
                ->first();

            if ($controlPanelCartData) {                 
                $request->power_rating = $getValue('powers', $controlPanelCartData->power_id);
                $request->voltage = $getValue('voltages', $controlPanelCartData->voltage_id);
                $request->application = $getValue('applications', $controlPanelCartData->application_id);
                $request->ambient_temp = $getValue('ambient_temps', $controlPanelCartData->ambient_temp_id);
                $request->stater_type = $getValue('starter_types', $controlPanelCartData->stater_type_id);
                $request->communication_protocol = $getValue('comunication_protocols', $controlPanelCartData->communication_protocol_id);
                $request->ip_rating = $getValue('ip_ratings', $controlPanelCartData->ip_rating_id);
                $request->component = $getValue('components', $controlPanelCartData->components_id);
                $request->enclosure = $getValue('enclousres', $controlPanelCartData->enclosure_id);
            }
        }
        // A Code: 17-04-2026 End 

        $price = 0.00;
        $qty   = (int)($val[$columnName] ?? 0);

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

        // if($range == 862 || $range == 662){
        //     dd($controlPanelCartData->components_id,$request->component);
        // }

        // Component Logic (Economic / Schneider / Lovato)
        $brand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-06-2026
        
        // ====================== LOVATO / SCHNEIDER ======================
        if (in_array($component, ['lovato', 'schneider'])) {

            // STEP 1: Enclosure Adder Item Override
            if (!empty($enclousreItem) && in_array($enclosure, ['metal', 'grp', 'stainless steel'])) {
                if (isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                    $brand === (int)$enclousreItem['brand_code'] &&
                    $function === (int)$enclousreItem['function_code']) {

                    if ($enclosure_count == 1) {
                        $range = $enclousreItem['range'] ?? $range;
                    }

                    $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                    $price     = $unitPrice * $qty;
                    //dd("case 1",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
                    $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
                    return $price; // case 1
                }
            }

            // STEP 2: Component + Enclosure Special Logic
            $specialUnitPrice = $this->getSpecialUnitPrice(
                $brand, $function, $range, $enclosure, $component, $starter
            );

            if ($specialUnitPrice !== null) {
                $price = $specialUnitPrice * $qty;
                //dd("case 2",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
                $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $specialUnitPrice);
                return $price; // case 2
            }

            // STEP 3: Default
            $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
            $price     = $unitPrice * $qty;

            //dd("case 3",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
            $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
            return $price; // case 3
        }

        // ====================== DEFAULT (Standard / Economic) ======================       

        // A Code: 14-04-2026 Start
        if (!empty($enclousreItem) &&
            in_array($enclosure, ['metal', 'grp', 'stainless steel'])
        ) { 
            if (
                isset($enclousreItem['brand_code'], $enclousreItem['function_code']) &&
                $brand == $enclousreItem['brand_code'] &&
                $function  == $enclousreItem['function_code']
            ) {
                if ($enclosure_count == 1) {
                    $range = $enclousreItem['range'] ?? $range;
                }

                $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range);
                $price     = $unitPrice * $qty;

                //dd("case 4",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
                $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
                return $price; // case 4
            }
        }
        // A Code: 14-04-2026 End

        // Component Logic (Economic / Schneider / Lovato)
        //$effectiveBrand = $this->getEffectiveBrand($brand, $component, $function, $range); // A Code: 01-06-2026 Comment

        // Special Enclosure Cases
        $specialUnitPrice = $this->getSpecialUnitPrice(
            $brand, $function, $range, $enclosure, $component, $starter
        );

        if ($specialUnitPrice !== null) {
            $price = $specialUnitPrice * $qty;
            //dd("case 5",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
            $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $specialUnitPrice);
            return $price; // case 5
        }

        // Default Case
        //$unitPrice = $this->getMasterSheetPriceData($effectiveBrand, $function, $range); // A Code: 01-06-2026 Comment
        $unitPrice = $this->getMasterSheetPriceData($brand, $function, $range); // A Code: 01-06-2026
        $price     = $unitPrice * $qty;

        //dd("case 6",$val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice);
        //$this->itemSave($val, $effectiveBrand, $function, $range, $columnName, $cpId, $price, $unitPrice); // A Code: 01-06-2026 Comment
        $this->itemSave($val, $brand, $function, $range, $columnName, $cpId, $price, $unitPrice); // A Code: 01-06-2026

        return $price; // case 6
    }

    // ====================== HELPER METHODS ======================
    private function getEffectiveBrand(int $brand, string $component, int $function, $range): int
    {
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
    
    private function getSpecialUnitPrice(int $brand, int $function, $range, string $enclosure, string $component, string $starter)
    {
        // GRP Enclosure
        if ($enclosure === 'grp' && $brand === 5 && $function === 1) {
            return $this->getMasterSheetPriceData(31, 63, $range) ?: null;
        }

        // Stainless Steel
        if ($enclosure === 'stainless steel' && $brand === 5 && $function === 1) {
            return $this->getMasterSheetPriceData(5, 64, $range) ?: null;
        }

        // Metal + Multi VFD + Bypass OR Metal + Xtreme
        if ($enclosure === 'metal' && $brand === 8) {
            //if (in_array($starter, ['multi vfd + bypass', 'xtreme'])) {
            if (in_array($starter, ['xtreme'])) {
                return $this->getMasterSheetPriceData(32, $function, $range) ?: null;
            }
        }

        return null;
    }
    // A Code: 10-04-2026 End

    public function addersData(Request $request, $cpId, $noofpump, $power, $voltage) 
    {
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
                                //if ($request->component == 2 && $val['brand_code'] == "1"){
                                if (($request->component == 2 || $request->component == 'Economic') && $val['brand_code'] == "1") 
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
                                
                                // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {
                                //     continue;
                                // }
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
                                //if ($request->component == 2 && $val['brand_code'] == "1"){
                                if (($request->component == 2 || $request->component == 'Economic') && $val['brand_code'] == "1") 
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
                                //if ($request->component == 2 && $val['brand_code'] == "1"){
                                if (($request->component == 2 || $request->component == 'Economic') && $val['brand_code'] == "1") 
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

                    case ($id >= 45 && $id <= 52):  // electrical_adder_per_pump_based_on_ampere
                        $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, '1');
                        // always true for this case
                        $column = $id . 'x' . $nearestColumn . 'ax1';
                        $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')
                            ->select(
                                'item_description',
                                'material_number',
                                'wilo_article_number',
                                'brand_code',
                                'function_code',
                                'range',
                                'weight',
                                'height',
                                'margin',
                                $column
                            )
                            ->whereNotNull($column)
                            ->where($column, '!=', 0)
                            ->get();

                        $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);

                        if ($arrayResult) {
                            foreach ($arrayResult as $val) {

                                // optimized condition
                                if (($request->component == 2 || $request->component == 'Economic') && $val['brand_code'] == "1") {
                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                                    if ($exist) {
                                        $val['brand_code'] = "2";
                                    }
                                }

                                $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                                $price = $unitPrice * $val[$column] * $noOfPump; // removed *1 (redundant)
                                $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;

                                $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
                            }
                        }
                        break;
                    
                    // case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code
                    //     // $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
                    //     $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage,'1');

                    //     if ($id >= 45 && $id <= 52) {
                    //         $column = $id . 'x' . $nearestColumn . 'ax1';
                    //     } else {
                    //         $column = $id . 'x' . $nearestColumn . 'ax2';
                    //     }
                    //     $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
                    //                     ->whereNotNull($column)->where($column, '!=', 0)->get();
                    //     $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
                    //     if ($arrayResult) {
                    //         foreach ($arrayResult as $key => $val) {
                    //             if ($id >= 45 && $id <= 52) {
                    //                 //if ($request->component == 2 && $val['brand_code'] == "1"){
                    //                 if (($request->component == 2 || $request->component == 'Economic') && $val['brand_code'] == "1") 
                    //                 {
                    //                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
                    //                    if($exist)
                    //                    {
                    //                        $val['brand_code'] = "2";
                    //                    }
                    //                 }
                    //                 $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
                    //                 $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    //                 $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                   
                    //                 $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
                    //             } else {
                    //                 $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
                    //                 $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
                    //                 $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
                                    
                    //                 $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump);
                    //             }
                    //         }
                            
                    //     }
                    //     break;

                        default: //default
                        echo "within no code";
                        break;
                }
            }
        }
        //exit();
        return $price;
    }

    // A Code: 23-04-2026 Start 
    // public function addersData(Request $request, $cpId, $noofpump, $power, $voltage) 
    // {
    //     $noOfPump = $noofpump;
    //     $motorPower = $power;
    //     $voltage = $voltage;
    //     $ids = explode(",", $request->adder_ids); //Code ids
    //     $price = 0.00;
    //     $encloureArea = 0.00;
    //     if ($ids) {
    //         foreach ($ids as $id) {
    //             switch ($id) {
    //                 case ($id >= 1 && $id <= 26): //electrical_common_adder code
    //                     $electricalCommonAdders = DB::table('electrical_common_adder')
    //                                 ->select('id', 'item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $id)
    //                                 ->whereNotNull($id)->where($id, '!=', 0)->get();
    //                     $arrayResult = json_decode(json_encode($electricalCommonAdders), true);
    //                     if ($arrayResult) {
    //                         foreach ($arrayResult as $key => $val) {
    //                             if ($request->component == 2 && $val['brand_code'] == "1")
    //                             {
    //                                 //$val['brand_code'] = "2";
    //                                 //echo $exist;
    //                                 $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
    //                                 if($exist)
    //                                 {
    //                                     $val['brand_code'] = "2";
    //                                 }
    //                             }
    //                             $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$id]; // Qty = $val[$id]
    //                             $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
    //                             $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$id];
                                
    //                             // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {
    //                             //     continue;
    //                             // }
    //                             $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$id], $cpId, $price, $unitPrice, $id, '', $id);
    //                         }
    //                     }

    //                     break;
    //                 case ($id >= 27 && $id <= 36):  //electrical_common_adder_based_on_ampere code
    //                     $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
    //                     $column = $id . 'x' . $nearestColumn . 'a';
    //                     $electricalCommonAdderBasedOnAmpere = DB::table('electrical_common_adder_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
    //                                     ->whereNotNull($column)->where($column, '!=', 0)->get();
    //                     $arrayResult = json_decode(json_encode($electricalCommonAdderBasedOnAmpere), true);
    //                     if ($arrayResult) {
    //                         foreach ($arrayResult as $key => $val) {
    //                             if ($request->component == 2 && $val['brand_code'] == "1")
    //                             {
    //                                 $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
    //                                 if($exist)
    //                                 {
    //                                     $val['brand_code'] = "2";
    //                                 }
    //                             }
    //                             $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column]; // Qty = $val[$id]
    //                             $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
    //                             $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
    //                             // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {                                    
    //                             //     continue;
    //                             // }
    //                             $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, '', $id);
    //                         }
    //                     }
    //                     break;
    //                 case ($id >= 37 && $id <= 44):  //electrical_adder_per_pump code
    //                     $column = $id . 'x1';
    //                     $electricalAdderPerPump = DB::table('electrical_adder_per_pump')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
    //                                     ->whereNotNull($column)->where($column, '!=', 0)->get();
    //                     $arrayResult = json_decode(json_encode($electricalAdderPerPump), true);
    //                     if ($arrayResult) {
    //                         foreach ($arrayResult as $key => $val) {
    //                             if ($request->component == 2 && $val['brand_code'] == "1")
    //                             {
    //                                 $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
    //                                 if($exist)
    //                                 {
    //                                     $val['brand_code'] = "2";
    //                                 }
    //                             }
    //                             $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
    //                             $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
    //                             $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column];
    //                             // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {
    //                             //     continue;
    //                             // }
    //                             $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump, $id);
    //                         }
    //                     }
    //                     break;
    //                 case ($id >= 45 && $id <= 52):  //electrical_adder_per_pump_based_on_ampere code
    //                     // $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage, $noOfPump);
    //                     $nearestColumn = $this->commonAdderBasedOnAmpereNearestColumn($id, $motorPower, $voltage,'1');

    //                     if ($id >= 45 && $id <= 52) {
    //                         $column = $id . 'x' . $nearestColumn . 'ax1';
    //                     } else {
    //                         $column = $id . 'x' . $nearestColumn . 'ax2';
    //                     }
    //                     $electricalAdderPerPumpBasedOnAmpere = DB::table('electrical_adder_per_pump_based_on_ampere')->select('item_description', 'material_number', 'wilo_article_number', 'brand_code', 'function_code', 'range', 'weight', 'height', 'margin', $column)
    //                                     ->whereNotNull($column)->where($column, '!=', 0)->get();
    //                     $arrayResult = json_decode(json_encode($electricalAdderPerPumpBasedOnAmpere), true);
    //                     if ($arrayResult) {
    //                         foreach ($arrayResult as $key => $val) {
    //                             if ($id >= 45 && $id <= 52) {
    //                                 if ($request->component == 2 && $val['brand_code'] == "1")
    //                                 {
    //                                    $exist = $this->getMasterSheetPriceData(2, $val['function_code'], $val['range']);
    //                                    if($exist)
    //                                    {
    //                                        $val['brand_code'] = "2";
    //                                    }
    //                                 }
    //                                 $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 1; // Column qty * no of pumps *  pump qty
    //                                 $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
    //                                 $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
    //                                 // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {
    //                                 //     continue;
    //                                 // }
    //                                 $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump, $id);
    //                             } else {
    //                                 $price = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump * 2; // Column qty * no of pumps *  pump qty
    //                                 $unitPrice = $this->getMasterSheetPriceData($val['brand_code'], $val['function_code'], $val['range']);
    //                                 $encloureArea += $this->getMasterSheetHeightMultiplyByWidth($val['brand_code'], $val['function_code'], $val['range']) * $val[$column] * $noOfPump;
    //                                 // if ($this->isDuplicate($processed, $val['brand_code'], $val['function_code'], $val['range'])) {
    //                                 //     continue;
    //                                 // }
    //                                 $this->itemSave($val, $val['brand_code'], $val['function_code'], $val['range'], $val[$column], $cpId, $price, $unitPrice, $column, $noOfPump, $id);
    //                             }
    //                         }
                            
    //                     }
    //                     break;
    //                     default: //default
    //                     echo "within no code";
    //                     break;
    //             }
    //         }
    //     }
    //     //exit();
    //     return $price;
    // }
    // A Code: 23-04-2026 End

    // A Code: 16-04-2026 Start
    private function isDuplicate(&$processed, $brand, $function, $range)
    {
        $key = $brand . '_' . $function . '_' . $range;
        if (isset($processed[$key])) {
            return true;
        }
        $processed[$key] = true;
        return false;
    }
    // A Code: 16-04-2026 End

    // public function getControlPanelRangeAndCode($request) {
    //     $returnRangeAndCode = [];
    //     $controlPanelData = ControlPanel::where('id', $request->cp_id)->get();

    //     return $returnRangeAndCode = array(
    //         'id' => $controlPanelData[0]->id,
    //         'range' => $controlPanelData[0]->range,
    //         'starter_code' => $controlPanelData[0]->starter_code
    //     );
    // }

    // A Code: 10-04-2026 Start
    public function getControlPanelRangeAndCode($request) {
        $returnRangeAndCode = [];
        //$controlPanelData = ControlPanel::where('id', $request->cp_id)->get();
        $controlPanelData = ControlPanelsMaster::where('id', $request->cp_id)->get();       

        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code
        );
    }
    // A Code: 10-04-2026 End

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

    public function itemSave($val = [],$brand_code = null, $function_code= null, $range= null, $columnName = null, $cpId= null, $totalPrice= null, $unitPrice= null, $adderCode = null, $noOfPump = null) 
    {
        $item = new Item;
        if ($this->itemDescription($brand_code, $function_code, $range) 
            && $this->itemDescription($brand_code, $function_code, $range) != ''){
            $item->item_description = $this->itemDescription($brand_code, $function_code, $range);
        } else {
            $item->item_description = $val['item_description'];
        }
        
        $item->cp_cart_id = $cpId;
        $item->material_number = $this->getMasterSheetMaterialNumber($brand_code, $function_code, $range);
        //$item->wilo_artilce_no = $this->getArticleNumberBySheet($brand_code, $function_code, $range);
        
        // A code: 23-04-2026 Start
        if(!$adderCode){  
            $item->wilo_artilce_no = $this->getArticleNumberBySheet($brand_code, $function_code, $range);
        }        
        // A code: 23-04-2026 End

        // A Code: 16-04-2026 Start
        $stock_master_data = $this->getArticleNumberByNewSheet($brand_code, $function_code, $range);
        if ($stock_master_data) {
            $item->replaced_article_number = $stock_master_data->art_no ?? '';
            $item->replaced_description    = $stock_master_data->product_name ?? '';                      
        }
        // A Code: 16-04-2026 End

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

        // A Code: 17-04-2026 Start
        if (!empty($stock_master_data) && isset($stock_master_data->total_qty)) {
            $item->available_qty = (float) $stock_master_data->total_qty;  
        } else {
            $item->available_qty = 0;
        }
        // A Code: 17-04-2026 End

        $item->save();
    }

    // A Code: 15-04-2026 Start
    // public function itemSave($val = [], $brand_code = null, $function_code = null, $range = null, $columnName = null, $cpId = null, $totalPrice = null, $unitPrice = null, $adderCode = null, $noOfPump = null)
    // {
    //     // Prepare values first
    //     $wiloArticleNo = $this->getArticleNumberBySheet($brand_code, $function_code, $range);

    //     $itemDescription = $this->itemDescription($brand_code, $function_code, $range) 
    //         ? $this->itemDescription($brand_code, $function_code, $range) 
    //         : $val['item_description'];

    //     // Duplicate check
    //     $exists = DB::table('items')
    //         ->where('cp_cart_id', $cpId)
    //         ->where('wilo_artilce_no', $wiloArticleNo)
    //         ->where('item_description', $itemDescription)
    //         ->exists();

    //     if ($exists) {
    //         return; // Stop inserting duplicate
    //     }

    //     $item = new Item;
    //     $item->item_description = $itemDescription;
    //     $item->cp_cart_id = $cpId;
    //     $item->material_number = $this->getMasterSheetMaterialNumber($brand_code, $function_code, $range);
    //     $item->wilo_artilce_no = $wiloArticleNo;

    //     // A Code: 16-04-2026 Start
    //     $stock_master_data = $this->getArticleNumberByNewSheet($brand_code, $function_code, $range);
    //     if ($stock_master_data) {
    //         $item->replaced_article_number = $stock_master_data->art_no ?? '';
    //         $item->replaced_description    = $stock_master_data->product_name ?? '';                      
    //     }
    //     // A Code: 16-04-2026 End

    //     $item->weight = $this->getMasterSheetHeight($brand_code, $function_code, $range);
    //     $item->brand_code = $brand_code;
    //     $item->function_code = $function_code;
    //     $item->ranges = $range;
    //     $item->price = $unitPrice;
    //     $item->total_price = $totalPrice;
    //     $item->margin = str_replace('_', '', $val['margin']);

    //     $item->height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
    //     $item->width = $this->getMasterSheetWidth($brand_code, $function_code, $range);
    //     $item->depth = $this->getMasterSheetDepth($brand_code, $function_code, $range);

    //     if ($adderCode) {
    //         $item->adder_code = $adderCode;
    //         $item->qty = $noOfPump ? $columnName * $noOfPump : $columnName;
    //     } else {
    //         $item->qty = $val[$columnName];
    //     }

    //     // A Code: 16-04-2026 Start
    //     if (!empty($stock_master_data) && isset($stock_master_data->total_qty)) {
    //         $stock_qty = (float) $stock_master_data->total_qty;
    //         $required_qty = (float) $item->qty;

    //         // Set available qty properly
    //         if ($stock_qty >= $required_qty) {
    //             $item->available_qty = $stock_qty;
    //         } else {
    //             $item->available_qty = $required_qty;
    //         }
    //     } else {
    //         $item->available_qty = 0; // fallback
    //     }
    //     // A Code: 16-04-2026 End

    //     $item->save();
    // }   
    // A Code: 15-04-2026 End 

    // A Code: 23-04-2026 Start
    // public function itemSave($val = [],$brand_code = null, $function_code= null, $range= null, $columnName = null, $cpId= null, $totalPrice= null, $unitPrice= null, $adderCode = null, $noOfPump = null, $adderId = null) 
    // {
    //     $item = new Item;
    //     if ($this->itemDescription($brand_code, $function_code, $range) 
    //         && $this->itemDescription($brand_code, $function_code, $range) != ''){
    //         $item->item_description = $this->itemDescription($brand_code, $function_code, $range);
    //     } else {
    //         $item->item_description = $val['item_description'];
    //     }
        
    //     $item->cp_cart_id = $cpId;
    //     $item->material_number = $this->getMasterSheetMaterialNumber($brand_code, $function_code, $range);
    //     if(!$adderCode){          
    //         $item->wilo_artilce_no = $this->getArticleNumberBySheet($brand_code, $function_code, $range);
    //     }else{
    //         if($adderId){                
    //             $adderdata = $this->getAddersArticleNumberBySheet($brand_code, $function_code, $range, $adderCode, $adderId); 
    //             if ($adderdata) {
    //                 $item->wilo_artilce_no = $adderdata['article_number'];
    //                 $item->item_description = $adderdata['item_description'];
    //             }
    //         }
    //     }       

    //     // A Code: 16-04-2026 Start
    //     $stock_master_data = $this->getArticleNumberByNewSheet($brand_code, $function_code, $range);
    //     if ($stock_master_data) {
    //         $item->replaced_article_number = $stock_master_data->art_no ?? '';
    //         $item->replaced_description    = $stock_master_data->product_name ?? '';
    //     }
    //     // A Code: 16-04-2026 End

    //     $item->weight = $this->getMasterSheetHeight($brand_code, $function_code, $range);
    //     $item->brand_code = $brand_code;
    //     $item->function_code = $function_code;
    //     $item->ranges = $range;
    //     $item->price = $unitPrice;
    //     $item->total_price = $totalPrice;
    //     $item->margin = str_replace('_', '', $val['margin']);
        
    //     $item->height = $this->getMasterSheetHeight($brand_code, $function_code, $range);
    //     $item->width = $this->getMasterSheetWidth($brand_code, $function_code, $range);
    //     $item->depth = $this->getMasterSheetDepth($brand_code, $function_code, $range);

    //     if ($adderCode) {
    //         $item->adder_code = $adderCode;
    //         if ($noOfPump) {
    //             $item->qty = $columnName * $noOfPump;
    //         } else {
    //             $item->qty = $columnName;
    //         }
    //     } else {
    //         $item->qty = $val[$columnName];
    //     }

    //     // A Code: 17-04-2026 Start
    //     if (!empty($stock_master_data) && isset($stock_master_data->total_qty)) {
    //         $item->available_qty = (float) $stock_master_data->total_qty;  
    //     } else {
    //         $item->available_qty = 0;
    //     }
    //     // A Code: 17-04-2026 End

    //     $item->save();
    // }
    // A Code: 23-04-2026 End

    //here1
    public function cartItems($cartId, $returnDataOnly = false) {
        $items = Item::where('cp_cart_id', $cartId)->with('contolPanelCart')->orderBy('adder_code')->get();
                      
        //$full_article_number_for_stock = ControlPanelCart::where('id', $cartId)->value('full_article_number_for_stock');
        $stock_check = ControlPanelCart::where('id', $cartId)->value('stock_check');

        if($returnDataOnly){
            $bomSummaryItems = $items;
            $items = Item::where('cp_cart_id', $cartId)->with('contolPanelCart')->orderBy('adder_code')->whereNotIn('brand_code', [16, 17, 18])->get();
            return [
                'items' => $items,
                'bomSummaryItems'=> $bomSummaryItems,
                'cartId' => $cartId,
            ];
        }
        return view('frontend.cart.items', compact('items','stock_check'));
    }

    // public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
    //     $noOfPump = $this->getValueById('App\NumberOfPump', 'id', $noOfPump);
    //     DB::enableQueryLog(); // Enable query log

    //     $voltage = $this->getValueById('App\Voltage', 'id', $voltage);
    //     $motorPower = $this->getValueById('App\Power', 'id', $motorPower);
    //     if($code >= 45 && $code <= 52)
    //     {
    //         $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8);
    //     }
    //     else
    //     {
    //         $ampere = ($motorPower * 1000) / (1.732 * $voltage * 0.8) * $noOfPump;
    //     }
    //     return AdderHelper::getClosestAmpere($code, $ampere);
    // }

    // A Code: 10-04-2026 Start
    public function commonAdderBasedOnAmpereNearestColumn($code, $motorPower, $voltage, $noOfPump) {
        DB::enableQueryLog(); // Enable query log
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

    // A Code: 27-04-2026 Start (Optmized)
    // public function ajaxDetailModalControlPanel(Request $request)
    // {
    //     $cpId = $request->cp_id;

    //     $controlPanelData = ControlPanelCart::with([
    //             'noofpumps',
    //             'powers',
    //             'voltages',
    //             'applications',
    //             'ambienttemps',
    //             'startertypes',
    //             'components',
    //             'ranges',
    //             'enclousres',
    //             'comunicationprotocols',
    //             'ipratings'
    //         ])
    //         ->where('id', $cpId)
    //         ->first();

    //     // Handle record not found
    //     if (!$controlPanelData) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Control Panel not found'
    //         ]);
    //     }

    //     $addersData = [];

    //     // Handle adders safely
    //     if (!empty($controlPanelData->adder_ids)) {
    //         $adderIds = explode(",", $controlPanelData->adder_ids);

    //         $addersData = DB::table('main_electrical_list')
    //             ->select('id', 'adder_list')
    //             ->whereIn('id', $adderIds)
    //             ->get();
    //     }

    //     // Safe value assignment
    //     if ($controlPanelData->powers) {
    //         $controlPanelData->powers->value .= " Kw";
    //     }

    //     if ($controlPanelData->voltages) {
    //         $controlPanelData->voltages->value .= " V";
    //     }

    //     if ($controlPanelData->ambienttemps) {
    //         $controlPanelData->ambienttemps->value .= " °C";
    //     }

    //     $returnHTML = view('frontend.cart.detail_modal', [
    //         'controlPanelData' => $controlPanelData,
    //         'addersData' => $addersData
    //     ])->render();

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'html' => $returnHTML
    //         ]
    //     ]);
    // }
    // A Code: 27-04-2026 End

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

    // A Code: 16-04-2026 Start
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
    // A Code: 16-04-2026 End

    // A Code: 23-04-2026 Start
    // public function getAddersArticleNumberBySheet($brand_code = null, $function_code = null, $range = null, $column = null, $id = null) 
    // {
    //     $table = null;

    //     if ($id >= 1 && $id <= 26) {
    //         $table = 'electrical_common_adder';
    //     } elseif ($id >= 27 && $id <= 36) {
    //         $table = 'electrical_common_adder_based_on_ampere';
    //     } elseif ($id >= 37 && $id <= 44) {
    //         $table = 'electrical_adder_per_pump';
    //     } elseif ($id >= 45 && $id <= 52) {
    //         $table = 'electrical_adder_per_pump_based_on_ampere';
    //     } else {
    //         return null;
    //     }

    //     if (!$column) {
    //         return null;
    //     }

    //     $result = DB::table($table)
    //         ->where('brand_code', $brand_code)
    //         ->where('function_code', $function_code)
    //         ->where('range', $range)
    //         ->whereNotNull($column)
    //         ->where($column, '!=', 0)
    //         ->select('wilo_article_number', 'item_description')
    //         ->first();

    //     if ($result) {
    //         return [
    //             'article_number' => $result->wilo_article_number,
    //             'item_description' => $result->item_description,
    //         ];
    //     }

    //     return null;
    // }
    // A Code: 23-04-2026 End

}
