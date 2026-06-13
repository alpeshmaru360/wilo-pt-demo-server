<?php

namespace App\Http\Controllers\api\Quotation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\AtmosCart;
use App\Models\BoosterCart;
use App\Cart;
use App\ControlPanel;
use App\ControlPanelCart;
use App\Customer;
use App\Models\FireFighting\FireFightingCarts;
use App\Quotation;
use App\ScpCart;
use App\Http\Controllers\Frontend\AtmosGigaController;
use App\Http\Controllers\Frontend\ScpController;
use App\Http\Controllers\Frontend\BoosterSetController;
use App\Http\Controllers\Frontend\ControlpanelController;
use App\Http\Controllers\Frontend\CPCartController;
use App\Http\Controllers\Frontend\FireFighting\FireFightingPumpController;

class ApiGetQuotationController extends Controller
{
    public function get_all_products(Request $request)
    {   
        $quotation = Quotation::where('quotation_number',$request->quotation_number)->get();
        if(count($quotation) > 0){
            $description = "";
            $qty = "";
            $full_article_number = "";
            $article_number = "";
            $unit_price = "";
            $total_price = "";
            $response = [];
            $customer = Customer::where('id',$quotation[0]->customer_id)->first();
            // $bom = [];
            foreach($quotation as $key => $val){
                if($val->cart_model_name == "atmos"){
                    $atmosIds = $val->cp_cart_id;
                    $cart_model_name = "Atmos";
                    $item_id = $atmosIds;
                    $atmosCartData = AtmosCart::find($atmosIds);
                    $description = $atmosCartData->pump_name.'/'.$atmosCartData->power.'KW/'.$atmosCartData->no_of_pole;
                    $qty = $atmosCartData->qty;
                    $full_article_number = $atmosCartData->full_article_number;
                    $article_number = $atmosCartData->article_number;
                    $unit_price = $atmosCartData->price;
                    $total_price = $atmosCartData->total_price;
                }

                else if($val->cart_model_name == "booster"){
                    $boosterIds = $val->cp_cart_id;
                    $cart_model_name = "Booster";
                    $item_id = $boosterIds;
                    $boosterCartData = BoosterCart::find($boosterIds);
                    if($boosterCartData){
                        $boosterCartDataDesc = ControlPanel::with('noofpumps')->find($boosterCartData->cp_id);

                        $const =null;
                        if (str_starts_with($boosterCartDataDesc->table_name, 'basic_')) {
                            $const = 'COE';
                        }
                        else{
                             $const = 'CO';
                            $arrayCheck = [3, 4, 7];
                            if (in_array($boosterCartDataDesc->stater_type_id, $arrayCheck)) {
                                $const = 'COR';
                            }
                        }

                        $description = $const .' '. $boosterCartDataDesc->noofpumps['value'] . ' ' . $boosterCartData->model_no.'/'.$boosterCartData->motor_power.'KW'.'/'.$boosterCartDataDesc->starter_code.'/AE';
                        $qty = $boosterCartData->qty;
                        $full_article_number = $boosterCartData->full_article_number;
                        $article_number = $boosterCartData->article_number;
                        $unit_price = $boosterCartData->price;
                        $total_price = $boosterCartData->total_price;
                    }
                }

                else if($val->cart_model_name == "controlpanel"){
                    $cpIds = $val->cp_cart_id;
                    $cart_model_name = "Control Panel";
                    $item_id = $cpIds;

                    $controlpanelCartData = ControlPanelCart::where('id',$cpIds)
                                            ->with('powers')
                                            ->with('noofpumps')
                                            ->first();
                    $description = 'Control Panel '.$controlpanelCartData->noofpumps['value'].'/'.$controlpanelCartData->powers['value'].'KW/'.$controlpanelCartData->starter_code.'/AE';
                    $qty = $controlpanelCartData->qty;
                    $full_article_number = $controlpanelCartData->full_article_number;
                    $article_number = $controlpanelCartData->article_number;
                    $unit_price = $controlpanelCartData->price;
                    $total_price = $controlpanelCartData->total_price;
                }

                else if($val->cart_model_name == "firefighting"){
                    $firefightingIds = $val->cp_cart_id;
                    $cart_model_name = "Fire-Fighting";
                    $item_id = $firefightingIds;
                    $fireFightingCartData = FireFightingCarts::find($firefightingIds);

                    $firefightdesc = ucwords(str_replace('-pump', '', $fireFightingCartData->category));

                    $description = $firefightdesc.' '.$fireFightingCartData->pump_models.'/AE';
                    $qty = $fireFightingCartData->qty;
                    $full_article_number = $fireFightingCartData->full_article_number;
                    $article_number = $fireFightingCartData->article_number;
                    $unit_price = $fireFightingCartData->price;
                    $total_price = $fireFightingCartData->total_price;
                }

                else if($val->cart_model_name == "scp"){
                    $scpIds = $val->cp_cart_id;
                    $cart_model_name = "SCP";
                    $item_id = $scpIds;
                    $SCPCartData = ScpCart::find($scpIds);
                    $description = $SCPCartData->pump_name.'/'.$SCPCartData->power.'KW/'.$SCPCartData->no_of_pole;
                    $qty = $SCPCartData->qty;
                    $full_article_number = $SCPCartData->full_article_number;
                    $article_number = $SCPCartData->article_number;

                    $unit_price = $SCPCartData->price;
                    $total_price = $SCPCartData->total_price;
                }

                else{
                    $atmosIds = $val->cp_cart_id;
                    $cart_model_name = "Other";
                    $item_id = $atmosIds;
                    $atmosCartData = AtmosCart::find($atmosIds);
                    $description = $atmosCartData->pump_name.'/'.$atmosCartData->power.'KW/'.$atmosCartData->no_of_pole;
                    $unit_price = '';
                    $total_price = '';
                }

                $data = [];
                $data['sr_no'] = $key + 1;
                $data['item_id'] = $item_id;
                $data['product_type'] = $cart_model_name;
                $data['description'] = $description;
                $data['qty'] = $qty;
                $data['full_article_number'] = $full_article_number;
                $data['article_number'] = $article_number;
                $data['customer_name'] = $customer->name;
                $data['country'] = $customer->country;
                $data['unit_price'] = $unit_price;
                $data['total_price'] = $total_price;
                // $data['bom'] = $bom;
                array_push($response,$data);
            }
            return response()->json(['status' => '1', 'message' => 'Products details listed successfully','data' => $response]);
        }
        else{
             return response()->json(['status' => '0', 'message' => 'Quotation number is not exist.','data' => []]);
        }
    }

    public function getBOM(Request $request){
        if($request->item_name == "Atmos"){
            $atmosGigaController = new AtmosGigaController();
            $cartData = $atmosGigaController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "Atmos";
        }

        if($request->item_name == "SCP"){
            $scpController = new ScpController();
            $cartData = $scpController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "SCP";
        }

        if($request->item_name == "booster"){
            $boosterController = new BoosterSetController();
            $cartData = $boosterController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "booster";
        }

        if($request->item_name == "Control Panel"){
            $controlPanelController = new CPCartController();
            $cartData = $controlPanelController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "Control Panel";
        }

        if($request->item_name == "Fire-Fighting"){
            $fire_fightingController = new FireFightingPumpController();
            $type = null;
            // $cartItems = $fireFightingPumpController->cartItems($id, false, $type, $returnDataOnly);

            $cartData = $fire_fightingController->cartItems($request->item_id, request(), $type, true);
            $bom = $cartData;
            $item_name = "Fire-Fighting";
        }

        if($request->item_name == "Booster"){
            $boosterController = new BoosterSetController();
            $cartData = $boosterController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "Booster";
        }

        return response()->json(['status' => '1', 'message' => 'BOM listed successfully.','data' => $bom,'item_name' => $item_name]);
    }

    public function getBOMCheckStatus(Request $request){

        if($request->cart_model_name == "Atmos"){
            $atmosCart = AtmosCart::where('quotation_no',$request->quotation_number)->where('full_article_number',$request->full_article_number)->first();
            $request->item_id = $atmosCart->id;
            $atmosGigaController = new AtmosGigaController();
            $cartData = $atmosGigaController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "Atmos";
        }

        if($request->cart_model_name == "SCP"){
            $ScpCart = ScpCart::where('quotation_no',$request->quotation_number)->where('full_article_number',$request->full_article_number)->first();
            $request->item_id = $ScpCart->id;
            $scpController = new ScpController();
            $cartData = $scpController->cartItems($request->item_id, true);
            $bom = $cartData;
            $item_name = "SCP";
        }
        return response()->json(['status' => '1', 'message' => 'BOM listed successfully.','data' => $bom,'item_name' => $item_name]);
    }

    public function get_witrack_project_data(Request $request){
        
    }
}
