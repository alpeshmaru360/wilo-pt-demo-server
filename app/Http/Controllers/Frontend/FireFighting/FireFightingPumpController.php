<?php

namespace App\Http\Controllers\Frontend\FireFighting;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\FireFighting\BatteryMaster;
use App\Models\FireFighting\ControlPanelMaster;
use App\Models\FireFighting\DieselPump;
use App\Models\FireFighting\DieselTankMaster;
use App\Models\FireFighting\ElectricalPump;
use App\Models\FireFighting\FireFightingAdders;
use App\Models\FireFighting\FireFightingCarts;
use App\Models\FireFighting\FireFightingFlowMeter;
use App\Models\FireFighting\FireFightingMotor;
use App\Models\FireFighting\FireFightingPressureReliefValve;
use App\Models\FireFighting\FireFightingWasteCone;
use App\Models\FireFighting\JockeyPump;
use App\Models\FireFighting\OptionalMaster;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\User;
use Illuminate\Http\Request;
use DB;

class FireFightingPumpController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */

    public function index()
    {
        $data['electrical_pump_type'] = ElectricalPump::select('pump_type')->groupBy('pump_type')->pluck('pump_type')->toArray();
        $data['diesel_pump_type'] = DieselPump::select('pump_type')->groupBy('pump_type')->pluck('pump_type')->toArray();
        $data['ic_margin'] = User::ic_margin_fire_fighting();
        $data['overhead'] = current(\DB::table('setup_fields')->where('name','fire_fighting_over_head')->pluck('value')->toArray());
        return view('frontend.fire-fighting.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'post_type' => 'required',
            'pump_type' => 'required'
        ]);

        switch ($request->post_type) {
            case 'price-calculate':
                    return $this->priceCalculate($request);
                break;

            case 'qty-change':
                    $qty = $request->qty;
                    $data = FireFightingCarts::find($request->firefighting_id);
                    $data->qty = $qty;
                    $data->total_adders_price = $data->adder_ids_prices * $qty;
                    $data->total_price = $data->price * $qty;
                    $data->save();

                    return [
                        'qty' => $data->qty,
                        'price' => $data->price,
                        'total_price' => CurrencyHelper::withCurrency($data->total_price)
                    ];
                break;

            case 'delete-cart':
                    $data = FireFightingCarts::find($request->firefighting_id);
                    if (!is_null($data)) {
                        $data->delete();
                    }
                    return true;
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function show($id)
    {
        switch ($id) {
            case 'jockey-pump':
                    $data = JockeyPump::select('id', 'pump_article_no', 'description', 'power', 'voltage' ,'frequency', 'unit_price')->get()->toArray();
                    return $data;
                break;
            
            // A Code: 10-03-2026 Start
            case 'electrical-pump':
                    $data = ElectricalPump::select('id', 'wilo_pump_models', 'pump_type', 'voltage', 'frequency', 'pump_approval', 'flow', 'head', 'speed_rpm', 'unit_price','motor_power')->get()->toArray();
                    return $data;
                break;
            // A Code: 10-03-2026 End
            
            case 'diesel-pump':
                    $data = DieselPump::select('id', 'pump_models', 'pump_type', 'frequency', 'pump_approval', 'engine_approval', 'flow', 'head', 'speed_rpm', 'unit_price')->get()->toArray();
                    return $data;
                break;

            // Full Articles Number
            // case 'electrical-pump-articles':
            //         $data = FireFightingCarts::select('id', 'quotation_no', 'article_number', 'full_article_number', 'pump_id', 'category', 'all_prices', 'field_val')->where('category', 'electrical')->get()->toArray();
            //         return $data;
            //     break;

            // A Code: 06-05-2026 Start    
            case 'electrical-pump-articles':

                $records = FireFightingCarts::select(
                    'id',
                    'quotation_no',
                    'article_number',
                    'full_article_number',
                    'pump_id',
                    'category',
                    'all_prices',
                    'field_val',
                    'power',
                    'frequency',
                    'voltage'
                )
                ->where('category', 'electrical')
                ->get();

                foreach ($records as $record) {
                    $fieldVal = $record->field_val ?? [];

                    $hasElectricalSpeed = false;
                    $hasMotorPower = false;

                    foreach ($fieldVal as &$item) {
                        
                        // Updated logic for Soft Starter (both cases)
                        if (isset($item['electrical_control_panel_type'])) {
                            $type = $item['electrical_control_panel_type'];

                            if ($type === "Soft Starter") {
                                $item['electrical_control_panel_type'] = "SoftStarter";
                            } 
                            elseif ($type === "Soft Starter + ATS") {
                                $item['electrical_control_panel_type'] = "SoftStarter + ATS";
                            }
                        }

                        // Check for existing keys
                        if (isset($item['electrical_speed'])) {
                            $hasElectricalSpeed = true;
                        }
                        if (isset($item['electrical_motor_power'])) {
                            $hasMotorPower = true;
                        }
                    }

                    // Add electrical_motor_power if conditions are met
                    if (!$hasMotorPower && $hasElectricalSpeed && !empty($record->power)) {
                        $fieldVal[] = [
                            'electrical_motor_power' => $record->power
                        ];
                    }

                    // Assign modified data back
                    $record->field_val = $fieldVal;
                }

                return $records->toArray();

            break;
            // A Code: 06-05-2026 End
                
            // case 'diesel-pump-articles':
            //         $data = FireFightingCarts::select('id', 'quotation_no', 'article_number', 'full_article_number', 'pump_id', 'category', 'all_prices', 'field_val')->where('category', 'diesel')->get()->toArray();
            //         return $data;
            //     break;

            // A Code: 21-05-2026 Start 
            case 'diesel-pump-articles':

                $records = FireFightingCarts::select(
                    'id',
                    'quotation_no',
                    'article_number',
                    'full_article_number',
                    'pump_id',
                    'category',
                    'all_prices',
                    'field_val'
                )
                ->where('category', 'diesel')
                ->get();

                foreach ($records as $record) {
                    $fieldVal = $record->field_val ?? [];

                    foreach ($fieldVal as &$item) {
                        
                        // Fix diesel_engine_approval: UL&FM → UL/FM
                        if (isset($item['diesel_engine_approval']) && $item['diesel_engine_approval'] === 'UL&FM') {
                            $item['diesel_engine_approval'] = 'UL/FM';
                        }

                        // Optional: You can also make diesel_pump_approval consistent if needed
                        if (isset($item['diesel_pump_approval']) && $item['diesel_pump_approval'] === 'UL&FM') {
                            $item['diesel_pump_approval'] = 'UL/FM';
                        }
                    }

                    // Assign modified field_val back to the record
                    $record->field_val = $fieldVal;
                }

                return $records->toArray();
            break;
            // A Code: 21-05-2026 End

            /** start 20241231 for jockey pump form auto fill***********/
            case 'jockey-pump-articles':
                    $data = FireFightingCarts::select('id', 'pump_models','pump_type','quotation_no', 'article_number', 'full_article_number', 'pump_id', 'category', 'all_prices', 'field_val','power','frequency','jockey_article_number')->where('category', 'jockey-pump')->latest('id')->first()->get()->toArray();//if multiple record then fetch latest one
                    return $data;
                break;
            /** end 20241231 for jockey pump form auto fill*************/
            
            // Adder Ids
            case 'adder-jockey-pump':
                    $data = FireFightingAdders::select('id', 'adder_list','version','code')->where('version', 'FireFighting/Jockey')->get()->toArray();
                    return $data;
                break;
                
            case 'adder-electrical':
                    $data = FireFightingAdders::select('id', 'adder_list','version','code')->where('version', 'FireFighting/Electrical')->get()->toArray();
                    return $data;
                break;
                
            case 'adder-diesel':
                    $data = FireFightingAdders::select('id', 'adder_list','version','code')->where('version', 'FireFighting/Diesel')->get()->toArray();
                    return $data;
                break;
                
            case 'adder-electrical-diesel':
                    $data = FireFightingAdders::select('id', 'adder_list','version','code')->whereIn('version', ['FireFighting/Electrical', 'FireFighting/Diesel'])->get()->toArray();
                    return $data;
                break;

            // Control panel type
            case 'electrical-control-panel-type':
                $data = ControlPanelMaster::select('id', 'model', 'enclosure', 'type', 'brand','approval', 'category', 'motor_power', 'frequency','voltage','unit_price')->where('category', 'electrical')->get()->toArray();
                return $data;
            break;

            // Control panel type
            case 'diesel-control-panel-type':
                $data = ControlPanelMaster::select('id', 'model', 'enclosure', 'type', 'brand','approval', 'category', 'motor_power', 'frequency','voltage','unit_price')->where('category', 'diesel')->get()->toArray();
                return $data;
            break;

            //start - 20250108 add motor power field in electrical flow
            case 'electrical-motor-power':
                $data = ControlPanelMaster::select('id', 'model', 'enclosure', 'type', 'brand','approval', 'category', 'motor_power', 'frequency','voltage','unit_price')->where('category', 'electrical')->get()->toArray();
                return $data;
            break;
            //end - 20250108 add motor power field in electrical flow

            default:
                $data = [];
                return $data;
            break;
            
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function edit($id)
    {
        if (!is_null($id) && !empty($id)) {
            $cart = FireFightingCarts::find($id);
            $data = [];
            if (!is_null($cart)) {
                $control_panel_type = null;
                if (!is_null($cart->field_val)) {
                    $type = $cart->field_val;
                    $control_panel_type_values = array_column($type, 'electrical_control_panel_type');
                    if (!empty($control_panel_type_values)) {
                        // If not empty, get the first value in the array
                        $control_panel_type = $control_panel_type_values[0];
                    }
                }
                $html = '<table class="table table-bordered"><thead><tr><th>Title</th><th>User Information</th></tr></thead><tbody>';
                switch ($cart->category) {
                    case 'diesel':
                    case 'electrical':
                            $html .= '<tr><td>Pump Type</td><td>'.$cart->pump_type.'</td></tr>';
                            // A Code: 11-03-2026 Start
                            if ($cart->category != 'diesel') {
                                $html .= '<tr><td>Voltage</td><td>'.$cart->voltage.'</td></tr>';
                            }
                            // A Code: 11-03-2026 End
                            $html .= '<tr><td>Frequency</td><td>'.$cart->frequency.'</td></tr>';
                            $html .= '<tr><td>Pump Approval</td><td>'.$cart->pump_approval.'</td></tr>';
                            if ($cart->category == 'diesel') {
                                $html .= '<tr><td>Engine Approval</td><td>'.$cart->engine_approval.'</td></tr>';
                            }
                            $html .= '<tr><td>Flow</td><td>'.$cart->flow.'</td></tr>';
                            $html .= '<tr><td>Head</td><td>'.$cart->head.'</td></tr>';
                            $html .= '<tr><td>Speed</td><td>'.$cart->speed_rpm.'</td></tr>';
                            $html .= '<tr><td>Motor Power</td><td>'.$cart->power.'</td></tr>';//20250108 add motor power field in electrical flow
                            $html .= '<tr><td>Control Panel Type</td><td>'.$control_panel_type.'</td></tr>';
                        break;
                    //20250109 start - jockey not getting modal
                    case 'jockey-pump':
                            $html .= '<tr><td>Pump Artical Number</td><td>'.$cart->jockey_article_number.'</td></tr>';
                            $html .= '<tr><td>Frequency</td><td>'.$cart->frequency.'</td></tr>';
                            $html .= '<tr><td>Pump Power</td><td>'.$cart->power.'</td></tr>';
                            $html .= '<tr><td>Voltage</td><td>'.$cart->voltage.'</td></tr>';
                        break;
                    //20250109 start - jockey not getting modal
                }
                $html .= '</tbody></table>';
                if ((!is_null($cart->adder_ids) && is_array($cart->adder_ids)) && (!is_null($cart->all_prices) && array_key_exists('adderpricelist', $cart->all_prices))) {
                    if (count($cart->all_prices['adderpricelist']) == count($cart->adder_ids)) {
                        $html .= '<table class="table table-bordered"><thead><tr><th>Optional</th></tr></thead><tbody>';
                        foreach ($cart->all_prices['adderpricelist'] as $key => $value) {
                            $html .= '<tr><td>'.$value['code'].' - '.$value['list'].' </td></tr>';
                        }
                        $html .= '</tbody></table>';
                    }
                }
                $data['html'] = $html;
            }
            return response()->json(array('success' => true, 'data' => $data));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'pump_type' => 'required'
        ]);

        switch ($request->pump_type) {
            case 'jockey-pump':
                    return $this->jockeyPumpAddToCard($request);
                break;
            
            case 'electrical':
                    return $this->electricalPumpAddToCard($request);
                break;
            
            case 'diesel':
                    return $this->dieselPumpAddToCard($request);
                break;

            case 'electrical-diesel':
                    return $this->electricalDieselPumpAddToCard($request);
                break;

            default:
                    return [
                        'success' => false,
                        'msg' => 'Pump type not found',
                        'price' => ''
                    ];
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function destroy($id)
    {
        //
    }

    public function cartItems($id, Request $request, $type = null, $returnDataOnly = false)
    {
        $cart = FireFightingCarts::find($id);
        if (!is_null($cart)) {
            if ($type == 'excel' && ($cart->category == 'electrical' || $cart->category == 'diesel')) {
                $control_panel_model_value = null;
                $cp_master = null;

                if ($cart->category == 'electrical') {
                    $file = public_path('assets/fire-fighting/Electrical (Technical Data Sheet).xlsx');

                    $data = ElectricalPump::where('pump_type', $cart->pump_type)
                        ->where('voltage', $cart->voltage) // A Code: 11-03-2026
                        ->where('frequency', $cart->frequency)                        
                        ->where('pump_approval', $cart->pump_approval)
                        ->where('flow', $cart->flow)
                        ->where('head', $cart->head)
                        ->where('speed_rpm', $cart->speed_rpm)
                        ->first();
                        
                    $pump_control_panel_type = "";
                    foreach ($cart->field_val as $cart_field_val) {
                      foreach($cart_field_val as $cart_feild_k =>$cart_feild_v)
                      {
                        if($cart_feild_k=='electrical_control_panel_type')
                        {
                            $pump_control_panel_type=$cart_feild_v;
                        }
                      }
                    }
                    $filename = 'Electrical (Technical Data Sheet)';

                    if (!is_null($data)) {
                        if(!empty( $pump_control_panel_type))
                        {
                            $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                ->where('motor_power', $data->motor_power)
                                ->where('voltage', $data->voltage) // A Code: 16-03-2026
                                ->where('frequency', $data->frequency)
                                ->where('type',$pump_control_panel_type)
                                ->first();
                        }
                        else
                        {
                            $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                ->where('motor_power', $data->motor_power)
                                ->where('voltage', $data->voltage) // A Code: 16-03-2026
                                ->where('frequency', $data->frequency)
                                ->first();
                        }


                        if (!is_null($cart->field_val)) {
                            $key = "electrical_control_panel_type";
                            $control_panel_type_value = array_reduce($cart->field_val, function ($carry, $item) use ($key) {
                                if (isset($item[$key])) {
                                    return $item[$key];
                                }
                                return $carry;
                            });
                            $control_panel_type_value = str_replace(" ", "", $control_panel_type_value); // A Code: 06-05-2026
                            if (!is_null($control_panel_type_value)) {

                                $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                    ->where('motor_power', $data->motor_power)
                                    ->where('voltage', $data->voltage) // A Code: 16-03-2026
                                    ->where('frequency', $data->frequency)
                                    ->where('type', $control_panel_type_value)
                                    ->first();
                            }
                        }
                        // if (!is_null($cp_master)) {
                        //     $control_panel_model_value = $cp_master->model;
                        // }
						// $voltage = $cp_master->voltage;
                        // $approval = $cp_master->approval;

                        // A Code: 05-05-2026 Start
                        if (!is_null($cp_master)) {
                            $control_panel_model_value = $cp_master->model;
                            $voltage = $cp_master->voltage;
                            $approval = $cp_master->approval;
                        } else {
                            // Handle the case when $cp_master is null
                            $control_panel_model_value = null; // or default value
                            $voltage = null;
                            $approval = null;
                        }
                        // A Code: 05-05-2026 End

						// if (isset($cart->adder_ids)) {
                           
                        //     $cp_master2 = FireFightingAdders::select('id', 'adder_list','version','code', 'type')
                        //         ->where('version', 'FireFighting/Electrical')
                        //         ->whereIn('id', $cart->adder_ids)
                        //         ->orderBy('id', 'DESC')
                        //         ->where('id','<=','8')
                        //         ->first();

                        //     $cp_master1 = ControlPanelMaster::where('category', 'Electrical')
                        //         ->where('motor_power', $data->motor_power)
                        //         ->where('voltage', $data->voltage) // A Code: 16-03-2026
                        //         ->where('frequency', $data->frequency)
                        //         ->where('type', $control_panel_type_value)
                        //         ->first();

                        //     $voltage = $cp_master1->voltage;
                        //     $approval = $cp_master1->approval;

                        //     if(!empty($cp_master2)){
                        //         $cp_master->enclosure = $cp_master2->type;
                        //     }
                            
                        //     $cp_master2 = FireFightingAdders::select('id', 'adder_list','version','code', 'type')
                        //                     ->where('version', 'FireFighting/Electrical')
                        //                     ->whereIn('id', $cart->adder_ids)
                        //                     ->orderBy('id', 'DESC')
                        //                     ->where('id','9')
                        //                     ->first();
                        //     if(!empty($cp_master2->adder_list))
                        //     {
                        //         $data->motor_type = $cp_master2->adder_list;
                        //     }
                        // }

                        // A Code: 06-05-2026 Start
                        if (isset($cart->adder_ids)) {

                            $cp_master = new \stdClass();

                            $cp_master2 = FireFightingAdders::select('id', 'adder_list','version','code', 'type')
                                ->where('version', 'FireFighting/Electrical')
                                ->whereIn('id', $cart->adder_ids)
                                ->where('id','<=','8')
                                ->orderBy('id', 'DESC')
                                ->first();

                            $control_panel_type_value = str_replace(" ", "", $control_panel_type_value);

                            $cp_master1 = ControlPanelMaster::where('category', 'Electrical')
                                ->where('motor_power', $data->motor_power)
                                ->where('voltage', $data->voltage)
                                ->where('frequency', $data->frequency)
                                ->where('type', $control_panel_type_value)
                                ->first();

                            $voltage  = $cp_master1->voltage ?? null;
                            $approval = $cp_master1->approval ?? null;

                            if (!empty($cp_master2)) {
                                $cp_master->enclosure = $cp_master2->type ?? null;
                            } else {
                                $cp_master->enclosure = null;
                            }

                            $cp_master2 = FireFightingAdders::select('id', 'adder_list','version','code', 'type')
                                ->where('version', 'FireFighting/Electrical')
                                ->whereIn('id', $cart->adder_ids)
                                ->where('id','9')
                                ->first();

                            if (!empty($cp_master2) && !empty($cp_master2->adder_list)) {
                                $data->motor_type = $cp_master2->adder_list;
                            }
                        }
                        // A Code: 06-05-2026 End


					}
                } else {
                    $file = public_path('assets/fire-fighting/Diesel (Technical Data Sheet).xlsx');
                    $data = DieselPump::where('pump_type', $cart->pump_type)
                                //->where('voltage', $cart->voltage) // A Code: 11-03-2026 Comment
                                ->where('frequency', $cart->frequency)
                                ->where('pump_approval', $cart->pump_approval)
                                ->where('engine_approval', $cart->engine_approval)
                                ->where('flow', $cart->flow)
                                ->where('head', $cart->head)
                                ->where('speed_rpm', $cart->speed_rpm)
                                ->first();
                    $filename = 'Diesel (Technical Data Sheet)';
                  
				    if (!is_null($data)) {
                        if (isset($cart->adder_ids)) {
                            $cp_master = FireFightingAdders::select('id', 'adder_list','version','code', 'type')
                                ->where('version', 'FireFighting/Diesel')
                                ->whereIn('id', $cart->adder_ids)
                                ->first();

                            $cp_master1 = ControlPanelMaster::where('category', 'Diesel')
                                            // ->where('motor_power', $data->motor_power)
                                            ->where('frequency', $data->frequency)                                        
                                            ->first();

                            $cp_master->model =  $data->control_panel_model;
							$voltage = $cp_master1->voltage;
                            $approval = $cp_master1->approval;
                            if($cp_master){
                                $cp_master->enclosure = $cp_master->type;
                            }
                        }
						else{
                            $cp_master = ControlPanelMaster::where('category', 'Diesel')
                                // ->where('motor_power', $data->motor_power)
                                // ->where('motor_power', $data->engine_power)
                                ->where('frequency', $data->frequency)
                                ->first();
                            $voltage = $cp_master->voltage;
                            $approval = $cp_master->approval;
						}
						
                        if (!is_null($cp_master)) {
                            $control_panel_model_value = $cp_master->model;
                        }
                    }
                }
                
                $spreadsheet = IOFactory::load($file);

                $cellData = [
                    'B2' => $cart->pump_models . ' - ' . $cart->pump_type,
                    //'C5' => $cart->article_number ?? '',
					'C5' => $cart->full_article_number ?? '',
                    'C6' => $cart->flow,
                    'C7' => $cart->head,
                    'E6' => $cart->pump_approval,
                    'E7' => $cart->frequency,
                    'C8' => $cart->speed_rpm,
                    
                    'C11' => $data->moc_casing ?? '-',
                    'E11' => $data->moc_shaft ?? '-',
                    'C12' => $data->moc_impeller ?? '-',
                    'E12' => $data->flange_size ?? '-',
                    'C13' => $data->flange_class ?? '-',

                    'C16' => $data->motor_power ?? '-',
                    'E16' => $data->voltage ?? '-',
                    //'C17' => !is_null($data->no_of_phase) ? $data->no_of_phase . ' phase' : '',
                    // A Code: 30-04-2026 Start
                    'C17' => (!empty($data) && !is_null($data->no_of_phase)) ? $data->no_of_phase . ' phase' : '',
                    // A Code: 30-04-2026 End
                    'E17' => $data->motor_type ?? '-',
                    'C18' => $data->frequency ?? '-',
                    'E18' => $data->motor_approval ?? '-',
                    'C19' => $data->engine_model ?? '-',
                    'E19' => $data->motor_brand ?? '-',
                    'C20' => $data->motor_make ?? '-',
                    'E20' => $data->motor_origin ?? '-',


                    'C23' => $cp_master->enclosure ?? '-',
                    //'E23' => $control_panel_model_value ?? '-',
					'E23' => $cp_master->model ?? '-',
                    'C24' => $voltage ?? '-',
                    'E24' => $approval ?? '-',
                ];
                if ($cart->category == 'diesel') {
                    $cellData['C37'] = $data->diesel_tank_us ?? '-';
                    $cellData['C40'] = $data->battery_rating.'Ah' ?? '-';
                    $cellData['C41'] = $data->battery_qty ?? '-';
                    $cellData['C42'] = $data->flow_meter_size ?? '-';
                    $cellData['C43'] = $data->pressure_releif_valve ?? '-';
                    $cellData['C44'] = $data->waste_cone_brand ?? '-';
                    $cellData['C45'] = $data->terminal_box ?? '-';
					$cellData['C16'] = $data->engine_power ?? '-';
					$cellData['E17'] = $data->engine_type ?? '-';
					$cellData['E18'] = $data->engine_approval ?? '-';
					$cellData['E19'] = $data->engine_brand ?? '-';
					$cellData['C20'] = $data->engine_make ?? '-';
					$cellData['E20'] = $data->engine_origin ?? '-';
                    // $cellData['B19'] = $data->engine_model ?? '-';
                    // $cellData['B42'] = $data->terminal_box ?? '-';
                    // $cellData['B16'] = $data->engine_power ?? '-';
                }

                foreach ($cellData as $cell => $value) {
                    $spreadsheet->getActiveSheet()->setCellValue($cell, $value);
                }

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save(public_path($filename.'.xlsx'));

                return response()->download(public_path($filename.'.xlsx'));
            }

            $items = [];
            $prices = $cart->all_prices;

            if (isset($request->test)) {
                dd($cart, $prices);
            }
            $cp_master = null;
            $diesel_tank_us = null;
            $battery_qty = null;
            $pressure_releif_valve = null;
            $flow_meter_size = null;
            $waste_cone_brand = null;
            $item_article_number = "";
            $item_article_no = null;

            $diesel_tank_item_article_number = null;
            $battery_item_article_number = null;

            $diesel_flow_meter_size = null ;
            $diesel_pressure_releif_valve = null ;
            $diesel_waste_cone_brand = null ;

            $diesel_flow_meter_size_article_number  = null ;
            $diesel_pressure_releif_valve_article_number  = null ;
            $diesel_waste_cone_brand_article_number  = null ;

            $exptra_values = [];
            switch ($cart->category) {
                    case 'jockey-pump':
                        $data = JockeyPump::where('pump_article_no', $cart->jockey_article_number)->first();
                        $item_article_no = $data->pump_article_no;
                        if (!is_null($data)) {
                            //here change of power
                            //$cp_master = ControlPanelMaster::where('category', 'Jockey')->where('motor_power', $data->power)->where('frequency', $data->frequency)->first();
                            //
                                $power = $data->power * 1.341;
                                $cp_master = ControlPanelMaster::where('category', 'Jockey')
                                                ->orderByRaw('ABS(motor_power - ?)', [$power])
                                                ->where('frequency', $data->frequency)
                                                ->first();
                                    
                            //
                            // $item_article_number = $cp_master->item_article_number;
                            // if (!is_null($cp_master)) {
                            //     $cp_master = 'Control Panel/'.$cp_master->model;
                            // }

                            // A Code: 24-04-2026 Start
                            if (!is_null($cp_master)) {
                                $item_article_number = $cp_master->item_article_number;
                                $cp_master = 'Control Panel/' . $cp_master->model;
                            } else {
                                $item_article_number = null;
                            }
                            // A Code: 24-04-2026 End

                        }
                    break;

                    case 'diesel':
                        $data = DieselPump::where('pump_type', $cart->pump_type)
                                    //->where('voltage', $cart->voltage) // A Code: 11-03-2026 Comment
                                    ->where('frequency', $cart->frequency)
                                    ->where('pump_approval', $cart->pump_approval)
                                    ->where(function ($query) use ($cart) {
                                        $normalized = str_replace(['/', '&'], '%', $cart->engine_approval); // Replace / or & with wildcard
                                        $query->where('engine_approval', 'LIKE', '%' . $normalized . '%')
                                            ->orWhere('engine_approval', 'LIKE', '%' . str_replace('/', '&', $cart->engine_approval) . '%')
                                            ->orWhere('engine_approval', 'LIKE', '%' . str_replace('&', '/', $cart->engine_approval) . '%');
                                    })
                                    ->where('flow', $cart->flow)
                                    ->where('head', $cart->head)
                                    ->where('speed_rpm', $cart->speed_rpm)
                                    ->first();

                        if (!is_null($data)) 
                        {
                            $cp_master = 'Control Panel/'.$data->control_panel_model;
                            $diesel_tank_us = $data->diesel_tank_us;
                            $diesel_tank_item_article_number = DieselTankMaster::where('tank_size',$diesel_tank_us)->value('item_article_number');
                            $battery_qty = '/'.$data->battery_qty.' ('.$data->battery_rating.'Ah)';
                            $battery_item_article_number = BatteryMaster::where('model',$data->battery_rating)->value('item_article_number');
                            
                            $pressure_releif_valve = '/'.$data->pressure_releif_valve;

                            $flow_meter_size = '/'.$data->flow_meter_size;
                            
                            $waste_cone_brand = '/'.$data->waste_cone_brand;
                            
                            $diesel_pressure_releif_valve = $data->pressure_releif_valve;
                            
                            $diesel_flow_meter_size = $data->flow_meter_size;
                            $flow = $data->flow;
                            
                            $diesel_waste_cone_brand = $data->waste_cone_brand;
                            
                            // need to add
                            //diesel_pressure_releif_valve_article_number

                            $diesel_pressure_releif_valve_article_number = DB::table('firefighting_pressure_relief_valve')
                                                                            ->where('size',$diesel_pressure_releif_valve)
                                                                            ->value('item_article_number');
                            
                            $diesel_flow_meter_size_article_number = DB::table('firefighting_flow_meter')
                                                                            ->where('size',$diesel_flow_meter_size)
                                                                            ->whereRaw('? BETWEEN min_gpm AND max_gpm', [$flow])
                                                                            ->value('item_article_number'); 
                                                                          
                            $diesel_waste_cone_brand_article_number = DB::table('firefighting_waste_cone')
                                                                            ->where('size',$diesel_waste_cone_brand)
                                                                            ->value('item_article_number');                                              
                                                                          
                            //
                            //control_panel_price aricle number
                            $control_panel_aricle_number = ControlPanelMaster::where('category', 'Diesel')
                                                                            ->where('frequency', $data->frequency)
                                                                            ->where('model', $data->control_panel_model)
                                                                            ->value('item_article_number');
                            if($control_panel_aricle_number)
                            { 
                                $item_article_number = $control_panel_aricle_number;
                            }                             

                            $exptra_values[] = [
                                'description' => 'Engine Model - '.$data->engine_model.' / Engine Power - '.$data->engine_power.'(HP)',
                                'article_number' => '',
                                'addder_code' => '',
                                'unit_price' => '',
                                'qty' => '',
                                'total_price' => '',
                            ];
                        }
                    break;

                    case 'electrical':
                        $data = ElectricalPump::where('pump_type', $cart->pump_type)
                                    ->where('voltage', $cart->voltage) // A Code: 11-03-2026
                                    ->where('frequency', $cart->frequency)
                                    ->where('pump_approval', $cart->pump_approval)
                                    ->where('flow', $cart->flow)
                                    ->where('head', $cart->head)
                                    ->where('speed_rpm', $cart->speed_rpm)
                                    ->first();
                        $pump_control_panel_type = "";
                       
                        foreach ($cart->field_val as $cart_field_val) {
                          foreach($cart_field_val as $cart_feild_k =>$cart_feild_v)
                          {
                            if($cart_feild_k=='electrical_control_panel_type')
                            {
                                $pump_control_panel_type=$cart_feild_v;
                            }
                          }
                        }

                        if (!is_null($data)) {
                            if(!empty( $pump_control_panel_type))
                            {
                                $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                    ->where('motor_power', $data->motor_power)
                                    ->where('voltage', $data->voltage) // A Code: 17-03-2026
                                    ->where('frequency', $data->frequency)
                                    ->where('type',$pump_control_panel_type)
                                    ->first();
                            }
                            else
                            {
                                $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                    ->where('motor_power', $data->motor_power)
                                    ->where('voltage', $data->voltage) // A Code: 17-03-2026
                                    ->where('frequency', $data->frequency)
                                    ->first();
                            }

                            if (!is_null($cart->field_val)) {
                                $key = "electrical_control_panel_type";
                                $control_panel_type_value = array_reduce($cart->field_val, function ($carry, $item) use ($key) {
                                    if (isset($item[$key])) {
                                        return $item[$key];
                                    }
                                    return $carry;
                                });
                                $control_panel_type_value = str_replace(" ", "", $control_panel_type_value); // A Code: 06-05-2026
                                if (!is_null($control_panel_type_value)) {                                    
                                    $cp_master = ControlPanelMaster::where('category', 'Electrical')
                                        ->where('motor_power', $data->motor_power)
                                        ->where('voltage', $data->voltage) // A Code: 17-03-2026
                                        ->where('frequency', $data->frequency)
                                        ->where('type', $control_panel_type_value)
                                        ->first();
                                    //$item_article_number = $cp_master->item_article_number;
                                    $item_article_number = $cp_master->item_article_number ?? null; // A Code: 05-05-2026
                                }
                            }
                            if (!is_null($cp_master)) {
                                //$cp_master = 'Control Panel/'.$cp_master->model;
                                $cp_master = $cp_master->description; // A Code: 17-03-2026
                                $exptra_values[] = [
                                    'description' => 'Motor Power/'.$data->motor_power.'(HP)',
                                    // 'article_number' => $cp_master->item_article_number ?? '--',
                                    'article_number' => '' ,
                                    'addder_code' => '',
                                    'unit_price' => '',
                                    'qty' => '',
                                    'total_price' => '',
                                ];
                            }
                        }
                    break;
            }
            if (array_key_exists('pump_price', $prices)) {
                switch ($cart->category) {
                        case 'jockey-pump':
                            $description = ucwords(str_replace('-pump', '', $cart->category)).' Pump/'.$cart->jockey_article_number.'('.$cart->pump_models.')';
                        break;

                case 'diesel':
                        $description = ucwords(str_replace('-pump', '', $cart->category)).' Pump/'.'('.$cart->pump_models.')';

                        $item_article_no = DieselPump::where('pump_type',$cart->pump_type)
                                                        //->where('voltage', $cart->voltage) // A Code: 11-03-2026 Comment
                                                        ->where('frequency',$cart->frequency)
                                                        ->where('pump_approval',$cart->pump_approval)
                                                        ->where('engine_approval',$cart->engine_approval)
                                                        ->where('flow',$cart->flow)
                                                        ->where('speed_rpm',$cart->speed_rpm)
                                                        ->where('head',$cart->head)
                                                        ->select('id','item_article_number')
                                                        ->value('item_article_number');
                                                       
                break;

                    case 'electrical':
                            $description = ucwords(str_replace('-pump', '', $cart->category)).' Pump ('.$cart->pump_models.')';
                            $item_article_no = ElectricalPump::where('pump_type', $cart->pump_type)
                                                ->where('voltage', $cart->voltage) // A Code: 11-03-2026
                                                ->where('frequency', $cart->frequency)
                                                ->where('pump_approval', $cart->pump_approval)
                                                ->where('flow', $cart->flow)
                                                ->where('head', $cart->head)
                                                ->where('speed_rpm', $cart->speed_rpm)
                                                ->select('id','item_article_number')
                                                ->value('item_article_number');
                    break;
                    
                    default:
                        $description = ucwords(str_replace('-pump', '', $cart->category)).' Pump';
                    break;
                }

                array_push($items, [
                    'description' => $description,
                    'article_number' => $item_article_no,
                    'addder_code' => '',
                    'unit_price' => $this->showAmount($prices['pump_price']),
                    'qty' => '1',
                    'total_price' => $this->showAmount($prices['pump_price']),
                ]);
            }
            if (array_key_exists('control_panel_price', $prices)) {
                array_push($items, [
                    'description' => $cp_master ?? 'Control Panel',
                    // 'article_number' => $data->item_article_number,
                    'article_number' => $item_article_number ?? '--',
                    'addder_code' => '',
                    'unit_price' => $this->showAmount($prices['control_panel_price']),
                    'qty' => '1',
                    'total_price' => $this->showAmount($prices['control_panel_price']),
                ]);
            }
            if (array_key_exists('disel_tank_price', $prices)) {
                array_push($items, [
                    'description' => 'Disel Tank'.(!is_null($diesel_tank_us) ? '/'.$diesel_tank_us.' (US - gallons)' : ''),
                    'article_number' => $diesel_tank_item_article_number,
                    'addder_code' => '',
                    'unit_price' => $this->showAmount($prices['disel_tank_price']),
                    'qty' => '1',
                    'total_price' => $this->showAmount($prices['disel_tank_price']),
                ]);
            }
            if (array_key_exists('battery_orignal_price', $prices) && array_key_exists('battery_qty', $prices)) {
                array_push($items, [
                    'description' => 'Battery'.($battery_qty ?? ''),
                    'article_number' => $battery_item_article_number,
                    'addder_code' => '',
                    'unit_price' => $this->showAmount($prices['battery_orignal_price']),
                    'qty' => $this->showAmount($prices['battery_qty'], 0),
                    'total_price' => $this->showAmount($prices['battery_orignal_price']) * $prices['battery_qty'],
                ]);
            }
            if (array_key_exists('adderprice', $prices) && (array_key_exists('adderpricelist', $prices) && is_array($prices['adderpricelist']))) {
                foreach ($prices['adderpricelist'] as $k => $v) {
                    if ($v['list'] == 'Pressure relief valve') {
                        array_push($items, [
                            'description' => $v['list'].($pressure_releif_valve ?? ''),
                            'article_number' => $diesel_pressure_releif_valve_article_number ?? 'NA',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    } elseif ($v['list'] == 'Flow meter') {
                        array_push($items, [
                            'description' => $v['list'].($flow_meter_size ?? ''),
                            'article_number' => $diesel_flow_meter_size_article_number ?? 'NA',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    } elseif ($v['list'] == 'Waste cone') {
                        array_push($items, [
                            'description' => $v['list'].($waste_cone_brand ?? ''),
                            'article_number' => $diesel_waste_cone_brand_article_number ?? 'NA',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    }elseif ($v['list'] == 'Motor upgrade to TEFC') {
                        array_push($items, [
                            'description' => $v['list'],
                            'article_number' => $item_article_no.'/'.$v['code'] ?? '--',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    } 
                    elseif (str_starts_with($v['list'], 'Control panel upgrade to')) {
                        array_push($items, [
                            'description' => $v['list'],
                            'article_number' => $item_article_number.'/'.$v['code'] ?? '--',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    }
                    else {
                        array_push($items, [
                            'description' => $v['list'],
                            'article_number' => '111',
                            'addder_code' => $v['code'],
                            'unit_price' => $this->showAmount($v['price']),
                            'qty' => '1',
                            'total_price' => $this->showAmount($v['price']),
                        ]);
                    }
                }
            }
            if (count($exptra_values) > 0) {
                foreach ($exptra_values as $k => $v) {
                    array_push($items, $v);
                }
            }

            $data['items'] = $items;

        } else {
            return redirect('controlpanel/cart/' . auth()->id());
        }

        if($returnDataOnly){
            return [
                'items' => $data['items'],
                'cartId' => $id,
            ];
        }

        return view('frontend.fire-fighting.items', $data);
    }

    public function jockeyPumpAddToCard($request)
    {
        $ic_margin = User::ic_margin_fire_fighting();
        $overhead = current(\DB::table('setup_fields')->where('name','fire_fighting_over_head')->pluck('value')->toArray());

        foreach ($request->data as $key => $value) {
            if($value['name'] == 'jockey_pumppower'){
                $pumppower = $value['value'];
            }
            if($value['name'] == 'jockey_voltage'){
                $voltage = $value['value'];
            }
            if($value['name'] == 'jockey_frequency'){
                $frequency = $value['value'];
            }
            if($value['name'] == 'jockey_article_number'){
                $article_number = $value['value'];
            }
           
        }
        $pump_data = JockeyPump::where('pump_article_no', $article_number)->where('power', $pumppower)->where('frequency', $frequency)->first();

        if (!is_null($pump_data)) {
            $pump_price = $pump_data->unit_price;
            $power = $pumppower * 1.341;

            $adder_ids = [];
            if (isset($request->adder_ids)) {
                $adder_ids = $request->adder_ids;
            }
            
            $price = $this->jockeyPumpPriceCalculate($pump_price, $power, $frequency, $overhead, $ic_margin, $adder_ids);

            // Pump Data check in cart
            $cart = FireFightingCarts::where('category', $request->pump_type)->where('pump_id', $pump_data->id)
                                        ->where('jockey_article_number', $article_number)->where('power', $pumppower)->where('frequency', $frequency);

            if (isset($request->adder_ids) && count($request->adder_ids) > 0) {

                // Adder Ids Search
                $adder_ids_search = $request->adder_ids;
                $adder_ids_search = '[' . implode(',', array_map(function($value) {
                    return '"' . $value . '"';
                }, array_map('strval', $adder_ids_search))) . ']';
                $cart = $cart->where('adder_ids', $adder_ids_search);
            } else {
                $cart = $cart->whereNull('adder_ids');
            }
            $cart = $cart->whereNull('quotation_no')->where('user_id', auth()->id())->first();
            // ->whereNull('quotation_no')->whereNull('article_number')->whereNull('full_article_number')
            if (!is_null($cart)) {
                return [
                    'success' => false,
                    'msg' => 'This item already in your cart.',
                ];
            }

            // New Cart Data create
            $cart = new FireFightingCarts();

            // Check same data in cart without userid or article number
            $cart_check_other = FireFightingCarts::where('category', $request->pump_type)->where('pump_id', $pump_data->id)
                                                    ->where('jockey_article_number', $article_number)->where('power', $pumppower)->where('frequency', $frequency);

            if (isset($request->adder_ids) && count($request->adder_ids) > 0) {

                // Adder Ids Search
                $adder_ids_search = $request->adder_ids;
                $adder_ids_search = '[' . implode(',', array_map(function($value) {
                    return '"' . $value . '"';
                }, array_map('strval', $adder_ids_search))) . ']';
                $cart_check_other = $cart_check_other->where('adder_ids', $adder_ids_search);
            } else {
                $cart_check_other = $cart_check_other->whereNull('adder_ids');
            }
            $cart_check_other = $cart_check_other->first();
            if (!is_null($cart_check_other)) {
                $cart->article_number = $cart_check_other->article_number;
                $cart->full_article_number = $cart_check_other->full_article_number;
                // $request->code_price = $atmosCartData1->total_adders_price;     
            }

            $cart->category = $request->pump_type;
            $cart->pump_id = $pump_data->id;
            $cart->jockey_article_number = $article_number;
            $cart->pump_models = $pump_data->description;
            $cart->power = $pumppower;
            $cart->voltage = $voltage;
            $cart->frequency = $frequency;
            if (isset($request->adder_ids)) {
                $cart->adder_ids = $request->adder_ids;
            }
            $cart->adder_ids_prices = $price['adderprice'];
            $cart->total_adders_price = $price['adderprice'];
            $cart->overhead_price = $overhead;
            $cart->inter_company_margin_price = $ic_margin;
            $cart->qty = 1;
            $cart->price = $price['price'];
            $cart->total_price = $price['price'];
            $cart->all_prices = $price;
            $cart->user_id = auth()->id();
            $cart->save();

            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
                'msg' => 'Jockey Pump data not found please contact to admin.',
                'price' => ''
            ];
        }
    }

    //here need to check
    public function electricalPumpAddToCard($request)
    {        
        $ic_margin = User::ic_margin_fire_fighting();
        $overhead = current(\DB::table('setup_fields')->where('name','fire_fighting_over_head')->pluck('value')->toArray());
        $price_res = $this->electricalPumpPriceCalculate($request, $overhead, $ic_margin, true);

        if (!$price_res['success']) {
            return $price_res;
        }

        $price = $price_res['price_list'];
        $electrical = $price_res['electrical_data'];
        $field_val = $price_res['field_val'];

        $adder_ids = [];
        if (isset($request->adder_ids)) {
            $adder_electrical = $this->show('adder-electrical');
            $adder_electrical_ids = array_map(function($val) use ($request)
            {
                if (in_array(''.$val['id'], $request->adder_ids)) {
                    return ''.$val['id'];
                }
            }, $adder_electrical);
            $adder_electrical_ids = array_filter($adder_electrical_ids);
            $adder_electrical_ids = array_values($adder_electrical_ids);
            // $request->adder_ids = $adder_electrical_ids;
            $adder_ids = $adder_electrical_ids;
        }

        $request->pump_type = 'electrical';
        // $pump_models = ucwords($request->pump_type);
        $pump_models = $electrical->wilo_pump_models;
        // $pump_models .= '/'.$electrical->control_panel_model;
        $pump_models .= '/'.$electrical->pump_type;
        $pump_models .= '/'.$electrical->frequency;
        $pump_models .= '/'.$electrical->pump_approval;

        // Pump Data check in cart
        //start 20250107 taking filter for Control panel type
        $electrical_control_panel_type = "";
        foreach($request->data as $request_data_key=>$request_data_value)
        {
            if($request_data_value['name']=="electrical_control_panel_type")
            {
                $electrical_control_panel_type = $request_data_value['value'];
            }
        }
        //end 20250107 taking filter for Control panel type
        $cart = FireFightingCarts::where('category', $request->pump_type)
                                    ->where('full_article_number',$request->electrical_article_number)
                                    //->where('pump_id', $electrical->id) // A Code: 06-05-2026 Comment
                                    ->where('pump_models', $pump_models) // A Code: 06-05-2026 Comment
                                    ->where('power', $electrical->motor_power)
                                    ->where('voltage', $electrical->voltage) // A Code: 12-03-2026
                                    ->where('frequency', $electrical->frequency)
                                    ->where('pump_approval', $electrical->pump_approval)
                                    ->where('flow', $electrical->flow)
                                    ->where('head', $electrical->head)
                                    ->where('speed_rpm', $electrical->speed_rpm)
                                    ->where('wilo_article_number', $electrical->wilo_article_number)
                                    ->where('field_val', 'like','%{"electrical_control_panel_type":"'.$electrical_control_panel_type.'"}%');//start 20250107 taking filter for Control panel type

        if (count($adder_ids) > 0) {

            // Adder Ids Search
            $adder_ids_search = $adder_ids;

            $adder_ids_search = '[' . implode(',', array_map(function($value) {
                return '"' . $value . '"';
            }, array_map('strval', $adder_ids_search))) . ']';

            $cart = $cart->where('adder_ids', $adder_ids_search);
        } else {
            $cart = $cart->whereNull('adder_ids');
        }
        $cart = $cart->whereNull('quotation_no')->where('user_id', auth()->id())->first();
        
        if (!is_null($cart)) {
            return [
                'success' => false,
                'msg' => 'This item already in your cart.',
            ];
        }
        // New Pump Data Save
        $cart = new FireFightingCarts();
        $control_panel_type_values = '';
        if (isset($request->data)) {
            $index = array_search('electrical_control_panel_type', array_column($request->data, 'name'));
            $control_panel_type_values = $request->data[$index]['value'];
        }
       
        $cart_check_other = FireFightingCarts::where('category', $request->pump_type)
                                                ->where('full_article_number',$request->electrical_article_number)
                                                //->where('pump_id', $electrical->id) // A Code: 06-05-2026 Comment
                                                //->where('pump_models', $pump_models) // A Code: 06-05-2026 Comment
                                                ->where('power', $electrical->motor_power)
                                                ->where('voltage', $electrical->voltage) // A Code: 12-03-2026
                                                ->where('frequency', $electrical->frequency)
                                                ->where('pump_approval', $electrical->pump_approval)
                                                ->where('flow', $electrical->flow)
                                                ->where('head', $electrical->head)
                                                ->where('speed_rpm', $electrical->speed_rpm)
                                                ->where('wilo_article_number', $electrical->wilo_article_number);

        //start 20250107 taking filter for Control panel type
        if ($electrical_control_panel_type != '') {
            
            //$cart_check_other = $cart_check_other->where('field_val', 'like', '%"electrical_control_panel_type":"'.$electrical_control_panel_type.'"%');
            
            // A Code: 06-05-2026 Start (Handle spaced and non-spaced versions of control panel type)
            $cart_check_other = $cart_check_other->where(function($q) use ($electrical_control_panel_type) {
                // Always search original value
                $q->where('field_val', 'like', '%"electrical_control_panel_type":"'.$electrical_control_panel_type.'"%');
                $q->orwhere('field_val', 'like', '%"electrical_control_panel_type": "'.$electrical_control_panel_type.'"%');
                // Generate spaced version
                $altValue = str_replace('SoftStarter', 'Soft Starter', $electrical_control_panel_type);
                // Add second condition only if different
                if ($altValue !== $electrical_control_panel_type) {
                    $q->orWhere('field_val', 'like', '%"electrical_control_panel_type":"'.$altValue.'"%');
                }
            });
            // A Code: 06-05-2026 End

        }
        //end 20250107 taking filter for Control panel type
        if (count($adder_ids) > 0) {

            // Adder Ids Search
            $adder_ids_search = $adder_ids;
            $adder_ids_search = '[' . implode(',', array_map(function($value) {
                return '"' . $value . '"';
            }, array_map('strval', $adder_ids_search))) . ']';
            $cart_check_other = $cart_check_other->where('adder_ids', $adder_ids_search);
        } else {
            $cart_check_other = $cart_check_other->whereNull('adder_ids');
        }
        $cart_check_other = $cart_check_other->first();

        //dd($electrical->id,$electrical_control_panel_type,$cart_check_other->article_number ?? null,$cart_check_other->full_article_number ?? null);

        if (!is_null($cart_check_other)) {
            $cart->article_number = $cart_check_other->article_number;
            $cart->full_article_number = $cart_check_other->full_article_number;
        }

        $cart->category = $request->pump_type;
        $cart->pump_id = $electrical->id;
        $cart->pump_models = $pump_models;
        $cart->pump_type = $electrical->pump_type;
        $cart->power = $electrical->motor_power;
        $cart->voltage = $electrical->voltage; // A Code: 10-03-2026
        $cart->frequency = $electrical->frequency;
        $cart->pump_approval = $electrical->pump_approval;
        $cart->flow = $electrical->flow;
        $cart->head = $electrical->head;
        $cart->speed_rpm = $electrical->speed_rpm;
        $cart->wilo_article_number = $electrical->wilo_article_number;

        if (count($adder_ids) > 0) {
            $cart->adder_ids = $adder_ids;
        }

        $cart->adder_ids_prices = $price['adderprice'];
        $cart->total_adders_price = $price['adderprice'];
        $cart->overhead_price = $overhead;
        $cart->inter_company_margin_price = $ic_margin;
        $cart->qty = 1;
        $cart->price = $price['total_price'];
        $cart->total_price = $price['total_price'];
        $cart->all_prices = $price;
        $cart->field_val = $field_val;
        $cart->user_id = auth()->id();
        $cart->save();

        // dd($request->all(), $pump_data, $price);
        return [
            'success' => true,
        ];
    }

    public function dieselPumpAddToCard($request)
    {
        $ic_margin = User::ic_margin_fire_fighting();
        $overhead = current(\DB::table('setup_fields')->where('name','fire_fighting_over_head')->pluck('value')->toArray());
        $price_res = $this->dieselPumpPriceCalculate($request, $overhead, $ic_margin, true);

        if (!$price_res['success']) {
            return $price_res;
        }

        $price = $price_res['price_list'];
        $diesel = $price_res['diesel_data'];
        $field_val = $price_res['field_val'];

        $adder_ids = [];
        if (isset($request->adder_ids)) {
            $adder_diesel = $this->show('adder-diesel');
            $adder_diesel_ids = array_map(function($val) use ($request)
            {
                if (in_array(''.$val['id'], $request->adder_ids)) {
                    return ''.$val['id'];
                }
            }, $adder_diesel);
            $adder_diesel_ids = array_filter($adder_diesel_ids);
            $adder_diesel_ids = array_values($adder_diesel_ids);
            // $request->adder_ids = $adder_diesel_ids;
            $adder_ids = $adder_diesel_ids;
        }
        $request->pump_type = 'diesel';
        
        $pump_models = $diesel->pump_models;
        $pump_models .= '/'.$diesel->pump_type;
        $pump_models .= '/'.$diesel->frequency;
        $pump_models .= '/'.$diesel->pump_approval;

        // Pump Data check in cart
        $cart = FireFightingCarts::where('category', $request->pump_type)
                                    ->where('pump_id', $diesel->id)
                                    ->where('pump_models', $pump_models)
                                    ->where('power', $diesel->engine_power)
                                    ->where('frequency', $diesel->frequency)
                                    ->where('pump_approval', $diesel->pump_approval)
                                    ->where('engine_approval', $diesel->engine_approval)
                                    //->where('engine_approval', $diesel->engine_approval) // A Code: 22-05-2026 Comment
                                    ->where(function($q) use ($diesel) {
                                        $q->where('engine_approval', $diesel->engine_approval)
                                        ->orWhere('engine_approval', str_replace('/', '&', $diesel->engine_approval));
                                    }) // A Code: 22-05-2026
                                    ->where('flow', $diesel->flow)
                                    ->where('head', $diesel->head)
                                    ->where('speed_rpm', $diesel->speed_rpm)
                                    ->where('wilo_article_number', $diesel->wilo_article_number);

        if (count($adder_ids) > 0) {

            // Adder Ids Search
            $adder_ids_search = $adder_ids;
            $adder_ids_search = '[' . implode(',', array_map(function($value) {
                return '"' . $value . '"';
            }, array_map('strval', $adder_ids_search))) . ']';
            $cart = $cart->where('adder_ids', $adder_ids_search);
        } else {
            $cart = $cart->whereNull('adder_ids');
        }
        $cart = $cart->whereNull('quotation_no')->where('user_id', auth()->id())->first();
        if (!is_null($cart)) {
            return [
                'success' => false,
                'msg' => 'This item already in your cart.',
            ];
        }

        // new Data Save
        $cart = new FireFightingCarts();

        // Check if other data exist
        $cart_check_other = FireFightingCarts::where('category', $request->pump_type)
                                                ->where('pump_id', $diesel->id)
                                                ->where('pump_models', $pump_models)
                                                ->where('power', $diesel->engine_power)
                                                ->where('frequency', $diesel->frequency)
                                                ->where('pump_approval', $diesel->pump_approval)
                                                //->where('engine_approval', $diesel->engine_approval) // A Code: 22-05-2026 Comment
                                                ->where(function($q) use ($diesel) {
                                                    $q->where('engine_approval', $diesel->engine_approval)
                                                    ->orWhere('engine_approval', str_replace('/', '&', $diesel->engine_approval));
                                                }) // A Code: 22-05-2026
                                                ->where('flow', $diesel->flow)
                                                ->where('head', $diesel->head)
                                                ->where('speed_rpm', $diesel->speed_rpm)
                                                ->where('wilo_article_number', $diesel->wilo_article_number);

        if (count($adder_ids) > 0) {

            // Adder Ids Search
            $adder_ids_search = $adder_ids;
            $adder_ids_search = '[' . implode(',', array_map(function($value) {
                return '"' . $value . '"';
            }, array_map('strval', $adder_ids_search))) . ']';
            $cart_check_other = $cart_check_other->where('adder_ids', $adder_ids_search);
        } else {
            $cart_check_other = $cart_check_other->whereNull('adder_ids');
        }
        $cart_check_other = $cart_check_other->first();

        //dd($diesel->id,$cart_check_other->article_number ?? null,$cart_check_other->full_article_number ?? null);

        if (!is_null($cart_check_other)) {
            $cart->article_number = $cart_check_other->article_number;
            $cart->full_article_number = $cart_check_other->full_article_number;
        }


        $cart->category = $request->pump_type;
        $cart->pump_id = $diesel->id;
        $cart->pump_models = $pump_models;
        $cart->pump_type = $diesel->pump_type;

        $cart->power = $diesel->engine_power;
        $cart->frequency = $diesel->frequency;
        $cart->pump_approval = $diesel->pump_approval;
        $cart->engine_approval = $diesel->engine_approval;
        $cart->flow = $diesel->flow;
        $cart->head = $diesel->head;
        $cart->speed_rpm = $diesel->speed_rpm;
        $cart->wilo_article_number = $diesel->wilo_article_number;

        if (count($adder_ids) > 0) {
            $cart->adder_ids = $adder_ids;
        }

        $cart->adder_ids_prices = $price['adderprice'];
        $cart->total_adders_price = $price['adderprice'];
        $cart->overhead_price = $overhead;
        $cart->inter_company_margin_price = $ic_margin;
        $cart->qty = 1;
        $cart->price = $price['total_price'];
        $cart->total_price = $price['total_price'];
        $cart->all_prices = $price;
        $cart->field_val = $field_val;
        $cart->user_id = auth()->id();
        $cart->save();

        // dd($request->all(), $pump_data, $price);
        return [
            'success' => true,
        ];
    }

    public function electricalDieselPumpAddToCard($request)
    {
        $electrical = $this->electricalPumpAddToCard($request);
        if (!$electrical['success']) {
            return $electrical;
        }

        $request->data = $request->extra_data;
        $diesel = $this->dieselPumpAddToCard($request);
        if (!$diesel['success']) {
            return $diesel;
        }
        return [
            'success' => true
        ];
    }

    public function priceCalculate($request)
    {
        $ic_margin = User::ic_margin_fire_fighting();
        $overhead = current(\DB::table('setup_fields')->where('name','fire_fighting_over_head')->pluck('value')->toArray());
        $price = '';
        switch ($request->pump_type) {
            case 'jockey-pump':
                    $frequency = $request->data['frequency'];

                    // Pump Price
                    $pump_price = $request->data['unit_price'];

                    // Control Panel Price
                    //here 1
                    $power = $request->data['power'] * 1.341;
                    
                    $adder_ids = [];
                    if (isset($request->adder_ids)) {
                        $adder_ids = $request->adder_ids;
                    }
                    $price = $this->jockeyPumpPriceCalculate($pump_price, $power, $frequency, $overhead, $ic_margin, $adder_ids);

                    return [
                        'success' => true,
                        'msg' => '',
                        'price' => $price['price']
                    ];
                break;
                
            case 'electrical':
                    return $this->electricalPumpPriceCalculate($request, $overhead, $ic_margin);
                break;
                
            case 'diesel':
                    return $this->dieselPumpPriceCalculate($request, $overhead, $ic_margin);
                break;
                
            case 'electrical-diesel':
                    return $this->electricalDieselPumpPriceCalculate($request, $overhead, $ic_margin);
                break;
        }
    }

    public function jockeyPumpPriceCalculate($pump_price, $power, $frequency, $overhead, $ic_margin, $adder_ids)
    {
        //$power = 30 //actual seletion
        //$power = 40.23 // when we are doing * with 1.341
        $control_panel = ControlPanelMaster::select('*')->where('category', 'Jockey')->where('frequency', $frequency)->get()->toArray();
        $control_panel_price = collect($control_panel)->pluck('unit_price', 'motor_power')->pipe(function ($data) use ($power) {
            $closest = null;
            $closest_price = null;
            foreach ($data as $item => $item_price) {
                if ($closest === null || abs($power - $closest) > abs($item - $power)) {
                    $closest = $item;
                    $closest_price = $item_price;
                }
            }
            return $closest_price;
        });

        $adderprice = 0;
        $adderpricelist = [];

        if (count($adder_ids) > 0) {
            $data = FireFightingAdders::select('id', 'adder_list','version','code', 'type')->where('version', 'FireFighting/Jockey')->whereIn('id', $adder_ids)->get();
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $type = str_replace(' ', '', strtolower($value->type));
                    $optional_master = OptionalMaster::where('category','Jockey')->where($type, 1)->first();
                    if ($optional_master) {
                        $adderprice = $adderprice + $optional_master->unit_price;
                        array_push($adderpricelist, [
                            'list' => $value->adder_list,
                            'code' => $value->id,
                            'price' => $optional_master->unit_price
                        ]);
                    }
                }
            }
        }

        $price = (($pump_price + $control_panel_price + $adderprice)*$overhead)/$ic_margin;
        return [
            'price' => $price,
            'pump_price' => $pump_price,
            'control_panel_price' => $control_panel_price,
            'adderprice' => $adderprice,
            'adderpricelist' => $adderpricelist
        ];
    }

    public function electricalPumpPriceCalculate($request, $overhead, $ic_margin, $cart = false)
    {
        $change = [
            'id' => 'id',
            'electrical_pumpmodels' => 'wilo_pump_models', 
            'electrical_pumptype' => 'pump_type', 
            'electrical_voltage' => 'voltage', // A Code: 11-03-2026
            'electrical_frequency' => 'frequency', 
            'electrical_pump_approval' => 'pump_approval', 
            'electrical_flow' => 'flow', 
            'electrical_head' => 'head', 
            'electrical_speed' => 'speed_rpm',
            'electrical_control_panel_type' => 'type',
            //'motor_power' => 'motor_power',//20250108 add motor power field in electrical flow
            'electrical_motor_power' => 'motor_power'//20250108 add motor power field in electrical flow
        ];

        $field_val = [];

        $electrical_control_panel_type = $electrical_voltage = $electrical_frequency = $electrical_pump_approval = $motor_power = ''; // A Code: 11-03-2026
       
        $fetchElectrical = ElectricalPump::select('*');
        foreach ($request->data as $key => $value) {
            if($value['name'] != 'electrical_control_panel_type' && $value['name'] != 'electrical_voltage'){
                $fetchElectrical = $fetchElectrical->where($change[$value['name']], $value['value']);
            }
            //start 20250107 taking filter for Control panel type
            if($value['name'] == 'electrical_control_panel_type'){
                $electrical_control_panel_type = $value['value'];
            }
            //end 20250107 taking filter for Control panel type
            // A Code: 10-03-2026 Start
            if($value['name'] == 'electrical_voltage'){
                $electrical_voltage = $value['value'];
            }
            // A Code: 10-03-2026 End
            if($value['name'] == 'electrical_frequency'){
                $electrical_frequency = $value['value'];
            }
            if($value['name'] == 'electrical_pump_approval'){
                $electrical_pump_approval = $value['value'];
            }
            if($value['name'] == 'motor_power'){
                $motor_power = $value['value'];
            }
            //start - 20250108 add motor power field in electrical flow
            if($value['name'] == 'electrical_motor_power'){
                $motor_power = $value['value'];
            }
            //end - 20250108 add motor power field in electrical flow
            array_push($field_val, [$value['name'] => $value['value']]);
        }
        $fetchElectrical = $fetchElectrical->first();
       
        if (!is_null($fetchElectrical)) {

            if ($motor_power == '') {
                $motor_power = $fetchElectrical->motor_power;
            }
            
            // Pump Price
            $pump_price = $fetchElectrical->unit_price;

            //$control_panel = ControlPanelMaster::select('*')->where('category', 'Electrical')->where('model', $fetchElectrical->control_panel_model)->where('frequency', $fetchElectrical->frequency)->where('motor_power', $fetchElectrical->motor_power)->first();
            $control_panel = ControlPanelMaster::select('*')->where('category', 'Electrical')
                                ->where('type', $electrical_control_panel_type )
                                ->where('voltage', $electrical_voltage) // A Code: 11-03-2026
                                ->where('frequency', $electrical_frequency)
                                ->where('motor_power',$motor_power)
                                ->where('approval', $electrical_pump_approval)
                                ->first();

            if (!is_null($control_panel)) {
                $control_panel_price = $control_panel->unit_price;
                $control_motor_power = (float)$control_panel->motor_power;

                // Adder Id price found
                $adderprice = 0;
                $adderpricelist = [];

                if (isset($request->adder_ids)) {
                    $data = FireFightingAdders::select('id', 'adder_list','version','code', 'type')->where('version', 'FireFighting/Electrical')->whereIn('id', $request->adder_ids)->get();
                    // dd($data);
                    if (count($data) > 0) {
                        foreach ($data as $key => $value) {
                            $type = str_replace(' ', '', strtolower($value->type));
                            if ($type != '' && $type != 'null') {
                                $type = $type == 'terminalbox' ? 'terminal_box' : $type;
                                $control_panel_model = explode("-", $control_panel->model);
                                $optional_master = OptionalMaster::where('category','Electrical')
                                ->where($type, 1);
                                if(!empty($control_panel_model[0]))
                                {
                                    $optional_master = $optional_master->where('model',$control_panel_model[0]);//for match model in firefighting_optional_master 20250129
                                }
                                $optional_master = $optional_master->where('min_power', '<=', $control_motor_power)
                                ->where('max_power', '>=', $control_motor_power)
                                ->first();
                                if ($optional_master) {
                                    $adderprice = $adderprice + $optional_master->unit_price;
                                    array_push($adderpricelist, [
                                        'list' => $value->adder_list,
                                        'code' => $value->id,
                                        'price' => $optional_master->unit_price
                                    ]);
                                } else {
                                    return [
                                        'success' => false,
                                        'msg' => $value->adder_list . ' data not match please contact to admin.',
                                        'price' => ''
                                    ];
                                }
                            } else {
                                if (strpos($value->adder_list, 'TEFC') !== false) {
                                    if ($fetchElectrical->speed_rpm >= 2900) {
                                        $pole = 2;
                                    } else {
                                        $pole = 4;
                                    }

                                    $motor = FireFightingMotor::where('motor_power', $control_motor_power)
                                        ->where('frequency', $fetchElectrical->frequency)
                                        ->where('number_of_pole', $pole)
                                        ->first();

                                    if (is_null($motor)) {
                                        return [
                                            'success' => false,
                                            'msg' => $value->adder_list . ' data not match please contact to admin.',
                                            'price' => ''
                                        ];
                                    } else {
                                        $adderprice = $adderprice + $motor->unit_price;
                                        array_push($adderpricelist, [
                                            'list' => $value->adder_list,
                                            'code' => $value->id,
                                            'price' => $motor->unit_price
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                // dump($pump_price , $control_panel_price , $adderprice);
                $price = (($pump_price + $control_panel_price + $adderprice)*$overhead)/$ic_margin;
                $return = [
                    'success' => true,
                    'msg' => '',
                    'price' => $price,
                    'data' => [
                        'wilo_pump_models' => $fetchElectrical->wilo_pump_models,
                        'pump_type' => $fetchElectrical->pump_type,
                        'voltage' => $fetchElectrical->voltage, // A Code: 11-03-2026
                        'frequency' => $fetchElectrical->frequency,
                        'motor_power' =>  $fetchElectrical->motor_power //20250108 add motor power field in electrical flow              
                    ]
                ];

                if ($cart) {
                    $return['price_list'] = [
                        'total_price' => $price,
                        'pump_price' => $pump_price,
                        'control_panel_price' => $control_panel_price,
                        'adderprice' => $adderprice,
                        'adderpricelist' => $adderpricelist
                    ];
                    $return['electrical_data'] = $fetchElectrical;
                    $return['field_val'] = $field_val;
                }

                return $return;
            } else {
                return [
                    'success' => false,
                    'msg' => 'Control panel modal data not match please try again..!!',
                    'price' => ''
                ];
            }
        } else {
            return [
                'success' => false,
                'msg' => 'Electrical Data not found please try again..!!',
                'price' => ''
            ];
        }
    }

    public function dieselPumpPriceCalculate($request, $overhead, $ic_margin, $cart = false)
    {
        $change = [
            'id' => 'id',
            'diesel_pumpmodels' => 'pump_models',
            'diesel_pumptype' => 'pump_type',
            //'diesel_voltage' => 'voltage', // A Code: 11-03-2026 Comment
            'diesel_frequency' => 'frequency',
            'diesel_pump_approval' => 'pump_approval',
            'diesel_engine_approval' => 'engine_approval',
            'diesel_flow' => 'flow',
            'diesel_head' => 'head',
            'diesel_speed' => 'speed_rpm',
            'diesel_control_panel_type' => 'type',
            'motor_power' => 'motor_power'
        ];

        $field_val = [];

        $fetchDiesel = DieselPump::select('*');
        foreach ($request->data as $key => $value) {
            if($value['name'] != 'diesel_control_panel_type'){
                $fetchDiesel = $fetchDiesel->where($change[$value['name']], $value['value']);
            }
            /*if($value['name'] == 'diesel_control_panel_type'){
                $diesel_control_panel_type = $value['value'];
            }
            if($value['name'] == 'diesel_frequency'){
                $diesel_frequency = $value['value'];
            }
            if($value['name'] == 'diesel_pump_approval'){
                $diesel_pump_approval = $value['value'];
            }
            if($value['name'] == 'motor_power'){
                $motor_power = $value['value'];
            }*/
            array_push($field_val, [$value['name'] => $value['value']]);
        }
        $fetchDiesel = $fetchDiesel->first();

        if (!is_null($fetchDiesel)) {
            
            // Pump Price
            $pump_price = $fetchDiesel->unit_price;
            

            // Control Panel Price Get
            $control_panel = ControlPanelMaster::select('*')->where('category', 'Diesel')
                                ->where('model', $fetchDiesel->control_panel_model)
                                ->where('frequency', $fetchDiesel->frequency)
                                ->first();
            //$control_panel = ControlPanelMaster::select('*')->where('category', 'Diesel')->where('type', $diesel_control_panel_type )->where('frequency', $diesel_frequency)->where('motor_power',$motor_power)->where('approval', $diesel_pump_approval)->first();

            if (!is_null($control_panel)) {
                $control_panel_price = $control_panel->unit_price;

                // Disel Tank Price Get
                $disel_tank = DieselTankMaster::where('tank_size', $fetchDiesel->diesel_tank_us)->first();
                if (!is_null($disel_tank)) {

                    $disel_tank_price = $disel_tank->unit_price;

                    // Battery Price Get
                    $battery = BatteryMaster::where('model', $fetchDiesel->battery_rating)->first();
                    if (!is_null($battery)) {
                        $battery_price = $battery->unit_price * $fetchDiesel->battery_qty;

                        // Adder Id price found
                        $adderprice = 0;
                        $adderpricelist = [];
                        if (isset($request->adder_ids)) {
                            $data = FireFightingAdders::select('id', 'adder_list','version','code', 'type')->where('version', 'FireFighting/Diesel')->whereIn('id', $request->adder_ids)->get();
                            if (count($data) > 0) {

                                foreach ($data as $key => $value) {
                                    $type = str_replace(' ', '', strtolower($value->type));
                                    if ($type != '' && (!is_null($type) && $type != 'null')) {
                                        $type = $type == 'terminalbox' ? 'terminal_box' : $type;

                                        $optional_master = OptionalMaster::where('category','Diesel')
                                        ->where($type, 1)
                                        ->first();
                                        if ($optional_master) {
                                            $adderprice = $adderprice + $optional_master->unit_price;
                                            array_push($adderpricelist, [
                                                'list' => $value->adder_list,
                                                'code' => $value->id,
                                                'price' => $optional_master->unit_price
                                            ]);
                                        }
                                    } else {
                                        if (strpos($value->adder_list, 'Pressure relief valve') !== false) {
                                            $search = [];
                                            if ($fetchDiesel->pressure_releif_valve != '' && $fetchDiesel->pressure_releif_valve != '-') {
                                                $find = str_replace('"', '', $fetchDiesel->pressure_releif_valve);
                                                $find = str_replace("''", '', $find);
                                                $search[] = $find;
                                                if (strpos('"', $find) === false) {
                                                    $search[] = $find.'"';
                                                }
                                                if (strpos("''", $find) === false) {
                                                    $search[] = $find."''";
                                                }
                                            }

                                            $pressure_releif_valve = FireFightingPressureReliefValve::whereIn('size', $search)->first();
                                            // dd($pressure_releif_valve, $search, $fetchDiesel);
                                            if (is_null($pressure_releif_valve)) {
                                                return [
                                                    'success' => false,
                                                    'msg' => $value->adder_list.' data not match please contact to admin.',
                                                    'price' => ''
                                                ];
                                            } else {
                                                $adderprice = $adderprice + $pressure_releif_valve->unit_price;
                                                array_push($adderpricelist, [
                                                    'list' => $value->adder_list,
                                                    'code' => $value->id,
                                                    'price' => $pressure_releif_valve->unit_price
                                                ]);
                                            }
                                        }
                                        if (strpos($value->adder_list, 'Flow meter') !== false) {
                                            $search = [];
                                            $find = str_replace('"', '', $fetchDiesel->flow_meter_size);
                                            $find = str_replace("''", '', $find);
                                            $search[] = $find;
                                            if (strpos('"', $find) === false) {
                                                $search[] = $find.'"';
                                            }
                                            if (strpos("''", $find) === false) {
                                                $search[] = $find."''";
                                            }
                                            $flow_meter = FireFightingFlowMeter::whereIn('size', $search)
                                                ->where('min_gpm', '<=', (float)$fetchDiesel->flow)
                                                ->where('max_gpm', '>=', (float)$fetchDiesel->flow)
                                                ->first();
                                            if (is_null($flow_meter)) {
                                                return [
                                                    'success' => false,
                                                    'msg' => $value->adder_list.' data not match please contact to admin.',
                                                    'price' => ''
                                                ];
                                            } else {
                                                $adderprice = $adderprice + $flow_meter->unit_price;
                                                array_push($adderpricelist, [
                                                    'list' => $value->adder_list,
                                                    'code' => $value->id,
                                                    'price' => $flow_meter->unit_price
                                                ]);
                                            }
                                        }
                                        if (strpos($value->adder_list, 'Waste cone') !== false) {
                                            $waste_cone_brand = FireFightingWasteCone::where('size', $fetchDiesel->waste_cone_brand)->first();
                                            if (is_null($waste_cone_brand)) {
                                                return [
                                                    'success' => false,
                                                    'msg' => $value->adder_list.' data not match please contact to admin.',
                                                    'price' => ''
                                                ];
                                            } else {
                                                $adderprice = $adderprice + $waste_cone_brand->unit_price;
                                                array_push($adderpricelist, [
                                                    'list' => $value->adder_list,
                                                    'code' => $value->id,
                                                    'price' => $waste_cone_brand->unit_price
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // dump($pump_price , $control_panel_price , $disel_tank_price , $battery_price , $adderprice, $overhead, $ic_margin);
                        $price = (($pump_price + $control_panel_price + $disel_tank_price + $battery_price + $adderprice)*$overhead)/$ic_margin;



                        $return = [
                            'success' => true,
                            'msg' => '',
                            'price' => $price,
                            'data' => [
                                'wilo_pump_models' => $fetchDiesel->pump_models,
                                'pump_type' => $fetchDiesel->pump_type,
                                //'voltage' => $fetchDiesel->voltage, // A Code: 11-03-2026 Comment
                                'frequency' => $fetchDiesel->frequency,
                            ]
                        ];

                        if ($cart) {
                            $return['price_list'] = [
                                'total_price' => $price,
                                'pump_price' => $pump_price,
                                'control_panel_price' => $control_panel_price,
                                'disel_tank_price' => $disel_tank_price,
                                'battery_orignal_price' => $battery->unit_price,
                                'battery_qty' => $fetchDiesel->battery_qty,
                                'battery_price' => $battery_price,
                                'adderprice' => $adderprice,
                                'adderpricelist' => $adderpricelist
                            ];
                            $return['diesel_data'] = $fetchDiesel;
                            $return['field_val'] = $field_val;
                        }

                        foreach ($adderpricelist as $adder_key => $adder_val) {
                            if (in_array('Pressure relief valve', $adder_val)) {
                                $return['data']['Pressure relief valve'] = $this->showAmount(($adder_val['price']*$overhead)/$ic_margin); 
                            }
                            if (in_array('Flow meter', $adder_val)) {
                                $return['data']['Flow meter'] = $this->showAmount(($adder_val['price']*$overhead)/$ic_margin); 
                            }
                            if (in_array('Waste cone', $adder_val)) {
                                $return['data']['Waste cone'] = $this->showAmount(($adder_val['price']*$overhead)/$ic_margin); 
                            }
                        }

                        // dd($return);
                        return $return;
                    } else {
                        return [
                            'success' => false,
                            'msg' => 'Battery master modal data not match please try again..!!',
                            'price' => ''
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'msg' => 'Disel Tank size data not match please try again..!!',
                        'price' => ''
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'msg' => 'Control panel modal data not match please try again..!!',
                    'price' => ''
                ];
            }
        } else {
            return [
                'success' => false,
                'msg' => 'Disel Data not found please try again..!!',
                'price' => ''
            ];
        }
    }

    public function electricalDieselPumpPriceCalculate($request, $overhead, $ic_margin, $cart = false)
    {
        $request->data = $request->electrical_data;
        $electrical = $this->electricalPumpPriceCalculate($request, $overhead, $ic_margin, $cart = false);
        if (!$electrical['success']) {
            return $electrical;
        }


        $request->data = $request->diesel_data;
        $diesel = $this->dieselPumpPriceCalculate($request, $overhead, $ic_margin, $cart = false);
        if (!$diesel['success']) {
            return $diesel;
        }


        $diesel_append = '';
        if (array_key_exists('Pressure relief valve', $diesel['data'])) {
            $diesel_append .= '<li class="grey">Pressure relief valve: '.$diesel['data']['Pressure relief valve'].'$</li>';
        }
        if (array_key_exists('Flow meter', $diesel['data'])) {
            $diesel_append .= '<li class="grey">Flow meter: '.$diesel['data']['Flow meter'].'$</li>';
        }
        if (array_key_exists('Waste cone', $diesel['data'])) {
            $diesel_append .= '<li class="grey">Waste cone: '.$diesel['data']['Waste cone'].'$</li>';
        }
        // dd($diesel, $diesel_append);



        // if (in_array('Pressure relief valve', )) {
        //     $return['data']['Pressure relief valve'] = $diesel['data']['price']; 
        // }
        // if (in_array('Flow meter', $diesel['data'])) {
        //     $return['data']['Flow meter'] = $diesel['data']['price']; 
        // }
        // if (in_array('Waste cone', $diesel['data'])) {
        //     $return['data']['Waste cone'] = $diesel['data']['price']; 
        // }
        // dd($electrical, $diesel);

        // A Code: 11-03-2026 Start
        return [
            'success' => true,
            'html' => '<div class="row">
                <div class="col-6">
                    <div class="columns">
                        <ul class="price" style="list-style: none;">
                            <li class="header"><u>Electrical</u></li>
                            <li class="header">'.$electrical['data']['wilo_pump_models'].'</li>
                            <li class="grey">'.$electrical['data']['pump_type'].'</li>
                            <li class="grey">'.$electrical['data']['voltage'].'</li>
                            <li class="grey">'.$electrical['data']['frequency'].' </li>
                            <li>Total Price: <b>'.round($electrical['price'], 2).'</b><span>$</span> </li>  
                        </ul>
                    </div>
                </div>
                <div class="col-6">
                    <div class="columns">
                        <ul class="price" style="list-style: none;">
                            <li class="header"><u>Diesel</u></li>
                            <li class="header">'.$diesel['data']['wilo_pump_models'].'</li>
                            <li class="grey">'.$diesel['data']['pump_type'].'</li>
                            <li class="grey">'.$diesel['data']['frequency'].' </li>
                            '.$diesel_append.'<li>Total Price: <b>'.round($diesel['price'], 2).'</b><span>$</span> </li>  
                        </ul>
                    </div>
                </div>
            </div>'
        ];
        // A Code: 11-03-2026 End

        // dd($electrical, $diesel);
    }

    public function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
        $separator = '';
        if($separate){
            $separator = ',';
        }
        $printAmount = number_format($amount, $decimal, '.', $separator);
        if($exceptZeros){
        $exp = explode('.', $printAmount);
            if($exp[1]*1 == 0){
                $printAmount = $exp[0];
            }
        }
        return $printAmount;
    }
}