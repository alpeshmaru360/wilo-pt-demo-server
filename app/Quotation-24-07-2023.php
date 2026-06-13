<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Customer;
use App\ControlPanelCart;
use App\ControlPanel;
use App\Helpers\Helper;
use App\Quotation;
use App\Item;
use App\AtmosCart;
use App\Models\FireFighting\FireFightingCarts;
use App\Models\FireFighting\ElectricalPump;
use App\Models\FireFighting\DieselPump;
use App\Models\FireFighting\JockeyPump;
use App\AtmosItem;
use App\ScpCart;
use App\Models\BoosterCart;
use App\AtmosPump;
use App\ScpPumpType;

class Quotation extends Model
{
    public static function get_excel_file()
    {
		set_time_limit(0);
         $quotation = Quotation::select('quotations.id as QuotationId','quotations.*','users.id as UserId','users.name as UserName','users.country_id','countries.id as CountryId','countries.country','customers.id as CustomerId','customers.name as CustomerName','customers.project_name','customers.country as ProjectCountry','customers.project_location as ProjectLocation','booster_carts.id as BoosterCartId','scp_carts.id as ScpCartId','atmos_carts.id as AtmosCartId','control_panel_carts.id as ControlPanelId','booster_carts.full_article_number as BFullArticleNumber','booster_carts.pump_type as BPumpType','booster_carts.supply_voltage as BSupplyVoltage','booster_carts.adder_ids as BElecticleAdderIds','booster_carts.total_price as BTotalPrice','booster_carts.qty as BQty','booster_carts.price as BUnitPrice','booster_carts.mechanical_adder_ids as BMechanicalAdderIds','booster_carts.system_pressure as BSystemPressure','booster_carts.model_no as BModelNo','booster_carts.manifold as BManifold','booster_carts.inter_company_margin as BInterCompanyMargin','booster_carts.booster_overhead as BOverHead','booster_carts.mechanical_total_adders_price as BMechanicalTotalAdderIdsPrice','booster_carts.total_adders_price as BTotalAddersPrice','booster_carts.cp_price as BCPPrice','booster_carts.cablePrice as BCablePrice','booster_carts.mechanical_system_price as BMechanicalSystemPrice','booster_carts.pump_price as BPumpPrice','booster_carts.cp_id as BCpId','booster_carts.booster_article_number as BBoosterArticleNumber','booster_carts.article_number as BArticleNumber','control_panel_carts.starter_code as CStarterCode','atmos_carts.full_article_number as AFullArticleNumber','atmos_carts.pump_id as APump_id','atmos_carts.pump_name as APumpName','atmos_carts.material_id as AMaterialId','atmos_carts.brand as ABrand','atmos_carts.power as APower','atmos_carts.no_of_pole as ANoOfPoles','atmos_carts.voltage as AVoltage','atmos_carts.frequency as AFrequency','atmos_carts.efficiency as AEfficiency','atmos_carts.adder_ids as AAdderIds','atmos_carts.qty as AQty','atmos_carts.article_number as AArticleNumber','atmos_carts.price as AUnitPrice','atmos_carts.total_price as ATotalPrice','atmos_carts.application as AApplication','atmos_carts.shipping_cost_price as AShippingCostPrice','atmos_carts.packing_charge as APackingCharge','atmos_carts.painting_charge as APaintingCharge','atmos_carts.assembly_charge as AAssemblyCharge','atmos_carts.insulate_bearing_price as AInsulateBearingPrice','atmos_carts.accesories_price as AAssesoriesPrice','atmos_carts.inter_company_margin_price as AInterCompanyMarginPrice','atmos_carts.overhead_price as AOverHead','atmos_carts.bare_pump_price as APumpPrice','control_panel_carts.full_article_number as CFatmos_cartsullArticleNumber','control_panel_carts.control_panel_id as CControlPanelId','control_panel_carts.no_of_pump_id as CNoOfPumpId','control_panel_carts.power_id as CPowerId','control_panel_carts.voltage_id as CVoltageId','control_panel_carts.application_id as CApplicationId','control_panel_carts.ambient_temp_id as CAmbientTempId','control_panel_carts.stater_type_id as CStaterTypeId','control_panel_carts.full_article_number as CFullArticleNumber','control_panel_carts.article_number as CArticleNumber','control_panel_carts.communication_protocol_id as CCommunicationProtocolId','control_panel_carts.ip_rating_id as CIpRatingId','control_panel_carts.components_id as CComponentId','control_panel_carts.enclosure_id as CEnclosureId','control_panel_carts.qty as CQty','control_panel_carts.price as CUnitPrice','control_panel_carts.total_price as CTotalPrice','control_panel_carts.adder_ids as CAdderIds','control_panel_carts.intercompany_margin as CInterCompanyMargin','control_panel_carts.overhead as COverHead','control_panel_carts.price as CPrice','scp_carts.full_article_number as SFullArticleNumber','scp_carts.pump_id as SPumpId','scp_carts.pump_name as SPumpName','scp_carts.material_id as SMaterialId','scp_carts.article_number as SArticleNumber','scp_carts.seal_gland_pack_id as SSealGlandPackId','scp_carts.master_id as SMasterId','scp_carts.brand as SBrand','scp_carts.power as SPower','scp_carts.power as SPower','scp_carts.no_of_pole as SNoOfPole','scp_carts.voltage as SVoltage','scp_carts.frequency as SFrequency','scp_carts.efficiency as SEfficiency','scp_carts.adder_ids as SAdderIds','scp_carts.qty as SQty','scp_carts.price as SUnitPrice','scp_carts.application as SApplication','scp_carts.total_price as STotalPrice','scp_carts.shipping_cost_price as SShippingCostPrice','scp_carts.packing_charge as SPackingCharge','scp_carts.painting_charge as SPaintingCharge','scp_carts.assembly_charge as SAssemblyCharge','scp_carts.insulate_bearing_price as SInsulateBearingPrice','scp_carts.accesories_price as SAssesoriesPrice','scp_carts.overhead_price as SOverHead','scp_carts.bare_pump_price as SPumpPrice','scp_carts.inter_company_margin_price as SInterCompanyMargin',DB::raw("(SELECT value from powers where id = booster_carts.motor_power) as BMotorPower"),DB::raw("(SELECT value from powers where id = control_panel_carts.power_id) as CPower"),DB::raw("(SELECT value from voltages where id = booster_carts.supply_voltage) as BSupplyVoltages"),DB::raw("(SELECT value from voltages where id = control_panel_carts.voltage_id) as CSupplyVoltages"),DB::raw("(SELECT value from applications where id = control_panel_carts.application_id) as CApplicationIdd"),DB::raw("(SELECT value from enclousres where id = control_panel_carts.enclosure_id) as CEnclosure"),DB::raw("(SELECT value from components where id = control_panel_carts.components_id) as CComponent"),DB::raw("(SELECT value from ip_ratings where id = control_panel_carts.ip_rating_id) as CIpRating"),DB::raw("(SELECT value from comunication_protocols where id = control_panel_carts.communication_protocol_id) as CCommunicationProtocol"),DB::raw("(SELECT value from starter_types where id = control_panel_carts.stater_type_id) as CStarterType"),DB::raw("(SELECT value from ambient_temps where id = control_panel_carts.ambient_temp_id) as CAmbientTemp"))
                    ->leftJoin('users','users.id','=','quotations.user_id')
                    ->leftJoin('customers','customers.id','=','quotations.customer_id')
                    ->leftJoin('countries','countries.id','=','users.country_id')
                    ->leftJoin('atmos_carts', function ($join) {
                        $join->on('atmos_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'atmos');
                            })
                    ->leftJoin('booster_carts', function ($join) {
                                $join->on('booster_carts.id', '=', 'quotations.cp_cart_id')
                                ->where('quotations.cart_model_name', '=', 'booster');
                            })
                    ->leftJoin('control_panel_carts', function ($join) {
                        $join->on('control_panel_carts.id', '=', 'quotations.cp_cart_id')
                        ->where('quotations.cart_model_name', '=', 'controlpanel');
                            })
                    ->leftJoin('scp_carts', function ($join) {
                        $join->on('scp_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'scp');
                               })
							   ->leftJoin('firefighting_carts', function ($join) {
                        $join->on('firefighting_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'firefighting');
                               })
                    ->get();
                    
                    $response = array();
                    $i = 1;
                    foreach($quotation as $value)
                    {
                        $data = array();
                        $data['id'] = $value->QuotationId;
                        $data['sr_no'] = $i;
                        $data['date'] = date('d-m-Y', strtotime($value->created_at));
                        $data['user_name'] = $value->UserName;
                        $data['country'] = $value->country;
                        $data['quotation_no'] = $value->quotation_number;
                        $data['project_name'] = $value->project_name;
                        $data['customer_name'] = $value->CustomerName;
                        $data['project_country'] = $value->ProjectCountry;
                        $data['project_location'] = $value->ProjectLocation;
                        if($value->cart_model_name == 'atmos')
                        {
							$data['simple_article_no'] = $value->AArticleNumber;
							$data['article_no'] = $value->AFullArticleNumber;
                            $short_code = static::atmos_short_code($value->AMaterialId);
                            if($short_code == "-")
                            {
                                $data['description'] = $value->APumpName.'/'.$value->APower.'KW'.'/'.$value->ANoOfPoles.'/AE';
                            }
                            else
                            {
                                $data['description'] = $value->APumpName.'-'.$short_code.'/'.$value->APower.'KW/'.$value->ANoOfPoles.'/AE';
                            }
                            $data['qty'] = $value->AQty;
                            $data['unit_price'] = $value->AUnitPrice;
                            $data['total_price'] = $value->ATotalPrice;
                            $data['pump_type'] = '-';
                            $data['system_pressure'] = '-';
                            if($value->AMaterialId == '1')
                            {
                                $data['impeller_material'] = 'Cast Iron';
                            }
                            elseif($value->AMaterialId == '2')
                            {
                                $data['impeller_material'] = 'Bronze';
                            }
                            elseif($value->AMaterialId == '3')
                            {
                                $data['impeller_material'] = 'Stainless steel';
                            }
                            else
                            {
                                $data['impeller_material'] = 'Other';
                            }
                            $data['no_of_pumps'] = '1';
                            $data['manifold_material'] = '-';
                            $data['Seal/gland pack'] = '-';
                            $data['pump_description'] = $value->APumpName;
                            $data['motor_power'] =$value->APower;
                            $data['voltage'] = $value->AVoltage;
                            $data['frequency'] = $value->AFrequency;
                            $data['no_of_poles'] = $value->ANoOfPoles;
                            $data['efficicency'] = $value->AEfficiency;
                            $data['motor_brand'] = $value->ABrand;
                            if($value->AApplication == '1')
                            {
                                $data['application'] = 'Constant';
                            }
                            elseif($value->AApplication == '2')
                            {
                                $data['application'] = 'Variable';
                            }
                            else
                            {
                                $data['application'] = 'Other';
                            }
                            $data['adder_ids'] = is_null($value->AAdderIds)?'-':$value->AAdderIds;
							$data['adder_ids_data'] = '-';
							$data['shipping_cost'] = $value->AShippingCostPrice;
                            $data['packing_charge'] = $value->APackingCharge;
                            $data['painting_charge'] = $value->APaintingCharge;
                            $data['assembly_charge'] = $value->AAssemblyCharge;
                            $data['insulate_bearing_price'] = $value->AInsulateBearingPrice;
                            $data['accesories_price'] = $value->AAssesoriesPrice;
                            $data['inter_company_margin_price'] = $value->AInterCompanyMarginPrice;
                            $data['overhead'] = $value->AOverHead;
                            $data['mechanical_total_adder_id_price'] = '-';
                            $data['total_adders_ids_price'] = '-';
                            $data['CP Price'] = '-';
                            $data['Cable Price'] = '-';
                            $data['Mechnical System price'] = '-';
                            if($value->APumpPrice == "0.00")
                            {
                                $data['Pump price'] = "0.00";
                            }
                            else
                            {
                                $data['Pump price'] = $value->APumpPrice;
                            }
                            $pump_article_number =static::atmos_pump_article_number($value->AMaterialId,$value->APump_id);
                            $data['pump_article_number'] = $pump_article_number;
                            $data['ambient_temp'] = '-';
                            $data['starter_type'] = '-';
                            $data['commication_protocal'] = '-';
                            $data['ip_rating'] = '-';
                            $data['components'] = '-';
                            $data['Enclosure'] = '-';
                        }
                        elseif($value->cart_model_name == 'booster')
                        {
							$data['simple_article_no'] = $value->BArticleNumber;
							$starter_type = static::cp_stater_type_id($value->BCpId); 
                            $no_of_pump =static::cp_no_of_pump($value->BCpId);
                            $table_name = static::cp_table_name($value->BCpId);
                            $data['article_no'] = $value->BFullArticleNumber;
                            if($starter_type == '-' || $no_of_pump == '-' || $table_name == '-')
                            {
                                $data['description'] = '';
                            }
                            else
                            {
                                $data['description'] = $table_name.' '.$no_of_pump.' '.$value->BModelNo.'/'.$starter_type.'/AE';
                            }
                            $data['qty'] = $value->BQty;
                            $data['unit_price'] = $value->BUnitPrice;
                            $data['total_price'] = $value->BTotalPrice;
                            $data['pump_type'] = $value->BPumpType;
                            $data['system_pressure'] = $value->BSystemPressure;
                            $data['impeller_material'] = '-';
                           
                            //if($starter_type == '-' || $no_of_pump == '-' || $table_name == '-')
                           // {
                             //   $data['no_of_pumps'] = '-';
                            //}
                            //else
                            //{
                              //  $data['no_of_pumps'] = $table_name.' '.$no_of_pump.' '.$value->BModelNo.'/'.$starter_type.'/AE';
                            //}
							if($no_of_pump == '-')
							{
								$data['no_of_pumps'] = '-';
							}
							else
							{
								$data['no_of_pumps'] = $no_of_pump;
							}

                            $data['manifold_material'] = $value->BManifold;
                            $data['Seal/gland pack'] = '-';
                            $data['pump_description'] = $value->BModelNo;
                            $data['motor_power'] = $value->BMotorPower;
                            $data['voltage'] = $value->BSupplyVoltages;
                            $data['frequency'] = '-';
                            $data['no_of_poles'] = '-';
                            $data['efficicency'] = '-';
                            if($value->BPumpType == 'full_pump')
                            {
                                $data['motor_brand'] = 'WSM';
                            }
                            elseif($value->BPumpType == 'bareshaft_pump')
                            {
                                $data['motor_brand'] = 'TEE';
                            }
                            else
                            {
                                $data['motor_brand'] = '-';
                            }
                            $data['application'] = 'Booster';
                            if($value->BElecticleAdderIds == null && $value->BMechanicalAdderIds == null)
                            {
                                $data['adder_ids'] = '-';
                            }
                            elseif($value->BElecticleAdderIds == null)
                            {
                                $data['adder_ids'] = $value->BMechanicalAdderIds;
                            }
                            else
                            {
                                $data['adder_ids'] = $value->BElecticleAdderIds.','.$value->BMechanicalAdderIds;
                            }
							
							  if(!empty($value->BMechanicalAdderIds) &&  $value->BMechanicalAdderIds != null)
                            {
                                $adderIds = explode(",", $value->BMechanicalAdderIds);
                                $mechanical_Data = DB::table('booster_items')
                                ->select('item_description','adder_code','qty')
                                ->where('booster_cart_id',$value->BoosterCartId)
                                ->whereIn('adder_code',  $adderIds)->get();
                                //dd($mechanical_Data);
                                $data['adder_ids_data'] = array();
                                foreach($mechanical_Data as $val)
                                {   
                                    if($val->adder_code == "60" || $val->adder_code == "61")
                                    {
                                        $data['adder_ids_data'][] = $val->adder_code.'[Qty = '.$val->qty.']';
                                    }
                                    if($val->adder_code == "65" || $val->adder_code == "66" || $val->adder_code == "67")
                                    {
                                        $item_description_in_liter = explode("-",$val->item_description);
									//	print_r($item_description_in_liter);
                                      //   echo "<br>";
                                        $data['adder_ids_data'][] = $val->adder_code.'[Liter ='.$item_description_in_liter[2].']';
                                    }
                                }
                                $data['adder_ids_data'] = implode(",", $data['adder_ids_data']);
                            }
                            else
                            {
                                  $data['adder_ids_data'] = '-';
                            }



                            $data['shipping_cost'] = '-';
                            $data['packing_charge'] = '-';
                            $data['painting_charge'] = '-';
                            $data['assembly_charge'] = '-';
                            $data['insulate_bearing_price'] = '-';
                            $data['accesories_price'] = '-';
                            $data['inter_company_margin_price'] = $value->BInterCompanyMargin;
                            $data['overhead'] = $value->BOverHead;
                            if($value->BMechanicalTotalAdderIdsPrice == "0.00")
                            {
                                $data['mechanical_total_adder_id_price'] = "0.00";
                            }
                            else{
                                $data['mechanical_total_adder_id_price'] = $value->BMechanicalTotalAdderIdsPrice;
                            }
                            $data['total_adders_ids_price'] = $value->BTotalAddersPrice;
                            if($value->BCPPrice == "0.0")
                            {
                                $data['CP Price'] = "0";    
                            }
                            else
                            {
                                $data['CP Price'] = $value->BCPPrice;
                            }
                            $data['Cable Price'] = $value->BCablePrice;
                            $data['Mechnical System price'] = $value->BMechanicalSystemPrice;
                            if($value->BPumpPrice == "0.00")
                            {
                                $data['Pump price'] = "0.00";    
                            }
                            else
                            {
                                $data['Pump price'] = $value->BPumpPrice;
                            }
                            $data['pump_article_number'] = $value->BBoosterArticleNumber;
                            $cp_ambient_temps = static::cp_ambient_temps($value->BCpId);
                            $data['ambient_temp'] = $cp_ambient_temps;
                            $starter_type = static::cp_stater_type_id($value->BCpId); 
                            $data['starter_type'] = $starter_type;
                            $communication_protocol = static::cp_communication_protocol($value->BCpId);
                            $data['commication_protocal'] = $communication_protocol;
                            $ip_rating = static::cp_ip_rating($value->BCpId);
                            $data['ip_rating'] = $ip_rating;
                            $component = static::cp_component($value->BCpId);
                            $data['components'] = $component;
                            $enclosure = static::cp_enclosure($value->BCpId);
                            $data['Enclosure'] = $enclosure;
                        }

                        elseif($value->cart_model_name == 'controlpanel')
                        {
							$data['simple_article_no'] = $value->CArticleNumber;
							$data['article_no'] = $value->CFullArticleNumber;
							$data['description'] = 'Control Panel'.' '.$value->CNoOfPumpId.' X '.$value->CPower.'KW'.' '.$value->CStarterCode.'/AE';
                            $data['qty'] = $value->CQty;
                            $data['unit_price'] = $value->CUnitPrice;
                            $data['total_price'] = $value->CTotalPrice;
                            $data['pump_type'] = '-';
                            $data['system_pressure'] = '-';
                            $data['impeller_material'] = '-';
                            $data['no_of_pumps'] = $value->CNoOfPumpId;
                            $data['manifold_material'] = '-';
                            $data['Seal/gland pack'] = '-';
                            $data['pump_description'] = '-';
                            $data['motor_power'] =$value->CPower;
                            $data['voltage'] = $value->CSupplyVoltages;
                            $data['frequency'] = '-';
                            $data['no_of_poles'] = '-';
                            $data['efficicency'] = '-';
                            $data['motor_brand'] = '-';
                            $data['application'] = $value->CApplicationIdd;
                            $data['adder_ids'] = is_null($value->CAdderIds)?'-':$value->CAdderIds;
							$data['adder_ids_data'] = '-';
							$data['shipping_cost'] = '-';
                            $data['packing_charge'] = '-';
                            $data['painting_charge'] = '-';
                            $data['assembly_charge'] = '-';
                            $data['insulate_bearing_price'] = '-';
                            $data['accesories_price'] = '-';
                            $data['inter_company_margin_price'] = $value->CInterCompanyMargin;
                            $data['overhead'] = $value->COverHead;
                            $data['mechanical_total_adder_id_price'] = '-';
                            $data['total_adders_ids_price'] = '-';
                            $data['CP Price'] = $value->CUnitPrice;
                            $data['Cable Price'] = '-';
                            $data['Mechnical System price'] = '-';
                            $data['Pump price'] = '-';
                            $data['pump_article_number'] = '-';
                            $data['ambient_temp'] = $value->CAmbientTemp;
                            $data['starter_type'] = $value->CStarterType;
                            $data['commication_protocal'] = $value->CCommunicationProtocol;
                            $data['ip_rating'] = $value->CIpRating;
                            $data['components'] = $value->CComponent;
                            $data['Enclosure'] = $value->CEnclosure;
                        }

                        elseif($value->cart_model_name == 'scp')
                        {
							$data['simple_article_no'] = $value->SArticleNumber;
							$data['article_no'] = $value->SFullArticleNumber;
                            $short_code = static::atmos_short_code($value->AMaterialId);
                            if($short_code == "-")
                            {
                                $data['description'] = $value->SPumpName.'/'.$value->SPower.'KW'.'/'.$value->SNoOfPole.'/AE';
                            }
                            else
                            {
                                $data['description'] = $value->SPumpName.'-'.$short_code.'/'.$value->SPower.'KW/'.$value->SNoOfPole.'/AE';
                            }
                            $data['qty'] = $value->SQty;
                            $data['unit_price'] = $value->SUnitPrice;
                            $data['total_price'] = $value->STotalPrice;
                            $data['pump_type'] = '-';
                            $data['system_pressure'] = '-';
                            if($value->SMaterialId == '1')
                            {
                                $data['impeller_material'] = 'Cast Iron';
                            }
                            elseif($value->SMaterialId == '2')
                            {
                                $data['impeller_material'] = 'Bronze';
                            }
                            elseif($value->SMaterialId == '3')
                            {
                                $data['impeller_material'] = 'Stainless steel';
                            }
                            else
                            {
                                $data['impeller_material'] = 'Other';
                            }                         
                            $data['no_of_pumps'] = '1';
                            $data['manifold_material'] = '-';
                            if($value->SSealGlandPackId == '1')
                            {
                                $data['Seal/gland pack'] = 'Seal';
                            }
                            elseif($value->SSealGlandPackId == '2')
                            {
                                $data['Seal/gland pack'] = 'Gland';
                            }
                            else
                            {
                                $data['Seal/gland pack'] = 'Other';
                            }
                            $data['pump_description'] = $value->SPumpName;
                            $data['motor_power'] =$value->SPower;
                            $data['voltage'] = $value->SVoltage;
                            $data['frequency'] = $value->SFrequency;
                            $data['no_of_poles'] = $value->SNoOfPole;
                            $data['efficicency'] = $value->SEfficiency;
                            $data['motor_brand'] = $value->SBrand;
                            if($value->SApplication == '1')
                            {
                                $data['application'] = 'Constant';
                            }
                            elseif($value->SApplication == '2')
                            {
                                $data['application'] = 'Variable';
                            }
                            else
                            {
                                $data['application'] = 'Other';
                            }
                            $data['adder_ids'] =  is_null($value->SAdderIds)?'-':$value->SAdderIds;
                            $data['adder_ids_data'] = '-';
							$data['shipping_cost'] = $value->SShippingCostPrice;
                            $data['packing_charge'] = $value->SPackingCharge;
                            $data['painting_charge'] = $value->SPaintingCharge;
                            $data['assembly_charge'] = $value->SAssemblyCharge;
                            $data['insulate_bearing_price'] = $value->SInsulateBearingPrice;
                            $data['accesories_price'] = $value->SAssesoriesPrice;
                            $data['inter_company_margin_price'] = $value->SInterCompanyMargin;
                            $data['overhead'] = $value->SOverHead;
                            $data['mechanical_total_adder_id_price'] = '-';
                            $data['total_adders_ids_price'] = '-';
                            $data['CP Price'] = '-';
                            $data['Cable Price'] = '-';
                            $data['Mechnical System price'] = '-';
                            if($value->SPumpPrice == "0.00")
                            {
                                $data['Pump price'] = "0.00";
                            }
                            else
                            {
                                $data['Pump price'] = $value->SPumpPrice;
                            }
                            $pump_article_number =static::scp_pump_article_number($value->SPumpName);
                            $data['pump_article_number'] = $pump_article_number;
                            $data['ambient_temp'] = '-';
                            $data['starter_type'] = '-';
                            $data['commication_protocal'] = '-';
                            $data['ip_rating'] = '-';
                            $data['components'] = '-';
                            $data['Enclosure'] = '-';
                        }
						elseif($value->cart_model_name == 'firefighting')
                        {
                            $firefightingCart = FireFightingCarts::where('quotation_no', $value->quotation_number)->where('id', $value->cp_cart_id)->first();
                            if (is_null($firefightingCart)) {
                                goto elsemode;
                            }
                            
                            $data['simple_article_no'] = $firefightingCart->article_number;
                            $data['article_no'] = $firefightingCart->full_article_number;
                            $data['description'] = $firefightingCart->pump_models;
                            $data['qty'] = $firefightingCart->qty;
                            $data['unit_price'] = $firefightingCart->price;
                            $data['total_price'] = $firefightingCart->total_price;
                            $data['pump_type'] = $firefightingCart->pump_type ?? '-';

                            $pump_data = static::fireFightingPumpData($firefightingCart);
                            // dd($value, $firefightingCart, $pump_data); 

                            $data['system_pressure'] = $pump_data['system_pressure'];
                            $data['impeller_material'] = $pump_data['impeller_material'];
                            $data['no_of_pumps'] = $pump_data['no_of_pumps'];
                            $data['manifold_material'] = '-';
                            $data['Seal/gland pack'] = '-';
                            $data['pump_description'] = current(explode('/', $firefightingCart->pump_models));
                            $data['motor_power'] = $pump_data['motor_power'];
                            $data['voltage'] = $pump_data['voltage'];
                            $data['frequency'] = $pump_data['frequency'];
                            $data['no_of_poles'] = $pump_data['no_of_poles'];
                            $data['efficicency'] = $pump_data['efficicency'];
                            $data['motor_brand'] = $pump_data['motor_brand'];

                            $data['application'] = 'Fire Pump';
                            $data['adder_ids'] =  implode(',', $firefightingCart->adder_ids ?? []);
                            $data['adder_ids_data'] = '-';
                            $data['shipping_cost'] = '-';
                            $data['packing_charge'] = '-';
                            $data['painting_charge'] = '-';
                            $data['assembly_charge'] = '-';
                            $data['insulate_bearing_price'] = '-';
                            $data['accesories_price'] = '-';
                            $data['inter_company_margin_price'] = $firefightingCart->inter_company_margin_price;
                            $data['overhead'] = $firefightingCart->overhead_price;
                            $data['mechanical_total_adder_id_price'] = '-';
                            $data['total_adders_ids_price'] = $firefightingCart->total_adders_price;
                            $data['CP Price'] = $firefightingCart->all_prices['control_panel_price'];
                            $data['Cable Price'] = '-';
                            $data['Mechnical System price'] = '-';
                            $data['Pump price'] = $firefightingCart->all_prices['pump_price'];
                            $data['pump_article_number'] = $pump_data['pump_article_number'] ?? '-';
                            $data['ambient_temp'] = '-';
                            $data['starter_type'] = '-';
                            $data['commication_protocal'] = '-';
                            $data['ip_rating'] = '-';
                            $data['components'] = '-';
                            $data['Enclosure'] = '-';
                        }

                        else
                        {
							elsemode:
							$data['simple_article_no'] = '-';
							$data['article_no'] = '-';
                            $data['description'] = '' ;
                            $data['qty'] = '-';
                            $data['unit_price'] = '-';
                            $data['total_price'] = '-';
                            $data['pump_type'] = '-';
                            $data['system_pressure'] = '-';
                            $data['impeller_material'] = '-';
                            $data['no_of_pumps'] = '-';
                            $data['manifold_material'] = '-';
                            $data['Seal/gland pack'] = '-';
                            $data['pump_description'] = '-';
                            $data['motor_power'] ='-';
                            $data['voltage'] = '-';
                            $data['frequency'] = '-';
                            $data['no_of_poles'] = '-';
                            $data['efficicency'] = '-';
                            $data['motor_brand'] = '-';
                            $data['application'] = '-';
                            $data['adder_ids'] =  '-';
							$data['adder_ids_data'] = '-';
							$data['shipping_cost'] = '-';
                            $data['packing_charge'] = '-';
                            $data['painting_charge'] = '-';
                            $data['assembly_charge'] = '-';
                            $data['insulate_bearing_price'] = '-';
                            $data['accesories_price'] = '-';
                            $data['accesories_price'] = '-';
                            $data['inter_company_margin_price'] = '-';
                            $data['overhead'] = '-';
                            $data['mechanical_total_adder_id_price'] = '-';
                            $data['total_adders_ids_price'] = '-';
                            $data['CP Price'] = '-';
                            $data['Cable Price'] = '-';
                            $data['Mechnical System price'] = '-';
                            $data['Pump price'] = '-';
                            $data['pump_article_number'] = '-';
                            $data['ambient_temp'] = '-';
                            $data['starter_type'] = '-';
                            $data['commication_protocal'] = '-';
                            $data['ip_rating'] = '-';
                            $data['components'] = '-';
                            $data['Enclosure'] = '-';
                        }
                        $data['Module'] = $value->cart_model_name;
                        $data['status'] = $value->status;
                        $data['reason'] = $value->reason;
                       array_push($response,$data);
                       $i++;
                    } 
					//	exit();
                    return $response;
    }

    public static function atmos_short_code($matirial_id)
    {
        $short_code = DB::table('atmos_materials')->where('id','=','matirial_id')->pluck("short_code")->first();
        if($short_code)
        {
            $short_code = $short_code;
        }
        else
        {
            $short_code = '-';
        }
        return $short_code;
    }

    public static function atmos_pump_article_number($pump_id,$material_id)
    {
        $article = AtmosPump::select('bare_pump_article_no')
                             ->where('pump_id','=',$pump_id)
                             ->where('material_id','=',$material_id)
                             ->first();
        if($article)
        {
            $article = $article->bare_pump_article_no;
        }
        else
        {
            $article = '-';
        }
        return $article;
    }

    public static function scp_pump_article_number($pump_name)
    {
        $article = ScpPumpType::select('bare_shaft_article_number')
                             ->where('name','=',$pump_name)
                             ->first();
        if($article)
        {
            $article = $article->bare_shaft_article_number;
        }
        else
        {
            $article = '-';
        }
        return $article;
    }

    public static function cp_table_name($cp_id)
    {
        $table_name = ControlPanel::select('table_name')->where('id','=',$cp_id)->first();
        if($table_name)
        {
            if(str_starts_with($table_name->table_name,"basic_") == true)
            {
                $table_name = "COE";
            }
            else{
                $table_name = "CO";
            }
        }
        else
        {
            $table_name = "-";
        }
        return $table_name;
    }

    public static function cp_no_of_pump($cp_id)
    {
        $cp_no_of_pump = ControlPanel::select('no_of_pump_id')->where('id','=',$cp_id)->first();
        if($cp_no_of_pump)
        {
            $no_of_pump = $cp_no_of_pump->no_of_pump_id;
        }
        else
        {
            $no_of_pump = '-';
        }
        return $no_of_pump;
    }

    public static function cp_enclosure($cp_id)
    {
        $enclosure = ControlPanel::select('enclosure_id')->where('id','=',$cp_id)->first();
        if($enclosure)
        {
            if($enclosure->enclosure_id == '1')
            {
                $enclosure = "PVC";    
            }
            elseif($enclosure->enclosure_id == '2')
            {
                $enclosure = "Metal";    
            }
            elseif($enclosure->enclosure_id == '3')
            {
                $enclosure = "GRP";    
            }
            elseif($enclosure->enclosure_id == '4')
            {
                $enclosure = "Stainless steel";
            }
            else
            {
                $enclosure = "-";    
            }
        }    
        else
        {
            $enclosure = "-";
        }
        return $enclosure;
    }

    public static function cp_component($cp_id)
    {
        $component = ControlPanel::select('components_id')->where('id','=',$cp_id)->first();
        if($component)
        {
            if($component->components_id == "1")
            {
                $component = "Standard";
            }
            elseif($component->components_id == "2")
            {
                $component = "Economic";
            }
            else
            {
                $component = "-";
            }
        }
        else
        {
            $component = "-";
        }
        return $component;
    }

    public static function cp_ip_rating($cp_id)
    {
        $ip_rating = ControlPanel::select('ip_rating_id')->where('id','=',$cp_id)->first();
        if($ip_rating)
        {
            if($ip_rating->ip_rating_id == "1")
            {
                $ip_rating = "IP54";
            }
            elseif($ip_rating->ip_rating_id == "2")
            {
                $ip_rating = "IP55";
            }
            elseif($ip_rating->ip_rating_id == "3")
            {
                $ip_rating = "IP56";
            }
            else
            {
                $ip_rating = "-";
            }
        }
        else
        {
            $ip_rating = "-";
        }
        return $ip_rating;
    }

    public static function cp_communication_protocol($cp_id)
    {
        $communication_protocol = ControlPanel::select('communication_protocol_id')->where('id','=',$cp_id)->first();
        {
            if($communication_protocol)
            {
                if($communication_protocol->communication_protocol_id == "1")
                {
                    $communication_protocol = "VFC";
                }
                elseif($communication_protocol->communication_protocol_id == "2")
                {
                    $communication_protocol = "Modbus RTU";
                }
                elseif($communication_protocol->communication_protocol_id == "3")
                {
                    $communication_protocol = "Modbus TCP";
                }
                elseif($communication_protocol->communication_protocol_id == "4")
                {
                    $communication_protocol = "Bacnet";
                }
                else
                {
                    $communication_protocol = "else1";
                }
            }
            else
            {
                $communication_protocol = "ele1";
            }
            return $communication_protocol;
        }
    }

    public static function cp_stater_type_id($cp_id)
    {
        $cp_starter_type_id = ControlPanel::select('stater_type_id')->where('id','=',$cp_id)->first();
        if($cp_starter_type_id)
        {
            if($cp_starter_type_id->stater_type_id == "1")
            {
                $cp_starter_type_id = "Xtreme";
            }
            elseif($cp_starter_type_id->stater_type_id == "2")
            {
                $cp_starter_type_id = "Constant speed- DOL";
            }
            elseif($cp_starter_type_id->stater_type_id == "3")
            {
                $cp_starter_type_id = "Multi VFD";
            }
            elseif($cp_starter_type_id->stater_type_id == "4")
            {
                $cp_starter_type_id = "Multi VFD + Bypass";
            }
            elseif($cp_starter_type_id->stater_type_id == "5")
            {
                $cp_starter_type_id = "Softstarter";
            }
            elseif($cp_starter_type_id->stater_type_id == "6")
            {
                $cp_starter_type_id = "Constant speed- SD";
            }
            elseif($cp_starter_type_id->stater_type_id == "7")
            {
                $cp_starter_type_id = "Single VFD";
            }
            else
            {
                $cp_starter_type_id = "-";
            }
        }
        else
        {
            $cp_starter_type_id = "-";
        }
        return $cp_starter_type_id;
    }

    public static function cp_ambient_temps($cp_id)
    {
        $cp_ambient_temps = ControlPanel::select('ambient_temp_id')->where('id','=',$cp_id)->first();
        if($cp_ambient_temps)
        {
            if($cp_ambient_temps->ambient_temp_id == "1")
            {
                $cp_ambient_temps = "40";
            }
            elseif($cp_ambient_temps->ambient_temp_id == "2")
            {
                $cp_ambient_temps = "50";
            }
            else
            {
                $cp_ambient_temps = "-";
            }
        }
        else
        {
            $cp_ambient_temps = "-";
        }
        return $cp_ambient_temps;
    }
    public static function fireFightingPumpData($firefightingCart)
    {
        $electricalchange = [
            'id' => 'id',
            'electrical_pumpmodels' => 'wilo_pump_models', 
            'electrical_pumptype' => 'pump_type', 
            'electrical_frequency' => 'frequency', 
            'electrical_pump_approval' => 'pump_approval', 
            'electrical_flow' => 'flow', 
            'electrical_head' => 'head', 
            'electrical_speed' => 'speed_rpm'
        ];
        $dieselchange = [
            'id' => 'id',
            'diesel_pumpmodels' => 'pump_models',
            'diesel_pumptype' => 'pump_type',
            'diesel_frequency' => 'frequency',
            'diesel_pump_approval' => 'pump_approval',
            'diesel_engine_approval' => 'engine_approval',
            'diesel_flow' => 'flow',
            'diesel_head' => 'head',
            'diesel_speed' => 'speed_rpm'
        ];
        switch ($firefightingCart->category) {
            case 'electrical':
                    $data = ElectricalPump::select('*');
                    $id = null;
                    foreach ($firefightingCart->field_val as $key => $value) {
                        if ($electricalchange[array_key_first($value)] != 'id') {
                            $data = $data->where($electricalchange[array_key_first($value)], $value[array_key_first($value)]);
                        } else {
                            $id = $value[array_key_first($value)];
                        }
                    }
                    $data = $data->get();

                    if (count($data) > 0) {
                        if (count($data) > 1) {
                            $data = $data->where('id', $id)->first();
                        } else {
                            $data = $data->first();
                        }
                    } else {
                        $data = null;
                    }

                    $datapass['system_pressure'] = '-';
                    $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                    $datapass['no_of_pumps'] = '1';
                    $datapass['motor_power'] = $data->motor_power ?? '-';
                    $datapass['voltage'] = $data->voltage ?? '-';
                    $datapass['frequency'] = $data->frequency ?? '-';
                    $datapass['no_of_poles'] = $data->speed_rpm >= 2900 ? 2 : 4;
                    $datapass['efficicency'] = '';
                    $datapass['motor_brand'] = $data->motor_brand ?? '-';
                    $datapass['pump_article_number'] = $data->electrical_pump_ordering_code ?? '-';
                    // dd($datapass);
                    return $datapass;
                break;

            case 'diesel':
                    $data = DieselPump::select('*');
                    $id = null;
                    foreach ($firefightingCart->field_val as $key => $value) {
                        if ($dieselchange[array_key_first($value)] != 'id') {
                            $data = $data->where($dieselchange[array_key_first($value)], $value[array_key_first($value)]);
                        } else {
                            $id = $value[array_key_first($value)];
                        }
                    }
                    $data = $data->get();

                    if (count($data) > 0) {
                        if (count($data) > 1) {
                            $data = $data->where('id', $id)->first();
                        } else {
                            $data = $data->first();
                        }
                    } else {
                        $data = null;
                    }

                    $datapass['system_pressure'] = '-';
                    $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                    $datapass['no_of_pumps'] = '1';
                    $datapass['motor_power'] = $data->engine_power ?? '-';
                    $datapass['voltage'] = $data->voltage ?? '-';
                    $datapass['frequency'] = $data->frequency ?? '-';
                    $datapass['no_of_poles'] = $data->speed_rpm >= 2900 ? 2 : 4;
                    $datapass['efficicency'] = '';
                    $datapass['motor_brand'] = $data->engine_brand ?? '-';
                    $datapass['pump_article_number'] = $data->diesel_pump_code ?? '-';

                    return $datapass;
                break;

            case 'jockey-pump':
                    $data = JockeyPump::where('pump_article_no', $firefightingCart->jockey_article_number)->where('power', $firefightingCart->power)->where('frequency', $firefightingCart->frequency)->first();

                    $datapass['system_pressure'] = '-';
                    $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                    $datapass['no_of_pumps'] = '1';
                    $datapass['motor_power'] = $data->power ?? '-';
                    $datapass['voltage'] = $data->voltage ?? '-';
                    $datapass['frequency'] = $data->frequency ?? '-';
                    $datapass['no_of_poles'] = '-';
                    $datapass['efficicency'] = '';
                    $datapass['motor_brand'] = $data->motor_brand ?? '-';
                    $datapass['pump_article_number'] = '-';
                    
                    return $datapass;
                break;
        }
    }
}
